@props(['id', 'title'])

<div class="modal-overlay" id="{{ $id }}" aria-modal="true" role="dialog" aria-labelledby="modalTitle">
    <div class="modal">
        <div class="modal-header">
            <h2>{{ $title }}</h2>
            <button class="close-btn" aria-label="Закрыть">&times;</button>
        </div>
        <div class="modal-body">
            {{ $slot }}
        </div>
    </div>
</div>
