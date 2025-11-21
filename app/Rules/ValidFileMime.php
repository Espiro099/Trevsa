<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ValidFileMime implements ValidationRule
{
    public function __construct(
        private readonly array $allowedMimes,
        private readonly ?string $customMessage = null
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile) {
            return;
        }

        if (! $value->isValid()) {
            $fail('El archivo cargado no es válido.');
            return;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $realMime = $finfo ? finfo_file($finfo, $value->getRealPath()) : $value->getMimeType();

        if ($finfo) {
            finfo_close($finfo);
        }

        if (! $realMime || ! in_array($realMime, $this->allowedMimes, true)) {
            $fail($this->customMessage ?: 'El tipo de archivo no está permitido.');
        }
    }
}


