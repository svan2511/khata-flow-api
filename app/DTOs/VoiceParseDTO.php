<?php

namespace App\DTOs;

class VoiceParseDTO
{
    public function __construct(
        public readonly string $transcript,
        public readonly string $language = 'hi',
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            transcript: trim((string) $data['transcript']),
            language: $data['language'] ?? 'hi',
        );
    }
}
