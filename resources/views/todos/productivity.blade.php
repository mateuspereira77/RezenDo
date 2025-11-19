<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Minha Produtividade - RezenDo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="custom-bg min-h-screen">
    <div class="container mx-auto px-3 sm:px-4 py-4 sm:py-8 max-w-7xl">
        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-2">
                    <span style="color: #fb9e0b;">Minha</span> <span style="color: #fbe20d;">Produtividade</span>
                </h1>
                <p class="text-sm sm:text-base text-gray-600">Acompanhe suas estatísticas e gráficos de produtividade</p>
            </div>
            <div class="flex items-center gap-2 sm:gap-4 flex-wrap">
                <a 
                    href="{{ route('todos.index') }}"
                    class="custom-btn-primary px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-semibold transition-colors text-sm sm:text-base w-full sm:w-auto text-center"
                >
                    ← Voltar
                </a>
            </div>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="main-card-bg rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Taxa de Conclusão</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $completionRate }}%</p>
                    </div>
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="main-card-bg rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total de Tarefas</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $totalTasks }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $completedTasks }} concluídas, {{ $pendingTasks }} pendentes</p>
                    </div>
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="main-card-bg rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Últimos 30 Dias</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $tasksCompletedLast30Days }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $tasksCreatedLast30Days }} criadas</p>
                    </div>
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="main-card-bg rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Tarefas Atrasadas</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $overdueTasks }}</p>
                        <p class="text-xs text-gray-500 mt-1">Com data passada</p>
                    </div>
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Gráfico de Timeline (Últimos 7 dias) -->
            <div class="main-card-bg rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Atividade dos Últimos 7 Dias</h3>
                <canvas id="timelineChart"></canvas>
            </div>

            <!-- Gráfico de Pizza (Por Prioridade) -->
            <div class="main-card-bg rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Distribuição por Prioridade</h3>
                <canvas id="priorityChart"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Gráfico de Barras (Por Dia da Semana) -->
            <div class="main-card-bg rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Produtividade por Dia da Semana (Últimos 30 dias)</h3>
                <canvas id="dayOfWeekChart"></canvas>
            </div>

            <!-- Gráfico de Conclusão por Prioridade -->
            <div class="main-card-bg rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Conclusão por Prioridade</h3>
                <canvas id="completedByPriorityChart"></canvas>
            </div>
        </div>

        <!-- Estatísticas Adicionais -->
        <div class="main-card-bg rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Estatísticas Adicionais</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-2">Tempo Médio de Conclusão</p>
                    <p class="text-2xl font-bold text-gray-800">
                        @if($avgTimeToComplete > 0)
                            {{ $avgTimeToComplete }}h
                        @else
                            N/A
                        @endif
                    </p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-2">Tarefas Simples</p>
                    <p class="text-2xl font-bold text-green-600">{{ $tasksByPriority['simple'] }}</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-2">Tarefas Médias</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $tasksByPriority['medium'] }}</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-2">Tarefas Urgentes</p>
                    <p class="text-2xl font-bold text-red-600">{{ $tasksByPriority['urgent'] }}</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-2">Simples Concluídas</p>
                    <p class="text-2xl font-bold text-green-600">{{ $completedByPriority['simple'] }}</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-2">Médias Concluídas</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $completedByPriority['medium'] }}</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-2">Urgentes Concluídas</p>
                    <p class="text-2xl font-bold text-red-600">{{ $completedByPriority['urgent'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Dados do PHP
        const timelineData = @json($timelineData);
        const tasksByPriority = @json($tasksByPriority);
        const completedByPriority = @json($completedByPriority);
        const tasksByDayOfWeek = @json($tasksByDayOfWeek);

        // Gráfico de Timeline
        const timelineCtx = document.getElementById('timelineChart').getContext('2d');
        new Chart(timelineCtx, {
            type: 'line',
            data: {
                labels: timelineData.map(d => {
                    const date = new Date(d.date);
                    return date.toLocaleDateString('pt-BR', { weekday: 'short', day: 'numeric', month: 'short' });
                }),
                datasets: [{
                    label: 'Tarefas Criadas',
                    data: timelineData.map(d => d.created),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Tarefas Concluídas',
                    data: timelineData.map(d => d.completed),
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Gráfico de Pizza (Prioridade)
        const priorityCtx = document.getElementById('priorityChart').getContext('2d');
        new Chart(priorityCtx, {
            type: 'doughnut',
            data: {
                labels: ['Simples', 'Média', 'Urgente'],
                datasets: [{
                    data: [tasksByPriority.simple, tasksByPriority.medium, tasksByPriority.urgent],
                    backgroundColor: [
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Gráfico de Barras (Dia da Semana)
        const dayOfWeekCtx = document.getElementById('dayOfWeekChart').getContext('2d');
        new Chart(dayOfWeekCtx, {
            type: 'bar',
            data: {
                labels: tasksByDayOfWeek.map(d => d.day),
                datasets: [{
                    label: 'Tarefas Criadas',
                    data: tasksByDayOfWeek.map(d => d.count),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
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
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Gráfico de Conclusão por Prioridade
        const completedByPriorityCtx = document.getElementById('completedByPriorityChart').getContext('2d');
        new Chart(completedByPriorityCtx, {
            type: 'bar',
            data: {
                labels: ['Simples', 'Média', 'Urgente'],
                datasets: [{
                    label: 'Concluídas',
                    data: [completedByPriority.simple, completedByPriority.medium, completedByPriority.urgent],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderColor: [
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)'
                    ],
                    borderWidth: 1
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
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>

