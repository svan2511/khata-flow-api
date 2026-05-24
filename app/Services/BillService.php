<?php

namespace App\Services;

use App\DTOs\CreateBillDTO;
use App\DTOs\PaymentDTO;
use App\Exceptions\BillingException;
use App\Exceptions\InsufficientStockException;
use App\Models\Bill;
use App\Models\User;
use App\Repositories\Contracts\BillingRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ShopRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BillService
{
    public function __construct(
        private readonly BillingRepositoryInterface $billingRepository,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly ShopRepositoryInterface $shopRepository,
    ) {}

    public function createBill(User $user, CreateBillDTO $dto): Bill
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            throw new BillingException('Shop not found. Please setup your shop first.');
        }

        $customer = null;
        if ($dto->hasCustomer()) {
            $customer = $this->customerRepository->findByUuid($dto->customerUuid, $shop->id);

            if (! $customer) {
                throw new BillingException('Customer not found.');
            }
        }

        return DB::transaction(function () use ($user, $shop, $dto, $customer) {
            $billNumber = $this->generateBillNumber($shop->id);

            $itemRecords = [];
            $subtotal = 0;
            $totalItemDiscount = 0;
            $totalTax = 0;
            $itemsTotalAfterDiscount = 0;

            foreach ($dto->items as $itemDTO) {
                if ($itemDTO->isCustomItem()) {
                    $productName = $itemDTO->productName ?? 'Custom Item';
                    $unitPrice = $itemDTO->unitPrice ?? 0;
                    $productId = null;
                } else {
                    $product = $this->productRepository->findByUuid($itemDTO->productUuid, $shop->id);

                    if (! $product) {
                        throw new BillingException("Product not found: {$itemDTO->productUuid}");
                    }

                    if ($product->stock_quantity > 0 && $product->stock_quantity < $itemDTO->quantity) {
                        throw new InsufficientStockException(
                            "Insufficient stock for '{$product->name}'. Available: {$product->stock_quantity}, Requested: {$itemDTO->quantity}"
                        );
                    }

                    $productName = $product->name;
                    $unitPrice = (float) $product->price;
                    $productId = $product->id;
                }

                $itemSubtotal = $unitPrice * (float) $itemDTO->quantity;

                $itemDiscountAmount = $this->calculateDiscountAmount(
                    $itemSubtotal,
                    $itemDTO->discountType,
                    $itemDTO->discountValue
                );

                $gstRate = $itemDTO->gstRate ?? 0;
                $taxableAmount = $itemSubtotal - $itemDiscountAmount;
                $gstAmount = $taxableAmount * ($gstRate / 100);
                $cgst = round($gstAmount / 2, 2);
                $sgst = round($gstAmount / 2, 2);

                $itemTotal = round($taxableAmount + $gstAmount, 2);

                $itemRecords[] = [
                    'uuid' => (string) Str::uuid(),
                    'product_id' => $productId,
                    'product_name' => $productName,
                    'quantity' => $itemDTO->quantity,
                    'unit_price' => $unitPrice,
                    'discount_type' => $itemDTO->discountType ?? 'fixed',
                    'discount_value' => $itemDTO->discountValue ?? 0,
                    'discount' => $itemDiscountAmount,
                    'gst_rate' => $gstRate,
                    'cgst' => $cgst,
                    'sgst' => $sgst,
                    'tax' => round($gstAmount, 2),
                    'subtotal' => round($itemSubtotal, 2),
                    'total' => $itemTotal,
                ];

                if (! $itemDTO->isCustomItem() && $product->stock_quantity > 0) {
                    $product->decrement('stock_quantity', $itemDTO->quantity);
                }

                $subtotal = round($subtotal + $itemSubtotal, 2);
                $totalItemDiscount = round($totalItemDiscount + $itemDiscountAmount, 2);
                $totalTax = round($totalTax + $gstAmount, 2);
                $itemsTotalAfterDiscount = round($itemsTotalAfterDiscount + $itemTotal, 2);
            }

            $billDiscountAmount = $this->calculateDiscountAmount(
                $subtotal,
                $dto->discountType,
                $dto->discountValue
            );

            $grandTotal = round(max(0, $itemsTotalAfterDiscount - $billDiscountAmount), 2);

            $paidAmount = $dto->paidAmount;
            if ($paidAmount === null) {
                $paidAmount = $dto->isCredit() ? 0 : $grandTotal;
            }
            $paidAmount = round($paidAmount, 2);
            $dueAmount = round(max(0, $grandTotal - $paidAmount), 2);
            $totalDiscount = round($totalItemDiscount + $billDiscountAmount, 2);

            if ($paidAmount > 0 && $paidAmount > $grandTotal) {
                throw new BillingException('Paid amount cannot exceed the total bill amount.');
            }

            if ($dto->isCredit() && ! $customer) {
                throw new BillingException('Customer is required for credit (Udhaar) billing.');
            }

            $paymentStatus = $this->determinePaymentStatus($paidAmount, $grandTotal);

            $bill = $this->billingRepository->create([
                'uuid' => (string) Str::uuid(),
                'bill_number' => $billNumber,
                'shop_id' => $shop->id,
                'user_id' => $user->id,
                'customer_id' => $customer?->id,
                'subtotal' => $subtotal,
                'discount_type' => $dto->discountType ?? 'fixed',
                'discount_value' => $dto->discountValue ?? 0,
                'discount' => $totalDiscount,
                'tax' => $totalTax,
                'total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_status' => $paymentStatus,
                'payment_method' => $dto->paymentMethod,
                'notes' => $dto->notes,
            ]);

            $bill->items()->createMany($itemRecords);

            if ($paidAmount > 0) {
                $bill->payments()->create([
                    'uuid' => (string) Str::uuid(),
                    'shop_id' => $shop->id,
                    'customer_id' => $customer?->id,
                    'amount' => $paidAmount,
                    'payment_method' => $dto->paymentMethod,
                    'payment_date' => now(),
                ]);
            }

            if ($dueAmount > 0 && $customer) {
                $customer->increment('total_credit', $dueAmount);
            }

            Log::info('Bill created', [
                'bill_number' => $billNumber,
                'shop_id' => $shop->id,
                'total' => $grandTotal,
                'payment_status' => $paymentStatus,
            ]);

            return $bill->load(['items', 'customer', 'payments']);
        });
    }

    public function getBill(User $user, string $uuid): ?Bill
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            return null;
        }

        return $this->billingRepository->findByUuid($uuid, $shop->id);
    }

    public function listBills(User $user, array $filters = []): LengthAwarePaginator
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            throw new BillingException('Shop not found.');
        }

        return $this->billingRepository->listBills($shop->id, $filters);
    }

    public function addPayment(User $user, string $billUuid, PaymentDTO $dto): Bill
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            throw new BillingException('Shop not found.');
        }

        $bill = $this->billingRepository->findByUuid($billUuid, $shop->id);

        if (! $bill) {
            throw new BillingException('Bill not found.', 404);
        }

        if ($bill->isPaid()) {
            throw new BillingException('Bill is already fully paid.');
        }

        if ($dto->amount <= 0) {
            throw new BillingException('Payment amount must be positive.');
        }

        if ($dto->amount > $bill->due_amount) {
            throw new BillingException(
                "Payment amount ({$dto->amount}) exceeds due amount ({$bill->due_amount})."
            );
        }

        return DB::transaction(function () use ($bill, $shop, $dto) {
            $newPaidAmount = round($bill->paid_amount + $dto->amount, 2);
            $newDueAmount = round($bill->total - $newPaidAmount, 2);
            $paymentStatus = $newDueAmount <= 0 ? 'paid' : 'partial';

            $bill->payments()->create([
                'uuid' => (string) Str::uuid(),
                'shop_id' => $shop->id,
                'customer_id' => $bill->customer_id,
                'amount' => $dto->amount,
                'payment_method' => $dto->paymentMethod,
                'reference' => $dto->reference,
                'notes' => $dto->notes,
                'payment_date' => now(),
            ]);

            $bill->update([
                'paid_amount' => $newPaidAmount,
                'due_amount' => $newDueAmount,
                'payment_status' => $paymentStatus,
            ]);

            if ($bill->customer) {
                $newCredit = round($bill->customer->total_credit - $dto->amount, 2);
                $bill->customer->update(['total_credit' => max(0, $newCredit)]);
            }

            Log::info('Payment added to bill', [
                'bill_number' => $bill->bill_number,
                'amount' => $dto->amount,
                'new_due' => $newDueAmount,
            ]);

            return $bill->fresh(['items', 'customer', 'payments']);
        });
    }

    private function generateBillNumber(int $shopId): string
    {
        $prefix = 'BILL-'.now()->format('Ymd');
        $latest = $this->billingRepository->getLatestBillNumber($shopId, $prefix);

        if ($latest) {
            $parts = explode('-', $latest);
            $sequence = (int) end($parts) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix.'-'.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }

    private function calculateDiscountAmount(float $baseAmount, ?string $discountType, ?float $discountValue): float
    {
        if (! $discountValue || $discountValue <= 0) {
            return 0;
        }

        return match ($discountType) {
            'percentage' => round($baseAmount * ($discountValue / 100), 2),
            default => round(min($discountValue, $baseAmount), 2),
        };
    }

    private function determinePaymentStatus(float $paidAmount, float $grandTotal): string
    {
        if ($paidAmount <= 0) {
            return 'pending';
        }

        if ($paidAmount >= $grandTotal) {
            return 'paid';
        }

        return 'partial';
    }
}
