/**
 * Скрипт для браузерных уведомлений о предстоящих событиях.
 * Работает на всех страницах приложения, использует один таймер на ближайшее событие.
 */
// ---------------------------- Конфигурация ----------------------------
const NOTIFY_BEFORE_MINUTES = 10; // За сколько минут до события показывать уведомление
const NOTIFY_BEFORE_MS = NOTIFY_BEFORE_MINUTES * 60 * 1000; // Задержка в миллисекундах

// ---------------------------- Состояние ----------------------------
let notificationTimer = null; // текущий таймер уведомления
let shownEvents = new Set(); // множество ID событий, о которых уже уведомили

// ---------------------------- Работа с localStorage ----------------------------
// Загружает уже показанные ID из localStorage
function loadShownEvents() {
    const stored = localStorage.getItem('shownEvents');
    if (stored) {
        shownEvents = new Set(JSON.parse(stored));
    }
}

// Сохраняет множество shownEvents в localStorage
function saveShownEvents() {
    localStorage.setItem('shownEvents', JSON.stringify(Array.from(shownEvents)));
}

// Добавляет ID события в множество и сохраняет
function markShown(eventId) {
    shownEvents.add(eventId);
    saveShownEvents();
}

// Удаляет из хранилища ID, которых нет в списке активных событий (очистка устаревших)
function pruneShownEvents(activeEvents) {
    const activeIds = new Set(activeEvents.map(e => e.id));
    let changed = false;
    for (let id of shownEvents) {
        if (!activeIds.has(id)) {
            shownEvents.delete(id);
            changed = true;
        }
    }
    if (changed) {
        saveShownEvents();
    }
}

// ---------------------------- Разрешение на уведомления ----------------------------
// Запрашивает разрешение, если ещё не запрашивали или не отклонено.
async function requestNotificationPermission() {
    if (Notification.permission === 'granted') return true;
    if (Notification.permission !== 'denied') {
        const permission = await Notification.requestPermission();
        if (permission === 'granted' && window.upcomingEvents) {
            scheduleNextNotification(window.upcomingEvents);
        }
    }
    return Notification.permission === 'granted';
}

// ---------------------------- Показ уведомления ----------------------------
function showNotification(event) {
    if (Notification.permission !== 'granted') return;
    if (shownEvents.has(event.id)) return;

    const company = event.vacancy.company || 'Не указана';
    const time = new Date(event.dateInterview).toLocaleTimeString('ru-RU', {
        hour: '2-digit',
        minute: '2-digit'
    });
    const title = '🔔 Напоминание о событии';
    const body = `Собеседование в ${company} · ${time}`;
    const notification = new Notification(title, { body, icon: '/favicon.ico' });

    // При клике на уведомление переходим на страницу события
    notification.onclick = () => {
        window.focus();
        window.location.href = `/events/${event.id}`;
    };

    markShown(event.id);
}

// ---------------------------- Планировщик ----------------------------
// Принимает массив событий (отсортированных по дате) и устанавливает
// таймер на самое ближнее событие, которое ещё не показывали.
// После срабатывания таймера переходит к следующему событию.
function scheduleNextNotification(events) {
    if (notificationTimer) clearTimeout(notificationTimer);
    if (!events?.length) return;
    if (Notification.permission !== 'granted') return;

    // Отбираем только будущие события, по которым ещё не уведомляли
    const now = new Date();
    const futureEvents = events.filter(e => new Date(e.dateInterview) > now && !shownEvents.has(e.id));
    if (futureEvents.length === 0) return;

    // Берём самое ближайшее событие
    const nextEvent = futureEvents[0];
    const eventTime = new Date(nextEvent.dateInterview);
    // Момент, за который нужно показать уведомление (за NOTIFY_BEFORE_MS до события)
    const notifyTime = eventTime - NOTIFY_BEFORE_MS;
    let delay = notifyTime - now;
    if (delay < 0) delay = 0;   // если уже пора – показываем сразу

    notificationTimer = setTimeout(() => {
        showNotification(nextEvent);
        scheduleNextNotification(futureEvents.slice(1));
    }, delay);
}

// ---------------------------- Инициализация ----------------------------
// Загружает события из API, очищает хранилище и запускает планировщик.
export async function initNotifications() {
    loadShownEvents();
    await requestNotificationPermission();

    const response = await axios.get('/api/upcoming-events');
    const events = response.data;
    window.upcomingEvents = events;
    pruneShownEvents(events);
    scheduleNextNotification(events);
}

// ---------------------------- Экспорт и глобальные функции для других скриптов ----------------------------

window.initNotifications = initNotifications;

// Глобальная функция для удаления ID события из хранилища (например, при переносе даты)
window.removeShownEvent = (eventId) => {
    if (shownEvents.has(eventId)) {
        shownEvents.delete(eventId);
        saveShownEvents();
        console.log(`🗑️ Событие ${eventId} удалено из хранилища (перенос/удаление)`);
    }
};

// Запускаем инициализацию при загрузке скрипта
initNotifications();
