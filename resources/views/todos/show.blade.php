<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $todo->text }} - RezenDo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="custom-bg min-h-screen">
    <div class="container mx-auto px-3 sm:px-4 py-4 sm:py-8 max-w-4xl">
        <div class="mb-6 flex items-center justify-between">
            <button 
                onclick="window.history.back()"
                class="text-[#fb9e0b] hover:text-[#fc6c04] font-medium text-sm"
            >
                ← Voltar
            </button>
            @if($todo->user_id === auth()->id() || $todo->hasWritePermission(auth()->id()))
                <a 
                    href="{{ route('todos.edit', $todo) }}"
                    class="custom-btn-primary px-6 py-3 rounded-lg font-semibold transition-colors text-sm"
                >
                    Editar
                </a>
            @endif
        </div>

        <!-- Card da Tarefa -->
        <div class="main-card-bg rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">
                        {{ $todo->text }}
                    </h1>
                    @if($todo->description)
                        <p class="text-gray-600 mb-4 whitespace-pre-wrap">{{ $todo->description }}</p>
                    @endif
                </div>
                <div class="ml-4">
                    @if($todo->priority?->value === 'urgent' || $todo->priority === 'urgent')
                        <span class="inline-block px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">Urgente</span>
                    @elseif($todo->priority?->value === 'medium' || $todo->priority === 'medium')
                        <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">Média</span>
                    @else
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">Simples</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4 text-sm">
                <div>
                    <span class="text-gray-500">Status:</span>
                    <span class="ml-2 font-semibold {{ $todo->completed ? 'text-green-600' : 'text-orange-600' }}">
                        {{ $todo->completed ? '✓ Concluída' : '⏳ Pendente' }}
                    </span>
                </div>
                @if($todo->date)
                    <div>
                        <span class="text-gray-500">Data:</span>
                        <span class="ml-2 font-semibold text-gray-700">{{ $todo->date->format('d/m/Y') }}</span>
                    </div>
                @endif
                @if($todo->assignedTo)
                    <div>
                        <span class="text-gray-500">Responsável:</span>
                        <span class="ml-2 font-semibold text-gray-700">{{ $todo->assignedTo->name }}</span>
                    </div>
                @endif
                <div>
                    <span class="text-gray-500">Criada por:</span>
                    <span class="ml-2 font-semibold text-gray-700">{{ $todo->user->name }}</span>
                </div>
            </div>

            @if($todo->sharedWith->count() > 0)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <span class="text-gray-500 text-sm">Compartilhada com:</span>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach($todo->sharedWith as $user)
                            <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs">
                                {{ $user->name }}
                                <span class="text-gray-500">({{ $user->pivot->permission === 'write' ? 'editar' : 'visualizar' }})</span>
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($todo->user_id === auth()->id())
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <button 
                        onclick="toggleTodoCompletion()"
                        id="toggleTodoBtn"
                        class="w-full sm:w-auto px-6 py-3 rounded-lg font-semibold transition-colors text-sm {{ $todo->completed ? 'bg-yellow-500 hover:bg-yellow-600 text-white' : 'bg-gradient-to-r from-[#fb9e0b] to-[#fc6c04] text-white hover:opacity-90' }}"
                    >
                        {{ $todo->completed ? '⏳ Marcar como Pendente' : '✓ Marcar como Concluída' }}
                    </button>
                </div>
            @endif
        </div>

        <!-- Seção de Comentários -->
        <div class="main-card-bg rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold mb-4 text-gray-700">💬 Comentários</h2>
            
            <!-- Formulário de Comentário -->
            <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                <textarea 
                    id="commentContent" 
                    rows="3"
                    maxlength="1000"
                    placeholder="Adicione um comentário..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none custom-focus resize-none"
                ></textarea>
                <div class="flex justify-between items-center mt-2">
                    <span id="commentCounter" class="text-xs text-gray-500">0 / 1000 caracteres</span>
                    <button 
                        type="button"
                        onclick="addComment()"
                        class="bg-gradient-to-r from-[#fb9e0b] to-[#fc6c04] text-white font-semibold py-2 px-4 rounded-lg hover:opacity-90 transition-opacity text-sm"
                    >
                        Adicionar Comentário
                    </button>
                </div>
            </div>

            <!-- Lista de Comentários -->
            <div id="commentsList" class="space-y-3">
                <p class="text-sm text-gray-500">Carregando comentários...</p>
            </div>
        </div>
    </div>

    <!-- Modal de Edição de Comentário -->
    <div id="editCommentModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeEditCommentModal()"></div>
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="main-card-bg rounded-lg shadow-2xl w-full max-w-2xl relative transform transition-all">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-xl sm:text-2xl font-semibold text-gray-700">Editar Comentário</h2>
                        <button 
                            onclick="closeEditCommentModal()"
                            class="text-gray-500 hover:text-gray-700 transition-colors p-1 rounded-full hover:bg-gray-100"
                            aria-label="Fechar"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form id="editCommentForm" onsubmit="saveCommentEdit(event)" class="space-y-4">
                        <input type="hidden" id="editCommentId" value="">
                        <div>
                            <label for="editCommentContent" class="block text-sm font-medium text-gray-700 mb-2">
                                Comentário
                            </label>
                            <textarea 
                                id="editCommentContent" 
                                rows="5"
                                maxlength="1000"
                                required
                                placeholder="Digite seu comentário..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none custom-focus resize-none"
                            ></textarea>
                            <div class="flex justify-end mt-1">
                                <span id="editCommentCounter" class="text-xs text-gray-500">0 / 1000 caracteres</span>
                            </div>
                        </div>
                        
                        <div class="flex gap-3 pt-3">
                            <button 
                                type="submit"
                                class="flex-1 bg-gradient-to-r from-[#fb9e0b] to-[#fc6c04] text-white font-semibold py-2.5 px-4 rounded-lg hover:opacity-90 transition-opacity"
                            >
                                Salvar Alterações
                            </button>
                            <button 
                                type="button"
                                onclick="closeEditCommentModal()"
                                class="px-6 py-2.5 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition-colors"
                            >
                                Cancelar
                            </button>
                        </div>
                    </form>
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
        const todoId = {{ $todo->id }};
        const currentUserId = {{ auth()->id() }};
        const isTodoOwner = {{ $todo->user_id === auth()->id() ? 'true' : 'false' }};
        let todoCompleted = {{ $todo->completed ? 'true' : 'false' }};

        // Carregar comentários ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            loadComments();
            setupCommentCounter();
        });

        function toggleTodoCompletion() {
            const btn = document.getElementById('toggleTodoBtn');
            const originalText = btn.textContent;
            
            // Desabilitar botão durante a requisição
            btn.disabled = true;
            btn.textContent = 'Processando...';

            window.axios.patch(`/api/todos/${todoId}/toggle`)
                .then(response => {
                    todoCompleted = response.data.completed;
                    
                    // Atualizar status na tela
                    const statusElement = document.querySelector('.grid.grid-cols-1.sm\\:grid-cols-2 > div:first-child .font-semibold');
                    if (statusElement) {
                        if (todoCompleted) {
                            statusElement.textContent = '✓ Concluída';
                            statusElement.classList.remove('text-orange-600');
                            statusElement.classList.add('text-green-600');
                        } else {
                            statusElement.textContent = '⏳ Pendente';
                            statusElement.classList.remove('text-green-600');
                            statusElement.classList.add('text-orange-600');
                        }
                    }

                    // Atualizar botão
                    if (todoCompleted) {
                        btn.textContent = '⏳ Marcar como Pendente';
                        btn.className = 'w-full sm:w-auto px-6 py-3 rounded-lg font-semibold transition-colors text-sm bg-yellow-500 hover:bg-yellow-600 text-white';
                    } else {
                        btn.textContent = '✓ Marcar como Concluída';
                        btn.className = 'w-full sm:w-auto px-6 py-3 rounded-lg font-semibold transition-colors text-sm bg-gradient-to-r from-[#fb9e0b] to-[#fc6c04] text-white hover:opacity-90';
                    }

                    showToast(todoCompleted ? 'Tarefa marcada como concluída! Todos os participantes foram notificados.' : 'Tarefa marcada como pendente.');
                })
                .catch(error => {
                    console.error('Erro ao alterar status da tarefa:', error);
                    showToast('Erro ao alterar status da tarefa', 'error');
                })
                .finally(() => {
                    btn.disabled = false;
                });
        }

        function setupCommentCounter() {
            const commentContent = document.getElementById('commentContent');
            const commentCounter = document.getElementById('commentCounter');
            
            if (commentContent && commentCounter) {
                commentContent.addEventListener('input', function() {
                    const length = this.value.length;
                    commentCounter.textContent = `${length} / 1000 caracteres`;
                });
            }
        }

        function addComment() {
            const content = document.getElementById('commentContent').value.trim();
            
            if (!content) {
                showToast('O comentário não pode estar vazio', 'error');
                return;
            }

            window.axios.post(`/api/todos/${todoId}/comments`, { content })
                .then(response => {
                    showToast('Comentário adicionado com sucesso!');
                    document.getElementById('commentContent').value = '';
                    document.getElementById('commentCounter').textContent = '0 / 1000 caracteres';
                    loadComments();
                })
                .catch(error => {
                    console.error('Erro ao adicionar comentário:', error);
                    const message = error.response?.data?.message || 'Erro ao adicionar comentário';
                    showToast(message, 'error');
                });
        }

        function loadComments() {
            window.axios.get(`/api/todos/${todoId}/comments`)
                .then(response => {
                    const comments = response.data;
                    const listDiv = document.getElementById('commentsList');
                    
                    if (comments.length === 0) {
                        listDiv.innerHTML = '<p class="text-sm text-gray-500">Nenhum comentário ainda. Seja o primeiro a comentar!</p>';
                    } else {
                        // Função auxiliar para escapar HTML
                        function escapeHtml(text) {
                            const div = document.createElement('div');
                            div.textContent = text;
                            return div.innerHTML;
                        }
                        
                        listDiv.innerHTML = comments.map(comment => {
                            const isOwner = comment.user_id === currentUserId;
                            const canEdit = isOwner;
                            const canDelete = isOwner || isTodoOwner;
                            
                            // Verificar se foi editado
                            const createdDate = new Date(comment.created_at);
                            const updatedDate = new Date(comment.updated_at);
                            const wasEdited = updatedDate.getTime() > createdDate.getTime() + 1000;
                            
                            const displayDate = wasEdited ? updatedDate : createdDate;
                            const dateText = wasEdited 
                                ? `Editado em ${displayDate.toLocaleString('pt-BR')}`
                                : displayDate.toLocaleString('pt-BR');
                            
                            // Função recursiva para renderizar respostas
                            function renderReplies(replies) {
                                if (!replies || replies.length === 0) {
                                    return '';
                                }
                                
                                return replies.map(reply => {
                                    const replyIsOwner = reply.user_id === currentUserId;
                                    const replyCanEdit = replyIsOwner;
                                    const replyCanDelete = replyIsOwner || isTodoOwner;
                                    
                                    const replyCreatedDate = new Date(reply.created_at);
                                    const replyUpdatedDate = new Date(reply.updated_at);
                                    const replyWasEdited = replyUpdatedDate.getTime() > replyCreatedDate.getTime() + 1000;
                                    const replyDisplayDate = replyWasEdited ? replyUpdatedDate : replyCreatedDate;
                                    const replyDateText = replyWasEdited 
                                        ? `Editado em ${replyDisplayDate.toLocaleString('pt-BR')}`
                                        : replyDisplayDate.toLocaleString('pt-BR');
                                    
                                    const replyRepliesHtml = renderReplies(reply.replies);

                                    return `
                                        <div class="mt-3 p-3 bg-white border border-gray-200 rounded-lg" id="comment-${reply.id}">
                                            <div class="flex items-start justify-between mb-2">
                                                <div>
                                                    <div class="flex items-center gap-1">
                                                        <span class="text-gray-400 text-xs">↳</span>
                                                        <div class="font-medium text-gray-800 text-sm">${escapeHtml(reply.user.name)}</div>
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        ${replyDateText}
                                                        ${replyWasEdited ? '<span class="ml-2 text-[#fb9e0b] font-medium">(Editado)</span>' : ''}
                                                    </div>
                                                </div>
                                                ${replyCanEdit || replyCanDelete ? `
                                                    <div class="flex gap-2">
                                                        ${replyCanEdit ? `<button onclick="editComment(${reply.id})" class="text-blue-600 hover:text-blue-800 text-xs">Editar</button>` : ''}
                                                        ${replyCanDelete ? `<button onclick="deleteComment(${reply.id})" class="text-red-600 hover:text-red-800 text-xs">Excluir</button>` : ''}
                                                    </div>
                                                ` : ''}
                                            </div>
                                            <div class="text-gray-700 text-sm whitespace-pre-wrap mb-2" id="comment-content-${reply.id}">${escapeHtml(reply.content)}</div>
                                            <input type="hidden" id="comment-original-${reply.id}" value="${escapeHtml(reply.content)}">
                                            
                                            <div class="flex items-center gap-4 mb-2">
                                                <button onclick="showReplyForm(${reply.id})" class="text-gray-600 hover:text-gray-800 text-xs font-medium">
                                                    💬 Responder
                                                </button>
                                            </div>
                                            
                                            <div id="reply-form-${reply.id}" class="hidden mt-2">
                                                <textarea id="reply-content-${reply.id}" rows="2" maxlength="1000" placeholder="Digite sua resposta..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none custom-focus resize-none text-sm"></textarea>
                                                <div class="flex gap-2 mt-2">
                                                    <button onclick="submitReply(${reply.id})" class="px-4 py-1.5 bg-gradient-to-r from-[#fb9e0b] to-[#fc6c04] text-white text-sm font-semibold rounded-lg hover:opacity-90 transition-opacity">
                                                        Enviar
                                                    </button>
                                                    <button onclick="hideReplyForm(${reply.id})" class="px-4 py-1.5 bg-gray-500 text-white text-sm font-semibold rounded-lg hover:bg-gray-600 transition-colors">
                                                        Cancelar
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            ${replyRepliesHtml ? `<div class="mt-3">${replyRepliesHtml}</div>` : ''}
                                        </div>
                                    `;
                                }).join('');
                            }
                            
                            const repliesHtml = renderReplies(comment.replies);
                            
                            return `
                                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg mb-3" id="comment-${comment.id}">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <div class="font-medium text-gray-800">${escapeHtml(comment.user.name)}</div>
                                            <div class="text-xs text-gray-500">
                                                ${dateText}
                                                ${wasEdited ? '<span class="ml-2 text-[#fb9e0b] font-medium">(Editado)</span>' : ''}
                                            </div>
                                        </div>
                                        ${canEdit || canDelete ? `
                                            <div class="flex gap-2">
                                                ${canEdit ? `<button onclick="editComment(${comment.id})" class="text-blue-600 hover:text-blue-800 text-sm">Editar</button>` : ''}
                                                ${canDelete ? `<button onclick="deleteComment(${comment.id})" class="text-red-600 hover:text-red-800 text-sm">Excluir</button>` : ''}
                                            </div>
                                        ` : ''}
                                    </div>
                                    <div class="text-gray-700 whitespace-pre-wrap mb-2" id="comment-content-${comment.id}">${escapeHtml(comment.content)}</div>
                                    <input type="hidden" id="comment-original-${comment.id}" value="${escapeHtml(comment.content)}">
                                    
                                    <div class="flex items-center gap-4 mb-2">
                                        <button onclick="showReplyForm(${comment.id})" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                                            💬 Responder
                                        </button>
                                    </div>
                                    
                                    <div id="reply-form-${comment.id}" class="hidden mt-2">
                                        <textarea id="reply-content-${comment.id}" rows="2" maxlength="1000" placeholder="Digite sua resposta..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none custom-focus resize-none text-sm"></textarea>
                                        <div class="flex gap-2 mt-2">
                                            <button onclick="submitReply(${comment.id})" class="px-4 py-1.5 bg-gradient-to-r from-[#fb9e0b] to-[#fc6c04] text-white text-sm font-semibold rounded-lg hover:opacity-90 transition-opacity">
                                                Enviar
                                            </button>
                                            <button onclick="hideReplyForm(${comment.id})" class="px-4 py-1.5 bg-gray-500 text-white text-sm font-semibold rounded-lg hover:bg-gray-600 transition-colors">
                                                Cancelar
                                            </button>
                                        </div>
                                    </div>
                                    
                                    ${repliesHtml ? `<div class="mt-3">${repliesHtml}</div>` : ''}
                                </div>
                            `;
                        }).join('');
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar comentários:', error);
                    document.getElementById('commentsList').innerHTML = '<p class="text-sm text-red-500">Erro ao carregar comentários.</p>';
                });
        }

        function editComment(commentId) {
            const originalContentEl = document.getElementById(`comment-original-${commentId}`);
            if (!originalContentEl) {
                showToast('Erro ao carregar comentário', 'error');
                return;
            }
            const originalContent = originalContentEl.value.replace(/&#39;/g, "'").replace(/&quot;/g, '"');
            
            // Preencher o modal
            document.getElementById('editCommentId').value = commentId;
            const textarea = document.getElementById('editCommentContent');
            textarea.value = originalContent;
            updateEditCommentCounter();
            
            // Mostrar modal
            document.getElementById('editCommentModal').classList.remove('hidden');
            textarea.focus();
        }

        function closeEditCommentModal() {
            document.getElementById('editCommentModal').classList.add('hidden');
            document.getElementById('editCommentId').value = '';
            document.getElementById('editCommentContent').value = '';
        }

        function updateEditCommentCounter() {
            const textarea = document.getElementById('editCommentContent');
            const counter = document.getElementById('editCommentCounter');
            if (textarea && counter) {
                const length = textarea.value.length;
                counter.textContent = `${length} / 1000 caracteres`;
            }
        }

        function saveCommentEdit(event) {
            event.preventDefault();
            
            const commentId = document.getElementById('editCommentId').value;
            const content = document.getElementById('editCommentContent').value.trim();
            
            if (!content) {
                showToast('O comentário não pode estar vazio', 'error');
                return;
            }

            window.axios.put(`/api/todos/comments/${commentId}`, { content })
                .then(response => {
                    showToast('Comentário atualizado com sucesso!');
                    closeEditCommentModal();
                    loadComments();
                })
                .catch(error => {
                    console.error('Erro ao editar comentário:', error);
                    showToast('Erro ao editar comentário', 'error');
                });
        }

        // Configurar contador do modal de edição
        document.addEventListener('DOMContentLoaded', function() {
            const editTextarea = document.getElementById('editCommentContent');
            if (editTextarea) {
                editTextarea.addEventListener('input', updateEditCommentCounter);
            }
            
            // Fechar modal com ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const modal = document.getElementById('editCommentModal');
                    if (modal && !modal.classList.contains('hidden')) {
                        closeEditCommentModal();
                    }
                }
            });
        });

        function deleteComment(commentId) {
            if (!confirm('Deseja realmente excluir este comentário?')) {
                return;
            }

            window.axios.delete(`/api/todos/comments/${commentId}`)
                .then(response => {
                    showToast('Comentário excluído com sucesso!');
                    loadComments();
                })
                .catch(error => {
                    console.error('Erro ao excluir comentário:', error);
                    showToast('Erro ao excluir comentário', 'error');
                });
        }

        // Função para mostrar formulário de resposta
        function showReplyForm(commentId) {
            const form = document.getElementById(`reply-form-${commentId}`);
            if (form) {
                form.classList.remove('hidden');
                const textarea = document.getElementById(`reply-content-${commentId}`);
                if (textarea) {
                    textarea.focus();
                }
            }
        }

        // Função para esconder formulário de resposta
        function hideReplyForm(commentId) {
            const form = document.getElementById(`reply-form-${commentId}`);
            if (form) {
                form.classList.add('hidden');
                const textarea = document.getElementById(`reply-content-${commentId}`);
                if (textarea) {
                    textarea.value = '';
                }
            }
        }

        // Função para enviar resposta
        function submitReply(commentId) {
            const textarea = document.getElementById(`reply-content-${commentId}`);
            if (!textarea) {
                showToast('Erro ao encontrar campo de resposta', 'error');
                return;
            }

            const content = textarea.value.trim();
            if (!content) {
                showToast('A resposta não pode estar vazia', 'error');
                return;
            }

            if (content.length > 1000) {
                showToast('A resposta não pode ter mais de 1000 caracteres', 'error');
                return;
            }

            window.axios.post(`/api/todos/${todoId}/comments/${commentId}/reply`, { content })
                .then(response => {
                    showToast('Resposta adicionada com sucesso!');
                    hideReplyForm(commentId);
                    loadComments();
                })
                .catch(error => {
                    console.error('Erro ao adicionar resposta:', error);
                    const message = error.response?.data?.message || 'Erro ao adicionar resposta';
                    showToast(message, 'error');
                });
        }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toastNotification');
            const toastMessage = document.getElementById('toastMessage');
            
            if (!toast || !toastMessage) {
                alert(message);
                return;
            }
            
            toastMessage.textContent = message;
            toast.classList.remove('bg-green-500', 'bg-red-500', 'bg-yellow-500');
            
            if (type === 'error') {
                toast.classList.add('bg-red-500');
            } else if (type === 'warning') {
                toast.classList.add('bg-yellow-500');
            } else {
                toast.classList.add('bg-green-500');
            }
            
            toast.classList.remove('hidden');
            
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }

        function hideToast() {
            const toast = document.getElementById('toastNotification');
            if (toast) {
                toast.classList.add('hidden');
            }
        }

        window.hideToast = hideToast;
    </script>
</body>
</html>
