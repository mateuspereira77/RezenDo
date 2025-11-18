// Estado do calendário
let currentDate = new Date();
let currentView = 'month'; // 'month' ou 'week'
let todos = [];
let todosByDate = {};

// Meses em português
const months = [
    'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
    'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
];

// Dias da semana em português
const weekDays = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

// Formatar data para ISO (YYYY-MM-DD) - definida antes de getHolidays
function formatDateISO(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Obter feriados do Brasil e Rio de Janeiro para um ano específico
function getHolidays(year) {
    const holidays = {};
    
    // Feriados nacionais fixos
    holidays[`${year}-01-01`] = 'Ano Novo';
    holidays[`${year}-04-21`] = 'Tiradentes';
    holidays[`${year}-05-01`] = 'Dia do Trabalhador';
    holidays[`${year}-09-07`] = 'Independência do Brasil';
    holidays[`${year}-10-12`] = 'Nossa Senhora Aparecida';
    holidays[`${year}-11-02`] = 'Finados';
    holidays[`${year}-11-15`] = 'Proclamação da República';
    holidays[`${year}-12-25`] = 'Natal';
    
    // Feriados móveis (calculados)
    const easter = calculateEaster(year);
    const goodFriday = new Date(easter);
    goodFriday.setDate(easter.getDate() - 2);
    const carnivalMonday = new Date(easter);
    carnivalMonday.setDate(easter.getDate() - 47);
    const carnivalTuesday = new Date(easter);
    carnivalTuesday.setDate(easter.getDate() - 46);
    const corpusChristi = new Date(easter);
    corpusChristi.setDate(easter.getDate() + 60);
    
    holidays[formatDateISO(goodFriday)] = 'Sexta-feira Santa';
    holidays[formatDateISO(carnivalMonday)] = 'Carnaval (Segunda-feira)';
    holidays[formatDateISO(carnivalTuesday)] = 'Carnaval (Terça-feira)';
    holidays[formatDateISO(corpusChristi)] = 'Corpus Christi';
    
    // Feriados específicos do Rio de Janeiro
    holidays[`${year}-01-20`] = 'São Sebastião (Padroeiro do Rio)';
    holidays[`${year}-04-23`] = 'São Jorge';
    holidays[`${year}-11-20`] = 'Dia da Consciência Negra';
    holidays[`${year}-12-08`] = 'Nossa Senhora da Conceição';
    
    return holidays;
}

// Calcular a data da Páscoa (algoritmo de Meeus/Jones/Butcher)
function calculateEaster(year) {
    const a = year % 19;
    const b = Math.floor(year / 100);
    const c = year % 100;
    const d = Math.floor(b / 4);
    const e = b % 4;
    const f = Math.floor((b + 8) / 25);
    const g = Math.floor((b - f + 1) / 3);
    const h = (19 * a + b - d - g + 15) % 30;
    const i = Math.floor(c / 4);
    const k = c % 4;
    const l = (32 + 2 * e + 2 * i - h - k) % 7;
    const m = Math.floor((a + 11 * h + 22 * l) / 451);
    const month = Math.floor((h + l - 7 * m + 114) / 31);
    const day = ((h + l - 7 * m + 114) % 31) + 1;
    
    return new Date(year, month - 1, day);
}

// Cache de feriados por ano
let holidaysCache = {};

// Função auxiliar para atualizar display do mês/ano (definida antes das funções globais)
function updateMonthYearDisplay() {
    const monthYearElement = document.getElementById('currentMonthYear');
    if (monthYearElement) {
        monthYearElement.textContent = `${months[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
    }
}

// Definir funções globais imediatamente para que estejam disponíveis nos atributos onclick
window.changeMonth = function(direction) {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth() + direction;
    // Criar nova data no primeiro dia do novo mês para evitar problemas com dias inválidos
    currentDate = new Date(year, month, 1);
    // Carregar feriados do novo ano se necessário
    const newYear = currentDate.getFullYear();
    if (!holidaysCache[newYear]) {
        holidaysCache[newYear] = getHolidays(newYear);
    }
    // Atualizar o display do mês/ano imediatamente para feedback visual
    updateMonthYearDisplay();
    // Carregar tarefas e atualizar calendário (loadTodosForCurrentPeriod já chama updateCalendar)
    loadTodosForCurrentPeriod();
};

window.goToToday = function() {
    currentDate = new Date();
    updateMonthYearDisplay();
    loadTodosForCurrentPeriod();
};

window.setView = function(view) {
    currentView = view;
    loadTodosForCurrentPeriod();
};

window.closeDayModal = function() {
    const modal = document.getElementById('dayModal');
    if (modal) {
        modal.classList.add('hidden');
    }
};

window.hideToast = function() {
    const toast = document.getElementById('toastNotification');
    if (toast) {
        toast.classList.add('hidden');
    }
};

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    initializeCalendar();
    requestNotificationPermission();
    setupReminderCheck();
});

// Inicializar calendário
function initializeCalendar() {
    currentDate = new Date();
    currentView = 'month';
    // Carregar feriados do ano atual
    const currentYear = currentDate.getFullYear();
    holidaysCache[currentYear] = getHolidays(currentYear);
    updateCalendar();
    loadTodosForCurrentPeriod();
}

// Carregar tarefas para o período atual
async function loadTodosForCurrentPeriod() {
    try {
        const startDate = getPeriodStartDate();
        const endDate = getPeriodEndDate();
        
        const response = await window.axios.get('/api/todos/by-date-range', {
            params: {
                start_date: formatDateISO(startDate),
                end_date: formatDateISO(endDate)
            }
        });
        
        todos = response.data;
        organizeTodosByDate();
        updateCalendar();
    } catch (error) {
        console.error('Erro ao carregar tarefas:', error);
    }
}

// Organizar tarefas por data
function organizeTodosByDate() {
    todosByDate = {};
    todos.forEach(todo => {
        if (todo.date) {
            // Extrair apenas a data (YYYY-MM-DD) se vier com hora
            let dateKey = todo.date;
            if (typeof dateKey === 'string' && dateKey.includes('T')) {
                dateKey = dateKey.split('T')[0];
            } else if (typeof dateKey === 'string' && dateKey.includes(' ')) {
                dateKey = dateKey.split(' ')[0];
            }
            
            if (!todosByDate[dateKey]) {
                todosByDate[dateKey] = [];
            }
            todosByDate[dateKey].push(todo);
        }
    });
}

// Obter data de início do período
function getPeriodStartDate() {
    if (currentView === 'week') {
        const start = new Date(currentDate);
        const day = start.getDay();
        start.setDate(start.getDate() - day);
        start.setHours(0, 0, 0, 0);
        return start;
    } else {
        const start = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        start.setHours(0, 0, 0, 0);
        return start;
    }
}

// Obter data de fim do período
function getPeriodEndDate() {
    if (currentView === 'week') {
        const end = new Date(currentDate);
        const day = end.getDay();
        end.setDate(end.getDate() + (6 - day));
        end.setHours(23, 59, 59, 999);
        return end;
    } else {
        const end = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
        end.setHours(23, 59, 59, 999);
        return end;
    }
}

// Função formatDateISO já definida no início do arquivo

// Atualizar calendário
function updateCalendar() {
    updateMonthYearDisplay();
    
    if (currentView === 'month') {
        renderMonthView();
    } else {
        renderWeekView();
    }
    
    updateViewButtons();
}

// Função já definida no início do arquivo

// Renderizar visualização mensal
function renderMonthView() {
    const grid = document.getElementById('monthCalendarGrid');
    if (!grid) return;
    
    grid.innerHTML = '';
    
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    // Primeiro dia do mês
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    
    // Dia da semana do primeiro dia (0 = domingo, 6 = sábado)
    const startDay = firstDay.getDay();
    
    // Número de dias no mês
    const daysInMonth = lastDay.getDate();
    
    // Dias do mês anterior para preencher a primeira semana
    const prevMonth = new Date(year, month, 0);
    const daysInPrevMonth = prevMonth.getDate();
    
    // Renderizar dias do mês anterior
    for (let i = startDay - 1; i >= 0; i--) {
        const day = daysInPrevMonth - i;
        const date = new Date(year, month - 1, day);
        const dayElement = createDayElement(date, true);
        grid.appendChild(dayElement);
    }
    
    // Renderizar dias do mês atual
    for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(year, month, day);
        const dayElement = createDayElement(date, false);
        grid.appendChild(dayElement);
    }
    
    // Calcular quantos dias faltam para completar a última semana
    const totalCells = grid.children.length;
    const remainingCells = 42 - totalCells; // 6 semanas * 7 dias
    
    // Renderizar dias do próximo mês
    for (let day = 1; day <= remainingCells; day++) {
        const date = new Date(year, month + 1, day);
        const dayElement = createDayElement(date, true);
        grid.appendChild(dayElement);
    }
}

// Renderizar visualização semanal
function renderWeekView() {
    const grid = document.getElementById('weekCalendarGrid');
    if (!grid) return;
    
    grid.innerHTML = '';
    
    const startDate = new Date(currentDate);
    const day = startDate.getDay();
    startDate.setDate(startDate.getDate() - day);
    
    for (let i = 0; i < 7; i++) {
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + i);
        const dayElement = createWeekDayElement(date);
        grid.appendChild(dayElement);
    }
}

// Criar elemento de dia para visualização mensal
function createDayElement(date, isOtherMonth) {
    const dayDiv = document.createElement('div');
    const dateKey = formatDateISO(date);
    const dayTodos = todosByDate[dateKey] || [];
    const isToday = isSameDay(date, new Date());
    
    // Verificar se é feriado
    const year = date.getFullYear();
    if (!holidaysCache[year]) {
        holidaysCache[year] = getHolidays(year);
    }
    const holidayName = holidaysCache[year][dateKey];
    const isHoliday = !!holidayName;
    
    let classes = 'min-h-[100px] p-2 border rounded-lg transition-colors';
    
    if (isOtherMonth) {
        classes += ' bg-gray-50 text-gray-400 border-gray-200';
    } else {
        classes += ' bg-white hover:bg-gray-50 cursor-pointer';
    }
    
    if (isHoliday) {
        classes += ' border-2 border-orange-600 bg-orange-50';
    } else if (isToday) {
        classes += ' border-2 border-blue-500 bg-blue-50';
    } else {
        classes += ' border border-gray-200';
    }
    
    dayDiv.className = classes;
    dayDiv.onclick = () => !isOtherMonth && openDayModal(date, dayTodos);
    
    // Tooltip com nome do feriado
    if (isHoliday) {
        dayDiv.title = holidayName;
    }
    
    const dayNumber = document.createElement('div');
    dayNumber.className = `font-semibold mb-1 ${isHoliday ? 'text-orange-700' : ''}`;
    dayNumber.textContent = date.getDate();
    dayDiv.appendChild(dayNumber);
    
    // Badge de feriado
    if (isHoliday && !isOtherMonth) {
        const holidayBadge = document.createElement('div');
        holidayBadge.className = 'text-xs font-bold text-orange-700 bg-orange-200 px-1.5 py-0.5 rounded mb-1 inline-block';
        holidayBadge.textContent = '🎉';
        holidayBadge.title = holidayName;
        dayDiv.insertBefore(holidayBadge, dayNumber.nextSibling);
    }
    
    // Mostrar até 3 tarefas
    const todosContainer = document.createElement('div');
    todosContainer.className = 'space-y-1';
    
    dayTodos.slice(0, 3).forEach(todo => {
        const todoElement = createTodoBadge(todo);
        todosContainer.appendChild(todoElement);
    });
    
    if (dayTodos.length > 3) {
        const moreElement = document.createElement('div');
        moreElement.className = 'text-xs text-gray-500 font-medium';
        moreElement.textContent = `+${dayTodos.length - 3} mais`;
        todosContainer.appendChild(moreElement);
    }
    
    dayDiv.appendChild(todosContainer);
    
    return dayDiv;
}

// Criar elemento de dia para visualização semanal
function createWeekDayElement(date) {
    const dayDiv = document.createElement('div');
    const dateKey = formatDateISO(date);
    const dayTodos = todosByDate[dateKey] || [];
    const isToday = isSameDay(date, new Date());
    
    // Verificar se é feriado
    const year = date.getFullYear();
    if (!holidaysCache[year]) {
        holidaysCache[year] = getHolidays(year);
    }
    const holidayName = holidaysCache[year][dateKey];
    const isHoliday = !!holidayName;
    
    let classes = 'min-h-[400px] max-h-[600px] p-3 border rounded-lg flex flex-col';
    
    if (isHoliday) {
        classes += ' border-2 border-orange-600 bg-orange-50';
    } else if (isToday) {
        classes += ' border-2 border-blue-500 bg-blue-50';
    } else {
        classes += ' border border-gray-200 bg-white';
    }
    
    dayDiv.className = classes;
    
    const dayHeader = document.createElement('div');
    dayHeader.className = 'mb-3 pb-2 border-b cursor-pointer hover:bg-gray-100 rounded px-2 py-1 transition-colors';
    dayHeader.onclick = () => {
        openDayModal(date, dayTodos);
    };
    
    const headerContent = document.createElement('div');
    headerContent.className = 'day-header-content';
    
    const dayName = document.createElement('div');
    dayName.className = 'text-sm font-medium text-gray-600';
    dayName.textContent = weekDays[date.getDay()];
    headerContent.appendChild(dayName);
    
    const dayNumberWrapper = document.createElement('div');
    dayNumberWrapper.className = 'flex items-center justify-between';
    
    const dayNumber = document.createElement('div');
    dayNumber.className = `text-xl font-bold ${isHoliday ? 'text-orange-700' : isToday ? 'text-blue-600' : 'text-gray-800'}`;
    dayNumber.textContent = date.getDate();
    dayNumberWrapper.appendChild(dayNumber);
    
    // Badge de feriado
    if (isHoliday) {
        const holidayBadge = document.createElement('div');
        holidayBadge.className = 'text-xs font-bold text-orange-700 bg-orange-200 px-2 py-1 rounded';
        holidayBadge.textContent = '🎉';
        holidayBadge.title = holidayName;
        dayNumberWrapper.appendChild(holidayBadge);
    }
    
    headerContent.appendChild(dayNumberWrapper);
    
    // Nome do feriado abaixo do número
    if (isHoliday) {
        const holidayLabel = document.createElement('div');
        holidayLabel.className = 'text-xs font-semibold text-orange-700 mt-1';
        holidayLabel.textContent = holidayName;
        headerContent.appendChild(holidayLabel);
    }
    
    dayHeader.appendChild(headerContent);
    dayDiv.appendChild(dayHeader);
    
    const todosContainer = document.createElement('div');
    todosContainer.className = 'space-y-2 overflow-y-auto flex-1 min-h-0';
    
    if (dayTodos.length === 0) {
        const emptyElement = document.createElement('div');
        emptyElement.className = 'text-sm text-gray-400 text-center py-4';
        emptyElement.textContent = 'Sem tarefas';
        todosContainer.appendChild(emptyElement);
    } else {
        dayTodos.forEach(todo => {
            const todoElement = createTodoCard(todo);
            todosContainer.appendChild(todoElement);
        });
    }
    
    dayDiv.appendChild(todosContainer);
    
    return dayDiv;
}

// Criar badge de tarefa para visualização mensal
function createTodoBadge(todo) {
    const badge = document.createElement('div');
    const priorityColors = {
        'urgent': 'bg-red-100 text-red-800 border-red-300',
        'medium': 'bg-yellow-100 text-yellow-800 border-yellow-300',
        'simple': 'bg-green-100 text-green-800 border-green-300'
    };
    
    const color = priorityColors[todo.priority] || priorityColors.simple;
    
    let badgeClasses = `text-xs px-2 py-1 rounded border ${color} truncate cursor-pointer hover:shadow-md transition-shadow`;
    if (todo.completed) {
        badgeClasses += ' opacity-60 line-through';
    }
    
    badge.className = badgeClasses;
    badge.textContent = todo.text;
    badge.title = todo.text + (todo.completed ? ' (Concluída)' : '');
    
    // Adicionar evento de clique para abrir modal de detalhes
    badge.onclick = (e) => {
        e.stopPropagation(); // Prevenir que abra o modal do dia
        openTodoDetailModal(todo);
    };
    
    return badge;
}

// Criar card de tarefa para visualização semanal
function createTodoCard(todo) {
    const card = document.createElement('div');
    const priorityColors = {
        'urgent': 'border-l-red-500 bg-red-50',
        'medium': 'border-l-yellow-500 bg-yellow-50',
        'simple': 'border-l-green-500 bg-green-50'
    };
    
    const color = priorityColors[todo.priority] || priorityColors.simple;
    
    // Altura máxima e overflow para manter proporção
    card.className = `p-2 rounded border-l-4 ${color} hover:shadow-md transition-shadow max-h-32 overflow-hidden flex flex-col`;
    
    const header = document.createElement('div');
    header.className = 'flex items-start justify-between mb-1 flex-shrink-0';
    
    const title = document.createElement('div');
    title.className = `font-semibold text-sm flex-1 min-w-0 ${todo.completed ? 'line-through text-gray-500' : 'text-gray-800'}`;
    title.style.overflow = 'hidden';
    title.style.textOverflow = 'ellipsis';
    title.style.display = '-webkit-box';
    title.style.webkitLineClamp = '2';
    title.style.webkitBoxOrient = 'vertical';
    title.textContent = todo.text;
    title.title = todo.text; // Tooltip com texto completo
    header.appendChild(title);
    
    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.className = 'ml-2 flex-shrink-0 mt-0.5';
    checkbox.checked = todo.completed;
    checkbox.onchange = (e) => {
        e.stopPropagation();
        toggleTodoCalendar(todo.id);
    };
    header.appendChild(checkbox);
    
    card.appendChild(header);
    
    const contentWrapper = document.createElement('div');
    contentWrapper.className = 'flex-1 min-h-0 overflow-hidden';
    
    if (todo.description) {
        const desc = document.createElement('div');
        desc.className = `text-xs text-gray-600 mt-1 line-clamp-2 ${todo.completed ? 'line-through text-gray-400' : ''}`;
        desc.textContent = todo.description;
        desc.title = todo.description; // Tooltip com descrição completa
        contentWrapper.appendChild(desc);
    }
    
    card.appendChild(contentWrapper);
    
    const actions = document.createElement('div');
    actions.className = 'flex gap-1 mt-2 flex-shrink-0';
    
    const editBtn = document.createElement('button');
    editBtn.className = 'text-xs px-2 py-1 rounded bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors';
    editBtn.textContent = 'Editar';
    editBtn.onclick = (e) => {
        e.stopPropagation();
        window.location.href = `/todos/${todo.id}/edit`;
    };
    actions.appendChild(editBtn);
    
    card.appendChild(actions);
    
    // Abrir modal de detalhes ao clicar no card (mas não nos botões)
    card.onclick = (e) => {
        // Se clicou em um botão ou checkbox, não abrir o modal
        if (e.target.tagName === 'BUTTON' || e.target.tagName === 'INPUT' || e.target.closest('button') || e.target.closest('input')) {
            e.stopPropagation();
            return;
        }
        e.stopPropagation(); // Prevenir que abra o modal do dia
        openTodoDetailModal(todo);
    };
    
    return card;
}

// Verificar se duas datas são do mesmo dia
function isSameDay(date1, date2) {
    return date1.getFullYear() === date2.getFullYear() &&
           date1.getMonth() === date2.getMonth() &&
           date1.getDate() === date2.getDate();
}

// Funções globais já definidas no início do arquivo

// Atualizar botões de visualização
function updateViewButtons() {
    const monthBtn = document.getElementById('viewMonthBtn');
    const weekBtn = document.getElementById('viewWeekBtn');
    const monthView = document.getElementById('monthView');
    const weekView = document.getElementById('weekView');
    
    if (currentView === 'month') {
        monthBtn.classList.remove('bg-gray-300', 'hover:bg-gray-400');
        monthBtn.classList.add('bg-green-500', 'hover:bg-green-600', 'text-white');
        weekBtn.classList.remove('bg-green-500', 'hover:bg-green-600', 'text-white');
        weekBtn.classList.add('bg-gray-300', 'hover:bg-gray-400');
        monthView.classList.remove('hidden');
        weekView.classList.add('hidden');
    } else {
        weekBtn.classList.remove('bg-gray-300', 'hover:bg-gray-400');
        weekBtn.classList.add('bg-green-500', 'hover:bg-green-600', 'text-white');
        monthBtn.classList.remove('bg-green-500', 'hover:bg-green-600', 'text-white');
        monthBtn.classList.add('bg-gray-300', 'hover:bg-gray-400');
        weekView.classList.remove('hidden');
        monthView.classList.add('hidden');
    }
}

// Abrir modal de detalhes de uma tarefa específica
function openTodoDetailModal(todo) {
    const modal = document.getElementById('dayModal');
    const title = document.getElementById('dayModalTitle');
    const content = document.getElementById('dayModalContent');
    
    // Obter data da tarefa para o título
    let dateStr = 'Sem data';
    if (todo.date) {
        let dateValue = todo.date;
        if (typeof dateValue === 'string' && dateValue.includes('T')) {
            dateValue = dateValue.split('T')[0];
        }
        if (typeof dateValue === 'string' && dateValue.match(/^\d{4}-\d{2}-\d{2}/)) {
            const [year, month, day] = dateValue.split('-');
            const date = new Date(year, month - 1, day);
            dateStr = date.toLocaleDateString('pt-BR', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
    }
    
    title.textContent = `Detalhes da Tarefa - ${dateStr}`;
    title.dataset.date = todo.date ? (typeof todo.date === 'string' && todo.date.includes('T') ? todo.date.split('T')[0] : todo.date) : '';
    
    content.innerHTML = '';
    
    // Criar card de detalhes da tarefa
    const todoElement = createModalTodoCard(todo);
    content.appendChild(todoElement);
    
    modal.classList.remove('hidden');
}

// Abrir modal de tarefas do dia
function openDayModal(date, dayTodos) {
    const modal = document.getElementById('dayModal');
    const title = document.getElementById('dayModalTitle');
    const content = document.getElementById('dayModalContent');
    
    const dateStr = date.toLocaleDateString('pt-BR', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    const dateKey = formatDateISO(date);
    
    // Verificar se é feriado
    const year = date.getFullYear();
    if (!holidaysCache[year]) {
        holidaysCache[year] = getHolidays(year);
    }
    const holidayName = holidaysCache[year][dateKey];
    
    let titleText = `Tarefas de ${dateStr}`;
    if (holidayName) {
        titleText += ` - ${holidayName}`;
    }
    
    title.textContent = titleText;
    title.dataset.date = dateKey; // Armazenar a data para atualização do modal
    
    content.innerHTML = '';
    
    if (dayTodos.length === 0) {
        // Formatar data no formato brasileiro para o link
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const dateBR = `${day}/${month}/${year}`;
        const addTodoUrl = `/?date=${encodeURIComponent(dateBR)}`;
        
        content.innerHTML = `
            <p class="text-gray-500 text-center py-8">
                Nenhuma tarefa para este dia. 
                <a href="${addTodoUrl}" class="text-blue-600 hover:underline">Crie uma nova tarefa aqui!</a>
            </p>
        `;
    } else {
        dayTodos.forEach(todo => {
            const todoElement = createModalTodoCard(todo);
            content.appendChild(todoElement);
        });
    }
    
    modal.classList.remove('hidden');
}

// Criar card de tarefa para o modal
function createModalTodoCard(todo) {
    const card = document.createElement('div');
    const priorityColors = {
        'urgent': 'border-l-red-500',
        'medium': 'border-l-yellow-500',
        'simple': 'border-l-green-500'
    };
    
    const color = priorityColors[todo.priority] || priorityColors.simple;
    
    card.className = `p-4 mb-3 rounded border-l-4 ${color} bg-white shadow-sm hover:shadow-md transition-shadow cursor-pointer`;
    card.onclick = (e) => {
        // Se não clicou em um botão, abrir visualização
        if (!e.target.closest('button')) {
            window.location.href = `/todos/${todo.id}`;
        }
    };
    
    const header = document.createElement('div');
    header.className = 'flex items-start justify-between mb-2';
    
    const titleWrapper = document.createElement('div');
    titleWrapper.className = 'flex-1';
    
    const title = document.createElement('h4');
    title.className = `font-semibold text-gray-800 cursor-pointer hover:text-[#fb9e0b] transition-colors ${todo.completed ? 'line-through text-gray-500' : ''}`;
    title.textContent = todo.text;
    title.onclick = (e) => {
        e.stopPropagation();
        window.location.href = `/todos/${todo.id}`;
    };
    titleWrapper.appendChild(title);
    
    header.appendChild(titleWrapper);
    
    const status = document.createElement('span');
    status.className = `ml-2 px-2 py-1 rounded text-xs font-medium ${todo.completed ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}`;
    status.textContent = todo.completed ? 'Concluída' : 'Pendente';
    header.appendChild(status);
    
    card.appendChild(header);
    
    if (todo.description) {
        const desc = document.createElement('p');
        desc.className = `text-sm text-gray-600 mt-2 ${todo.completed ? 'line-through text-gray-400' : ''}`;
        desc.textContent = todo.description;
        card.appendChild(desc);
    }
    
    const priority = document.createElement('div');
    priority.className = 'mt-2 text-xs text-gray-500';
    const priorityLabels = {
        'urgent': 'Urgente',
        'medium': 'Média',
        'simple': 'Simples'
    };
    priority.textContent = `Prioridade: ${priorityLabels[todo.priority] || 'Simples'}`;
    card.appendChild(priority);
    
    // Botões de ação
    const actions = document.createElement('div');
    actions.className = 'flex gap-2 mt-3 pt-3 border-t border-gray-200';
    
    // Botão de concluir/pendente
    const toggleBtn = document.createElement('button');
    toggleBtn.className = `px-3 py-1.5 rounded text-sm font-medium transition-colors ${
        todo.completed 
            ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' 
            : 'bg-green-100 text-green-800 hover:bg-green-200'
    }`;
    toggleBtn.textContent = todo.completed ? 'Marcar como Pendente' : 'Concluir';
    toggleBtn.onclick = (e) => {
        e.stopPropagation();
        toggleTodoCalendar(todo.id);
    };
    actions.appendChild(toggleBtn);
    
    // Botão de editar
    const editBtn = document.createElement('button');
    editBtn.className = 'px-3 py-1.5 rounded text-sm font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors';
    editBtn.textContent = 'Editar';
    editBtn.onclick = (e) => {
        e.stopPropagation();
        closeDayModal();
        window.location.href = `/todos/${todo.id}/edit`;
    };
    actions.appendChild(editBtn);
    
    card.appendChild(actions);
    
    return card;
}

// Alternar status de conclusão de uma tarefa no calendário
async function toggleTodoCalendar(todoId) {
    try {
        const response = await window.axios.patch(`/api/todos/${todoId}/toggle`);
        
        // Atualizar a tarefa na lista local
        const index = todos.findIndex(t => t.id === todoId);
        if (index !== -1) {
            todos[index] = response.data;
        }
        
        // Reorganizar tarefas por data
        organizeTodosByDate();
        
        // Recarregar o calendário
        updateCalendar();
        
        // Se o modal estiver aberto, atualizar o conteúdo
        const modal = document.getElementById('dayModal');
        if (modal && !modal.classList.contains('hidden')) {
            const modalTitle = document.getElementById('dayModalTitle');
            if (modalTitle) {
                // Verificar se é modal de detalhes de uma tarefa específica
                if (modalTitle.textContent.startsWith('Detalhes da Tarefa')) {
                    // Atualizar modal de detalhes da tarefa
                    const updatedTodo = todos.find(t => t.id === todoId);
                    if (updatedTodo) {
                        openTodoDetailModal(updatedTodo);
                    }
                } else if (modalTitle.dataset.date) {
                    // Atualizar modal de tarefas do dia
                    const dateKey = modalTitle.dataset.date;
                    // Converter string YYYY-MM-DD para Date
                    const [year, month, day] = dateKey.split('-').map(Number);
                    const date = new Date(year, month - 1, day);
                    const dayTodos = todosByDate[dateKey] || [];
                    openDayModal(date, dayTodos);
                }
            }
        }
        
        // Mostrar feedback visual
        showCalendarToast('Tarefa atualizada com sucesso!');
    } catch (error) {
        console.error('Erro ao alternar tarefa:', error);
        showCalendarToast('Erro ao atualizar tarefa. Tente novamente.', 'error');
    }
}

// Mostrar toast de notificação no calendário
function showCalendarToast(message, type = 'success') {
    const toast = document.getElementById('toastNotification');
    const toastMessage = document.getElementById('toastMessage');
    
    if (toast && toastMessage) {
        toastMessage.textContent = message;
        
        // Remover classes de cor anteriores
        toast.classList.remove('bg-green-500', 'bg-red-500');
        
        // Adicionar classe de cor baseada no tipo
        if (type === 'error') {
            toast.classList.add('bg-red-500');
        } else {
            toast.classList.add('bg-green-500');
        }
        
        toast.classList.remove('hidden');
        
        // Esconder após 3 segundos
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 3000);
    }
}

// Funções globais já definidas no início do arquivo

// Solicitar permissão de notificação
function requestNotificationPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
}

// Configurar verificação de lembretes
function setupReminderCheck() {
    // Verificar lembretes a cada minuto
    setInterval(checkReminders, 60000);
    checkReminders(); // Verificar imediatamente
}

// Verificar lembretes
function checkReminders() {
    if (!('Notification' in window) || Notification.permission !== 'granted') {
        return;
    }
    
    const now = new Date();
    const today = formatDateISO(now);
    const tomorrow = formatDateISO(new Date(now.getTime() + 24 * 60 * 60 * 1000));
    
    // Verificar tarefas de hoje
    const todayTodos = todosByDate[today] || [];
    const pendingToday = todayTodos.filter(t => !t.completed);
    
    if (pendingToday.length > 0) {
        const count = pendingToday.length;
        const message = count === 1 
            ? `Você tem 1 tarefa pendente hoje: ${pendingToday[0].text}`
            : `Você tem ${count} tarefas pendentes hoje`;
        
        showNotification('Lembrete de Tarefas', message);
    }
    
    // Verificar tarefas de amanhã
    const tomorrowTodos = todosByDate[tomorrow] || [];
    const pendingTomorrow = tomorrowTodos.filter(t => !t.completed);
    
    if (pendingTomorrow.length > 0 && now.getHours() >= 18) {
        const count = pendingTomorrow.length;
        const message = count === 1
            ? `Lembrete: Você tem 1 tarefa amanhã: ${pendingTomorrow[0].text}`
            : `Lembrete: Você tem ${count} tarefas amanhã`;
        
        showNotification('Tarefas de Amanhã', message);
    }
}

// Mostrar notificação
function showNotification(title, message) {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(title, {
            body: message,
            icon: '/favicon.ico',
            badge: '/favicon.ico'
        });
    }
}

// Fechar modal ao clicar fora
document.addEventListener('click', function(e) {
    const modal = document.getElementById('dayModal');
    if (e.target === modal) {
        closeDayModal();
    }
});

