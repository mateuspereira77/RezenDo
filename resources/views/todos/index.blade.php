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

        <!-- Menu de Autenticação -->
        <div class="flex justify-end mb-4">
            @auth
                <div class="flex items-center gap-4">
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
                    
                    <a href="{{ route('help.index') }}" class="text-sm text-gray-700 hover:text-[#fb9e0b] font-medium transition-colors">📚 Ajuda</a>
                    <span class="text-sm text-gray-700">Olá, <strong>{{ Auth::user()->name }}</strong></span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button 
                            type="submit"
                            class="text-sm text-[#fb9e0b] hover:text-[#fc6c04] font-medium transition-colors"
                        >
                            Sair
                        </button>
                    </form>
                </div>
            @else
                <div class="flex items-center gap-4">
                    <a 
                        href="{{ route('login') }}"
                        class="text-sm text-gray-700 hover:text-[#fb9e0b] font-medium transition-colors"
                    >
                        Entrar
                    </a>
                    <a 
                        href="{{ route('register') }}"
                        class="text-sm text-[#fb9e0b] hover:text-[#fc6c04] font-medium transition-colors"
                    >
                        Cadastrar
                    </a>
                </div>
            @endauth
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
                        Data de Início (opcional)
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
                
                <div>
                    <label for="todoEndDate" class="block text-sm font-medium text-gray-700 mb-2">
                        Data de Término (opcional)
                    </label>
                    <input 
                        type="text" 
                        id="todoEndDate" 
                        name="end_date"
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
        
        <!-- Botões de Navegação -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
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
            <div class="main-card-bg rounded-lg shadow-md p-6 text-center">
                <h2 class="text-xl sm:text-2xl font-semibold mb-4 text-gray-700">Calendário</h2>
                <p class="text-gray-600 mb-6">Visualize suas tarefas organizadas por mês ou semana</p>
                <a 
                    href="{{ route('todos.calendar') }}"
                    class="inline-block custom-btn-primary px-8 py-3 rounded-lg font-semibold transition-colors"
                >
                    📅 Ver Calendário
                </a>
            </div>
            <div class="main-card-bg rounded-lg shadow-md p-6 text-center">
                <h2 class="text-xl sm:text-2xl font-semibold mb-4 text-gray-700">Meu Histórico</h2>
                <p class="text-gray-600 mb-6">Visualize e restaure tarefas excluídas</p>
                <a 
                    href="{{ route('todos.history') }}"
                    class="inline-block custom-btn-primary px-8 py-3 rounded-lg font-semibold transition-colors"
                >
                    📜 Ver Histórico
                </a>
            </div>
            <div class="main-card-bg rounded-lg shadow-md p-6 text-center">
                <h2 class="text-xl sm:text-2xl font-semibold mb-4 text-gray-700">Minha Produtividade</h2>
                <p class="text-gray-600 mb-6">Acompanhe suas estatísticas e gráficos</p>
                <a 
                    href="{{ route('todos.productivity') }}"
                    class="inline-block custom-btn-primary px-8 py-3 rounded-lg font-semibold transition-colors"
                >
                    📊 Ver Produtividade
                </a>
            </div>
        </div>
    </div>
    
    <script>
        // Gerenciamento de Notificações
        let notificationsOpen = false;

        // Carregar notificações ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            loadUnreadCount();
            setInterval(loadUnreadCount, 30000); // Atualizar a cada 30 segundos
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
            // Marcar como lida
            window.axios.post(`/api/notifications/${notificationId}/read`)
                .then(() => {
                    loadUnreadCount();
                    loadNotifications();
                })
                .catch(error => {
                    console.error('Erro ao marcar notificação como lida:', error);
                });

            // Redirecionar para a tarefa
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

        // Fechar dropdown ao clicar fora
        document.addEventListener('click', function(event) {
            const container = document.getElementById('notificationsContainer');
            if (container && !container.contains(event.target)) {
                const dropdown = document.getElementById('notificationsDropdown');
                dropdown.classList.add('hidden');
                notificationsOpen = false;
            }
        });
    </script>
    
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
