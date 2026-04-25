<!-- Модалка переноса даты события -->
<x-modal id="modalTransferEvent" title="Перенести дату события">
    <form id="formTransferEvent">
        <div class="form-group">
            <div class="form-error" id="errorMessage"></div>
        </div>
        <div class="form-group">
            <label for="date">Новая дата и время</label>
            <input type="datetime-local" id="dateTransferEvent" name="date" required>
        </div>
        <div class="form-actions">
            <button type="button" class="btn-modal btn-cancel">Отмена</button>
            <button type="submit" class="btn-modal btn-submit">Изменить</button>
        </div>
    </form>
</x-modal>
