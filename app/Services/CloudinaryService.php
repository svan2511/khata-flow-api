<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    private Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key' => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ],
        ]);
    }

    public function upload(UploadedFile $file, string $folder = 'dukaan-sahayak', array $options = []): ?array
    {
        try {
            $uploadOptions = array_merge([
                'folder' => $folder,
                'resource_type' => 'image',
            ], $options);

            $uploaded = $this->cloudinary->uploadApi()->upload(
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
            $this->cloudinary->uploadApi()->destroy($publicId);

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
        return $this->cloudinary->image($publicId)->toUrl();
    }
}
