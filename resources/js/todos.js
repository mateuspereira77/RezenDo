// Estado da aplicação
let todos = [];
let filteredTodos = [];
let currentFilter = 'all'; // 'all', 'pending', 'completed'
let editingTodoId = null;

// Elementos DOM
const todoForm = document.getElementById('todoForm');
const todosList = document.getElementById('todosList');
const todoText = document.getElementById('todoText');
const todoDescription = document.getElementById('todoDescription');
const todoDate = document.getElementById('todoDate');

// Função para formatar data no padrão brasileiro (DD/MM/YYYY)
function formatDateBR(dateString) {
    if (!dateString || dateString === null || dateString === '') return '';
    
    // Se a data vier como objeto Date ou string ISO completa
    let dateValue = dateString;
    
    // Se a data vier com hora (YYYY-MM-DDTHH:mm:ss), pegar apenas a parte da data
    if (typeof dateString === 'string' && dateString.includes('T')) {
        dateValue = dateString.split('T')[0];
    }
    
    // Se já estiver no formato YYYY-MM-DD, converter diretamente
    if (typeof dateValue === 'string' && dateValue.match(/^\d{4}-\d{2}-\d{2}/)) {
        const [year, month, day] = dateValue.split('-');
        return `${day}/${month}/${year}`;
    }
    
    // Tentar criar uma data
    try {
        const date = new Date(dateString);
        if (!isNaN(date.getTime())) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }
    } catch (e) {
        console.error('Erro ao formatar data:', e, dateString);
    }
    
    // Se falhar, retornar string vazia
    return '';
}

// Função para validar se uma data é válida
function isValidDate(day, month, year) {
    // Verificar se os valores são números válidos
    const d = parseInt(day, 10);
    const m = parseInt(month, 10);
    const y = parseInt(year, 10);
    
    // Verificar limites básicos
    if (d < 1 || d > 31 || m < 1 || m > 12 || y < 1900 || y > 2100) {
        return false;
    }
    
    // Criar data e verificar se é válida
    const date = new Date(y, m - 1, d);
    
    // Verificar se a data criada corresponde aos valores fornecidos
    // (isso detecta datas inválidas como 31/02/2024)
    return date.getDate() === d && 
           date.getMonth() === (m - 1) && 
           date.getFullYear() === y;
}

// Função para converter data brasileira (DD/MM/YYYY ou DD/MM/YY) para formato ISO (YYYY-MM-DD)
function convertBRToISO(dateBR) {
    if (!dateBR || !dateBR.trim()) return null;
    
    // Remover espaços e caracteres especiais
    const cleaned = dateBR.trim().replace(/\s/g, '');
    
    // Se estiver vazio, retornar null
    if (!cleaned) return null;
    
    // Verificar se está no formato DD/MM/YY (ano com 2 dígitos) - verificar ANTES do formato de 4 dígitos
    let match = cleaned.match(/^(\d{2})\/(\d{2})\/(\d{2})$/);
    if (match) {
        const [, day, month, yearShort] = match;
        
        // Converter ano de 2 dígitos para 4 dígitos
        // Assumir que anos 00-30 são 2000-2030, e anos 31-99 são 1931-1999
        let year = parseInt(yearShort, 10);
        if (year <= 30) {
            year = 2000 + year;
        } else {
            year = 1900 + year;
        }
        const yearStr = String(year);
        
        console.log('Data com 2 dígitos detectada:', cleaned, '-> Ano convertido:', yearStr);
        
        // Validar se a data é válida
        if (isValidDate(day, month, yearStr)) {
            const result = `${yearStr}-${month}-${day}`;
            console.log('Data convertida com sucesso:', result);
            return result;
        } else {
            console.log('Data inválida após conversão');
            return 'INVALID';
        }
    }
    
    // Verificar se está no formato DD/MM/YYYY (ano com 4 dígitos)
    match = cleaned.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
    if (match) {
        const [, day, month, year] = match;
        
        // Validar se a data é válida
        if (isValidDate(day, month, year)) {
            return `${year}-${month}-${day}`;
        } else {
            return 'INVALID'; // Retornar string especial para indicar data inválida
        }
    }
    
    // Se já estiver no formato YYYY-MM-DD, validar e retornar
    const isoMatch = cleaned.match(/^(\d{4})-(\d{2})-(\d{2})$/);
    if (isoMatch) {
        const [, year, month, day] = isoMatch;
        if (isValidDate(day, month, year)) {
            return cleaned;
        } else {
            return 'INVALID';
        }
    }
    
    console.log('Formato de data não reconhecido:', cleaned);
    return null;
}

// Função para aplicar máscara de data brasileira (DD/MM/YYYY ou DD/MM/YY)
function applyDateMask(input) {
    input.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Remove tudo que não é dígito
        
        if (value.length > 0) {
            if (value.length <= 2) {
                value = value;
            } else if (value.length <= 4) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            } else if (value.length <= 6) {
                // Aceitar até 6 dígitos para DD/MM/YY
                value = value.substring(0, 2) + '/' + value.substring(2, 4) + '/' + value.substring(4, 6);
            } else {
                // Aceitar até 8 dígitos para DD/MM/YYYY
                value = value.substring(0, 2) + '/' + value.substring(2, 4) + '/' + value.substring(4, 8);
            }
        }
        
        e.target.value = value;
    });
    
    // Limitar a 10 caracteres (DD/MM/YYYY) ou 8 caracteres (DD/MM/YY)
    input.addEventListener('keypress', function(e) {
        const value = e.target.value.replace(/\D/g, '');
        // Permitir até 8 dígitos (DD/MM/YYYY) ou 6 dígitos (DD/MM/YY)
        if (value.length >= 8 && e.key !== 'Backspace' && e.key !== 'Delete') {
            e.preventDefault();
        }
    });
}

