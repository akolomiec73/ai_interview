<!-- Модалка добавления следующего этапа события -->
<x-modal id="modalNextStageEvent" title="Добавить следующий этап">
    <form id="formNextStageEvent">
        <div class="form-group">
            <div class="form-error" id="errorMessage"></div>
        </div>
        <div class="form-group">
            <label for="eventStageNext">Этап собеседования</label>
            <select id="eventStageNext" name="eventStageNext" required>
                @foreach(\App\Enums\EventStage::cases() as $stage)
                    <option value="{{ $stage->value }}" {{ $stage->value === 'technical_meet' ? 'selected' : '' }}>
                        {{ $stage->label() }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="dateEventNext">Дата и время</label>
            <input type="datetime-local" id="dateEventNext" name="dateEventNext" required>
        </div>
        <div class="form-group">
            <label for="commentNext">Комментарий</label>
            <textarea id="commentNext" name="commentNext" placeholder="Дополнительная информация..."></textarea>
        </div>
        <div class="form-actions">
            <button type="button" class="btn-modal btn-cancel">Отмена</button>
            <button type="submit" class="btn-modal btn-submit">Добавить</button>
        </div>
    </form>
</x-modal>
