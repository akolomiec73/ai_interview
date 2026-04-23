@extends('layouts.app')

@section('title', "Событие собеседование в {$event->vacancy->company}")

@push('styles')
    @vite(['resources/css/event-show.css', 'resources/css/day.css'])
@endpush

@section('content')
    <div class="event-show-container">
        <div class="event-card">
            <div class="event-header">
                <h1>Собеседование</h1>
                <div class="event-actions">
                    <button type="button" class="btn-move" id="moveEventBtn" data-id="{{ $event->id }}">Перенести</button>
                    <button type="button" class="btn-danger" id="deleteEventBtn" data-id="{{ $event->id }}">Удалить</button>
                </div>
            </div>

            <div class="event-details">
                <div class="detail-row">
                    <span class="label">Дата и время:</span>
                    <span class="value" id="eventDateDisplay">{{ $event->dateInterview->format('d.m.Y H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Комментарий:</span>
                    <span class="value">{{ $event->comment ?: '—' }}</span>
                </div>
            </div>

            @if($event->vacancy)
                <div class="vacancy-block">
                    <h2>Вакансия</h2>
                    <div class="vacancy-info">
                        <p><strong>Компания:</strong> {{ $event->vacancy->company }}</p>
                        <p><strong>Зарплата:</strong> {{ $event->vacancy->salary }}</p>
                        <p><strong>Формат работы:</strong> {{ $event->vacancy->format_work }}</p>
                        <p><strong>Навыки:</strong> {{ $event->vacancy->skills }}</p>
                        <p><strong>Ссылка:</strong> <a href="{{ $event->vacancy->url }}" target="_blank">открыть вакансию</a></p>

                        @if($event->vacancy->top_questions)
                            <div class="top-questions">
                                <h3>Топ вопросов для подготовки</h3>
                                <ul>
                                    @foreach($event->vacancy->top_questions as $question)
                                        <li>{{ $question }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <a href="{{ url('/day/'.$event->dateInterview->format('Y-m-d')) }}" class="back-link">← Назад к событиям дня</a>
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/event-show.js'])
@endpush
