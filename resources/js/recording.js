document.addEventListener('DOMContentLoaded', () => {
    const startRecordBtn = document.getElementById('startRecordBtn');
    const stopRecordBtn = document.getElementById('stopRecordBtn');
    const recordingIndicator = document.getElementById('recordingIndicator');
    const eventId = startRecordBtn.getAttribute('data-id');

    let mediaRecorder = null;
    let audioChunks = [];
    let recordedBlob = null;
    let microphoneStream = null;
    let tabAudioStream = null;
    let audioContext = null;
    let isRecording = false;

    // Единый обработчик beforeunload (добавляем один раз)
    window.addEventListener('beforeunload', () => {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
        }
    });

    /**
     *  Старт записи, захват потока
     */
    async function startRecording() {
        if (isRecording) return;
        try {
            // Запрос микрофона и звука вкладки
            const micStream = await navigator.mediaDevices.getUserMedia({ audio: true });
            const tabStream = await navigator.mediaDevices.getDisplayMedia({ audio: true, video: true });
            // Выключаем видео-трек (он нам не нужен для записи)
            tabStream.getVideoTracks().forEach(track => track.enabled = false);

            // Сохраняем потоки для последующего закрытия
            microphoneStream = micStream;
            tabAudioStream = tabStream;

            // Создаём AudioContext
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const micSource = audioContext.createMediaStreamSource(micStream);
            const tabSource = audioContext.createMediaStreamSource(tabStream);

            // Создаём стерео-мешалку: 2 канала
            const channelMerger = audioContext.createChannelMerger(2);
            micSource.connect(channelMerger, 0, 0);
            tabSource.connect(channelMerger, 0, 1);

            // Создаём конечный стерео-поток
            const destination = audioContext.createMediaStreamDestination();
            channelMerger.connect(destination);

            // Определяем поддерживаемый MIME-тип для стерео
            let mimeType = 'audio/webm';
            const codecs = ['audio/webm;codecs=opus', 'audio/webm', 'audio/ogg;codecs=opus'];
            for (const mime of codecs) {
                if (MediaRecorder.isTypeSupported(mime)) {
                    mimeType = mime;
                    break;
                }
            }

            // Инициализация MediaRecorder с выбранным MIME-типом
            mediaRecorder = new MediaRecorder(destination.stream, { mimeType });
            audioChunks = [];

            mediaRecorder.ondataavailable = (event) => {
                if (event.data.size > 0) audioChunks.push(event.data);
            };

            mediaRecorder.onstop = () => {
                recordedBlob = new Blob(audioChunks, { type: mediaRecorder.mimeType || mimeType });
                uploadAudioFile();
            };

            // Начинаем запись
            mediaRecorder.start();
            isRecording = true;

            // Обновляем UI
            startRecordBtn.disabled = true;
            startRecordBtn.style.display = 'none';
            stopRecordBtn.disabled = false;
            stopRecordBtn.style.display = 'inline-flex';
            recordingIndicator.style.display = 'flex';
        } catch (err) {
            console.error('Ошибка захвата аудио:', err);
            resetUI();
        }
    }

    /**
     * Обновление интерфейса и закрытие ресурсов
     */
    function resetUI() {
        startRecordBtn.disabled = false;
        startRecordBtn.style.display = 'inline-flex';
        startRecordBtn.textContent = '🎙 Начать запись';
        stopRecordBtn.disabled = true;
        stopRecordBtn.style.display = 'none';
        recordingIndicator.style.display = 'none';

        if (microphoneStream) {
            microphoneStream.getTracks().forEach(t => t.stop());
            microphoneStream = null;
        }
        if (tabAudioStream) {
            tabAudioStream.getTracks().forEach(t => t.stop());
            tabAudioStream = null;
        }
        if (audioContext) {
            audioContext.close().catch(e => console.warn);
            audioContext = null;
        }
        mediaRecorder = null;
        audioChunks = [];
        recordedBlob = null;
        isRecording = false;
    }

    /**
     * Остановка записи
     */
    function stopRecording() {
        if (!mediaRecorder || mediaRecorder.state === 'inactive') return;
        mediaRecorder.stop();
        stopRecordBtn.disabled = true;
        if (recordingIndicator) recordingIndicator.style.display = 'none';
        startRecordBtn.disabled = true;
        startRecordBtn.textContent = '⏳ Отправка...';
    }

    /**
     * Отправка аудиофайла на сервер
     */
    async function uploadAudioFile() {
        if (!recordedBlob) return;

        const formData = new FormData();
        formData.append('audio', recordedBlob, 'recording.webm');

        try {
            await axios.post(`/api/events/${eventId}/upload-audio`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        } catch (error) {
            console.error('Ошибка отправки:', error);
        } finally {
            resetUI();
        }
    }

    /*-------------------------События---------------------------*/
    // Кнопка "Начать запись"
    startRecordBtn.addEventListener('click', startRecording);

    // Кнопка "Остановить запись"
    stopRecordBtn.addEventListener('click', stopRecording);
});
