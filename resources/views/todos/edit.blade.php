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
                
                const formData = {
                    text: document.getElementById('todoText').value.trim(),
                    description: document.getElementById('todoDescription').value.trim() || null,
                    priority: document.querySelector('input[name="priority"]:checked')?.value || 'simple',
                    date: dateValue,
                };
                
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
                    console.log('Fazendo requisição PUT para:', `/api/todos/${todoId}`);
                    console.log('Headers do axios:', window.axios.defaults.headers);
                    console.log('FormData sendo enviado:', JSON.stringify(formData));
                    
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
                    
                    console.log('Resposta recebida:', response);
                    console.log('Status:', response.status);
                    console.log('Data:', response.data);
                    
                    showToast('Tarefa atualizada com sucesso!');
                    
                    // Fechar a aba após 1 segundo ou redirecionar
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
    </script>
</body>
</html>