// Configurar contadores de caracteres
function setupCharacterCounters() {
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
            
            // Mudar cor quando próximo do limite
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
        todoText.addEventListener('paste', () => setTimeout(updateTextCounter, 10));
        updateTextCounter(); // Atualizar inicialmente
    }
    
    // Contador para descrição
    if (todoDescription && todoDescriptionCounter) {
        const updateDescriptionCounter = () => {
            const length = todoDescription.value.length;
            const maxLength = 500;
            todoDescriptionCounter.textContent = `${length} / ${maxLength} caracteres`;
            
            // Mudar cor quando próximo do limite
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
        todoDescription.addEventListener('paste', () => setTimeout(updateDescriptionCounter, 10));
        updateDescriptionCounter(); // Atualizar inicialmente
    }
    
    // Contadores para o modal de edição (serão configurados quando o modal abrir)
    const editTodoText = document.getElementById('editTodoText');
    const editTodoDescription = document.getElementById('editTodoDescription');
    const editTodoTextCounter = document.getElementById('editTodoTextCounter');
    const editTodoDescriptionCounter = document.getElementById('editTodoDescriptionCounter');
    
    // Contador para título no modal
    if (editTodoText && editTodoTextCounter) {
        const updateEditTextCounter = () => {
            const length = editTodoText.value.length;
            const maxLength = 200;
            editTodoTextCounter.textContent = `${length} / ${maxLength} caracteres`;
            
            if (length > maxLength * 0.9) {
                editTodoTextCounter.classList.remove('text-gray-500');
                editTodoTextCounter.classList.add('text-orange-500');
            } else if (length > maxLength * 0.95) {
                editTodoTextCounter.classList.remove('text-gray-500', 'text-orange-500');
                editTodoTextCounter.classList.add('text-red-500');
            } else {
                editTodoTextCounter.classList.remove('text-orange-500', 'text-red-500');
                editTodoTextCounter.classList.add('text-gray-500');
            }
        };
        
        editTodoText.addEventListener('input', updateEditTextCounter);
        editTodoText.addEventListener('keyup', updateEditTextCounter);
        editTodoText.addEventListener('paste', () => setTimeout(updateEditTextCounter, 10));
    }
    
    // Contador para descrição no modal
    if (editTodoDescription && editTodoDescriptionCounter) {
        const updateEditDescriptionCounter = () => {
            const length = editTodoDescription.value.length;
            const maxLength = 500;
            editTodoDescriptionCounter.textContent = `${length} / ${maxLength} caracteres`;
            
            if (length > maxLength * 0.9) {
                editTodoDescriptionCounter.classList.remove('text-gray-500');
                editTodoDescriptionCounter.classList.add('text-orange-500');
            } else if (length > maxLength * 0.95) {
                editTodoDescriptionCounter.classList.remove('text-gray-500', 'text-orange-500');
                editTodoDescriptionCounter.classList.add('text-red-500');
            } else {
                editTodoDescriptionCounter.classList.remove('text-orange-500', 'text-red-500');
                editTodoDescriptionCounter.classList.add('text-gray-500');
            }
        };
        
        editTodoDescription.addEventListener('input', updateEditDescriptionCounter);
        editTodoDescription.addEventListener('keyup', updateEditDescriptionCounter);
        editTodoDescription.addEventListener('paste', () => setTimeout(updateEditDescriptionCounter, 10));
    }
}

// Inicialização
document.addEventListener('DOMContentLoaded', () => {
    setupEventListeners();
    
    // Configurar contadores de caracteres
    setupCharacterCounters();
    
    // Aplicar máscara de data brasileira em todos os campos de data
    const dateInputs = document.querySelectorAll('input[type="text"][data-date-mask]');
    dateInputs.forEach(input => applyDateMask(input));
    
    // Pré-preencher data se houver parâmetro na URL
    const urlParams = new URLSearchParams(window.location.search);
    const dateParam = urlParams.get('date');
    if (dateParam && todoDate) {
        todoDate.value = dateParam;
    }
    
    // Só carregar tarefas se estiver na página de listagem
    if (todosList && window.location.pathname === '/minhas-tarefas') {
        // Inicializar filtros
        updateFilterButtons();
        loadTodos();
    }
});

// Configurar event listeners
function setupEventListeners() {
    if (todoForm) {
        todoForm.addEventListener('submit', handleSubmit);
    }
    
    // Formulário de edição no modal
    const editTodoForm = document.getElementById('editTodoForm');
    if (editTodoForm) {
        editTodoForm.addEventListener('submit', (e) => {
            e.preventDefault();
            saveEditFromModal();
        });
    }
    
    // Fechar modal com ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const editModal = document.getElementById('editModal');
            if (editModal && !editModal.classList.contains('hidden')) {
                closeEditModal();
            }
            const deleteModal = document.getElementById('deleteConfirmModal');
            if (deleteModal && !deleteModal.classList.contains('hidden')) {
                closeDeleteModal();
            }
        }
    });
}

