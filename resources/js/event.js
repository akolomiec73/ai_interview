document.addEventListener('DOMContentLoaded', () => {
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const moveEventBtn = document.getElementById('moveEventBtn');

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

    async function transferEvent(eventId) {

    }

    /*-------------------------События---------------------------*/
    //удаление события
    confirmDeleteBtn.addEventListener('click', async () => {
        const deleteBtn = document.querySelector('[data-modal-open="modalDeleteEvent"]');
        const eventId = deleteBtn?.getAttribute('data-id');
        const eventDate = deleteBtn?.getAttribute('data-event-date');

        deleteEvent(eventId, eventDate);
    });

    //перенос события
    moveEventBtn.addEventListener('click', () => {
        const eventId = moveEventBtn.getAttribute('data-id');
        transferEvent(eventId);
    });
});
