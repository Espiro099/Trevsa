<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between animate-slide-up">
            <div>
                <h2 class="text-4xl font-bold font-display gradient-text">
                    {{ __('Dashboard') }}
                </h2>
                <p class="text-sm text-text-muted mt-2">Resumen ejecutivo del sistema</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-full animate-fade-in">
        <!-- KPIs Principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Total Servicios -->
            <div class="stat-card animate-scale-in" style="animation-delay: 0.1s">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h3 class="text-xs font-medium text-text-muted uppercase tracking-wider">Total Servicios</h3>
                        <p class="text-4xl font-bold text-text-primary mt-3">{{ $kpis['total_filtrado'] ?? 0 }}</p>
                        <p class="text-xs text-text-muted mt-2 flex items-center">
                            @if(isset($serviciosMesAnterior) && $serviciosMesAnterior > 0)
                                @php
                                    $variacion = (($kpis['total_filtrado'] - $serviciosMesAnterior) / $serviciosMesAnterior) * 100;
                                @endphp
                                <span class="{{ $variacion >= 0 ? 'text-green-400' : 'text-error' }} font-semibold">
                                    {{ $variacion >= 0 ? '↑' : '↓' }} {{ abs(number_format($variacion, 1)) }}%
                                </span>
                                <span class="ml-2">vs mes anterior</span>
                            @else
                                Período seleccionado
                            @endif
                        </p>
                    </div>
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-primary to-accent hover:scale-110 transition-transform duration-300 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Finalizadas -->
            <div class="stat-card animate-scale-in" style="animation-delay: 0.2s">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h3 class="text-xs font-medium text-text-muted uppercase tracking-wider">Finalizadas</h3>
                        <p class="text-4xl font-bold text-text-primary mt-3">{{ $kpis['finalizadas_filtrado'] ?? 0 }}</p>
                        <p class="text-xs text-text-muted mt-2 flex items-center">
                            @if(isset($finalizadasMesAnterior) && $finalizadasMesAnterior > 0)
                                @php
                                    $variacion = (($kpis['finalizadas_filtrado'] - $finalizadasMesAnterior) / $finalizadasMesAnterior) * 100;
                                @endphp
                                <span class="{{ $variacion >= 0 ? 'text-green-400' : 'text-error' }} font-semibold">
                                    {{ $variacion >= 0 ? '↑' : '↓' }} {{ abs(number_format($variacion, 1)) }}%
                                </span>
                                <span class="ml-2">vs mes anterior</span>
                            @else
                                Período seleccionado
                            @endif
                        </p>
                    </div>
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 hover:scale-110 transition-transform duration-300 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Gráfico de Tendencias -->
            <div class="glass-card animate-slide-up" style="animation-delay: 0.2s">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-bold font-display text-text-primary">Tendencias de Servicios</h4>
                </div>
                <canvas id="tendenciasChart" height="100"></canvas>
            </div>

            <!-- Gráfico de Distribución por Estado -->
            <div class="glass-card animate-slide-up" style="animation-delay: 0.3s">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-bold font-display text-text-primary">Distribución por Estado</h4>
                </div>
                <canvas id="estadoChart"></canvas>
            </div>
        </div>

        <!-- Gráfico de Tipo de Transporte y Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="glass-card animate-slide-up" style="animation-delay: 0.4s">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-bold font-display text-text-primary">Distribución por Tipo de Transporte</h4>
                </div>
                <canvas id="tipoTransporteChart"></canvas>
            </div>

            <!-- Quick Actions -->
            <div class="modern-card animate-slide-up" style="animation-delay: 0.5s">
                <div class="modern-card-header">
                    <h4 class="text-lg font-bold font-display text-text-primary flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Acciones Rápidas
                    </h4>
                </div>
                <div class="modern-card-body">
                    <div class="space-y-3">
                        @if(auth()->check() && optional(auth()->user())->hasPermission('registro.manage'))
                            <a href="{{ route('registro.create') }}" 
                               class="flex items-center p-4 rounded-xl border border-border-color hover:border-primary hover:bg-bg-glass-strong transition-all duration-300 group">
                                <div class="mr-4 p-3 rounded-xl bg-gradient-to-br from-primary to-accent group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </div>
                                <div>
                                    <h5 class="font-semibold text-text-primary group-hover:text-primary transition-colors">Nuevo Servicio</h5>
                                    <p class="text-sm text-text-muted">Registrar nueva carga</p>
                                </div>
                            </a>

                            <a href="{{ route('clientes.create') }}" 
                               class="flex items-center p-4 rounded-xl border border-border-color hover:border-primary hover:bg-bg-glass-strong transition-all duration-300 group">
                                <div class="mr-4 p-3 rounded-xl bg-gradient-to-br from-secondary to-primary group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h5 class="font-semibold text-text-primary group-hover:text-primary transition-colors">Nuevo Cliente</h5>
                                    <p class="text-sm text-text-muted">Registrar prospecto</p>
                                </div>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Actividad Reciente -->
        <div class="modern-card mb-8 animate-slide-up" style="animation-delay: 0.6s">
            <div class="modern-card-header">
                <h4 class="text-lg font-bold font-display text-text-primary">Actividad Reciente</h4>
            </div>
            <div class="modern-card-body p-0">
                @if(isset($cargas) && count($cargas) > 0)
                    <x-table>
                        <x-slot name="headers">
                            <th>Cliente</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Total</th>
                        </x-slot>
                        @foreach($cargas as $carga)
                            <tr>
                                <td class="font-semibold text-text-primary">{{ $carga['cliente'] }}</td>
                                <td class="text-text-secondary">{{ $carga['origen'] ?? '-' }}</td>
                                <td class="text-text-secondary">{{ $carga['destino'] ?? '-' }}</td>
                                <td class="text-text-secondary">{{ $carga['salida'] ? \Carbon\Carbon::parse($carga['salida'])->format('d/m/Y') : '-' }}</td>
                                <td>
                                    <x-badge variant="{{ strtolower(str_replace(' ', '_', $carga['estado'])) }}">
                                        {{ ucfirst($carga['estado']) }}
                                    </x-badge>
                                </td>
                                <td class="font-semibold text-text-primary">{{ $carga['total'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </x-table>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-text-muted mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-text-muted text-sm">No hay actividad reciente</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Datos para JavaScript -->
    <div id="dashboard-data" 
         data-tendencias='@json($tendencias ?? [])'
         data-distribucion-estado='@json($distribucionEstado ?? [])'
         data-distribucion-tipo='@json($distribucionTipo ?? [])'
         style="display: none;"></div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dataElement = document.getElementById('dashboard-data');
            const tendenciasData = JSON.parse(dataElement.getAttribute('data-tendencias') || '[]');
            const distribucionEstadoData = JSON.parse(dataElement.getAttribute('data-distribucion-estado') || '{}');
            const distribucionTipoData = JSON.parse(dataElement.getAttribute('data-distribucion-tipo') || '{}');

            // Gráfico de Tendencias
            const tendenciasCtx = document.getElementById('tendenciasChart');
            if (tendenciasCtx && tendenciasData.length > 0) {
                new Chart(tendenciasCtx, {
                    type: 'line',
                    data: {
                        labels: tendenciasData.map(t => t.label),
                        datasets: [{
                            label: 'Total Servicios',
                            data: tendenciasData.map(t => t.total),
                            borderColor: '#667eea',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3
                        }, {
                            label: 'Finalizadas',
                            data: tendenciasData.map(t => t.finalizadas),
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    font: { family: 'Poppins', size: 12, weight: '600' },
                                    color: '#ffffff'
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    font: { family: 'Poppins', size: 11 },
                                    color: '#cbd5e1'
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                }
                            },
                            x: {
                                ticks: {
                                    font: { family: 'Poppins', size: 11 },
                                    color: '#cbd5e1'
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                }
                            }
                        }
                    }
                });
            }

            // Gráfico de Distribución por Estado
            const estadoCtx = document.getElementById('estadoChart');
            if (estadoCtx && Object.keys(distribucionEstadoData).length > 0) {
                const labels = Object.keys(distribucionEstadoData);
                const values = Object.values(distribucionEstadoData);
                const colors = ['#667eea', '#764ba2', '#f5576c', '#00d2ff', '#10b981', '#f59e0b'];
                
                new Chart(estadoCtx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: colors.slice(0, labels.length),
                            borderWidth: 3,
                            borderColor: '#0f172a'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    font: { family: 'Poppins', size: 12, weight: '600' },
                                    color: '#ffffff',
                                    padding: 15
                                }
                            }
                        }
                    }
                });
            }

            // Gráfico de Tipo de Transporte
            const tipoCtx = document.getElementById('tipoTransporteChart');
            if (tipoCtx && Object.keys(distribucionTipoData).length > 0) {
                const labels = Object.keys(distribucionTipoData);
                const values = Object.values(distribucionTipoData);
                
                new Chart(tipoCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Cantidad',
                            data: values,
                            backgroundColor: 'rgba(102, 126, 234, 0.8)',
                            borderColor: '#667eea',
                            borderWidth: 2,
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    font: { family: 'Poppins', size: 11 },
                                    color: '#cbd5e1',
                                    stepSize: 1
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                }
                            },
                            x: {
                                ticks: {
                                    font: { family: 'Poppins', size: 11 },
                                    color: '#cbd5e1'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