// Atualizar estatísticas
function updateStats() {
    const totalCount = document.getElementById('totalCount');
    if (totalCount) {
        const total = todos.length;
        const completed = todos.filter(t => t.completed).length;
        const pending = total - completed;
        
        let statsText = '';
        switch (currentFilter) {
            case 'pending':
                statsText = `${filteredTodos.length} tarefa${filteredTodos.length !== 1 ? 's' : ''} pendente${filteredTodos.length !== 1 ? 's' : ''}`;
                break;
            case 'completed':
                statsText = `${filteredTodos.length} tarefa${filteredTodos.length !== 1 ? 's' : ''} concluída${filteredTodos.length !== 1 ? 's' : ''}`;
                break;
            default:
                statsText = `${total} tarefa${total !== 1 ? 's' : ''} (${pending} pendente${pending !== 1 ? 's' : ''}, ${completed} concluída${completed !== 1 ? 's' : ''})`;
        }
        
        totalCount.textContent = statsText;
    }
}

// Carregar tarefas do servidor
async function loadTodos() {
    try {
        const response = await window.axios.get('/api/todos');
        todos = response.data;
        // Debug: verificar datas
        console.log('Tarefas carregadas:', todos.map(t => ({ id: t.id, text: t.text, date: t.date })));
        applyFilter();
    } catch (error) {
        console.error('Erro ao carregar tarefas:', error);
        todosList.innerHTML = '<p class="text-red-500 text-center py-8">Erro ao carregar tarefas. Recarregue a página.</p>';
    }
}

// Aplicar filtro
function applyFilter() {
    switch (currentFilter) {
        case 'pending':
            filteredTodos = todos.filter(t => !t.completed);
            break;
        case 'completed':
            filteredTodos = todos.filter(t => t.completed);
            break;
        case 'all':
        default:
            filteredTodos = todos;
            break;
    }
    
    // Atualizar botões de filtro
    updateFilterButtons();
    
    // Renderizar tarefas filtradas
    renderTodos();
}

// Atualizar botões de filtro
function updateFilterButtons() {
    const filterAll = document.getElementById('filterAll');
    const filterPending = document.getElementById('filterPending');
    const filterCompleted = document.getElementById('filterCompleted');
    const deleteAllCompletedBtn = document.getElementById('deleteAllCompletedBtn');
    
    if (filterAll) filterAll.classList.remove('active');
    if (filterPending) filterPending.classList.remove('active');
    if (filterCompleted) filterCompleted.classList.remove('active');
    
    // Mostrar/ocultar botão de apagar todas as concluídas
    if (deleteAllCompletedBtn) {
        const shouldShow = currentFilter === 'completed' && filteredTodos.length > 0;
        if (shouldShow) {
            deleteAllCompletedBtn.style.display = 'block';
            deleteAllCompletedBtn.classList.remove('hidden');
        } else {
            deleteAllCompletedBtn.style.display = 'none';
            deleteAllCompletedBtn.classList.add('hidden');
        }
    }
    
    switch (currentFilter) {
        case 'all':
            if (filterAll) filterAll.classList.add('active');
            break;
        case 'pending':
            if (filterPending) filterPending.classList.add('active');
            break;
        case 'completed':
            if (filterCompleted) filterCompleted.classList.add('active');
            break;
    }
}

// Filtrar tarefas
function filterTodos(filter) {
    currentFilter = filter;
    applyFilter();
}

