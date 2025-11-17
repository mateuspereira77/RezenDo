<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RezenDo - Lista de Tarefas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="custom-bg min-h-screen">
    <div class="container mx-auto px-3 sm:px-4 py-4 sm:py-8 max-w-4xl">
        <!-- Header com Post-its Decorativos -->
        <div class="header-with-postits mb-8">
            <div class="header-postits-bg">
                <div class="decorative-postit postit-1"></div>
                <div class="decorative-postit postit-2"></div>
                <div class="decorative-postit postit-3"></div>
                <div class="decorative-postit postit-4"></div>
                <div class="decorative-postit postit-5"></div>
                <div class="decorative-postit postit-6"></div>
            </div>
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-center text-gray-800 relative z-10">
                <span style="color: #fb9e0b;">Rezen</span><span style="color: #fbe20d;">Do</span>
            </h1>
        </div>
        
        <!-- Formulário para Criar Tarefa -->
        <div class="main-card-bg rounded-lg shadow-md p-4 sm:p-6 mb-6 sm:mb-8">
            <h2 class="text-xl sm:text-2xl font-semibold mb-4 text-gray-700">Nova Tarefa</h2>
            <form id="todoForm" class="space-y-4">
                <div>
                    <label for="todoText" class="block text-sm font-medium text-gray-700 mb-2">
                        Título da Tarefa
                    </label>
                    <input 
                        type="text" 
                        id="todoText" 
                        name="text"
                        required
                        maxlength="200"
                        placeholder="Digite o título da tarefa..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none custom-focus"
                    >
                    <div class="flex justify-end mt-1">
                        <span id="todoTextCounter" class="text-xs text-gray-500">0 / 200 caracteres</span>
                    </div>
                </div>
                
                <div>
                    <label for="todoDescription" class="block text-sm font-medium text-gray-700 mb-2">
                        Descrição (opcional)
                    </label>
                    <textarea 
                        id="todoDescription" 
                        name="description"
                        rows="3"
                        maxlength="500"
                        placeholder="Adicione uma descrição..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none custom-focus resize-none"
                    ></textarea>
                    <div class="flex justify-end mt-1">
                        <span id="todoDescriptionCounter" class="text-xs text-gray-500">0 / 500 caracteres</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Prioridade
                    </label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="priority" value="simple" checked class="mr-2">
                            <span class="text-green-600 font-medium">Simples</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="priority" value="medium" class="mr-2">
                            <span class="text-yellow-600 font-medium">Média</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="priority" value="urgent" class="mr-2">
                            <span class="text-red-600 font-medium">Urgente</span>
                        </label>
                    </div>
                </div>
                
                <div>
                    <label for="todoDate" class="block text-sm font-medium text-gray-700 mb-2">
                        Data da Tarefa (opcional)
                    </label>
                    <input 
                        type="text" 
                        id="todoDate" 
                        name="date"
                        data-date-mask
                        maxlength="10"
                        placeholder="DD/MM/AAAA"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none custom-focus"
                    >
                            <p class="text-xs text-gray-500 mt-1">Digite a data no formato DD/MM/AAAA ou DD/MM/AA</p>
                </div>
                
                <button 
                    type="submit"
                    class="w-full custom-btn-primary py-3 rounded-lg font-semibold transition-colors"
                >
                    Adicionar Tarefa
                </button>
            </form>
        </div>
        
        <!-- Botão para Ver Tarefas -->
        <div class="main-card-bg rounded-lg shadow-md p-6 text-center">
            <h2 class="text-xl sm:text-2xl font-semibold mb-4 text-gray-700">Minhas Tarefas</h2>
            <p class="text-gray-600 mb-6">Visualize e gerencie todas as suas tarefas em uma página dedicada</p>
            <a 
                href="{{ route('todos.list') }}"
                class="inline-block custom-btn-primary px-8 py-3 rounded-lg font-semibold transition-colors"
            >
                📋 Ver Minhas Tarefas
            </a>
        </div>
    </div>
    
    <!-- Rodapé -->
    <footer class="mt-8 sm:mt-12 py-4 text-center">
        <p class="text-xs sm:text-sm text-gray-500">
            Desenvolvido por <span class="font-medium text-gray-600">Mateus Pereira</span> - 2025
        </p>
    </footer>
    
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
