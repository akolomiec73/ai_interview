@vite(['resources/css/create-event-modal.css', 'resources/js/create-event-modal.js'])
<div class="modal-overlay" id="modalOverlay" aria-modal="true" role="dialog" aria-labelledby="modalTitle">
    <div class="modal">
        <div class="modal-header">
            <h2 id="modalTitle">Запланировать собеседование</h2>
            <button class="close-btn" id="closeModalBtn" aria-label="Закрыть">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addForm">
                <div class="form-group">
                    <div class="form-error" id="errorMessage"></div>
                </div>
                <div class="form-group">
                    <label for="dateInterview">Дата и время</label>
                    <input type="datetime-local" id="dateInterview" name="dateInterview" required>
                </div>
                <div class="form-group">
                    <label for="linkVacantion">Ссылка на вакансию</label>
                    <input type="text" id="linkVacantion" name="linkVacantion" required placeholder="Укажите ссылку на вакансию hh">
                </div>
                <div class="form-group">
                    <label for="comment">Комментарий</label>
                    <textarea id="comment" name="comment" placeholder="Дополнительная информация..."></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" id="cancelFormBtn">Отмена</button>
                    <button type="submit" class="btn-submit">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
