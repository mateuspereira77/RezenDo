<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Meu Histórico de Tarefas - RezenDo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="custom-bg min-h-screen">
    <div class="container mx-auto px-3 sm:px-4 py-4 sm:py-8 max-w-6xl">
        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-2">
                    <span style="color: #fb9e0b;">Meu</span> <span style="color: #fbe20d;">Histórico</span>
                </h1>
                <p class="text-sm sm:text-base text-gray-600">Tarefas excluídas - você pode restaurá-las ou excluí-las permanentemente</p>
            </div>
            <div class="flex items-center gap-2 sm:gap-4 flex-wrap">
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
                    📋 Minhas Tarefas
                </a>
            </div>
        </div>
        
        <!-- Lista de Tarefas do Histórico -->
        <div class="main-card-bg rounded-lg shadow-md p-4 sm:p-6">
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
                    <h2 class="text-xl sm:text-2xl font-semibold text-gray-700">Tarefas Excluídas</h2>
                    <div class="flex items-center gap-3">
                        <div id="historyStats" class="text-sm text-gray-600">
                            <span id="totalCount">0 tarefas</span>
                        </div>
                        <div class="flex gap-2 border border-gray-300 rounded-lg p-1">
                            <button 
                                id="filterAllBtn"
                                onclick="setFilter('all')"
                                class="px-3 py-1.5 text-sm font-medium rounded transition-colors bg-orange-500 text-white"
                            >
                                Todas
                            </button>
                            <button 
                                id="filterMineBtn"
                                onclick="setFilter('mine')"
                                class="px-3 py-1.5 text-sm font-medium rounded transition-colors text-gray-700 hover:bg-gray-100"
                            >
                                Minhas
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="historyList" class="post-it-grid">
                <p class="text-gray-500 text-center py-8">Carregando histórico...</p>
            </div>
        </div>
    </div>
    
    <!-- Modal de Confirmação de Exclusão Permanente -->
    <div id="forceDeleteModal" class="hidden fixed inset-0 z-[9999]">
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeForceDeleteModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-2xl w-full max-w-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl sm:text-2xl font-semibold text-gray-700">Excluir Permanentemente</h2>
                    <button 
                        onclick="closeForceDeleteModal()"
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
                    <p class="text-gray-700 text-center text-base">
                        Tem certeza que deseja excluir esta tarefa permanentemente?
                    </p>
                    <p class="text-gray-500 text-center text-sm mt-2">
                        Esta ação não pode ser desfeita e a tarefa será removida do histórico.
                    </p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <button 
                        onclick="confirmForceDelete()"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg font-semibold hover:from-red-600 hover:to-red-700 transition-all shadow-md hover:shadow-lg"
                    >
                        Excluir Permanentemente
                    </button>
                    <button 
                        onclick="closeForceDeleteModal()"
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
        let historyTodos = [];
        let todoToForceDelete = null;
        let currentFilter = 'all'; // 'all' ou 'mine'
        const currentUserId = {{ auth()->id() }};

        // Definir viewTodo no escopo global ANTES de qualquer coisa
        function viewTodo(todoId) {
            if (!todoId) {
                console.error('viewTodo chamado sem ID');
                return;
            }
            console.log('Navegando para tarefa do histórico:', todoId);
            const url = '/todos/history/' + todoId;
            console.log('URL:', url);
            window.location.href = url;
        }
        
        // Garantir que está no window
        window.viewTodo = viewTodo;

        function setFilter(filter) {
            currentFilter = filter;
            const allBtn = document.getElementById('filterAllBtn');
            const mineBtn = document.getElementById('filterMineBtn');
            
            if (filter === 'all') {
                allBtn.classList.add('bg-orange-500', 'text-white');
                allBtn.classList.remove('text-gray-700', 'hover:bg-gray-100');
                mineBtn.classList.remove('bg-orange-500', 'text-white');
                mineBtn.classList.add('text-gray-700', 'hover:bg-gray-100');
            } else {
                mineBtn.classList.add('bg-orange-500', 'text-white');
                mineBtn.classList.remove('text-gray-700', 'hover:bg-gray-100');
                allBtn.classList.remove('bg-orange-500', 'text-white');
                allBtn.classList.add('text-gray-700', 'hover:bg-gray-100');
            }
            
            renderHistory();
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadHistory();
        });

        async function loadHistory() {
            try {
                const response = await window.axios.get('/api/todos/history/all');
                historyTodos = response.data;
                renderHistory();
            } catch (error) {
                console.error('Erro ao carregar histórico:', error);
                document.getElementById('historyList').innerHTML = '<p class="text-red-500 text-center py-8">Erro ao carregar histórico. Recarregue a página.</p>';
            }
        }

        function renderHistory() {
            const listDiv = document.getElementById('historyList');
            const totalCount = document.getElementById('totalCount');
            
            // Filtrar tarefas baseado no filtro atual
            let filteredTodos = historyTodos;
            if (currentFilter === 'mine') {
                filteredTodos = historyTodos.filter(todo => todo.user_id === currentUserId);
            }
            
            if (totalCount) {
                totalCount.textContent = `${filteredTodos.length} tarefa${filteredTodos.length !== 1 ? 's' : ''} excluída${filteredTodos.length !== 1 ? 's' : ''}`;
            }

            if (filteredTodos.length === 0) {
                const message = currentFilter === 'mine' 
                    ? '<p class="text-gray-500 text-center py-8">Nenhuma tarefa sua excluída encontrada no histórico.</p>'
                    : '<p class="text-gray-500 text-center py-8">Nenhuma tarefa excluída encontrada no histórico.</p>';
                listDiv.innerHTML = message;
                return;
            }

            listDiv.innerHTML = '';
            filteredTodos.forEach((todo, index) => {
                const priorityColors = {
                    'urgent': 'post-it-urgent',
                    'medium': 'post-it-medium',
                    'simple': 'post-it-simple'
                };
                
                const priorityClass = priorityColors[todo.priority] || 'post-it-simple';
                const completedClass = todo.completed ? 'post-it-completed' : '';
                
                const deletedDate = new Date(todo.deleted_at);
                const deletedDateStr = deletedDate.toLocaleDateString('pt-BR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                const todoId = todo.id;
                const isOwner = todo.user_id === currentUserId;
                const isAssigned = todo.assigned_to === currentUserId;
                // Laravel serializa relacionamentos em snake_case no JSON
                const sharedWith = todo.shared_with || [];
                const isShared = !isOwner && sharedWith.length > 0 && sharedWith.some(share => share.id === currentUserId);
                
                // Determinar o tipo de relação
                let relationBadge = '';
                if (!isOwner) {
                    if (isAssigned && isShared) {
                        relationBadge = '<span class="inline-block px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800 mb-2">Responsável & Compartilhada</span>';
                    } else if (isAssigned) {
                        relationBadge = '<span class="inline-block px-2 py-1 text-xs font-semibold rounded bg-purple-100 text-purple-800 mb-2">Responsável</span>';
                    } else if (isShared) {
                        relationBadge = '<span class="inline-block px-2 py-1 text-xs font-semibold rounded bg-indigo-100 text-indigo-800 mb-2">Compartilhada</span>';
                    }
                }
                
                const ownerName = todo.user ? escapeHtml(todo.user.name) : 'Desconhecido';
                const ownerInfo = !isOwner ? `<div class="text-xs text-gray-600 mt-1">Excluída por: <span class="font-semibold">${ownerName}</span></div>` : '';
                
                console.log('Renderizando tarefa:', todoId);
                const todoHtml = `
                    <div class="post-it-task ${priorityClass} ${completedClass}" style="opacity: 0.8; cursor: pointer;" data-todo-id="${todoId}" onclick="try { if(typeof window.viewTodo === 'function') { window.viewTodo(${todoId}); } else { window.location.href = '/todos/history/${todoId}'; } } catch(e) { console.error('Erro ao navegar:', e); window.location.href = '/todos/history/${todoId}'; } return false;">
                        <div class="post-it-content">
                            <div class="post-it-title-wrapper">
                                <h3 class="post-it-title">${escapeHtml(todo.text)}</h3>
                            </div>
                            ${relationBadge}
                            ${todo.description ? `
                                <div class="post-it-description-wrapper">
                                    <p class="post-it-description">${escapeHtml(todo.description)}</p>
                                </div>
                            ` : ''}
                            <div class="post-it-date text-xs text-gray-500 mt-2">
                                Excluída em: ${deletedDateStr}
                            </div>
                            ${ownerInfo}
                            ${isOwner ? `
                                <div class="post-it-actions" onclick="event.stopPropagation();">
                                    <button 
                                        onclick="restoreTodo(${todo.id}); event.stopPropagation();"
                                        class="post-it-btn post-it-btn-edit"
                                        title="Restaurar tarefa"
                                    >
                                        ↺ Restaurar
                                    </button>
                                    <button 
                                        onclick="openForceDeleteModal(${todo.id}); event.stopPropagation();"
                                        class="post-it-btn post-it-btn-delete"
                                        title="Excluir permanentemente"
                                    >
                                        🗑️ Excluir
                                    </button>
                                </div>
                            ` : `
                                <div class="text-xs text-gray-500 mt-2 italic">
                                    Apenas o dono pode restaurar ou excluir permanentemente
                                </div>
                            `}
                        </div>
                    </div>
                `;
                listDiv.insertAdjacentHTML('beforeend', todoHtml);
                console.log('Tarefa adicionada ao DOM:', todoId);
                
                // Adicionar event listener diretamente após inserir no DOM
                setTimeout(() => {
                    const todoElement = listDiv.querySelector(`[data-todo-id="${todoId}"]`);
                    if (todoElement) {
                        todoElement.addEventListener('click', function(e) {
                            // Não navegar se clicou nos botões de ação
                            if (e.target.closest('.post-it-actions')) {
                                return;
                            }
                            e.preventDefault();
                            e.stopPropagation();
                            console.log('Clique detectado na tarefa:', todoId);
                            window.location.href = '/todos/history/' + todoId;
                        });
                    }
                }, 10);
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        async function restoreTodo(todoId) {
            try {
                const response = await window.axios.post(`/api/todos/history/${todoId}/restore`);
                showToast('Tarefa restaurada com sucesso!');
                loadHistory();
            } catch (error) {
                console.error('Erro ao restaurar tarefa:', error);
                showToast('Erro ao restaurar tarefa. Tente novamente.', 'error');
            }
        }

        function openForceDeleteModal(todoId) {
            todoToForceDelete = todoId;
            document.getElementById('forceDeleteModal').classList.remove('hidden');
        }

        function closeForceDeleteModal() {
            todoToForceDelete = null;
            document.getElementById('forceDeleteModal').classList.add('hidden');
        }

        async function confirmForceDelete() {
            if (!todoToForceDelete) {
                return;
            }

            try {
                await window.axios.delete(`/api/todos/history/${todoToForceDelete}/force`);
                showToast('Tarefa excluída permanentemente.');
                closeForceDeleteModal();
                loadHistory();
            } catch (error) {
                console.error('Erro ao excluir permanentemente:', error);
                
                // Verificar se é erro de autorização (403)
                if (error.response && error.response.status === 403) {
                    showToast('Somente o dono da tarefa pode excluí-la permanentemente.', 'error');
                } else {
                    showToast('Erro ao excluir tarefa. Tente novamente.', 'error');
                }
            }
        }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toastNotification');
            const toastMessage = document.getElementById('toastMessage');
            
            if (toast && toastMessage) {
                toastMessage.textContent = message;
                
                toast.classList.remove('bg-green-500', 'bg-red-500');
                
                if (type === 'error') {
                    toast.classList.add('bg-red-500');
                } else {
                    toast.classList.add('bg-green-500');
                }
                
                toast.classList.remove('hidden');
                
                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 3000);
            }
        }

        function hideToast() {
            const toast = document.getElementById('toastNotification');
            if (toast) {
                toast.classList.add('hidden');
            }
        }

    </script>
</body>
</html>

