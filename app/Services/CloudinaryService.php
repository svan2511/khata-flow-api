<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    private ?Cloudinary $cloudinary = null;

    protected function client(): ?Cloudinary
    {
        if ($this->cloudinary === null) {
            $cloudName = config('cloudinary.cloud_name');
            $apiKey = config('cloudinary.api_key');
            $apiSecret = config('cloudinary.api_secret');

            if (!$cloudName || !$apiKey || !$apiSecret) {
                return null;
            }

            $this->cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => $cloudName,
                    'api_key' => $apiKey,
                    'api_secret' => $apiSecret,
                ],
            ]);
        }

        return $this->cloudinary;
    }

    public function upload(UploadedFile $file, string $folder = 'dukaan-sahayak', array $options = []): ?array
    {
        try {
            $client = $this->client();
            if (!$client) {
                Log::warning('Cloudinary not configured, skipping upload');

                return null;
            }

            $uploadOptions = array_merge([
                'folder' => $folder,
                'resource_type' => 'image',
            ], $options);

            $uploaded = $client->uploadApi()->upload(
                $file->getRealPath(),
                $uploadOptions
            );

            return [
                'url' => $uploaded['secure_url'],
                'public_id' => $uploaded['public_id'],
                'format' => $uploaded['format'] ?? null,
                'size' => $uploaded['bytes'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Cloudinary upload failed', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);

            return null;
        }
    }

    public function delete(string $publicId): bool
    {
        try {
            $client = $this->client();
            if (!$client) {
                return false;
            }
            $client->uploadApi()->destroy($publicId);

            return true;
        } catch (\Exception $e) {
            Log::error('Cloudinary delete failed', [
                'public_id' => $publicId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function getImageUrl(string $publicId, array $transformations = []): string
    {
        $client = $this->client();
        if (!$client) {
            return '';
        }

        return $client->image($publicId)->toUrl();
    }
}
