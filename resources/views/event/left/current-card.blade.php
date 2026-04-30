{{-- КАРТОЧКА СОБЫТИЯ --}}
<div class="event-card current-event-card">
    <div class="event-header-row">
        <div class="event-stage-title">Текущий этап</div>
        <div class="event-status {{ $event->status->color() }}">{{ $event->status->label() }}</div>
    </div>
    <div class="event-time">{{ $event->dateInterview->translatedFormat('d F H:i') }}</div>
    <div class="event-stage">{{ $event->stage->label() }}</div>
    <div class="event-comment">{{ $event->comment }}</div>
    @if($event->resultComment)
        <div class="result-comment-div">
            <span class="event-stage">Результат этапа:</span>
            <div class="event-comment result-comment">{{ $event->resultComment }}</div>
        </div>
    @endif
    @if(count($descendants) == 0)
        <div class="event-actions">
            <button type="button" class="btn btn-result-stage" id="resultStageBtn" data-modal-open="modalResultStage" data-id="{{ $event->id }}">➕ Результат</button>
            <button type="button" class="btn btn-record" id="startRecordBtn" data-id="{{ $event->id }}">
                🎙 Начать запись
                <span class="tooltip-text">
                    🔹 После нажатия, необходимо разрешить доступ к микрофону.<br>
                    🔹 Затем разрешить доступ к экрану.<br>
                    🔹 Минимально достаточно доступ к вкладке, где идёт беседа.<br>
                    🔹 <strong>Необходимо разрешить доступ к аудио вкладки</strong><br>
                    🔹 Сам экран не записывается.<br>
                    🔹 Записывает ваш микрофон и звук собеседника (из вкладки).<br>
                    🔹 После остановки, аудио отправляется на распознавание.<br>
                    🔹 Транскрипция формируется в Yandex SpeechKit<br>
                    🔹 Результат появится во вкладке "Анализ (AI)".
                </span>
            </button>
            <button type="button" class="btn btn-stop" id="stopRecordBtn" style="display: none;">⏹ Остановить</button>
        </div>
    @endif
</div>
