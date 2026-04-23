document.addEventListener('DOMContentLoaded', () => {
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
});
