document.addEventListener('DOMContentLoaded', () => {
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const transferEventBtn = document.getElementById('transferEventBtn');
    const resultStageBtn = document.getElementById('resultStageBtn');
    const formResultStage = document.getElementById('formResultStage');
    const formTransferEvent = document.getElementById('formTransferEvent')

    /**
     * Удаление события
     * @param eventId
     * @param eventDate
     * @returns {Promise<void>}
     */
    async function deleteEvent(eventId, eventDate) {
        try {
            const response = await axios.delete(`/api/events/${eventId}`);

            if (response.status === 200) {
                window.location.href = `/day/${eventDate}`;
            } else {
                console.log(response.status + ' : ' + response.statusText)
            }
        } catch (error) {
            console.error('Ошибка сети:', error);
        }
    }

    async function transferEvent() {
        const submitBtn = document.querySelector('.btn-submit');
        const originalText = submitBtn.textContent;
        const errorDiv = document.getElementById('errorMessage');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Сохранение...';
        errorDiv.textContent = '';
        try {
            const dateTransferEvent = document.getElementById('dateTransferEvent').value.trim();
            const eventId = transferEventBtn.getAttribute('data-id');

            await axios.put(`/api/events/${eventId}`, {dateTransferEvent});
            window.removeShownEvent(eventId);
            window.location.href = `/events/${eventId}`;
        } catch (error) {
            errorDiv.textContent = error.response?.data?.message || 'Произошла ошибка при сохранении';
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    }

    /**
     * Фиксация результатов события
     */
    async function ResultStage() {
        const submitBtn = document.querySelector('.btn-submit');
        const originalText = submitBtn.textContent;
        const errorDiv = document.getElementById('errorMessage');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Сохранение...';
        errorDiv.textContent = '';

        try {
            const action = document.querySelector('input[name="action"]:checked').value;
            const eventId = resultStageBtn.getAttribute('data-id');
            let payload = {action};

            if (action === 'next_stage') {
                payload.dateEvent = document.getElementById('dateEventNext').value;
                payload.eventStage = document.getElementById('eventStageNext').value;
                payload.comment = document.getElementById('commentNext').value.trim();
            } else if (action === 'complete') {
                payload.comment = document.getElementById('completeComment').value;
            }

            await axios.post(`/api/events/${eventId}/result-stage`, payload);
            window.location.href = `/events/${eventId}`;
        } catch (error) {
            errorDiv.textContent = error.response?.data?.message || 'Произошла ошибка при сохранении';
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    }

    /*-------------------------События---------------------------*/
    //удаление события
    confirmDeleteBtn.addEventListener('click', async () => {
        const deleteBtn = document.querySelector('[data-modal-open="modalDeleteEvent"]');
        const eventId = deleteBtn?.getAttribute('data-id');
        const eventDate = deleteBtn?.getAttribute('data-event-date');

        deleteEvent(eventId, eventDate);
    });

    //Добавление следующей стадии
    formResultStage.addEventListener('submit', function (e) {
        e.preventDefault();
        ResultStage();
    });

    //Перенос события
    formTransferEvent.addEventListener('submit', function (e) {
        e.preventDefault();
        transferEvent();
    })
});
