@extends('layouts.app')

@section('title', 'События ' . $date->format('d.m.Y'))

@push('styles')
@vite(['resources/css/day.css'])
@endpush

@section('content')
    <div class="content">
        <div class="content-header">
            <h1>📅 События на {{ $date->format('d.m.Y') }}</h1>
            <div>{{ $date->translatedFormat('l') }}</div>
        </div>

        <div class="content-body">
            @if($events->count())
                <ul class="events-list">
                    @foreach($events as $event)
                        <li class="event-item-card">
                            <a href="{{ route('events.show', $event) }}" class="event-card-link">
                                <div class="event-time">
                                    {{  $event->dateInterview->format('H:i') }}
                                </div>
                                <div class="event-name">
                                    Собеседование в
                                    <span class="company-name">{{ $event->vacancy->company }}</span>
                                    <span class="company-salary">{{ $event->vacancy->salary }}</span>
                                    <span class="company-format_work">{{ $event->vacancy->format_work }}</span>
                                </div>
                                <div class="company-skills">{{ $event->vacancy->skills }}</div>
                                @if($event->comment)
                                    <div class="event-comment">{{ $event->comment }}</div>
                                @endif
                            </a>
                            <div class="link-vacancy">
                                <a href="{{ $event->linkVacantion }}" target="_blank" rel="noopener noreferrer">
                                    Ссылка на вакансию
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="no-events">
                    🕊️ На этот день событий не запланировано.
                </div>
            @endif
            <a href="{{ url('/') }}" class="back-link">← Назад к календарю</a>
        </div>
        <div class="content-footer">
            Выберите событие, чтобы посмотреть детали
        </div>
    </div>
@endsection
