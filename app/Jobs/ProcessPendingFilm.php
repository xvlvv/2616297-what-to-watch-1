<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Film;
use App\Models\Genre;
use App\Services\FilmService;
use App\IMDB\IMDBRepository;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessPendingFilm implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private int $filmId
    ) {

    }

    /**
     * Execute the job.
     */
    public function handle(FilmService $service): void
    {
        $service->updateWithIMDB($this->filmId);
    }
}
