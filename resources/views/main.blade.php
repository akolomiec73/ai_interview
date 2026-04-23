@extends('layouts.app')

@section('title', 'Календарь')

@push('styles')
    @vite(['resources/css/calendar.css', 'resources/css/modal.css'])
@endpush

@push('scripts')
    @vite(['resources/js/app.js', 'resources/js/calendar.js', 'resources/js/modal.js'])
@endpush

@section('content')
    <div class="content">
        <div class="content-header">
            <div class="month-year" id="monthYearDisplay"></div>
            <div>
                <button class="btn btn-add" data-modal-open="modalCreateEvent">+ Добавить</button>
            </div>
            <div class="nav-buttons">
                <button class="btn nav-btn" id="prevMonthBtn" aria-label="Предыдущий месяц">←</button>
                <button class="btn nav-btn" id="todayBtn" aria-label="Сегодня">•</button>
                <button class="btn nav-btn" id="nextMonthBtn" aria-label="Следующий месяц">→</button>
            </div>
        </div>

        <div class="weekdays">
            <span>Пн</span><span>Вт</span><span>Ср</span><span>Чт</span><span>Пт</span><span>Сб</span><span>Вс</span>
        </div>
        <div class="calendar-days" id="calendarDaysGrid">
        </div>

        <div class="content-footer">
            📅 Выберите день, чтобы посмотреть события
        </div>
    </div>
    @include('modals.create-event-modal')
@endsection
