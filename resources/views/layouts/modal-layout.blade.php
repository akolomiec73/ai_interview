<div class="modal-overlay" id="@yield('modal-id')" aria-modal="true" role="dialog" aria-labelledby="modalTitle">
    <div class="modal">
        <div class="modal-header">
            <h2>@yield('modal-title')</h2>
            <button class="close-btn" aria-label="Закрыть">&times;</button>
        </div>
        <div class="modal-body">
            @yield('modal-body')
        </div>
    </div>
</div>
