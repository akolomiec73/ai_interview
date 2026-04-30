@if($event->audio_transcription)
    <div class="analysis-block">
        <h3>Транскрипция собеседования</h3>
        <div class="transcription-list">
            @foreach($event->audio_transcription as $item)
                <div class="transcription-item speaker-{{ $item['speaker'] ?? 0 }}">
                    <strong>{{ ($item['speaker'] ?? 0) == 0 ? '👨‍💼 Собеседник:' : '🧑‍ Вы:' }}</strong>
                    <span>{{ $item['text'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
@else
    <div class="no-data">
        <p>🔊 Транскрипция ещё не готова или отсутствует.</p>
        <p class="hint">Запишите разговор, чтобы получить расшифровку и анализ.</p>
    </div>
@endif
