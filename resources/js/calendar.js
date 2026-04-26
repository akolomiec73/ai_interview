document.addEventListener('DOMContentLoaded', () => {
    const monthYearSpan = document.getElementById('monthYearDisplay');
    const daysContainer = document.getElementById('calendarDaysGrid');
    const prevBtn = document.getElementById('prevMonthBtn');
    const nextBtn = document.getElementById('nextMonthBtn');
    const todayBtn = document.getElementById('todayBtn');
    const todayDateSpan = document.getElementById('todayDateLabel');
    let currentDisplayDate = new Date();
    const today = new Date();

    // ---------- Вспомогательные функции ----------
    /**
     * Форматирует месяц и год для заголовка
     * @param {Date} date
     * @returns {string}
     */
    function formatMonthYear(date) {
        const months = [
            'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
            'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
        ];
        return `${months[date.getMonth()]} ${date.getFullYear()}`;
    }

    /**
     * Возвращает количество дней в месяце
     * @param {number} year
     * @param {number} month
     * @returns {number}
     */
    function getDaysInMonth(year, month) {
        return new Date(year, month + 1, 0).getDate();
    }

    /**
     * Индекс первого дня месяца (понедельник = 0, воскресенье = 6)
     * @param {number} year
     * @param {number} month
     * @returns {number}
     */
    function getFirstDayIndex(year, month) {
        const firstDay = new Date(year, month, 1);
        let dayIndex = firstDay.getDay(); // 0 = воскресенье
        return dayIndex === 0 ? 6 : dayIndex - 1;
    }

    /**
     * Извлекает время из строки даты для отображения в календаре
     * @param {string} dateInterview
     * @returns {string}
     */
    function getEventTime(dateInterview) {
        if (!dateInterview) return '';
        const dateObj = new Date(dateInterview);
        if (!isNaN(dateObj.getTime())) {
            return dateObj.toLocaleTimeString('ru-RU', {hour: '2-digit', minute: '2-digit'});
        }
        const match = dateInterview.match(/\d{2}:\d{2}/);
        return match ? match[0] : '';
    }

    /**
     * Создаёт DOM-элемент одного события для ячейки календаря
     * @param {Object} event
     * @returns {HTMLDivElement}
     */
    function createEventElement(event) {
        const eventEl = document.createElement('div');
        eventEl.className = 'event-item '+event.stage_color;
        const timeStr = getEventTime(event.dateInterview);
        eventEl.textContent = `${timeStr} ${event.stage_label}`;
        eventEl.title = `${event.stage_label}\nКомпания: ${event.vacancy?.company ?? 'Нет данных'}\nВакансия: ${event.vacancy?.job_title ?? 'Нет данных'}`;
        eventEl.addEventListener('click', (e) => {
            e.stopPropagation();
            window.location.href = `/events/${event.id}`;
        });
        return eventEl;
    }

    /**
     * Строит массив ячеек календаря на основе текущей отображаемой даты
     * @returns {Array}
     */
    function buildCalendarCells() {
        const year = currentDisplayDate.getFullYear();
        const month = currentDisplayDate.getMonth();
        const daysInMonth = getDaysInMonth(year, month);
        const startOffset = getFirstDayIndex(year, month);

        const prevMonthDate = new Date(year, month, 0);
        const prevMonthYear = prevMonthDate.getFullYear();
        const prevMonth = prevMonthDate.getMonth();
        const daysInPrevMonth = getDaysInMonth(prevMonthYear, prevMonth);

        const nextMonthDate = new Date(year, month + 1, 1);
        const nextMonthYear = nextMonthDate.getFullYear();
        const nextMonthIdx = nextMonthDate.getMonth();

        const totalCells = 42; // 6 строк × 7 дней
        const cells = [];

        // Дни предыдущего месяца
        for (let i = startOffset - 1; i >= 0; i--) {
            const dayNum = daysInPrevMonth - i;
            cells.push({
                day: dayNum,
                year: prevMonthYear,
                month: prevMonth,
                isCurrentMonth: false,
                dateObj: new Date(prevMonthYear, prevMonth, dayNum)
            });
        }

        // Дни текущего месяца
        for (let d = 1; d <= daysInMonth; d++) {
            cells.push({
                day: d,
                year: year,
                month: month,
                isCurrentMonth: true,
                dateObj: new Date(year, month, d)
            });
        }

        // Дни следующего месяца (заполняем до 42)
        const remaining = totalCells - cells.length;
        for (let i = 1; i <= remaining; i++) {
            cells.push({
                day: i,
                year: nextMonthYear,
                month: nextMonthIdx,
                isCurrentMonth: false,
                dateObj: new Date(nextMonthYear, nextMonthIdx, i)
            });
        }

        return cells;
    }

    /**
     * Рендерит одну ячейку дня
     * @param {Object} cell
     * @param {Object} eventsMap
     * @returns {HTMLDivElement}
     */
    function renderCell(cell, eventsMap) {
        const dayDiv = document.createElement('div');
        dayDiv.className = 'day-cell';
        if (!cell.isCurrentMonth) dayDiv.classList.add('other-month');

        const dayOfWeek = cell.dateObj.getDay();
        const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
        if (isWeekend && cell.isCurrentMonth) dayDiv.classList.add('weekend');

        const isToday = (cell.year === today.getFullYear() &&
            cell.month === today.getMonth() &&
            cell.day === today.getDate());
        if (isToday) dayDiv.classList.add('today');

        // Номер дня
        const dayNumber = document.createElement('span');
        dayNumber.className = 'day-number';
        dayNumber.textContent = cell.day;
        dayDiv.appendChild(dayNumber);

        // Контейнер для событий
        const eventsContainer = document.createElement('div');
        eventsContainer.className = 'day-events';
        const dateKey = `${cell.year}-${String(cell.month + 1).padStart(2, '0')}-${String(cell.day).padStart(2, '0')}`;
        const dayEvents = eventsMap[dateKey] || [];

        if (dayEvents.length) {
            dayDiv.classList.add('has-event');
            const maxDisplay = 4;
            dayEvents.slice(0, maxDisplay).forEach(event => {
                eventsContainer.appendChild(createEventElement(event));
            });
            if (dayEvents.length > maxDisplay) {
                const moreEl = document.createElement('div');
                moreEl.className = 'event-more';
                moreEl.textContent = `+ ещё ${dayEvents.length - maxDisplay}`;
                eventsContainer.appendChild(moreEl);
            }
        }
        dayDiv.appendChild(eventsContainer);

        // Обработчик клика по ячейке — переход на страницу дня
        dayDiv.addEventListener('click', () => {
            const year = cell.year;
            const month = String(cell.month + 1).padStart(2, '0');
            const day = String(cell.day).padStart(2, '0');
            window.location.href = `/day/${year}-${month}-${day}`;
        });

        return dayDiv;
    }

    /**
     * Основная функция рендеринга календаря
     * @param {Object} events
     */
    function renderCalendar(events = {}) {
        const cells = buildCalendarCells();
        daysContainer.innerHTML = '';
        cells.forEach(cell => {
            daysContainer.appendChild(renderCell(cell, events));
        });
        monthYearSpan.textContent = formatMonthYear(currentDisplayDate);
    }

    // ---------- Загрузка событий с сервера ----------
    /**
     * Загружает события за месяц через API
     * @param {number} year
     * @param {number} month
     * @returns {Promise<Object>}
     */
    async function fetchEvents(year, month) {
        const monthNumber = month + 1;
        try {
            const response = await axios.get('/api/events', {
                params: {year, month: monthNumber}
            });
            return response.data;
        } catch (error) {
            console.error('Ошибка загрузки событий', error);
            return {};
        }
    }

    // ---------- Обновление календаря ----------
    /**
     * Загружает события и перерисовывает календарь
     */
    async function loadAndRender() {
        const year = currentDisplayDate.getFullYear();
        const month = currentDisplayDate.getMonth();
        const events = await fetchEvents(year, month);
        renderCalendar(events);
    }

    // ---------- Навигация ----------
    function changeMonth(delta) {
        currentDisplayDate = new Date(currentDisplayDate.getFullYear(), currentDisplayDate.getMonth() + delta, 1);
        loadAndRender();
    }

    function goToToday() {
        const now = new Date();
        currentDisplayDate = new Date(now.getFullYear(), now.getMonth(), 1);
        loadAndRender();
    }

    // ---------- Инициализация ----------
    async function init() {
        currentDisplayDate = new Date(today.getFullYear(), today.getMonth(), 1);
        await loadAndRender();
        if (todayDateSpan) {
            todayDateSpan.textContent = today.toLocaleDateString('ru-RU', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        prevBtn.addEventListener('click', () => changeMonth(-1));
        nextBtn.addEventListener('click', () => changeMonth(1));
        todayBtn.addEventListener('click', goToToday);
    }

    // Делаем функцию доступной глобально для вызова из модалки
    window.loadAndRender = loadAndRender;

    // Запуск
    init();

    /*-------------------------Обработка создания события---------------------------*/
    const form = document.getElementById('addForm');
    const errorDiv = document.getElementById('errorMessage');

    /**
     * Отправка данных формы на сервер
     */
    async function submitForm() {
        const submitBtn = document.querySelector('.btn-submit');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Сохранение...';
        errorDiv.textContent = '';

        try {
            const dateEvent = document.getElementById('dateEvent').value.trim();
            const linkVacancy = document.getElementById('linkVacancy').value.trim();
            const comment = document.getElementById('comment').value.trim();
            const eventStage = document.getElementById('eventStage').value;

            await axios.post('/api/events', {dateEvent, linkVacancy, comment, eventStage});
            window.modal.close('modalCreateEvent');
            if (typeof window.loadAndRender === 'function') {
                window.loadAndRender();
            }
        } catch (error) {
            errorDiv.textContent = error.response?.data?.message || 'Произошла ошибка при сохранении';
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    }

    /*-------------------------События---------------------------*/

    // Обработка отправки формы
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        submitForm();
    });
});
