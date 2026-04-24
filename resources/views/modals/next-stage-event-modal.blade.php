<!-- Модалка добавления следующей стадии события -->
<x-modal id="modalNextStageEvent" title="Добавить следующую стадию">
    <form id="formNextStageEvent">
        <div class="form-group">
            <div class="form-error" id="errorMessage"></div>
        </div>
        <div class="form-group">
            <label for="dateInterview">Дата и время</label>
            <input type="datetime-local" id="dateInterviewNext" name="dateInterview" required>
        </div>
        <div class="form-group">
            <label for="comment">Комментарий</label>
            <textarea id="commentNext" name="comment" placeholder="Дополнительная информация..."></textarea>
        </div>
        <div class="form-actions">
            <button type="button" class="btn-modal btn-cancel">Отмена</button>
            <button type="submit" class="btn-modal btn-submit">Добавить</button>
        </div>
    </form>
</x-modal>
