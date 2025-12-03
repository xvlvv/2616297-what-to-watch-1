<?php

declare(strict_types=1);

namespace App\IMDB;

use App\DTO\IMDBMovieDTO;

interface IMDBRepositoryInterface
{
    public function findById(int $id): ?IMDBMovieDTO;
}