// Renderizar lista de tarefas
function renderTodos() {
    if (!todosList) return;
    
    // Atualizar estatísticas
    updateStats();
    
    if (filteredTodos.length === 0) {
        let message = '';
        switch (currentFilter) {
            case 'pending':
                message = 'Nenhuma tarefa pendente. <a href="/" class="text-blue-600 hover:underline">Crie uma nova tarefa aqui!</a>';
                break;
            case 'completed':
                message = 'Nenhuma tarefa concluída ainda.';
                break;
            default:
                message = 'Nenhuma tarefa cadastrada. <a href="/" class="text-blue-600 hover:underline">Crie uma nova tarefa aqui!</a>';
        }
        todosList.innerHTML = `<p class="text-gray-500 text-center py-8">${message}</p>`;
        return;
    }
    
    todosList.innerHTML = filteredTodos.map((todo, index) => {
        const completedClass = todo.completed ? 'line-through text-gray-500' : '';
        const priorityClass = todo.completed ? 'post-it-completed' : `post-it-${todo.priority || 'simple'}`;
        const priorityLabels = {
            simple: 'Simples',
            medium: 'Média',
            urgent: 'Urgente'
        };
        
        // Rotação aleatória para cada post-it (entre -2 e 2 graus)
        const rotation = (index % 5) === 0 ? '-1deg' : 
                        (index % 5) === 1 ? '1deg' : 
                        (index % 5) === 2 ? '-0.5deg' : 
                        (index % 5) === 3 ? '0.8deg' : '0deg';
        
        return `
            <div class="post-it-task ${priorityClass}" style="transform: rotate(${rotation});" data-todo-id="${todo.id}">
                ${todo.completed ? `
                    <div class="completed-stamp">
                        <div class="stamp-text">CONCLUÍDA</div>
                    </div>
                ` : ''}
                <div class="post-it-content">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-2 flex-1 min-w-0">
                            <input 
                                type="checkbox" 
                                ${todo.completed ? 'checked' : ''}
                                onchange="event.stopPropagation(); toggleTodo(${todo.id})"
                                class="post-it-checkbox flex-shrink-0"
                            >
                            <div class="post-it-title-wrapper flex-1 min-w-0">
                                <h3 
                                    class="post-it-title ${completedClass} cursor-pointer hover:text-[#fb9e0b] transition-colors" 
                                    title="${escapeHtml(todo.text)}"
                                    onclick="event.stopPropagation(); viewTodo(${todo.id})"
                                >
                                    ${escapeHtml(todo.text)}
                                </h3>
                            </div>
                        </div>
                        <span class="post-it-priority-badge post-it-priority-${todo.priority || 'simple'}">
                            ${priorityLabels[todo.priority || 'simple']}
                        </span>
                    </div>
                    ${todo.description ? `
                        <div class="post-it-description-wrapper">
                            <p class="post-it-description ${completedClass}" id="desc-${todo.id}" title="${escapeHtml(todo.description)}">
                                ${escapeHtml(todo.description)}
                            </p>
                        </div>
                    ` : ''}
                    ${(() => {
                        if (!todo.date || todo.date === null || todo.date === '') return '';
                        const formattedDate = formatDateBR(todo.date);
                        if (!formattedDate) return '';
                        return `
                            <div class="post-it-date mb-2">
                                <span class="text-xs text-gray-600 font-medium">
                                    📅 ${formattedDate}
                                </span>
                            </div>
                        `;
                    })()}
                    <div class="post-it-actions">
                        <button 
                            onclick="event.stopPropagation(); startEdit(${todo.id})"
                            class="post-it-btn post-it-btn-edit"
                            title="Editar tarefa"
                        >
                            ✏️ Editar
                        </button>
                        <button 
                            onclick="event.stopPropagation(); deleteTodo(${todo.id})"
                            class="post-it-btn post-it-btn-delete"
                            title="Deletar tarefa"
                        >
                            🗑️ Deletar
                        </button>
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    // Adicionar event listeners para clique no card inteiro
    if (todosList) {
        todosList.querySelectorAll('.post-it-task').forEach(card => {
            const todoId = card.dataset.todoId;
            if (todoId) {
                card.addEventListener('click', function(e) {
                    // Se não clicou em um botão, checkbox ou input, abrir visualização
                    if (!e.target.closest('button') && !e.target.closest('input[type="checkbox"]') && !e.target.closest('input')) {
                        viewTodo(todoId);
                    }
                });
            }
        });
    }
}

// Visualizar tarefa individualmente
function viewTodo(todoId) {
    console.log('viewTodo chamado com ID:', todoId);
    window.location.href = `/todos/${todoId}`;
}

// Tornar a função global
window.viewTodo = viewTodo;

// Escapar HTML para prevenir XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}


// Manipular submit do formulário
async function handleSubmit(e) {
    e.preventDefault();
    
    if (editingTodoId) {
        await updateTodo(editingTodoId);
    } else {
        await createTodo();
    }
}

// Criar nova tarefa
async function createTodo() {
    const todoDate = document.getElementById('todoDate');
    let dateValue = null;
    
    if (todoDate && todoDate.value.trim()) {
        const dateInput = todoDate.value.trim();
        console.log('Criando tarefa - Data digitada:', dateInput);
        dateValue = convertBRToISO(dateInput);
        console.log('Criando tarefa - Data convertida:', dateValue);
        
        // Validar se a data é inválida
        if (dateValue === 'INVALID') {
            showToast('Por favor, insira uma data válida no formato DD/MM/AAAA ou DD/MM/AA.', 'error');
            todoDate.focus();
            return;
        }
        
        if (dateValue === null) {
            showToast('Formato de data inválido. Use DD/MM/AAAA ou DD/MM/AA.', 'error');
            todoDate.focus();
            return;
        }
    }
    
    // Verificar se a descrição excede 500 caracteres antes de enviar
    const descriptionValue = todoDescription.value.trim();
    if (descriptionValue && descriptionValue.length > 500) {
        showToast('A descrição não pode ter mais de 500 caracteres. Atualmente tem ' + descriptionValue.length + ' caracteres.', 'error');
        todoDescription.focus();
        return;
    }
    
    const formData = {
        text: todoText.value.trim(),
        description: descriptionValue || null,
        priority: document.querySelector('input[name="priority"]:checked').value,
        date: dateValue,
    };
    
    console.log('Dados do formulário antes de enviar:', formData);
    
    if (!formData.text) {
        showToast('Por favor, preencha o título da tarefa.', 'warning');
        todoText.focus();
        return;
    }
    
    try {
        console.log('Enviando dados para o servidor:', formData);
        const response = await window.axios.post('/api/todos', formData);
        console.log('Resposta do servidor:', response.data);
        
        // Limpar formulário
        todoForm.reset();
        editingTodoId = null;
        
        // Resetar contadores
        if (todoText && todoTextCounter) {
            todoTextCounter.textContent = '0 / 200 caracteres';
        }
        if (todoDescription && todoDescriptionCounter) {
            todoDescriptionCounter.textContent = '0 / 500 caracteres';
        }
        
        // Se estiver na página principal, mostrar mensagem de sucesso
        if (window.location.pathname === '/') {
            showToast('Tarefa criada com sucesso! Clique em "Ver Minhas Tarefas" para visualizá-la.');
        } else {
            // Se estiver na página de listagem, atualizar a lista
            todos.unshift(response.data);
            applyFilter();
            showToast('Tarefa criada com sucesso!');
        }
    } catch (error) {
        console.error('Erro ao criar tarefa:', error);
        console.error('Detalhes do erro:', error.response);
        
        // Tratar erros de validação do Laravel
        if (error.response && error.response.status === 422) {
            const errors = error.response.data.errors;
            let errorMessages = [];
            
            // Coletar todas as mensagens de erro
            if (errors) {
                Object.keys(errors).forEach(field => {
                    if (Array.isArray(errors[field])) {
                        errorMessages.push(...errors[field]);
                    } else {
                        errorMessages.push(errors[field]);
                    }
                });
            }
            
            // Mostrar mensagens de erro específicas
            if (errorMessages.length > 0) {
                showToast(errorMessages.join(' '), 'error');
            } else {
                showToast('Erro de validação. Verifique os campos preenchidos.', 'error');
            }
        } else if (error.response && error.response.data && error.response.data.message) {
            showToast('Erro: ' + error.response.data.message, 'error');
        } else {
            showToast('Erro ao criar tarefa. Tente novamente.', 'error');
        }
    }
}

// Atualizar tarefa
async function updateTodo(id) {
    const todoDate = document.getElementById('todoDate');
    let dateValue = null;
    
    if (todoDate && todoDate.value.trim()) {
        const dateInput = todoDate.value.trim();
        console.log('Atualizando tarefa - Data digitada:', dateInput);
        dateValue = convertBRToISO(dateInput);
        console.log('Atualizando tarefa - Data convertida:', dateValue);
        
        // Validar se a data é inválida
        if (dateValue === 'INVALID') {
            showToast('Por favor, insira uma data válida no formato DD/MM/AAAA ou DD/MM/AA.', 'error');
            todoDate.focus();
            return;
        }
        
        if (dateValue === null) {
            showToast('Formato de data inválido. Use DD/MM/AAAA ou DD/MM/AA.', 'error');
            todoDate.focus();
            return;
        }
    }
    
    const formData = {
        text: todoText.value.trim(),
        description: todoDescription.value.trim() || null,
        priority: document.querySelector('input[name="priority"]:checked').value,
        date: dateValue,
    };
    
    if (!formData.text) {
        showToast('Por favor, preencha o título da tarefa.', 'warning');
        todoText.focus();
        return;
    }
    
    try {
        const response = await window.axios.put(`/api/todos/${id}`, formData);
        
        // Limpar formulário
        todoForm.reset();
        editingTodoId = null;
        
        // Mudar texto do botão
        const submitBtn = todoForm.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.textContent = 'Adicionar Tarefa';
        }
        
        // Se estiver na página de listagem, atualizar a lista
        if (window.location.pathname === '/minhas-tarefas') {
            const index = todos.findIndex(t => t.id === id);
            if (index !== -1) {
                todos[index] = response.data;
            }
            applyFilter();
            showToast('Tarefa atualizada com sucesso!');
        } else {
            // Se estiver na página principal, redirecionar para a página de listagem
            showToast('Tarefa atualizada com sucesso! Redirecionando...');
            setTimeout(() => {
                window.location.href = '/minhas-tarefas';
            }, 1500);
        }
    } catch (error) {
        console.error('Erro ao atualizar tarefa:', error);
        if (error.response && error.response.data && error.response.data.message) {
            showToast('Erro: ' + error.response.data.message, 'error');
        } else if (error.response && error.response.data && error.response.data.errors) {
            const errors = Object.values(error.response.data.errors).flat();
            showToast('Erro: ' + errors.join(', '), 'error');
        } else {
            showToast('Erro ao atualizar tarefa. Tente novamente.', 'error');
        }
    }
}

// Iniciar edição
function startEdit(id) {
    console.log('startEdit chamado com ID:', id);
    
    if (!id) {
        console.error('Erro: ID da tarefa não fornecido');
        return;
    }
    
    // Redirecionar para página de edição na mesma aba
    const editUrl = `/todos/${id}/edit`;
    console.log('Redirecionando para:', editUrl);
    window.location.href = editUrl;
}

// Abrir modal de edição
function openEditModal(id) {
    const todo = todos.find(t => t.id === id);
    if (!todo) return;
    
    const modal = document.getElementById('editModal');
    const editText = document.getElementById('editTodoText');
    const editDescription = document.getElementById('editTodoDescription');
    const editForm = document.getElementById('editTodoForm');
    
    if (!modal || !editText || !editDescription || !editForm) return;
    
    // Preencher campos
    editText.value = todo.text;
    editDescription.value = todo.description || '';
    
    // Configurar contadores para o modal (se ainda não foram configurados)
    const editTodoTextCounter = document.getElementById('editTodoTextCounter');
    const editTodoDescriptionCounter = document.getElementById('editTodoDescriptionCounter');
    
    if (editText && editTodoTextCounter) {
        const updateEditTextCounter = () => {
            const length = editText.value.length;
            const maxLength = 200;
            editTodoTextCounter.textContent = `${length} / ${maxLength} caracteres`;
            
            if (length > maxLength * 0.9) {
                editTodoTextCounter.classList.remove('text-gray-500');
                editTodoTextCounter.classList.add('text-orange-500');
            } else if (length > maxLength * 0.95) {
                editTodoTextCounter.classList.remove('text-gray-500', 'text-orange-500');
                editTodoTextCounter.classList.add('text-red-500');
            } else {
                editTodoTextCounter.classList.remove('text-orange-500', 'text-red-500');
                editTodoTextCounter.classList.add('text-gray-500');
            }
        };
        
        // Remover listeners antigos se existirem
        editText.removeEventListener('input', updateEditTextCounter);
        editText.removeEventListener('keyup', updateEditTextCounter);
        
        // Adicionar novos listeners
        editText.addEventListener('input', updateEditTextCounter);
        editText.addEventListener('keyup', updateEditTextCounter);
        editText.addEventListener('paste', () => setTimeout(updateEditTextCounter, 10));
        updateEditTextCounter(); // Atualizar inicialmente
    }
    
    if (editDescription && editTodoDescriptionCounter) {
        const updateEditDescriptionCounter = () => {
            const length = editDescription.value.length;
            const maxLength = 500;
            editTodoDescriptionCounter.textContent = `${length} / ${maxLength} caracteres`;
            
            if (length > maxLength * 0.9) {
                editTodoDescriptionCounter.classList.remove('text-gray-500');
                editTodoDescriptionCounter.classList.add('text-orange-500');
            } else if (length > maxLength * 0.95) {
                editTodoDescriptionCounter.classList.remove('text-gray-500', 'text-orange-500');
                editTodoDescriptionCounter.classList.add('text-red-500');
            } else {
                editTodoDescriptionCounter.classList.remove('text-orange-500', 'text-red-500');
                editTodoDescriptionCounter.classList.add('text-gray-500');
            }
        };
        
        // Remover listeners antigos se existirem
        editDescription.removeEventListener('input', updateEditDescriptionCounter);
        editDescription.removeEventListener('keyup', updateEditDescriptionCounter);
        
        // Adicionar novos listeners
        editDescription.addEventListener('input', updateEditDescriptionCounter);
        editDescription.addEventListener('keyup', updateEditDescriptionCounter);
        editDescription.addEventListener('paste', () => setTimeout(updateEditDescriptionCounter, 10));
        updateEditDescriptionCounter(); // Atualizar inicialmente
    }
    
    // Preencher data no formato brasileiro
    const editDate = document.getElementById('editTodoDate');
    if (editDate) {
        if (todo.date && todo.date !== null) {
            editDate.value = formatDateBR(todo.date);
        } else {
            editDate.value = '';
        }
    }
    
    // Selecionar prioridade - desmarcar todos primeiro
    const allPriorityRadios = document.querySelectorAll('input[name="editPriority"]');
    allPriorityRadios.forEach(radio => {
        radio.checked = false;
    });
    
    // Marcar o radio correto baseado na prioridade da tarefa
    const priorityValue = todo.priority || 'simple';
    const priorityRadio = document.querySelector(`input[name="editPriority"][value="${priorityValue}"]`);
    if (priorityRadio) {
        priorityRadio.checked = true;
        console.log('Prioridade selecionada:', priorityValue);
    } else {
        console.warn('Radio de prioridade não encontrado para:', priorityValue);
        // Fallback: marcar 'simple' se não encontrar
        const simpleRadio = document.querySelector('input[name="editPriority"][value="simple"]');
        if (simpleRadio) {
            simpleRadio.checked = true;
        }
    }
    
    // Armazenar ID da tarefa sendo editada
    editForm.dataset.todoId = id;
    
    // Mostrar modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Focar no campo de texto
    setTimeout(() => {
        editText.focus();
        editText.select();
    }, 100);
}

// Fechar modal de edição
function closeEditModal() {
    const modal = document.getElementById('editModal');
    const editForm = document.getElementById('editTodoForm');
    
    if (!modal) return;
    
    modal.classList.add('hidden');
    document.body.style.overflow = '';
    
    if (editForm) {
        editForm.dataset.todoId = '';
        editForm.reset();
    }
}

// Salvar edição do modal
async function saveEditFromModal() {
    const editForm = document.getElementById('editTodoForm');
    const editText = document.getElementById('editTodoText');
    const editDescription = document.getElementById('editTodoDescription');
    const editDate = document.getElementById('editTodoDate');
    
    if (!editForm || !editText) return;
    
    const todoId = editForm.dataset.todoId;
    if (!todoId) return;
    
    let dateValue = null;
    
    if (editDate && editDate.value.trim()) {
        const dateInput = editDate.value.trim();
        console.log('Salvando edição do modal - Data digitada:', dateInput);
        dateValue = convertBRToISO(dateInput);
        console.log('Salvando edição do modal - Data convertida:', dateValue);
        console.log('Tipo da data convertida:', typeof dateValue);
        
        // Validar se a data é inválida
        if (dateValue === 'INVALID') {
            showToast('Por favor, insira uma data válida no formato DD/MM/AAAA ou DD/MM/AA.', 'error');
            editDate.focus();
            return;
        }
        
        if (dateValue === null) {
            console.error('Erro: Data convertida é null para input:', dateInput);
            showToast('Formato de data inválido. Use DD/MM/AAAA ou DD/MM/AA.', 'error');
            editDate.focus();
            return;
        }
    } else {
        console.log('Salvando edição do modal - Campo de data está vazio, enviando null');
        dateValue = null;
    }
    
    // Obter prioridade selecionada ou usar a prioridade atual da tarefa
    const checkedPriority = document.querySelector('input[name="editPriority"]:checked');
    let priorityValue = checkedPriority ? checkedPriority.value : null;
    
    // Se nenhum radio estiver marcado, buscar a prioridade da tarefa atual
    if (!priorityValue) {
        const todo = todos.find(t => t.id == todoId);
        if (todo && todo.priority) {
            priorityValue = todo.priority;
            console.warn('Nenhuma prioridade selecionada, usando prioridade atual da tarefa:', priorityValue);
        } else {
            priorityValue = 'simple';
            console.warn('Nenhuma prioridade encontrada, usando "simple" como padrão');
        }
    }
    
    const formData = {
        text: editText.value.trim(),
        description: editDescription ? editDescription.value.trim() || null : null,
        priority: priorityValue,
        date: dateValue, // Pode ser null, string ISO ou 'INVALID'
    };
    
    console.log('FormData completo antes de enviar:', JSON.stringify(formData, null, 2));
    console.log('Prioridade selecionada:', priorityValue);
    
    if (!formData.text) {
        showToast('Por favor, preencha o título da tarefa.', 'warning');
        editText.focus();
        return;
    }
    
    try {
        console.log('Enviando dados para atualizar:', formData);
        const response = await window.axios.put(`/api/todos/${todoId}`, formData);
        console.log('Resposta do servidor após atualização:', response.data);
        console.log('Data na resposta:', response.data.date);
        
        // Atualizar tarefa na lista
        const index = todos.findIndex(t => t.id === parseInt(todoId));
        if (index !== -1) {
            // Garantir que a data seja preservada mesmo se vier null
            const updatedTodo = { ...response.data };
            console.log('Tarefa atualizada no array local (antes):', todos[index]);
            console.log('Tarefa recebida do servidor:', updatedTodo);
            console.log('Data recebida do servidor:', updatedTodo.date, 'tipo:', typeof updatedTodo.date);
            
            todos[index] = updatedTodo;
            console.log('Tarefa atualizada no array local (depois):', todos[index]);
            console.log('Data formatada para exibição:', formatDateBR(todos[index].date));
        }
        
        // Forçar re-renderização
        applyFilter();
        closeEditModal();
        showToast('Tarefa atualizada com sucesso!');
    } catch (error) {
        console.error('Erro ao atualizar tarefa:', error);
        if (error.response && error.response.data && error.response.data.message) {
            showToast('Erro: ' + error.response.data.message, 'error');
        } else if (error.response && error.response.data && error.response.data.errors) {
            const errors = Object.values(error.response.data.errors).flat();
            showToast('Erro: ' + errors.join(', '), 'error');
        } else {
            showToast('Erro ao atualizar tarefa. Tente novamente.', 'error');
        }
    }
}

// Alternar status de conclusão
async function toggleTodo(id) {
    try {
        const response = await window.axios.patch(`/api/todos/${id}/toggle`);
        const index = todos.findIndex(t => t.id === id);
        if (index !== -1) {
            todos[index] = response.data;
        }
        applyFilter();
    } catch (error) {
        console.error('Erro ao alternar tarefa:', error);
        showToast('Erro ao atualizar tarefa. Tente novamente.', 'error');
    }
}

// Deletar tarefa
// Variável para armazenar o ID da tarefa a ser deletada
let todoToDeleteId = null;
let todosToDeleteCount = null;

// Mostrar modal de confirmação de exclusão
function showDeleteModal(id = null, count = null) {
    const modal = document.getElementById('deleteConfirmModal');
    const message = document.getElementById('deleteConfirmMessage');
    
    if (!modal) {
        console.error('Modal deleteConfirmModal não encontrado');
        return;
    }
    
    if (!message) {
        console.error('Elemento deleteConfirmMessage não encontrado');
        return;
    }
    
    todoToDeleteId = id;
    todosToDeleteCount = count;
    
    if (count !== null) {
        message.textContent = `Tem certeza que deseja excluir todas as ${count} tarefa${count !== 1 ? 's' : ''} concluída${count !== 1 ? 's' : ''}?`;
    } else {
        message.textContent = 'Tem certeza que deseja excluir esta tarefa?';
    }
    
    // Forçar remoção da classe hidden e garantir que o modal seja visível
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

// Fechar modal de confirmação
function closeDeleteModal() {
    const modal = document.getElementById('deleteConfirmModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    todoToDeleteId = null;
    todosToDeleteCount = null;
}

// Confirmar exclusão
async function confirmDelete() {
    if (todosToDeleteCount !== null) {
        // Excluir todas as tarefas concluídas
        await deleteAllCompletedConfirmed();
    } else if (todoToDeleteId !== null) {
        // Excluir uma tarefa
        await deleteTodoConfirmed(todoToDeleteId);
    }
    closeDeleteModal();
}

// Excluir tarefa confirmada
async function deleteTodoConfirmed(id) {
    try {
        await window.axios.delete(`/api/todos/${id}`);
        todos = todos.filter(t => t.id !== id);
        applyFilter();
        showToast('Tarefa excluída com sucesso!', 'success');
    } catch (error) {
        console.error('Erro ao deletar tarefa:', error);
        showToast('Erro ao deletar tarefa. Tente novamente.', 'error');
    }
}

// Excluir todas as tarefas concluídas confirmadas
async function deleteAllCompletedConfirmed() {
    const completedTodos = todos.filter(t => t.completed);
    const count = completedTodos.length;
    
    if (count === 0) {
        showToast('Não há tarefas concluídas para deletar.', 'warning');
        return;
    }
    
    try {
        const deletePromises = completedTodos.map(todo => 
            window.axios.delete(`/api/todos/${todo.id}`)
        );
        
        await Promise.all(deletePromises);
        
        todos = todos.filter(t => !t.completed);
        applyFilter();
        
        showToast(`${count} tarefa${count !== 1 ? 's' : ''} concluída${count !== 1 ? 's' : ''} deletada${count !== 1 ? 's' : ''} com sucesso!`);
    } catch (error) {
        console.error('Erro ao deletar tarefas concluídas:', error);
        showToast('Erro ao deletar tarefas concluídas. Tente novamente.', 'error');
    }
}

async function deleteTodo(id) {
    showDeleteModal(id);
}

// Deletar todas as tarefas concluídas
async function deleteAllCompleted() {
    const completedTodos = todos.filter(t => t.completed);
    const count = completedTodos.length;
    
    if (count === 0) {
        showToast('Não há tarefas concluídas para deletar.', 'warning');
        return;
    }
    
    showDeleteModal(null, count);
}

// Mostrar toast de notificação
function showToast(message, type = 'success') {
    const toast = document.getElementById('toastNotification');
    const toastMessage = document.getElementById('toastMessage');
    
    if (!toast || !toastMessage) return;
    
    // Definir mensagem
    toastMessage.textContent = message;
    
    // Remover classes de tipo anteriores
    toast.classList.remove('toast-error', 'toast-warning');
    
    // Adicionar classe de tipo se não for sucesso
    if (type === 'error') {
        toast.classList.add('toast-error');
    } else if (type === 'warning') {
        toast.classList.add('toast-warning');
    }
    
    // Posicionar toast abaixo do botão "Adicionar Tarefa" se estiver na página principal
    if (window.location.pathname === '/') {
        const submitButton = document.querySelector('#todoForm button[type="submit"]');
        if (submitButton) {
            const buttonRect = submitButton.getBoundingClientRect();
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
            
            // Posicionar logo abaixo do botão
            toast.style.position = 'absolute';
            toast.style.top = `${buttonRect.bottom + scrollTop + 16}px`; // 16px de espaçamento
            toast.style.left = `${buttonRect.left + scrollLeft}px`;
            toast.style.right = 'auto';
            toast.style.transform = 'translateY(-20px)';
            toast.style.width = `${buttonRect.width}px`;
            toast.style.maxWidth = 'none';
            toast.style.minWidth = 'auto';
        }
    } else {
        // Para outras páginas, usar posicionamento fixo no topo centralizado
        toast.style.position = 'fixed';
        toast.style.top = '1.5rem';
        toast.style.left = '50%';
        toast.style.right = 'auto';
        toast.style.transform = 'translateX(-50%) translateY(-100px)';
        toast.style.width = 'auto';
        toast.style.maxWidth = '500px';
        toast.style.minWidth = '320px';
    }
    
    // Remover classe hidden e mostrar toast
    toast.classList.remove('hidden');
    
    // Forçar reflow para garantir que a animação funcione
    void toast.offsetWidth;
    
    // Animação de entrada
    setTimeout(() => {
        toast.classList.add('toast-show');
        if (window.location.pathname === '/') {
            toast.style.transform = 'translateY(0)';
        } else {
            toast.style.transform = 'translateX(-50%) translateY(0)';
        }
    }, 10);
    
    // Remover toast após 4 segundos
    setTimeout(() => {
        hideToast();
    }, 4000);
}

// Esconder toast
function hideToast() {
    const toast = document.getElementById('toastNotification');
    if (!toast) return;
    
    // Remover classe de show
    toast.classList.remove('toast-show');
    
    // Aguardar animação de saída e esconder
    setTimeout(() => {
        toast.classList.add('hidden');
    }, 400);
}

// Tornar funções globais para uso em onclick
window.toggleTodo = toggleTodo;
window.deleteTodo = deleteTodo;
window.startEdit = startEdit;
window.openEditModal = openEditModal;
window.hideToast = hideToast;
window.closeEditModal = closeEditModal;
window.saveEditFromModal = saveEditFromModal;
window.convertBRToISO = convertBRToISO;
window.formatDateBR = formatDateBR;
window.filterTodos = filterTodos;
window.deleteAllCompleted = deleteAllCompleted;
window.showDeleteModal = showDeleteModal;
window.closeDeleteModal = closeDeleteModal;
window.confirmDelete = confirmDelete;

