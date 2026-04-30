{{-- ВСЕ ПРЕДЫДУЩИЕ ЭТАПЫ (цепочка предков) --}}
@foreach($ancestors as $ancestor)
    <div class="event-card chain-parent">
        <a href="{{ route('events.show', $ancestor) }}" class="event-card-link">
            <div class="event-header-row">
                <div class="event-stage-title">Предыдущий этап</div>
                <div class="event-status {{ $ancestor->status->color() }}">{{ $ancestor->status->label() }}</div>
            </div>
            <div class="event-time">{{ $ancestor->dateInterview->translatedFormat('d F H:i') }}</div>
            <div class="event-stage">{{ $ancestor->stage->label() }}</div>
            <div class="event-comment">{{ $ancestor->comment }}</div>
            @if($ancestor->resultComment)
                <div class="result-comment-div">
                    <span class="event-stage">Результат этапа:</span>
                    <div class="event-comment result-comment">{{ $ancestor->resultComment }}</div>
                </div>
            @endif
        </a>
    </div>
@endforeach
