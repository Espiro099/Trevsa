<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Filtros de fecha (por defecto: últimos 30 días)
        $fechaDesde = $request->get('fecha_desde', Carbon::now()->subDays(30)->format('Y-m-d'));
        $fechaHasta = $request->get('fecha_hasta', Carbon::now()->format('Y-m-d'));
        
        $fechaDesdeCarbon = Carbon::parse($fechaDesde)->startOfDay();
        $fechaHastaCarbon = Carbon::parse($fechaHasta)->endOfDay();

        $cacheKey = $this->buildDashboardCacheKey($fechaDesdeCarbon, $fechaHastaCarbon);

        $metrics = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($fechaDesdeCarbon, $fechaHastaCarbon) {
            return $this->calculateDashboardMetrics($fechaDesdeCarbon, $fechaHastaCarbon);
        });

        return view('dashboard', array_merge(
            $metrics,
            [
                'fechaDesde' => $fechaDesde,
                'fechaHasta' => $fechaHasta,
            ]
        ));
    }

    private function buildDashboardCacheKey(Carbon $desde, Carbon $hasta): string
    {
        return sprintf(
            'dashboard:%s:%s',
            $desde->format('YmdHis'),
            $hasta->format('YmdHis')
        );
    }

    private function calculateDashboardMetrics(Carbon $fechaDesdeCarbon, Carbon $fechaHastaCarbon): array
    {
        $totalServicios = Servicio::count();
        $enTransito = Servicio::whereNotIn('estado', ['finalizado', 'completado'])->count();
        $finalizadas = Servicio::whereIn('estado', ['finalizado', 'completado'])->count();
        $alertas = Servicio::where('estado', 'pendiente')
            ->where('fecha_servicio', '<', now())
            ->count();

        $serviciosFiltrados = Servicio::whereBetween('fecha_servicio', [$fechaDesdeCarbon, $fechaHastaCarbon])
            ->get([
                'fecha_servicio',
                'estado',
                'tarifa_cliente',
                'tarifa_proveedor',
                'tipo_transporte',
            ]);

        $totalFiltrado = $serviciosFiltrados->count();
        $finalizadasFiltrado = $serviciosFiltrados->whereIn('estado', ['finalizado', 'completado'])->count();
        $enTransitoFiltrado = $serviciosFiltrados->reject(function ($servicio) {
            return in_array($servicio->estado, ['finalizado', 'completado'], true);
        })->count();

        $ingresosTotales = $serviciosFiltrados->sum(function ($servicio) {
            return (float) ($servicio->tarifa_cliente ?? 0);
        });

        $costosTotales = $serviciosFiltrados->sum(function ($servicio) {
            return (float) ($servicio->tarifa_proveedor ?? 0);
        });

        $margenTotal = $ingresosTotales - $costosTotales;

        $diferenciaDias = $fechaHastaCarbon->diffInDays($fechaDesdeCarbon);
        $periodo = $this->determinePeriodo($diferenciaDias);

        $tendencias = $this->buildTendencias($serviciosFiltrados, $fechaDesdeCarbon, $fechaHastaCarbon, $periodo);

        $distribucionEstado = $serviciosFiltrados->pluck('estado')
            ->filter()
            ->countBy()
            ->toArray();

        $distribucionTipo = $serviciosFiltrados->pluck('tipo_transporte')
            ->filter()
            ->countBy()
            ->toArray();

        $mesAnteriorInicio = (clone $fechaDesdeCarbon)->subMonth()->startOfMonth();
        $mesAnteriorFin = (clone $fechaDesdeCarbon)->subMonth()->endOfMonth();

        $serviciosMesAnterior = Servicio::whereBetween('fecha_servicio', [$mesAnteriorInicio, $mesAnteriorFin])->count();
        $finalizadasMesAnterior = Servicio::whereBetween('fecha_servicio', [$mesAnteriorInicio, $mesAnteriorFin])
            ->whereIn('estado', ['finalizado', 'completado'])
            ->count();

        $cargas = Servicio::orderBy('_id', 'desc')->limit(10)->get()
            ->map(function ($r) {
                return [
                    'id' => (string) ($r->_id ?? $r->id ?? ''),
                    'cliente' => $r->cliente_nombre ?? '-',
                    'origen' => $r->origen ?? '-',
                    'destino' => $r->destino ?? '-',
                    'salida' => isset($r->fecha_servicio) ? $r->fecha_servicio->toDateTimeString() : null,
                    'estado' => $r->estado ?? 'En tránsito',
                    'total' => isset($r->tarifa_cliente) ? ('$' . number_format($r->tarifa_cliente, 2)) : null,
                ];
            })->toArray();

        return [
            'kpis' => [
                'total' => $totalServicios,
                'en_transito' => $enTransito,
                'finalizadas' => $finalizadas,
                'alertas' => $alertas,
                'total_filtrado' => $totalFiltrado,
                'finalizadas_filtrado' => $finalizadasFiltrado,
                'en_transito_filtrado' => $enTransitoFiltrado,
                'ingresos' => $ingresosTotales,
                'costos' => $costosTotales,
                'margen' => $margenTotal,
            ],
            'tendencias' => $tendencias,
            'distribucionEstado' => $distribucionEstado,
            'distribucionTipo' => $distribucionTipo,
            'periodo' => $periodo,
            'serviciosMesAnterior' => $serviciosMesAnterior,
            'finalizadasMesAnterior' => $finalizadasMesAnterior,
            'cargas' => $cargas,
        ];
    }

    private function determinePeriodo(int $diferenciaDias): string
    {
        if ($diferenciaDias <= 31) {
            return 'dia';
        }

        if ($diferenciaDias <= 365) {
            return 'semana';
        }

        return 'mes';
    }

    private function buildTendencias(Collection $servicios, Carbon $desde, Carbon $hasta, string $periodo): array
    {
        $serviciosConFecha = $servicios->filter(fn ($servicio) => $servicio->fecha_servicio instanceof Carbon);

        $agrupados = $serviciosConFecha->groupBy(function ($servicio) use ($periodo) {
            $fecha = $servicio->fecha_servicio->copy();

            return match ($periodo) {
                'dia' => $fecha->format('Y-m-d'),
                'semana' => $fecha->startOfWeek()->format('Y-m-d'),
                default => $fecha->startOfMonth()->format('Y-m-d'),
            };
        })->map(function (Collection $items) {
            return [
                'total' => $items->count(),
                'finalizadas' => $items->whereIn('estado', ['finalizado', 'completado'])->count(),
            ];
        });

        $tendencias = [];

        switch ($periodo) {
            case 'dia':
                $cursor = $desde->copy();
                while ($cursor <= $hasta) {
                    $key = $cursor->format('Y-m-d');
                    $values = $agrupados->get($key, ['total' => 0, 'finalizadas' => 0]);

                    $tendencias[] = [
                        'fecha' => $key,
                        'label' => $cursor->format('d/m'),
                        'total' => $values['total'],
                        'finalizadas' => $values['finalizadas'],
                    ];

                    $cursor->addDay();
                }
                break;

            case 'semana':
                $cursor = $desde->copy()->startOfWeek();
                $limit = $hasta->copy()->startOfWeek();

                while ($cursor <= $limit) {
                    $key = $cursor->format('Y-m-d');
                    $values = $agrupados->get($key, ['total' => 0, 'finalizadas' => 0]);

                    $tendencias[] = [
                        'fecha' => $key,
                        'label' => 'Sem ' . $cursor->format('W/Y'),
                        'total' => $values['total'],
                        'finalizadas' => $values['finalizadas'],
                    ];

                    $cursor->addWeek();
                }
                break;

            default:
                $cursor = $desde->copy()->startOfMonth();
                $limit = $hasta->copy()->startOfMonth();

                while ($cursor <= $limit) {
                    $key = $cursor->format('Y-m-d');
                    $values = $agrupados->get($key, ['total' => 0, 'finalizadas' => 0]);

                    $tendencias[] = [
                        'fecha' => $key,
                        'label' => $cursor->format('M/Y'),
                        'total' => $values['total'],
                        'finalizadas' => $values['finalizadas'],
                    ];

                    $cursor->addMonth();
                }
                break;
        }

        return $tendencias;
    }
}
