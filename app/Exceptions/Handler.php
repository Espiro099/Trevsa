<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if ($this->shouldntReport($e)) {
                return;
            }

            $context = $this->buildExceptionContext($e);

            try {
                Log::channel('structured')->error($e->getMessage(), $context);
            } catch (Throwable $loggingException) {
                Log::error($e->getMessage(), $context);
                Log::debug('Logging channel "structured" is not available.', [
                    'logging_exception' => $loggingException->getMessage(),
                ]);
            }
        });

        $this->renderable(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() === 403) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => __('No tienes permisos para acceder a esta sección.'),
                        'request_id' => $request->attributes->get('exception_uuid'),
                    ], 403);
                }

                return $this->respondWithErrorView($request, 'errors.403', 403, [
                    'requestId' => $request->attributes->get('exception_uuid'),
                ]);
            }
        });

        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('No se encontró el recurso solicitado.'),
                    'request_id' => $request->attributes->get('exception_uuid'),
                ], 404);
            }

            return $this->respondWithErrorView($request, 'errors.404', 404, [
                'requestId' => $request->attributes->get('exception_uuid'),
                'path' => $request->path(),
            ]);
        });

        $this->renderable(function (Throwable $e, Request $request) {
            if (config('app.debug')) {
                return null;
            }

            if ($e instanceof HttpExceptionInterface && $e->getStatusCode() !== 500) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('Ha ocurrido un error inesperado.'),
                    'request_id' => $request->attributes->get('exception_uuid'),
                ], 500);
            }

            return $this->respondWithErrorView($request, 'errors.500', 500, [
                'requestId' => $request->attributes->get('exception_uuid'),
            ]);
        });
    }

    /**
     * Construye el contexto detallado para registrar excepciones.
     */
    protected function buildExceptionContext(Throwable $e): array
    {
        $context = [
            'exception' => get_class($e),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ];

        try {
            $request = request();

            if ($request instanceof Request) {
                $requestId = $request->attributes->get('exception_uuid');

                if (! $requestId) {
                    $requestId = (string) Str::uuid();
                    $request->attributes->set('exception_uuid', $requestId);
                }

                $context['request'] = [
                    'id' => $requestId,
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'ip' => $request->ip(),
                    'user_id' => optional($request->user())->id,
                    'payload' => $this->sanitizeRequestPayload($request),
                    'headers' => Arr::except($request->headers->all(), ['cookie', 'authorization']),
                ];
            }
        } catch (Throwable $requestException) {
            $context['request_context_error'] = $requestException->getMessage();
        }

        return $context;
    }

    /**
     * Sanitiza la carga del request para evitar exponer datos sensibles.
     */
    protected function sanitizeRequestPayload(Request $request): array
    {
        return Arr::map(
            Arr::except($request->all(), $this->dontFlash),
            fn ($value) => in_array(gettype($value), ['array', 'object'], true)
                ? json_decode(json_encode($value), true)
                : $value
        );
    }

    /**
     * Renderiza una vista de error, reutilizando el layout adecuado.
     */
    protected function respondWithErrorView(Request $request, string $view, int $status, array $data = [])
    {
        if ($request->expectsJson()) {
            return null;
        }

        if (! view()->exists($view)) {
            return null;
        }

        return response()->view($view, $data, $status);
    }
}
