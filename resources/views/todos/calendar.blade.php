<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Calendário de Tarefas - RezenDo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="custom-bg min-h-screen">
    <div class="container mx-auto px-3 sm:px-4 py-4 sm:py-8 max-w-7xl">
        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-2">
                    <span style="color: #fb9e0b;">Calendário</span> <span style="color: #fbe20d;">de Tarefas</span>
                </h1>
                <p class="text-sm sm:text-base text-gray-600">Visualize suas tarefas por mês ou semana</p>
            </div>
            <div class="flex gap-2 flex-wrap">
                <a 
                    href="{{ route('todos.index') }}"
                    class="custom-btn-primary px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-semibold transition-colors text-sm sm:text-base w-full sm:w-auto text-center"
                >
                    ← Voltar
                </a>
                <a 
                    href="{{ route('todos.list') }}"
                    class="px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-semibold transition-colors text-sm sm:text-base w-full sm:w-auto text-center border-2 border-gray-300 hover:bg-gray-50"
                >
                    📋 Lista
                </a>
            </div>
        </div>

        <!-- Controles do Calendário -->
        <div class="main-card-bg rounded-lg shadow-md p-4 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <button 
                        id="prevMonthBtn"
                        onclick="changeMonth(-1)"
                        class="px-3 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition-colors"
                    >
                        ←
                    </button>
                    <h2 id="currentMonthYear" class="text-xl sm:text-2xl font-semibold text-gray-700 min-w-[200px] text-center">
                        Janeiro 2025
                    </h2>
                    <button 
                        id="nextMonthBtn"
                        onclick="changeMonth(1)"
                        class="px-3 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition-colors"
                    >
                        →
                    </button>
                </div>
                <div class="flex gap-2">
                    <button 
                        id="todayBtn"
                        onclick="goToToday()"
                        class="px-4 py-2 rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition-colors text-sm font-medium"
                    >
                        Hoje
                    </button>
                    <button 
                        id="viewMonthBtn"
                        onclick="setView('month')"
                        class="px-4 py-2 rounded-lg bg-green-500 text-white hover:bg-green-600 transition-colors text-sm font-medium"
                    >
                        Mês
                    </button>
                    <button 
                        id="viewWeekBtn"
                        onclick="setView('week')"
                        class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400 transition-colors text-sm font-medium"
                    >
                        Semana
                    </button>
                </div>
            </div>
        </div>

        <!-- Calendário Mensal -->
        <div id="monthView" class="main-card-bg rounded-lg shadow-md p-4 sm:p-6">
            <div class="grid grid-cols-7 gap-2 mb-2">
                <div class="text-center font-semibold text-gray-600 py-2">Dom</div>
                <div class="text-center font-semibold text-gray-600 py-2">Seg</div>
                <div class="text-center font-semibold text-gray-600 py-2">Ter</div>
                <div class="text-center font-semibold text-gray-600 py-2">Qua</div>
                <div class="text-center font-semibold text-gray-600 py-2">Qui</div>
                <div class="text-center font-semibold text-gray-600 py-2">Sex</div>
                <div class="text-center font-semibold text-gray-600 py-2">Sáb</div>
            </div>
            <div id="monthCalendarGrid" class="grid grid-cols-7 gap-2">
                <!-- Dias serão renderizados aqui via JavaScript -->
            </div>
        </div>

        <!-- Calendário Semanal -->
        <div id="weekView" class="main-card-bg rounded-lg shadow-md p-4 sm:p-6 hidden">
            <div class="grid grid-cols-7 gap-2 mb-2">
                <div class="text-center font-semibold text-gray-600 py-2">Dom</div>
                <div class="text-center font-semibold text-gray-600 py-2">Seg</div>
                <div class="text-center font-semibold text-gray-600 py-2">Ter</div>
                <div class="text-center font-semibold text-gray-600 py-2">Qua</div>
                <div class="text-center font-semibold text-gray-600 py-2">Qui</div>
                <div class="text-center font-semibold text-gray-600 py-2">Sex</div>
                <div class="text-center font-semibold text-gray-600 py-2">Sáb</div>
            </div>
            <div id="weekCalendarGrid" class="grid grid-cols-7 gap-2">
                <!-- Dias serão renderizados aqui via JavaScript -->
            </div>
            <!-- Barra de rolagem para navegação de semanas -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600 font-medium min-w-[80px]">Semana:</span>
                    <input 
                        type="range" 
                        id="weekSlider" 
                        min="0" 
                        max="52" 
                        value="0" 
                        class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider-week"
                    />
                    <span id="weekSliderValue" class="text-sm text-gray-600 font-medium min-w-[100px] text-right">Semana atual</span>
                </div>
            </div>
        </div>

        <!-- Modal de Tarefas do Dia -->
        <div id="dayModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
                <div class="p-6 border-b flex items-center justify-between">
                    <h3 id="dayModalTitle" class="text-xl font-semibold text-gray-800">Tarefas do Dia</h3>
                    <button onclick="closeDayModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="dayModalContent" class="p-6 overflow-y-auto flex-1">
                    <!-- Tarefas serão renderizadas aqui -->
                </div>
            </div>
        </div>
    </div>

    <!-- Toast de Notificação -->
    <div id="toastNotification" class="toast-notification hidden fixed z-50 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 transition-all">
        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span id="toastMessage" class="font-medium flex-1"></span>
        <button onclick="hideToast()" class="text-white hover:text-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

</body>
</html>

