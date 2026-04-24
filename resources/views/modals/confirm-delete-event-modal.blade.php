<!-- Модалка подтверждения удаления события -->
<x-modal id="modalDeleteEvent" title="Подтверждение удаления">
    <p>Вы действительно хотите удалить это событие?</p>
    <div class="form-actions" style="justify-content: center;">
        <button type="button" class="btn-modal btn-cancel">Отмена</button>
        <button type="button" class="btn-modal btn-danger" id="confirmDeleteBtn">Удалить</button>
    </div>
</x-modal>
