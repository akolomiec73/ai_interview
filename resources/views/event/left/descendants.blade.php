{{-- ВСЕ СЛЕДУЮЩИЕ ЭТАПЫ (цепочка потомков) --}}
@foreach($descendants as $descendant)
    <div class="event-card chain-child">
        <a href="{{ route('events.show', $descendant) }}" class="event-card-link">
            <div class="event-header-row">
                <div class="event-stage-title">Следующий этап</div>
                <div class="event-status {{ $descendant->status->color() }}">{{ $descendant->status->label() }}</div>
            </div>
            <div class="event-time">{{ $descendant->dateInterview->translatedFormat('d F H:i') }}</div>
            <div class="event-stage">{{ $descendant->stage->label() }}</div>
            <div class="event-comment">{{ $descendant->comment }}</div>
            @if($descendant->resultComment)
                <div class="result-comment-div">
                    <span class="event-stage">Результат этапа:</span>
                    <div class="event-comment result-comment">{{ $descendant->resultComment }}</div>
                </div>
            @endif
        </a>
    </div>
@endforeach
