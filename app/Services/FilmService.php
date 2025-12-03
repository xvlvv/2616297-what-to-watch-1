<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Film;
use App\Data\FilmsRequestData;
use App\Jobs\ProcessPendingFilm;
use App\IMDB\IMDBRepositoryInterface;
use App\Repositories\FilmRepositoryInterface;
use App\Repositories\GenreRepositoryInterface;

readonly class FilmService
{
    public function __construct(
        private FilmRepositoryInterface $filmRepository,
        private IMDBRepositoryInterface $IMDBRepository,
        private GenreRepositoryInterface $genreRepository,
    ) {
    }

    public function getAll(FilmsRequestData $DTO, bool $isAuthorized): array
    {
        return $this->filmRepository->getAllWithRating($DTO, $isAuthorized);
    }

    public function getById(int $id, ?int $userId): array
    {
        return $this->filmRepository->getById($id, $userId);
    }

    public function getSimilar(int $id, bool $isAuthorized): array
    {
        $genre = $this->filmRepository->getFilmGenre($id);

        $data = new FilmsRequestData(
            genre: $genre,
        );

        $returnCount = 4;

        return $this->filmRepository->getSimilar($data, $id, $returnCount);
    }

    public function createFilm(string $imdbId): int
    {
        $id = $this->filmRepository->create($imdbId);

        ProcessPendingFilm::dispatch($id);

        return $id;
    }

    public function updateWithIMDB(int $id): void
    {
        $imdbData = $this->IMDBRepository->findById($id);

        if (null === $imdbData) {
            throw new \Exception('Failed fetching IMDB data');
        }

        if (null !== $imdbData->genre) {
            $this->genreRepository->attachToGenre($id, $imdbData->genre);
        }

        $this->filmRepository->updateWithIMDB($id, $imdbData);
    }
}
