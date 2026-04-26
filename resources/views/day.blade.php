@extends('layouts.app')

@section('title', 'События ' . $date->format('d.m.Y'))

@push('styles')
@vite(['resources/css/day.css'])
@endpush

@section('content-header')
    <h1>📅 События на {{ $date->format('d.m.Y') }}</h1>
    <div>{{ $date->translatedFormat('l') }}</div>
@endsection

@section('content')
    <div class="content-body">
        @if($events->count())
            <ul class="events-list">
                @foreach($events as $event)
                    <li class="event-item-card {{ $event->stage->color() }}">
                        <a href="{{ route('events.show', $event) }}" class="event-card-link">
                            <div class="event-header-row">
                                <div class="block-time-stage">
                                    <div class="event-time">{{ $event->dateInterview->format('H:i') }}</div>
                                    <div class="event-stage">{{ $event->stage->label() }}</div>
                                </div>
                                <div class="event-status {{ $event->status->color() }}">{{ $event->status->label() }}</div>
                            </div>

                            <div class="event-main-info">
                                <span class="company-name">{{ $event->vacancy->company }}</span>
                                <span class="company-job-title">{{ $event->vacancy->job_title }}</span>
                                <span class="company-salary">{{ $event->vacancy->salary }}</span>
                                <span class="company-format_work">{{ $event->vacancy->format_work }}</span>
                                @if($event->vacancy->industry !== 'Не указано')
                                    <span class="company-industry">{{ $event->vacancy->industry }}</span>
                                @endif
                                @if($event->vacancy->city !== 'Не указано')
                                    <span class="company-city">{{ $event->vacancy->city }}</span>
                                @endif
                            </div>

                            <div class="company-skills">{{ $event->vacancy->skills }}</div>
                            @if($event->comment)
                                <div class="event-comment">{{ $event->comment }}</div>
                            @endif
                        </a>
                        <div class="link-vacancy">
                            <a href="{{ $event->linkVacantion }}" target="_blank" rel="noopener noreferrer">Ссылка на вакансию</a>
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
@endsection

@section('content-footer')
    Выберите событие, чтобы посмотреть детали
@endsection
