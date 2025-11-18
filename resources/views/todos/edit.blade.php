<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editar Tarefa - RezenDo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="custom-bg min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-4xl font-bold text-gray-800">
                <span style="color: #fb9e0b;">Editar</span> <span style="color: #fbe20d;">Tarefa</span>
            </h1>
            <a 
                href="{{ route('todos.list') }}"
                class="custom-btn-primary px-6 py-3 rounded-lg font-semibold transition-colors"
            >
                ← Voltar
            </a>
        </div>
        
        <!-- Formulário para Editar Tarefa -->
        <div class="main-card-bg rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold mb-4 text-gray-700">Editar Tarefa</h2>
            <form id="editTodoForm" class="space-y-4">
                <input type="hidden" id="todoId" value="{{ $todo->id }}">
                
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
                        value="{{ $todo->text }}"
                        placeholder="Digite o título da tarefa..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none custom-focus"
                    >
                    <div class="flex justify-end mt-1">
                        <span id="todoTextCounter" class="text-xs text-gray-500">{{ strlen($todo->text) }} / 200 caracteres</span>
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
                    >{{ $todo->description ?? '' }}</textarea>
                    <div class="flex justify-end mt-1">
                        <span id="todoDescriptionCounter" class="text-xs text-gray-500">{{ strlen($todo->description ?? '') }} / 500 caracteres</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Prioridade
                    </label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="priority" value="simple" {{ $todo->priority === 'simple' ? 'checked' : '' }} class="mr-2">
                            <span class="text-green-600 font-medium">Simples</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="priority" value="medium" {{ $todo->priority === 'medium' ? 'checked' : '' }} class="mr-2">
                            <span class="text-yellow-600 font-medium">Média</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="priority" value="urgent" {{ $todo->priority === 'urgent' ? 'checked' : '' }} class="mr-2">
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
                        value="{{ $todo->date ? $todo->date->format('d/m/Y') : '' }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none custom-focus"
                    >
                    <p class="text-xs text-gray-500 mt-1">Digite a data no formato DD/MM/AAAA ou DD/MM/AA</p>
                </div>

                <!-- Atribuição de Responsável -->
                <div>
                    <label for="assignedTo" class="block text-sm font-medium text-gray-700 mb-2">
                        Responsável (opcional)
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="assignedToSearch" 
                            placeholder="Buscar usuário por nome ou email..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none custom-focus"
                            autocomplete="off"
                        >
                        <div id="assignedToResults" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"></div>
                    </div>
                    <div id="assignedToDisplay" class="mt-2">
                        @if($todo->assignedTo)
                            <div class="flex items-center justify-between p-2 bg-blue-50 border border-blue-200 rounded-lg">
                                <span class="text-sm text-gray-700">
                                    <strong>{{ $todo->assignedTo->name }}</strong> ({{ $todo->assignedTo->email }})
                                </span>
                                <button type="button" onclick="removeAssignedTo()" class="text-red-600 hover:text-red-800 text-sm">
                                    Remover
                                </button>
                            </div>
                        @endif
                    </div>
                    <input type="hidden" id="assignedTo" name="assigned_to" value="{{ $todo->assigned_to ?? '' }}">
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button 
                        type="submit"
                        class="flex-1 custom-btn-primary py-3 rounded-lg font-semibold transition-colors"
                    >
                        Salvar Alterações
                    </button>
                    <button 
                        type="button"
                        onclick="window.close()"
                        class="px-6 py-3 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition-colors"
                    >
                        Cancelar
                    </button>
                </div>
            </form>
        </div>

        <!-- Seção de Compartilhamento -->
        <div class="main-card-bg rounded-lg shadow-md p-6 mt-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-semibold text-gray-700">👥 Compartilhamento</h2>
                <button 
                    type="button"
                    onclick="toggleShareSection()"
                    class="text-[#fb9e0b] hover:text-[#fc6c04] font-medium text-sm"
                    id="toggleShareBtn"
                >
                    Mostrar
                </button>
            </div>
            
            <div id="shareSection" class="hidden">
                <!-- Formulário de Compartilhamento -->
                <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-semibold mb-3 text-gray-700">Compartilhar com usuário</h3>
                    <div class="space-y-3">
                        <div class="relative">
                            <input 
                                type="text" 
                                id="shareUserSearch" 
                                placeholder="Buscar usuário por nome ou email..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none custom-focus"
                                autocomplete="off"
                            >
                            <div id="shareUserResults" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Permissão</label>
                            <select id="sharePermission" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none custom-focus">
                                <option value="read">Apenas visualizar</option>
                                <option value="write">Visualizar e editar</option>
                            </select>
                        </div>
                        <button 
                            type="button"
                            onclick="shareTodo()"
                            class="w-full bg-gradient-to-r from-[#fb9e0b] to-[#fc6c04] text-white font-semibold py-2 px-4 rounded-lg hover:opacity-90 transition-opacity"
                        >
                            Compartilhar
                        </button>
                    </div>
                </div>

                <!-- Lista de Usuários Compartilhados -->
                <div>
                    <h3 class="text-lg font-semibold mb-3 text-gray-700">Compartilhado com</h3>
                    <div id="sharedUsersList" class="space-y-2">
                        <p class="text-sm text-gray-500">Carregando...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção de Comentários -->
        <div class="main-card-bg rounded-lg shadow-md p-6 mt-6">
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
    
    <!-- Rodapé -->
    <footer class="mt-8 sm:mt-12 py-4 text-center">
        <p class="text-xs sm:text-sm text-gray-500">
            Desenvolvido por <span class="font-medium text-gray-600">Mateus Pereira</span> - 2025
        </p>
    </footer>
    
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
        // Script específico para a página de edição
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded - Inicializando página de edição');
            
            const editForm = document.getElementById('editTodoForm');
            const todoId = document.getElementById('todoId')?.value;
            
            console.log('Formulário encontrado:', editForm);
            console.log('Todo ID encontrado:', todoId);
            console.log('Axios disponível:', typeof window.axios !== 'undefined');
            
            if (!editForm) {
                console.error('Erro: Formulário de edição não encontrado');
                return;
            }
            
            if (!todoId) {
                console.error('Erro: ID da tarefa não encontrado');
                return;
            }
            
            if (typeof window.axios === 'undefined') {
                console.error('Erro: axios não está disponível. Verifique se o JavaScript foi carregado corretamente.');
                alert('Erro: Sistema não inicializado. Recarregue a página.');
                return;
            }
            
            // Garantir que o CSRF token está configurado
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
                console.log('CSRF token configurado:', csrfToken.content.substring(0, 10) + '...');
            } else {
                console.warn('CSRF token não encontrado no meta tag');
            }
            
            // Configurar headers padrão
            window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            window.axios.defaults.headers.common['Accept'] = 'application/json';
            
            // Configurar interceptor de requisição para debug
            window.axios.interceptors.request.use(
                function (config) {
                    console.log('Interceptor REQUEST: Requisição sendo enviada', config);
                    console.log('URL:', config.url);
                    console.log('Method:', config.method);
                    console.log('Data:', config.data);
                    console.log('Headers:', config.headers);
                    return config;
                },
                function (error) {
                    console.error('Interceptor REQUEST: Erro ao configurar requisição', error);
                    return Promise.reject(error);
                }
            );
            
            // Configurar interceptor de resposta para debug
            window.axios.interceptors.response.use(
                function (response) {
                    console.log('Interceptor RESPONSE: Resposta recebida com sucesso', response);
                    console.log('Status:', response.status);
                    console.log('Data:', response.data);
                    return response;
                },
                function (error) {
                    console.error('Interceptor RESPONSE: Erro na resposta', error);
                    if (error.response) {
                        console.error('Status:', error.response.status);
                        console.error('Data:', error.response.data);
                        console.error('Headers:', error.response.headers);
                    } else if (error.request) {
                        console.error('Requisição feita mas sem resposta:', error.request);
                        console.error('Request config:', error.config);
                    } else {
                        console.error('Erro ao configurar requisição:', error.message);
                    }
                    return Promise.reject(error);
                }
            );
            
            // Aplicar máscara de data brasileira
            const todoDate = document.getElementById('todoDate');
            if (todoDate) {
                todoDate.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 0) {
                        if (value.length <= 2) {
                            value = value;
                        } else if (value.length <= 4) {
                            value = value.substring(0, 2) + '/' + value.substring(2, 4);
                        } else {
                            value = value.substring(0, 2) + '/' + value.substring(2, 4) + '/' + value.substring(4, 8);
                        }
                    }
                    e.target.value = value;
                });
            }
            
            // Configurar contadores de caracteres
            const todoText = document.getElementById('todoText');
            const todoDescription = document.getElementById('todoDescription');
            const todoTextCounter = document.getElementById('todoTextCounter');
            const todoDescriptionCounter = document.getElementById('todoDescriptionCounter');
            
            // Contador para título
            if (todoText && todoTextCounter) {
                const updateTextCounter = () => {
                    const length = todoText.value.length;
                    const maxLength = 200;
                    todoTextCounter.textContent = `${length} / ${maxLength} caracteres`;
                    
                    if (length > maxLength * 0.9) {
                        todoTextCounter.classList.remove('text-gray-500');
                        todoTextCounter.classList.add('text-orange-500');
                    } else if (length > maxLength * 0.95) {
                        todoTextCounter.classList.remove('text-gray-500', 'text-orange-500');
                        todoTextCounter.classList.add('text-red-500');
                    } else {
                        todoTextCounter.classList.remove('text-orange-500', 'text-red-500');
                        todoTextCounter.classList.add('text-gray-500');
                    }
                };
                
                todoText.addEventListener('input', updateTextCounter);
                todoText.addEventListener('keyup', updateTextCounter);
            }
            
            // Contador para descrição
            if (todoDescription && todoDescriptionCounter) {
                const updateDescriptionCounter = () => {
                    const length = todoDescription.value.length;
                    const maxLength = 500;
                    todoDescriptionCounter.textContent = `${length} / ${maxLength} caracteres`;
                    
                    if (length > maxLength * 0.9) {
                        todoDescriptionCounter.classList.remove('text-gray-500');
                        todoDescriptionCounter.classList.add('text-orange-500');
                    } else if (length > maxLength * 0.95) {
                        todoDescriptionCounter.classList.remove('text-gray-500', 'text-orange-500');
                        todoDescriptionCounter.classList.add('text-red-500');
                    } else {
                        todoDescriptionCounter.classList.remove('text-orange-500', 'text-red-500');
                        todoDescriptionCounter.classList.add('text-gray-500');
                    }
                };
                
                todoDescription.addEventListener('input', updateDescriptionCounter);
                todoDescription.addEventListener('keyup', updateDescriptionCounter);
            }
            
            editForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Formulário de edição submetido');
                
                const todoId = document.getElementById('todoId').value;
                console.log('Todo ID:', todoId);
                
                if (!todoId) {
                    console.error('Erro: ID da tarefa não encontrado');
                    showToast('Erro: ID da tarefa não encontrado.', 'error');
                    return false;
                }
                
                // Verificar se axios está disponível - aguardar um pouco se necessário
                if (typeof window.axios === 'undefined') {
                    console.error('Erro: axios não está disponível');
                    showToast('Erro: Sistema não inicializado. Recarregue a página.', 'error');
                    return false;
                }
                
                // Função para converter data brasileira para ISO (suporta DD/MM/YY e DD/MM/YYYY)
                function convertBRToISO(dateBR) {
                    if (!dateBR || !dateBR.trim()) return null;
                    const cleaned = dateBR.trim().replace(/\s/g, '');
                    if (!cleaned) return null;
                    
                    // Verificar formato DD/MM/YY (ano com 2 dígitos) - verificar ANTES do formato de 4 dígitos
                    let match = cleaned.match(/^(\d{2})\/(\d{2})\/(\d{2})$/);
                    if (match) {
                        const [, day, month, yearShort] = match;
                        // Converter ano de 2 dígitos para 4 dígitos
                        let year = parseInt(yearShort, 10);
                        if (year <= 30) {
                            year = 2000 + year;
                        } else {
                            year = 1900 + year;
                        }
                        const yearStr = String(year);
                        const dayNum = parseInt(day, 10);
                        const monthNum = parseInt(month, 10);
                        const yearNum = parseInt(yearStr, 10);
                        const date = new Date(yearNum, monthNum - 1, dayNum);
                        if (!isNaN(date.getTime()) && 
                            date.getDate() === dayNum && 
                            date.getMonth() + 1 === monthNum && 
                            date.getFullYear() === yearNum) {
                            return `${yearStr}-${month}-${day}`;
                        }
                        return 'INVALID';
                    }
                    
                    // Verificar formato DD/MM/YYYY (ano com 4 dígitos)
                    match = cleaned.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
                    if (match) {
                        const [, day, month, year] = match;
                        const dayNum = parseInt(day, 10);
                        const monthNum = parseInt(month, 10);
                        const yearNum = parseInt(year, 10);
                        const date = new Date(yearNum, monthNum - 1, dayNum);
                        if (!isNaN(date.getTime()) && 
                            date.getDate() === dayNum && 
                            date.getMonth() + 1 === monthNum && 
                            date.getFullYear() === yearNum) {
                            return `${year}-${month}-${day}`;
                        }
                        return 'INVALID';
                    }
                    
                    // Se já estiver no formato YYYY-MM-DD
                    if (cleaned.match(/^\d{4}-\d{2}-\d{2}$/)) {
                        return cleaned;
                    }
                    
                    return null;
                }
                
                const dateValue = todoDate && todoDate.value.trim() ? convertBRToISO(todoDate.value.trim()) : null;
                
                // Buscar o campo novamente no momento do submit para garantir que temos o valor mais recente
                const assignedToInput = document.getElementById('assignedTo');
                console.log('🔍 DEBUG - Campo assignedToInput encontrado:', !!assignedToInput);
                console.log('🔍 DEBUG - Valor bruto do campo assignedTo:', assignedToInput?.value);
                console.log('🔍 DEBUG - selectedAssignedUser:', window.selectedAssignedUser);
                
                // Se o campo está vazio mas temos selectedAssignedUser, usar esse valor
                let assignedToValue = assignedToInput?.value?.trim() || null;
                if (!assignedToValue && window.selectedAssignedUser?.id) {
                    console.log('⚠️ Campo vazio, mas selectedAssignedUser existe. Usando:', window.selectedAssignedUser.id);
                    assignedToValue = String(window.selectedAssignedUser.id);
                    if (assignedToInput) {
                        assignedToInput.value = assignedToValue;
                    }
                }
                
                console.log('🔍 DEBUG - Valor após trim e verificação:', assignedToValue);
                console.log('🔍 DEBUG - Tipo do valor:', typeof assignedToValue);
                
                const assignedToFinal = assignedToValue && assignedToValue !== '' && assignedToValue !== '0' 
                    ? parseInt(assignedToValue, 10) 
                    : null;
                
                console.log('🔍 DEBUG - Valor final para enviar:', assignedToFinal);
                
                const formData = {
                    text: document.getElementById('todoText').value.trim(),
                    description: document.getElementById('todoDescription').value.trim() || null,
                    priority: document.querySelector('input[name="priority"]:checked')?.value || 'simple',
                    date: dateValue,
                    assigned_to: assignedToFinal,
                };
                
                console.log('🔍 DEBUG - FormData completo:', JSON.stringify(formData, null, 2));
                
                if (!formData.text) {
                    showToast('Por favor, preencha o título da tarefa.', 'warning');
                    return;
                }
                
                // Validar se a data é inválida
                if (dateValue === 'INVALID') {
                    showToast('Por favor, insira uma data válida no formato DD/MM/AAAA ou DD/MM/AA.', 'error');
                    if (todoDate) todoDate.focus();
                    return false;
                }
                
                console.log('Enviando dados:', formData);
                
                // Desabilitar botão de submit para evitar duplo clique
                const submitBtn = editForm.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Salvando...';
                }
                
                try {
                    console.log('🚀 Fazendo requisição PUT para:', `/api/todos/${todoId}`);
                    console.log('📦 FormData sendo enviado:', JSON.stringify(formData, null, 2));
                    console.log('🔑 assigned_to no payload:', formData.assigned_to);
                    
                    // Criar a promise da requisição
                    const requestPromise = window.axios.put(`/api/todos/${todoId}`, formData, {
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        timeout: 10000, // 10 segundos de timeout
                        validateStatus: function (status) {
                            return status >= 200 && status < 500; // Aceitar qualquer status < 500
                        }
                    });
                    
                    console.log('Promise criada, aguardando resposta...');
                    
                    // Adicionar timeout manual para debug
                    const timeoutPromise = new Promise((_, reject) => {
                        setTimeout(() => {
                            reject(new Error('Timeout: Requisição demorou mais de 10 segundos'));
                        }, 10000);
                    });
                    
                    const response = await Promise.race([requestPromise, timeoutPromise]);
                    
                    console.log('✅ Resposta recebida:', response);
                    console.log('📊 Status:', response.status);
                    console.log('📋 Data completa:', response.data);
                    console.log('👤 assigned_to na resposta:', response.data?.assigned_to);
                    console.log('👤 assigned_to_user na resposta:', response.data?.assigned_to_user);
                    
                    // Log detalhado para debug
                    console.log('🔍 DEBUG COMPLETO - Resposta JSON:', JSON.stringify(response.data, null, 2));
                    
                    showToast('Tarefa atualizada com sucesso!');
                    
                    // Redirecionar após 1 segundo
                    setTimeout(() => {
                        if (window.opener) {
                            // Se foi aberta em nova aba, recarregar a página pai e fechar esta
                            window.opener.location.reload();
                            window.close();
                        } else {
                            // Se não foi aberta em nova aba, redirecionar
                            window.location.href = '{{ route("todos.list") }}';
                        }
                    }, 1000);
                } catch (error) {
                    console.error('Erro ao atualizar tarefa:', error);
                    console.error('Tipo do erro:', error.constructor.name);
                    console.error('Mensagem do erro:', error.message);
                    
                    if (error.response) {
                        console.error('Resposta do erro:', error.response);
                        console.error('Status do erro:', error.response.status);
                        console.error('Data do erro:', error.response.data);
                        console.error('Headers do erro:', error.response.headers);
                    } else if (error.request) {
                        console.error('Requisição feita mas sem resposta:', error.request);
                    } else {
                        console.error('Erro ao configurar requisição:', error.message);
                    }
                    
                    // Reabilitar botão de submit
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Salvar Alterações';
                    }
                    
                    // Tratar erros de validação do Laravel
                    if (error.response && error.response.status === 422) {
                        const errors = error.response.data.errors;
                        let errorMessages = [];
                        
                        if (errors) {
                            Object.keys(errors).forEach(field => {
                                if (Array.isArray(errors[field])) {
                                    errorMessages.push(...errors[field]);
                                } else {
                                    errorMessages.push(errors[field]);
                                }
                            });
                        }
                        
                        if (errorMessages.length > 0) {
                            showToast(errorMessages.join(' '), 'error');
                        } else {
                            showToast('Erro de validação. Verifique os campos preenchidos.', 'error');
                        }
                    } else if (error.response && error.response.data && error.response.data.message) {
                        showToast('Erro: ' + error.response.data.message, 'error');
                    } else {
                        showToast('Erro ao atualizar tarefa. Tente novamente.', 'error');
                    }
                }
            });

            // ========== FUNCIONALIDADES DE COLABORAÇÃO ==========
            window.todoId = todoId; // Tornar todoId global para as funções
            window.currentUserId = {{ auth()->id() }};
            window.isTodoOwner = {{ $todo->user_id === auth()->id() ? 'true' : 'false' }};
            window.searchTimeout = null;
            window.selectedShareUser = null;
            window.selectedAssignedUser = null;

            // Carregar dados iniciais
            if (window.todoId) {
                window.loadSharedUsers();
                window.loadComments();
                window.setupCommentCounter();
            }

            // ========== ATRIBUIÇÃO DE RESPONSÁVEL ==========
            window.assignedToSearch = document.getElementById('assignedToSearch');
            window.assignedToResults = document.getElementById('assignedToResults');
            window.assignedToDisplay = document.getElementById('assignedToDisplay');
            window.assignedToInput = document.getElementById('assignedTo');
            window.searchTimeout = null;
            window.selectedAssignedUser = null;
            window.selectedShareUser = null;

            if (window.assignedToSearch) {
                console.log('✅ Campo assignedToSearch encontrado e listener adicionado');
                window.assignedToSearch.addEventListener('input', function() {
                    const query = this.value.trim();
                    console.log('🔍 Input detectado - query:', query, 'length:', query.length);
                    if (query.length < 2) {
                        window.assignedToResults.classList.add('hidden');
                        console.log('⚠️ Query muito curta, escondendo resultados');
                        return;
                    }

                    console.log('⏳ Aguardando 300ms antes de buscar...');
                    clearTimeout(window.searchTimeout);
                    window.searchTimeout = setTimeout(() => {
                        console.log('🚀 Chamando searchUsers com query:', query);
                        searchUsers(query, 'assigned');
                    }, 300);
                });
            } else {
                console.error('❌ ERRO: Campo assignedToSearch não encontrado!');
            }

            // Fechar resultados ao clicar fora
            document.addEventListener('click', function(e) {
                if (window.assignedToSearch && window.assignedToResults && 
                    !window.assignedToSearch.contains(e.target) && 
                    !window.assignedToResults.contains(e.target)) {
                    window.assignedToResults.classList.add('hidden');
                }
            });
        });
        
        // Funções de toast (mesmas do todos.js)
        function showToast(message, type = 'success') {
            try {
                const toast = document.getElementById('toastNotification');
                const toastMessage = document.getElementById('toastMessage');
                
                if (!toast || !toastMessage) {
                    console.warn('Toast elements not found, using alert instead');
                    alert(message);
                    return;
                }
                
                toastMessage.textContent = message;
                
                // Remover classes de cor anteriores
                if (toast && toast.classList) {
                    toast.classList.remove('bg-green-500', 'bg-red-500', 'bg-yellow-500');
                    
                    // Adicionar classe de cor baseada no tipo
                    if (type === 'error') {
                        toast.classList.add('bg-red-500');
                    } else if (type === 'warning') {
                        toast.classList.add('bg-yellow-500');
                    } else {
                        toast.classList.add('bg-green-500');
                    }
                    
                    toast.classList.remove('hidden');
                    
                    // Esconder após 3 segundos
                    setTimeout(() => {
                        if (toast && toast.classList) {
                            toast.classList.add('hidden');
                        }
                    }, 3000);
                }
            } catch (error) {
                console.error('Erro ao mostrar toast:', error);
                alert(message);
            }
        }
        
        function hideToast() {
            try {
                const toast = document.getElementById('toastNotification');
                if (!toast || !toast.classList) return;
                
                toast.classList.add('hidden');
            } catch (error) {
                console.error('Erro ao esconder toast:', error);
            }
        }
        
        window.hideToast = hideToast;

        function searchUsers(query, type) {
            console.log('🔍 searchUsers chamado - query:', query, 'type:', type);
            console.log('🔍 URL da requisição:', '/api/users/search?q=' + encodeURIComponent(query));
            
            window.axios.get('/api/users/search', { params: { q: query } })
                .then(response => {
                    console.log('✅ Resposta da API recebida:', response);
                    console.log('✅ Dados recebidos:', response.data);
                    const users = response.data;
                    const resultsDiv = type === 'assigned' ? window.assignedToResults : document.getElementById('shareUserResults');
                    
                    console.log('🔍 resultsDiv encontrado:', !!resultsDiv);
                    console.log('🔍 Número de usuários encontrados:', users.length);
                    
                    if (users.length === 0) {
                        resultsDiv.innerHTML = '<div class="p-3 text-sm text-gray-500">Nenhum usuário encontrado</div>';
                        console.log('⚠️ Nenhum usuário encontrado');
                    } else {
                        resultsDiv.innerHTML = users.map(user => `
                            <div class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0" 
                                 onclick="selectUser(${user.id}, '${escapeHtml(user.name)}', '${escapeHtml(user.email)}', '${type}')">
                                <div class="font-medium text-gray-800">${escapeHtml(user.name)}</div>
                                <div class="text-sm text-gray-500">${escapeHtml(user.email)}</div>
                            </div>
                        `).join('');
                        console.log('✅ HTML gerado para', users.length, 'usuários');
                    }
                    resultsDiv.classList.remove('hidden');
                    console.log('✅ Resultados exibidos, classe hidden removida');
                })
                .catch(error => {
                    console.error('❌ ERRO ao buscar usuários:', error);
                    console.error('❌ Detalhes do erro:', error.response?.data || error.message);
                    console.error('❌ Status do erro:', error.response?.status);
                });
        }
        
        // Função auxiliar para escapar HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function selectUser(userId, name, email, type) {
            console.log('🎯 selectUser chamado - userId:', userId, 'type:', type);
            if (type === 'assigned') {
                window.selectedAssignedUser = { id: userId, name, email };
                
                // Buscar o campo novamente para garantir que está atualizado
                const assignedToField = document.getElementById('assignedTo');
                console.log('🔍 Campo assignedTo encontrado:', !!assignedToField);
                console.log('🔍 Valor ANTES de atualizar:', assignedToField?.value);
                
                if (assignedToField) {
                    assignedToField.value = String(userId); // Garantir que é string
                    console.log('✅ Usuário selecionado como responsável. ID:', userId, 'Nome:', name);
                    console.log('✅ Valor do campo hidden assignedTo DEPOIS:', assignedToField.value);
                    console.log('✅ Tipo do valor:', typeof assignedToField.value);
                    console.log('✅ Campo hidden encontrado:', !!assignedToField);
                    
                    // Verificar novamente após um pequeno delay
                    setTimeout(() => {
                        console.log('🔍 Verificação após 100ms - Valor:', document.getElementById('assignedTo')?.value);
                    }, 100);
                } else {
                    console.error('❌ ERRO: Campo assignedToInput não encontrado!');
                }
                if (window.assignedToSearch) window.assignedToSearch.value = '';
                if (window.assignedToResults) window.assignedToResults.classList.add('hidden');
                
                if (window.assignedToDisplay) {
                    window.assignedToDisplay.innerHTML = `
                        <div class="flex items-center justify-between p-2 bg-blue-50 border border-blue-200 rounded-lg">
                            <span class="text-sm text-gray-700">
                                <strong>${escapeHtml(name)}</strong> (${escapeHtml(email)})
                            </span>
                            <button type="button" onclick="removeAssignedTo()" class="text-red-600 hover:text-red-800 text-sm">
                                Remover
                            </button>
                        </div>
                    `;
                }
            } else {
                window.selectedShareUser = { id: userId, name, email };
                document.getElementById('shareUserSearch').value = `${name} (${email})`;
                document.getElementById('shareUserResults').classList.add('hidden');
            }
        }

        window.removeAssignedTo = function() {
            if (window.assignedToInput) window.assignedToInput.value = '';
            if (window.assignedToDisplay) window.assignedToDisplay.innerHTML = '';
            window.selectedAssignedUser = null;
        };

        // ========== COMPARTILHAMENTO ==========
        const shareUserSearch = document.getElementById('shareUserSearch');
        const shareUserResults = document.getElementById('shareUserResults');

        if (shareUserSearch) {
            shareUserSearch.addEventListener('input', function() {
                const query = this.value.trim();
                if (query.length < 2) {
                    shareUserResults.classList.add('hidden');
                    selectedShareUser = null;
                    return;
                }

                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchUsers(query, 'share');
                }, 300);
            });
        }

        window.toggleShareSection = function() {
            const section = document.getElementById('shareSection');
            const btn = document.getElementById('toggleShareBtn');
            if (section.classList.contains('hidden')) {
                section.classList.remove('hidden');
                btn.textContent = 'Ocultar';
                loadSharedUsers();
            } else {
                section.classList.add('hidden');
                btn.textContent = 'Mostrar';
            }
        };

        window.shareTodo = function() {
            if (!window.selectedShareUser) {
                showToast('Selecione um usuário para compartilhar', 'error');
                return;
            }

            const permission = document.getElementById('sharePermission').value;

            window.axios.post(`/api/todos/${window.todoId}/shares`, {
                user_id: window.selectedShareUser.id,
                permission: permission
            })
            .then(response => {
                showToast(response.data.message || 'Tarefa compartilhada com sucesso!');
                const shareUserSearch = document.getElementById('shareUserSearch');
                if (shareUserSearch) shareUserSearch.value = '';
                window.selectedShareUser = null;
                loadSharedUsers();
            })
            .catch(error => {
                console.error('Erro ao compartilhar:', error);
                const message = error.response?.data?.message || 'Erro ao compartilhar tarefa';
                showToast(message, 'error');
            });
        };

        window.loadSharedUsers = function() {
            window.axios.get(`/api/todos/${window.todoId}/shares`)
                .then(response => {
                    const users = response.data;
                    const listDiv = document.getElementById('sharedUsersList');
                    
                    if (users.length === 0) {
                        listDiv.innerHTML = '<p class="text-sm text-gray-500">Nenhum usuário compartilhado ainda.</p>';
                    } else {
                        listDiv.innerHTML = users.map(user => `
                            <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                <div>
                                    <div class="font-medium text-gray-800">${user.name}</div>
                                    <div class="text-sm text-gray-500">${user.email}</div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        Permissão: ${user.pivot.permission === 'write' ? 'Visualizar e editar' : 'Apenas visualizar'}
                                    </div>
                                </div>
                                <button 
                                    onclick="removeShare(${user.id})" 
                                    class="text-red-600 hover:text-red-800 text-sm font-medium"
                                >
                                    Remover
                                </button>
                            </div>
                        `).join('');
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar usuários compartilhados:', error);
                    document.getElementById('sharedUsersList').innerHTML = '<p class="text-sm text-red-500">Erro ao carregar usuários compartilhados.</p>';
                });
        };

        window.removeShare = function(userId) {
            if (!confirm('Deseja remover o compartilhamento com este usuário?')) {
                return;
            }

            window.axios.delete(`/api/todos/${window.todoId}/shares/${userId}`)
                .then(response => {
                    showToast('Compartilhamento removido com sucesso!');
                    window.loadSharedUsers();
                })
                .catch(error => {
                    console.error('Erro ao remover compartilhamento:', error);
                    showToast('Erro ao remover compartilhamento', 'error');
                });
        };

        // ========== COMENTÁRIOS ==========
        window.setupCommentCounter = function() {
            const commentContent = document.getElementById('commentContent');
            const commentCounter = document.getElementById('commentCounter');
            
            if (commentContent && commentCounter) {
                commentContent.addEventListener('input', function() {
                    const length = this.value.length;
                    commentCounter.textContent = `${length} / 1000 caracteres`;
                });
            }
        };

        window.addComment = function() {
            console.log('🔍 addComment() chamado');
            console.log('🔍 window.todoId:', window.todoId);
            console.log('🔍 window.axios:', typeof window.axios);
            if (!window.todoId) {
                console.error('❌ window.todoId não está definido!');
                showToast('Erro: ID da tarefa não encontrado', 'error');
                return;
            }
            
            const contentEl = document.getElementById('commentContent');
            if (!contentEl) {
                console.error('❌ Campo commentContent não encontrado!');
                showToast('Erro: Campo de comentário não encontrado', 'error');
                return;
            }
            
            const content = contentEl.value.trim();
            
            if (!content) {
                showToast('O comentário não pode estar vazio', 'error');
                return;
            }

            console.log('🚀 Enviando comentário para todoId:', window.todoId, 'content:', content);
            window.axios.post(`/api/todos/${window.todoId}/comments`, { content })
                .then(response => {
                    showToast('Comentário adicionado com sucesso!');
                    document.getElementById('commentContent').value = '';
                    document.getElementById('commentCounter').textContent = '0 / 1000 caracteres';
                    window.loadComments();
                })
                .catch(error => {
                    console.error('Erro ao adicionar comentário:', error);
                    const message = error.response?.data?.message || 'Erro ao adicionar comentário';
                    showToast(message, 'error');
                });
        };

        window.loadComments = function() {
            if (!window.todoId) {
                console.error('❌ window.todoId não está definido!');
                return;
            }
            
            console.log('🔍 Carregando comentários para todoId:', window.todoId);
            window.axios.get(`/api/todos/${window.todoId}/comments`)
                .then(response => {
                    console.log('✅ Comentários carregados:', response.data);
                    const comments = response.data;
                    const listDiv = document.getElementById('commentsList');
                    
                    if (!listDiv) {
                        console.error('❌ Elemento commentsList não encontrado!');
                        return;
                    }
                    
                    if (comments.length === 0) {
                        listDiv.innerHTML = '<p class="text-sm text-gray-500">Nenhum comentário ainda. Seja o primeiro a comentar!</p>';
                    } else {
                        listDiv.innerHTML = comments.map(comment => {
                            const isOwner = comment.user_id === window.currentUserId;
                            const canEdit = isOwner;
                            const canDelete = isOwner || window.isTodoOwner;
                            
                            // Verificar se foi editado
                            const createdDate = new Date(comment.created_at);
                            const updatedDate = new Date(comment.updated_at);
                            const wasEdited = updatedDate.getTime() > createdDate.getTime() + 1000;
                            
                            const displayDate = wasEdited ? updatedDate : createdDate;
                            const dateText = wasEdited 
                                ? `Editado em ${displayDate.toLocaleString('pt-BR')}`
                                : displayDate.toLocaleString('pt-BR');
                            
                            // Função recursiva para renderizar respostas
                            function renderReplies(replies, depth = 1) {
                                if (!replies || replies.length === 0) {
                                    return '';
                                }
                                
                                const marginLeft = depth * 2; // 2rem por nível (32px)
                                
                                return replies.map(reply => {
                                    const replyIsOwner = reply.user_id === window.currentUserId;
                                    const replyCanEdit = replyIsOwner;
                                    const replyCanDelete = replyIsOwner || window.isTodoOwner;
                                    
                                    const replyCreatedDate = new Date(reply.created_at);
                                    const replyUpdatedDate = new Date(reply.updated_at);
                                    const replyWasEdited = replyUpdatedDate.getTime() > replyCreatedDate.getTime() + 1000;
                                    const replyDisplayDate = replyWasEdited ? replyUpdatedDate : replyCreatedDate;
                                    const replyDateText = replyWasEdited
                                        ? `Editado em ${replyDisplayDate.toLocaleString('pt-BR')}`
                                        : replyDisplayDate.toLocaleString('pt-BR');
                                    
                                    const replyRepliesHtml = renderReplies(reply.replies, depth + 1);

                                    return `
                                        <div class="mt-3 p-3 bg-white border border-gray-200 rounded-lg" style="margin-left: ${marginLeft}rem;" id="comment-${reply.id}">
                                            <div class="flex items-start justify-between mb-2">
                                                <div>
                                                    <div class="font-medium text-gray-800 text-sm">${escapeHtml(reply.user.name)}</div>
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
                    console.error('❌ Erro ao carregar comentários:', error);
                    console.error('❌ Detalhes:', error.response?.data || error.message);
                    console.error('❌ Status:', error.response?.status);
                    const listDiv = document.getElementById('commentsList');
                    if (listDiv) {
                        listDiv.innerHTML = '<p class="text-sm text-red-500">Erro ao carregar comentários. Verifique o console para mais detalhes.</p>';
                    }
                });
        };

        window.editComment = function(commentId) {
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
            window.updateEditCommentCounter();
            
            // Mostrar modal
            document.getElementById('editCommentModal').classList.remove('hidden');
            textarea.focus();
        };

        window.closeEditCommentModal = function() {
            document.getElementById('editCommentModal').classList.add('hidden');
            document.getElementById('editCommentId').value = '';
            document.getElementById('editCommentContent').value = '';
        };

        window.updateEditCommentCounter = function() {
            const textarea = document.getElementById('editCommentContent');
            const counter = document.getElementById('editCommentCounter');
            if (textarea && counter) {
                const length = textarea.value.length;
                counter.textContent = `${length} / 1000 caracteres`;
            }
        };

        window.saveCommentEdit = function(event) {
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
                    window.loadComments();
                })
                .catch(error => {
                    console.error('Erro ao editar comentário:', error);
                    showToast('Erro ao editar comentário', 'error');
                });
        };

        window.deleteComment = function(commentId) {
            if (!confirm('Deseja realmente excluir este comentário?')) {
                return;
            }

            window.axios.delete(`/api/todos/comments/${commentId}`)
                .then(response => {
                    showToast('Comentário excluído com sucesso!');
                    window.loadComments();
                })
                .catch(error => {
                    console.error('Erro ao excluir comentário:', error);
                    showToast('Erro ao excluir comentário', 'error');
                });
        };

        // Função para mostrar formulário de resposta
        window.showReplyForm = function(commentId) {
            const form = document.getElementById(`reply-form-${commentId}`);
            if (form) {
                form.classList.remove('hidden');
                const textarea = document.getElementById(`reply-content-${commentId}`);
                if (textarea) {
                    textarea.focus();
                }
            }
        };

        // Função para esconder formulário de resposta
        window.hideReplyForm = function(commentId) {
            const form = document.getElementById(`reply-form-${commentId}`);
            if (form) {
                form.classList.add('hidden');
                const textarea = document.getElementById(`reply-content-${commentId}`);
                if (textarea) {
                    textarea.value = '';
                }
            }
        };

        // Função para enviar resposta
        window.submitReply = function(commentId) {
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

            window.axios.post(`/api/todos/${window.todoId}/comments/${commentId}/reply`, { content })
                .then(response => {
                    showToast('Resposta adicionada com sucesso!');
                    hideReplyForm(commentId);
                    window.loadComments();
                })
                .catch(error => {
                    console.error('Erro ao adicionar resposta:', error);
                    const message = error.response?.data?.message || 'Erro ao adicionar resposta';
                    showToast(message, 'error');
                });
        };

        // Configurar contador do modal de edição
        document.addEventListener('DOMContentLoaded', function() {
            const editTextarea = document.getElementById('editCommentContent');
            if (editTextarea) {
                editTextarea.addEventListener('input', window.updateEditCommentCounter);
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
    </script>
</body>
</html>

