@extends('layouts.app')

@section('title', 'Календарь')

@push('styles')
    @vite(['resources/css/calendar.css'])
@endpush

@push('scripts')
    @vite(['resources/js/app.js', 'resources/js/calendar.js'])
@endpush

@section('content')
    <div class="calendar-card">
        <div class="calendar-header">
            <div class="month-year" id="monthYearDisplay"></div>
            <div>
                <button class="btn-add" id="addInterview">Добавить</button>
            </div>
            <div class="nav-buttons">
                <button class="nav-btn" id="prevMonthBtn" aria-label="Предыдущий месяц">←</button>
                <button class="nav-btn" id="todayBtn" aria-label="Сегодня">•</button>
                <button class="nav-btn" id="nextMonthBtn" aria-label="Следующий месяц">→</button>
            </div>
        </div>
        <div class="weekdays">
            <span>Пн</span><span>Вт</span><span>Ср</span><span>Чт</span><span>Пт</span><span>Сб</span><span>Вс</span>
        </div>
        <div class="calendar-days" id="calendarDaysGrid">
        </div>
        <div class="calendar-footer">
            📅 Выберите день, чтобы посмотреть события
        </div>
    </div>
    @include('create-event-modal')
@endsection
