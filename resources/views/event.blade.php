@extends('layouts.app')

@section('title', "Событие собеседование в {$event->vacancy->company}")

@push('styles')
    @vite(['resources/css/event.css', 'resources/css/modal.css'])
@endpush

@push('scripts')
    @vite(['resources/js/app.js', 'resources/js/event.js', 'resources/js/modal.js'])
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
                            <div class="event-stage-title">Предыдущий этап</div>
                            <div class="event-time">{{ $ancestor->dateInterview->format('d.m H:i') }}</div>
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
                    <div class="event-stage-title">Текущий этап</div>
                    <div class="event-time">{{ $event->dateInterview->format('d.m H:i') }}</div>
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
                            <div class="event-stage-title">Следующий этап</div>
                            <div class="event-time">{{ $descendant->dateInterview->format('d.m H:i') }}</div>
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
                    <h2>Вакансия</h2>
                    <div class="vacancy-info">
                        <p><strong>Компания:</strong> {{ $event->vacancy->company }}</p>
                        <p><strong>Зарплата:</strong> {{ $event->vacancy->salary }}</p>
                        <p><strong>Формат работы:</strong> {{ $event->vacancy->format_work }}</p>
                        <p><strong>Навыки:</strong> {{ $event->vacancy->skills }}</p>
                        <p><strong>Ссылка:</strong> <span class="link-vacancy"><a href="{{ $event->vacancy->url }}" target="_blank">открыть вакансию</a></span></p>
                        <div class="top-questions">
                            <h3>Топ вопросов для подготовки</h3>
                            <ul>
                                @foreach($event->vacancy->top_questions as $question)
                                    <li>{{ $question }}</li>
                                @endforeach
                            </ul>
                        </div>
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
