@extends('layouts.app')

@section('title', ($event->stage->label()) . ' в ' . ($event->vacancy->company ?? 'Нет данных'))

@push('styles')
    @vite(['resources/css/event.css', 'resources/css/modal.css'])
@endpush

@push('scripts')
    @vite(['resources/js/event.js', 'resources/js/modal.js', 'resources/js/recording.js'])
@endpush

@section('content-header')
    <h1>
        <a href="{{ route('main') }}">📅</a>
        {{ $event->stage->label() }}
    </h1>
    <div class="dropdown">
        <button class="btn btn-more" id="moreActionsBtn">⋮</button>
        <div class="dropdown-menu" id="moreActionsMenu" style="display: none;">
            <button type="button" class="btn-dropdown" id="transferEventBtn" data-modal-open="modalTransferEvent" data-id="{{ $event->id }}">📅 Перенести событие</button>
            <button type="button" class="btn-dropdown btn-danger-dropdown" data-modal-open="modalDeleteEvent" data-id="{{ $event->id }}" data-event-date="{{ $event->dateInterview->format('Y-m-d') }}">✕ Удалить событие</button>
        </div>
    </div>
@endsection

@section('content')
    <div class="content-body">
        <div class="content-body-info">
            <div class="left-side">
                @include('event.left.ancestors')
                @include('event.left.current-card')
                @include('event.left.descendants')
            </div>
            <div class="right-side tabs-container">
                <div class="tabs-header">
                    <button class="tab-btn active" data-tab="vacancy">💼 Вакансия</button>
                    <button class="tab-btn" data-tab="analysis">🤖 Анализ (AI)</button>
                    <button class="tab-btn" data-tab="notes">📝 Легенда</button>
                </div>
                <div class="tabs-content">
                    <div class="tab-pane active" id="tab-vacancy">
                        @include('event.right.tabs.vacancy')
                    </div>
                    <div class="tab-pane" id="tab-analysis">
                        @include('event.right.tabs.analysis')
                    </div>
                    <div class="tab-pane" id="tab-notes">
                        @include('event.right.tabs.legend')
                    </div>
                </div>
            </div>
        </div>
        <a href="{{ url('/day/'.$event->dateInterview->format('Y-m-d')) }}" class="back-link">← Назад к событиям дня</a>
    </div>
@endsection

@section('content-footer')
    Карточка события
@endsection

@section('include-modals')
    @include('modals.confirm-delete-event-modal')
    @include('modals.result-stage-modal')
    @include('modals.transfer-event-modal')
@endsection
