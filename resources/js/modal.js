document.addEventListener('DOMContentLoaded', () => {
    const radioButtons = document.querySelectorAll('input[name="action"]');
    const nextStageFields = document.getElementById('nextStageFields');
    const completeFields = document.getElementById('completeFields');
    const dateEventNext = document.getElementById('dateEventNext');
    const eventStageNext = document.getElementById('eventStageNext');
    /**
     * Функция открытия модального окна
     * @param modal
     */
    function openModal(modal) {
        modal.classList.add('active');
        const errorDiv = document.getElementById('errorMessage');
        if (errorDiv) errorDiv.textContent = '';
    }

    /**
     * Функция закрытия модалки
     * @param modal
     * @param isReset
     */
    function closeModal(modal, isReset = true) {
        modal.classList.remove('active');
        const form = document.getElementById('addForm');
        if (form) {
            if (isReset){
                form.reset();
            }
        }
    }

    /**
     * Вспомогательная функция переключения обязательного значения поля
     */
    function toggleRequired(isNextStage) {
        dateEventNext.required = isNextStage;
        eventStageNext.required = isNextStage;
    }

    /*-------------------------События---------------------------*/

    // Открытие модалок
    document.querySelectorAll('[data-modal-open]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const modalId = btn.getAttribute('data-modal-open');
            const modal = document.getElementById(modalId);
            if (modal) openModal(modal);
        });
    });

    // Закрытие по клику на фон или кнопке
    document.addEventListener('click', (e) => {
        // Клик на оверлей
        if (e.target.classList.contains('modal-overlay')) {
            closeModal(e.target, false);
        }
        // Клик на кнопку закрытия .close-btn внутри модалки
        if (e.target.classList.contains('close-btn')) {
            const modal = e.target.closest('.modal-overlay');
            if (modal) closeModal(modal);
        }
        // Клик на кнопку отмены .btn-cancel внутри модалки
        if (e.target.classList.contains('btn-cancel')) {
            const modal = e.target.closest('.modal-overlay');
            if (modal) closeModal(modal);
        }
    });

    // Делаем функции доступными глобально
    window.modal = {
        close: (id) => {
            const modal = document.getElementById(id);
            if (modal) closeModal(modal);
        }
    };

    // Выбор действия в модалке result-stage-modal
    radioButtons.forEach(radio => {
        radio.addEventListener('change', () => {
            nextStageFields.style.display = 'none';
            completeFields.style.display = 'none';
            if (radio.value === 'next_stage'){
                nextStageFields.style.display = 'block';
                toggleRequired(false)
            } else if (radio.value === 'complete'){
                completeFields.style.display = 'block';
                toggleRequired(false)
            }
        });
    });
});
