<!-- Модалка подтверждения удаления события -->
@extends('layouts.modal-layout')

@section('modal-title', 'Подтверждение удаления')

@section('modal-id', 'modalDeleteEvent')

@section('modal-body')
    <p>Вы действительно хотите удалить это событие?</p>
    <div class="form-actions" style="justify-content: center;">
        <button type="button" class="btn-modal btn-cancel">Отмена</button>
        <button type="button" class="btn-modal btn-danger" id="confirmDeleteBtn">Удалить</button>
    </div>
@endsection
