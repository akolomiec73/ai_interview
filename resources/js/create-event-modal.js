const openBtn = document.getElementById('addInterview');
const modalOverlay = document.getElementById('modalOverlay');
const closeModalBtn = document.getElementById('closeModalBtn');
const cancelFormBtn = document.getElementById('cancelFormBtn');
const form = document.getElementById('addForm');
const errorDiv = document.getElementById('errorMessage');

/**
 * Функция открытия модального окна
 */
function openModal() {
    modalOverlay.classList.add('active');
    errorDiv.textContent = '';
}

/**
 * Функция закрытия модального окна
 * @param isReset определяет очищать форму или нет
 */
function closeModal(isReset = true) {
    modalOverlay.classList.remove('active');
    if (isReset){
        form.reset();
    }
}

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
        const dateInterview = document.getElementById('dateInterview').value.trim();
        const linkVacantion = document.getElementById('linkVacantion').value.trim();
        const comment = document.getElementById('comment').value.trim();

        await axios.post('/api/events', { dateInterview, linkVacantion, comment });
        closeModal(true);
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

// Открытие по кнопке "Добавить"
openBtn.addEventListener('click', openModal);

// Закрытие по крестику
closeModalBtn.addEventListener('click', closeModal);

// Закрытие по кнопке "Отмена"
cancelFormBtn.addEventListener('click', closeModal);

// Закрытие по клику на фон (оверлей)
modalOverlay.addEventListener('click', function(e) {
    if (e.target === modalOverlay) {
        closeModal(false);
    }
});

// Обработка отправки формы
form.addEventListener('submit', function(e) {
    e.preventDefault();
    submitForm();
});
