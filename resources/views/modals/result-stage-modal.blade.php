<!-- Модалка фиксации результатов этапа -->
<x-modal id="modalResultStage" title="Результат этапа">
    <form id="formResultStage">
        <div class="form-group">
            <label>Выберите действие</label>
            <div class="radio-group">
                <label>
                    <input type="radio" name="action" value="next_stage" checked>
                    <span></span>Создать следующий этап
                </label>
                <label>
                    <input type="radio" name="action" value="complete">
                    <span></span>Завершить (без следующего этапа)
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="form-error" id="errorMessage"></div>
        </div>
        <!-- поля для следующего этапа -->
        <div id="nextStageFields" style="display: block;">
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
                <textarea id="commentNext" rows="10" name="commentNext" placeholder="Дополнительная информация..."></textarea>
            </div>
        </div>
        <!-- поля для Завершить (без следующего этапа) -->
        <div id="completeFields" style="display: none;">
            <div class="form-group">
                <label>Комментарий к завершению</label>
                <textarea id="completeComment" rows="10" placeholder="Укажите здесь информацию полученную в результате события"></textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="button" class="btn-modal btn-cancel">Отмена</button>
            <button type="submit" class="btn-modal btn-submit">Подтвердить</button>
        </div>
    </form>
</x-modal>
