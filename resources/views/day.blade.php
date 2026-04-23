@extends('layouts.app')

@section('title', 'События ' . $date->format('d.m.Y'))

@push('styles')
@vite(['resources/css/day.css'])
@endpush

@section('content')
    <div class="day-page-container">
        <div class="day-page-card">
            <div class="day-page-header">
                <h1>📅 События на {{ $date->format('d.m.Y') }}</h1>
                <div class="date-sub">{{ $date->translatedFormat('l') }}</div>
            </div>
            <div class="day-page-content">
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
                                <div class="event-link">
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
        </div>
    </div>
@endsection
