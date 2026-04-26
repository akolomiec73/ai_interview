@extends('layouts.app')

@section('title', "Событие собеседование в {$event->vacancy->company}")

@push('styles')
    @vite(['resources/css/event.css', 'resources/css/modal.css'])
@endpush

@push('scripts')
    @vite(['resources/js/event.js', 'resources/js/modal.js'])
@endpush

@section('content-header')
    <h1>
        <a href="{{ route('main') }}">📅</a>
         Собеседование
    </h1>
    <div>
        <button type="button" class="btn btn-next-stage" id="nextStageBtn" data-modal-open="modalNextStageEvent" data-id="{{ $event->id }}">➕ Добавить этап</button>
        <button type="button" class="btn btn-move" id="transferEventBtn" data-modal-open="modalTransferEvent" data-id="{{ $event->id }}">📅 Перенести</button>
        <button type="button" class="btn btn-danger" data-modal-open="modalDeleteEvent" data-id="{{ $event->id }}" data-event-date="{{ $event->dateInterview->format('Y-m-d') }}">✕ Удалить</button>
    </div>
@endsection

@section('content')
    <div class="content-body">
        <div class="content-body-info">
            <div class="left-side">
                {{-- ВСЕ ПРЕДЫДУЩИЕ ЭТАПЫ (цепочка предков) --}}
                @foreach($ancestors as $ancestor)
                    <div class="event-card chain-parent">
                        <a href="{{ route('events.show', $ancestor) }}" class="event-card-link">
                            <div class="event-header-row">
                                <div class="event-stage-title">Предыдущий этап</div>
                                <div class="event-status {{ $ancestor->status->color() }}">{{ $ancestor->status->label() }}</div>
                            </div>
                            <div class="event-time">{{ $ancestor->dateInterview->translatedFormat('d F H:i') }}</div>
                            <div class="event-name">
                                Собеседование в
                                <span class="company-name">{{ $ancestor->vacancy->company }}</span>
                            </div>
                            <div class="event-comment">{{ $ancestor->comment }}</div>
                        </a>
                    </div>
                @endforeach

                {{-- КАРТОЧКА СОБЫТИЯ --}}
                <div class="event-card current-event-card">
                    <div class="event-header-row">
                        <div class="event-stage-title">Текущий этап</div>
                        <div class="event-status {{ $event->status->color() }}">{{ $event->status->label() }}</div>
                    </div>
                    <div class="event-time">{{ $event->dateInterview->translatedFormat('d F H:i') }}</div>
                    <div class="event-name">
                        Собеседование в
                        <span class="company-name">{{ $event->vacancy->company }}</span>
                    </div>
                    <div class="event-comment">{{ $event->comment }}</div>
                </div>

                {{-- ВСЕ СЛЕДУЮЩИЕ ЭТАПЫ (цепочка потомков) --}}
                @foreach($descendants as $descendant)
                    <div class="event-card chain-child">
                        <a href="{{ route('events.show', $descendant) }}" class="event-card-link">
                            <div class="event-header-row">
                                <div class="event-stage-title">Следующий этап</div>
                                <div class="event-status {{ $descendant->status->color() }}">{{ $descendant->status->label() }}</div>
                            </div>
                            <div class="event-time">{{ $descendant->dateInterview->translatedFormat('d F H:i') }}</div>
                            <div class="event-name">
                                Собеседование в
                                <span class="company-name">{{ $descendant->vacancy->company }}</span>
                            </div>
                            <div class="event-comment">{{ $descendant->comment }}</div>
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="right-side">
                <div class="vacancy-block">
                    <div class="vacancy-header">
                        <h2>Вакансия</h2>
                        <span class="value job-title">{{ $event->vacancy->job_title }}</span>
                    </div>

                    <div class="vacancy-grid">
                        <div class="vacancy-item">
                            <span class="label">Компания</span>
                            <div>
                                <span class="value company">{{ $event->vacancy->company }}</span>
                                <span class="company-industry">{{ $event->vacancy->industry }}</span>
                            </div>
                        </div>
                        <div class="vacancy-item">
                            <span class="label">Зарплата</span>
                            <span class="value salary">{{ $event->vacancy->salary }}</span>
                        </div>
                        <div class="vacancy-item">
                            <span class="label">Формат работы</span>
                            <span class="value">{{ $event->vacancy->format_work }}</span>
                        </div>
                        <div class="vacancy-item">
                            <span class="label">Город</span>
                            <span class="value">{{ $event->vacancy->city}}</span>
                        </div>
                    </div>

                    @if($event->vacancy->benefits !== 'Не указано')
                    <div class="benefits-block">
                        <span class="label">Плюшки</span>
                        <div class="value benefits-list">
                            @foreach(explode(',', $event->vacancy->benefits) as $benefit)
                                @if(trim($benefit))
                                    <span class="benefit-badge">{{ trim($benefit) }}</span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="skills-block">
                        <span class="label">Технологии и навыки</span>
                        <div class="skills-list">
                            @foreach(explode(',', $event->vacancy->skills) as $skill)
                                @if(trim($skill))
                                    <span class="skill-badge">{{ trim($skill) }}</span>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="vacancy-footer">
                        <div class="link-vacancy-outline">
                            <a href="{{ $event->vacancy->url }}" target="_blank" rel="noopener noreferrer">Открыть вакансию</a>
                        </div>
                    </div>

                    <div class="top-questions">
                        <h3>📋 Топ вопросов для подготовки</h3>
                        <ul>
                            @foreach($event->vacancy->top_questions as $question)
                                <li>{{ $question }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <a href="{{ url('/day/'.$event->dateInterview->format('Y-m-d')) }}" class="back-link">← Назад к событиям дня</a>
    </div>
@endsection

@section('content-footer')
    Представлена цепочка событий + инфа о вакансии
@endsection

@section('include-modals')
    @include('modals.confirm-delete-event-modal')
    @include('modals.next-stage-event-modal')
    @include('modals.transfer-event-modal')
@endsection
