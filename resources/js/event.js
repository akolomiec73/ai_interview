document.addEventListener('DOMContentLoaded', () => {
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const transferEventBtn = document.getElementById('transferEventBtn');
    const nextStageBtn = document.getElementById('nextStageBtn');
    const formNextStageEvent = document.getElementById('formNextStageEvent');
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
     * Создание следующей стадии события
     * @returns {Promise<void>}
     */
    async function nextStageEvent() {
        const submitBtn = document.querySelector('.btn-submit');
        const originalText = submitBtn.textContent;
        const errorDiv = document.getElementById('errorMessage');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Сохранение...';
        errorDiv.textContent = '';

        try {
            const dateInterview = document.getElementById('dateInterviewNext').value.trim();
            const comment = document.getElementById('commentNext').value.trim();
            const eventId = nextStageBtn.getAttribute('data-id');

            await axios.post(`/api/events/${eventId}/next-stage`, {dateInterview, comment});
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
    formNextStageEvent.addEventListener('submit', function (e) {
        e.preventDefault();
        nextStageEvent();
    });

    //Перенос события
    formTransferEvent.addEventListener('submit', function (e) {
        e.preventDefault();
        transferEvent();
    })
});
