<?php

namespace App\Http\Controllers;

use App\DTOs\VoiceParseDTO;
use App\Exceptions\VoiceParseException;
use App\Http\Requests\Voice\VoiceParseRequest;
use App\Services\VoiceParseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class VoiceController extends Controller
{
    public function __construct(
        private readonly VoiceParseService $voiceParseService,
    ) {}

    public function parse(VoiceParseRequest $request): JsonResponse
    {
        try {
            $result = $this->voiceParseService->parse(
                $request->user(),
                VoiceParseDTO::fromRequest($request->validated()),
            );

            return $this->success($result->toArray(), 'Voice bill draft ready.');
        } catch (VoiceParseException $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        } catch (\Throwable $e) {
            Log::error('Voice parse failed', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->error('Voice parsing failed. Please try again.');
        }
    }
}
