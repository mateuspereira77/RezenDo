<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Minhas Tarefas - RezenDo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="custom-bg min-h-screen">
    <div class="container mx-auto px-3 sm:px-4 py-4 sm:py-8 max-w-6xl">
        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-2">
                    <span style="color: #fb9e0b;">Minhas</span> <span style="color: #fbe20d;">Tarefas</span>
                </h1>
                <p class="text-sm sm:text-base text-gray-600">Gerencie todas as suas tarefas</p>
            </div>
            <div class="flex items-center gap-2 sm:gap-4 flex-wrap">
                <!-- Componente de Notificações -->
                <div class="relative" id="notificationsContainer">
                    <button 
                        onclick="toggleNotifications()"
                        class="relative p-2 text-gray-700 hover:text-[#fb9e0b] transition-colors rounded-full hover:bg-gray-100"
                        aria-label="Notificações"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span id="notificationBadge" class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
                    </button>
                    
                    <!-- Dropdown de Notificações -->
                    <div id="notificationsDropdown" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 overflow-hidden flex flex-col">
                        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                            <h3 class="font-semibold text-gray-800">Notificações</h3>
                            <button 
                                onclick="markAllAsRead()"
                                class="text-sm text-[#fb9e0b] hover:text-[#fc6c04] font-medium"
                            >
                                Marcar todas como lidas
                            </button>
                        </div>
                        <div id="notificationsList" class="overflow-y-auto flex-1">
                            <div class="p-4 text-center text-gray-500 text-sm">Carregando...</div>
                        </div>
                    </div>
                </div>
                
                <a 
                    href="{{ route('todos.index') }}"
                    class="custom-btn-primary px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-semibold transition-colors text-sm sm:text-base w-full sm:w-auto text-center"
                >
                    ← Voltar
                </a>
                <a 
                    href="{{ route('todos.calendar') }}"
                    class="px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-semibold transition-colors text-sm sm:text-base w-full sm:w-auto text-center border-2 border-gray-300 hover:bg-gray-50"
                >
                    📅 Calendário
                </a>
                <a 
                    href="{{ route('todos.history') }}"
                    class="px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-semibold transition-colors text-sm sm:text-base w-full sm:w-auto text-center border-2 border-gray-300 hover:bg-gray-50"
                >
                    📜 Meu Histórico
                </a>
            </div>
        </div>
        
        <!-- Lista de Tarefas -->
        <div class="main-card-bg rounded-lg shadow-md p-4 sm:p-6">
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl sm:text-2xl font-semibold text-gray-700">Minhas Tarefas</h2>
                    <div id="todoStats" class="text-sm text-gray-600">
                        <span id="totalCount">0 tarefas</span>
                    </div>
                </div>
                
                <!-- Filtros -->
                <div class="flex flex-col sm:flex-row gap-3 mb-4 items-stretch sm:items-center flex-wrap">
                    <div class="flex gap-2 sm:gap-3 flex-wrap">
                        <button 
                            id="filterAll" 
                            onclick="filterTodos('all')"
                            class="filter-btn active px-3 sm:px-4 py-2 rounded-lg font-medium transition-colors text-sm sm:text-base flex-1 sm:flex-none"
                        >
                            Todas
                        </button>
                        <button 
                            id="filterPending" 
                            onclick="filterTodos('pending')"
                            class="filter-btn px-3 sm:px-4 py-2 rounded-lg font-medium transition-colors text-sm sm:text-base flex-1 sm:flex-none"
                        >
                            A Concluir
                        </button>
                        <button 
                            id="filterCompleted" 
                            onclick="filterTodos('completed')"
                            class="filter-btn px-3 sm:px-4 py-2 rounded-lg font-medium transition-colors text-sm sm:text-base flex-1 sm:flex-none"
                        >
                            Concluídas
                        </button>
                    </div>
                    <button 
                        id="deleteAllCompletedBtn"
                        onclick="deleteAllCompleted()"
                        class="delete-all-btn px-3 sm:px-4 py-2 rounded-lg font-medium transition-colors text-xs sm:text-sm"
                        style="display: none;"
                    >
                        🗑️ Apagar Todas as Concluídas
                    </button>
                </div>
            </div>
            <div id="todosList" class="post-it-grid">
                <p class="text-gray-500 text-center py-8">Carregando tarefas...</p>
            </div>
        </div>
    </div>
    
    <!-- Rodapé -->
    <footer class="mt-8 sm:mt-12 py-4 text-center">
        <p class="text-xs sm:text-sm text-gray-500">
            Desenvolvido por <span class="font-medium text-gray-600">Mateus Pereira</span> - 2025
        </p>
    </footer>
    
    <!-- Modal de Edição -->
    <div id="editModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeEditModal()"></div>
        <div class="flex min-h-screen items-start sm:items-center justify-center p-3 sm:p-4 py-4 sm:py-8">
            <div class="edit-modal-content main-card-bg rounded-lg shadow-2xl w-full max-w-lg relative transform transition-all my-auto">
                <div class="p-5 sm:p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-xl sm:text-2xl font-semibold text-gray-700">Editar Tarefa</h2>
                        <button 
                            onclick="closeEditModal()"
                            class="text-gray-500 hover:text-gray-700 transition-colors p-1 rounded-full hover:bg-gray-100"
                            aria-label="Fechar"
                        >
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form id="editTodoForm" class="space-y-4">
                        <div>
                            <label for="editTodoText" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Título da Tarefa
                            </label>
                            <input 
                                type="text" 
                                id="editTodoText" 
                                required
                                maxlength="200"
                                placeholder="Digite o título da tarefa..."
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none custom-focus text-sm sm:text-base"
                            >
                            <div class="flex justify-end mt-1">
                                <span id="editTodoTextCounter" class="text-xs text-gray-500">0 / 200 caracteres</span>
                            </div>
                        </div>
                        
                        <div>
                            <label for="editTodoDescription" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Descrição (opcional)
                            </label>
                            <textarea 
                                id="editTodoDescription" 
                                rows="3"
                                maxlength="500"
                                placeholder="Adicione uma descrição..."
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none custom-focus resize-none text-sm sm:text-base"
                            ></textarea>
                            <div class="flex justify-end mt-1">
                                <span id="editTodoDescriptionCounter" class="text-xs text-gray-500">0 / 500 caracteres</span>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Prioridade
                            </label>
                            <div class="flex flex-wrap gap-3">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="editPriority" value="simple" class="mr-2 cursor-pointer">
                                    <span class="text-green-600 font-medium text-sm sm:text-base">Simples</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="editPriority" value="medium" class="mr-2 cursor-pointer">
                                    <span class="text-yellow-600 font-medium text-sm sm:text-base">Média</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="editPriority" value="urgent" class="mr-2 cursor-pointer">
                                    <span class="text-red-600 font-medium text-sm sm:text-base">Urgente</span>
                                </label>
                            </div>
                        </div>
                        
                        <div>
                            <label for="editTodoDate" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Data de Início (opcional)
                            </label>
                            <input 
                                type="text" 
                                id="editTodoDate" 
                                data-date-mask
                                maxlength="10"
                                placeholder="DD/MM/AAAA"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none custom-focus text-sm sm:text-base"
                            >
                            <p class="text-xs text-gray-500 mt-1">Digite a data no formato DD/MM/AAAA ou DD/MM/AA</p>
                        </div>
                        
                        <div>
                            <label for="editTodoEndDate" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Data de Término (opcional)
                            </label>
                            <input 
                                type="text" 
                                id="editTodoEndDate" 
                                data-date-mask
                                maxlength="10"
                                placeholder="DD/MM/AAAA"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none custom-focus text-sm sm:text-base"
                            >
                            <p class="text-xs text-gray-500 mt-1">Digite a data no formato DD/MM/AAAA ou DD/MM/AA</p>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row gap-3 pt-3">
                            <button 
                                type="submit"
                                class="flex-1 custom-btn-primary py-2.5 sm:py-3 rounded-lg font-semibold transition-colors text-sm sm:text-base"
                            >
                                Salvar Alterações
                            </button>
                            <button 
                                type="button"
                                onclick="closeEditModal()"
                                class="px-5 sm:px-6 py-2.5 sm:py-3 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition-colors text-sm sm:text-base whitespace-nowrap"
                            >
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Confirmação de Exclusão -->
    <div id="deleteConfirmModal" class="hidden fixed inset-0 z-[9999]" style="display: none;">
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeDeleteModal()" style="z-index: 1;"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4" style="z-index: 2; pointer-events: none;">
            <div class="bg-white rounded-lg shadow-2xl w-full max-w-md p-6" style="pointer-events: auto; position: relative;">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl sm:text-2xl font-semibold text-gray-700">Confirmar Exclusão</h2>
                    <button 
                        onclick="closeDeleteModal()"
                        class="text-gray-400 hover:text-gray-600 transition-colors"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="mb-6">
                    <div class="flex items-center justify-center mb-4">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </div>
                    </div>
                    <p id="deleteConfirmMessage" class="text-gray-700 text-center text-base">
                        Tem certeza que deseja excluir esta tarefa?
                    </p>
                    <p class="text-gray-500 text-center text-sm mt-2">
                        A tarefa será movida para o histórico e poderá ser restaurada posteriormente.
                    </p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <button 
                        onclick="confirmDelete()"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg font-semibold hover:from-red-600 hover:to-red-700 transition-all shadow-md hover:shadow-lg"
                    >
                        Excluir
                    </button>
                    <button 
                        onclick="closeDeleteModal()"
                        class="flex-1 px-4 py-2.5 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition-colors"
                    >
                        Cancelar
                    </button>
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
    
    <script>
        // Gerenciamento de Notificações (mesmo código do index.blade.php)
        let notificationsOpen = false;

        document.addEventListener('DOMContentLoaded', function() {
            loadUnreadCount();
            setInterval(loadUnreadCount, 30000);
        });

        function toggleNotifications() {
            const dropdown = document.getElementById('notificationsDropdown');
            notificationsOpen = !notificationsOpen;
            
            if (notificationsOpen) {
                dropdown.classList.remove('hidden');
                loadNotifications();
            } else {
                dropdown.classList.add('hidden');
            }
        }

        function loadUnreadCount() {
            window.axios.get('/api/notifications/unread-count')
                .then(response => {
                    const badge = document.getElementById('notificationBadge');
                    const count = response.data.unread_count;
                    
                    if (count > 0) {
                        badge.textContent = count > 99 ? '99+' : count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar contador de notificações:', error);
                });
        }

        function loadNotifications() {
            window.axios.get('/api/notifications')
                .then(response => {
                    const listDiv = document.getElementById('notificationsList');
                    const notifications = response.data.notifications;
                    
                    if (notifications.length === 0) {
                        listDiv.innerHTML = '<div class="p-4 text-center text-gray-500 text-sm">Nenhuma notificação</div>';
                    } else {
                        listDiv.innerHTML = notifications.map(notification => {
                            const data = notification.data;
                            const isRead = notification.read_at !== null;
                            const date = new Date(notification.created_at);
                            
                            return `
                                <div 
                                    class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors ${!isRead ? 'bg-blue-50' : ''}"
                                    onclick="openNotification('${notification.id}', ${data.todo_id || 'null'})"
                                >
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-2 h-2 rounded-full ${!isRead ? 'bg-[#fb9e0b]' : 'bg-transparent'}"></div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-800 font-medium">${data.message || 'Nova notificação'}</p>
                                            <p class="text-xs text-gray-500 mt-1">${date.toLocaleString('pt-BR')}</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }).join('');
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar notificações:', error);
                    document.getElementById('notificationsList').innerHTML = '<div class="p-4 text-center text-red-500 text-sm">Erro ao carregar notificações</div>';
                });
        }

        function openNotification(notificationId, todoId) {
            window.axios.post(`/api/notifications/${notificationId}/read`)
                .then(() => {
                    loadUnreadCount();
                    loadNotifications();
                })
                .catch(error => {
                    console.error('Erro ao marcar notificação como lida:', error);
                });

            if (todoId) {
                window.location.href = `/todos/${todoId}`;
            }
        }

        function markAllAsRead() {
            window.axios.post('/api/notifications/mark-all-read')
                .then(() => {
                    loadUnreadCount();
                    loadNotifications();
                })
                .catch(error => {
                    console.error('Erro ao marcar todas como lidas:', error);
                });
        }

        document.addEventListener('click', function(event) {
            const container = document.getElementById('notificationsContainer');
            if (container && !container.contains(event.target)) {
                const dropdown = document.getElementById('notificationsDropdown');
                dropdown.classList.add('hidden');
                notificationsOpen = false;
            }
        });
    </script>
</body>
</html>

