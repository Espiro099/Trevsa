<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceUploadLimits
{
    private const MAX_POST_BYTES = 200 * 1024 * 1024; // 200MB

    public function handle(Request $request, Closure $next): Response
    {
        $this->configurePhpLimits();

        $contentLength = (int) $request->server('CONTENT_LENGTH', 0);

        if ($contentLength > self::MAX_POST_BYTES) {
            $message = 'El tamaño total de la carga excede el límite permitido (200MB).';

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => $message,
                    'max_bytes' => self::MAX_POST_BYTES,
                    'content_length' => $contentLength,
                ], 413);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => $message]);
        }

        return $next($request);
    }

    private function configurePhpLimits(): void
    {
        ini_set('upload_max_filesize', '100M');
        ini_set('post_max_size', '200M');
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '300');
        ini_set('max_input_time', '300');
    }
}


