<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UploadAudioRequest;
use App\Jobs\RecognizeAudioJob;
use App\Models\Event;
use App\Repositories\Contract\EventRepositoryInterface;
use Illuminate\Http\JsonResponse;

class AudioController extends Controller
{
    public function __construct(
        private readonly EventRepositoryInterface $db,
    ) {}

    public function upload(UploadAudioRequest $request, Event $event): JsonResponse
    {
        $file = $request->file('audio');
        $path = $file->store("audio/{$event->id}");

        $this->db->saveAudioPath($path, $event);

        dispatch(new RecognizeAudioJob($event));

        return response()->json(['message' => 'Аудио сохранено', 'path' => $path]);
    }
}
