<!-- Модалка добавления события -->
<x-modal id="modalCreateEvent" title="Добавить событие">
    <form id="addForm">
        <div class="form-group">
            <div class="form-error" id="errorMessage"></div>
        </div>
        <div class="form-group">
            <label for="dateEvent">Дата и время</label>
            <input type="datetime-local" id="dateEvent" name="dateEvent" required>
        </div>
        <div class="form-group">
            <label for="linkVacancy">Ссылка на вакансию</label>
            <input type="text" id="linkVacancy" name="linkVacancy" required placeholder="Укажите ссылку на вакансию hh">
        </div>
        <div class="form-group">
            <label for="comment">Комментарий</label>
            <textarea id="comment" name="comment" placeholder="Дополнительная информация..."></textarea>
        </div>
        <div class="form-actions">
            <button type="button" class="btn-modal btn-cancel">Отмена</button>
            <button type="submit" class="btn-modal btn-submit">Сохранить</button>
        </div>
    </form>
</x-modal>
