<?php

declare(strict_types=1);

namespace App\IMDB;

use App\Models\Film;
use App\DTO\IMDBMovieDTO;
use Exception;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class IMDBRepository implements IMDBRepositoryInterface
{
    public function __construct(
        private ClientInterface $client,
    ) {
    }

    public function findById(int $id): ?IMDBMovieDTO
    {
        $imdbId = Film::findOrFail($id)->imdb_id;

        $request = new Request('GET', "https://api.imdbapi.dev/titles/$imdbId");

        try {
            $response = $this->client->sendRequest($request);

            if (Response::HTTP_OK !== $response->getStatusCode()) {
                throw new Exception("IMDB API responded with code {$response->getStatusCode()} for id: $imdbId");
            }

            $data = json_decode((string) $response->getBody(), flags: JSON_THROW_ON_ERROR);

            if (!is_object($data)) {
                throw new Exception('Response is not an object, therefore cannot used to retrieve data');
            }
        } catch (Throwable $e) {
            Log::info("Error getting film info from IMDB API: message {$e->getMessage()}", );

            return null;
        }

        $filmTitle = $data->primaryTitle ?? null;

        $genre = null;

        if (isset($data->genres) && is_array($data->genres)) {
            $genre = $data->genres[0] ?? null;
        }

        $startYear = $data->startYear ?? null;

        $description = $data->plot ?? null;

        $director = null;

        if (isset($data->directors[0]) && is_array($data->directors) && is_object($data->directors[0])
        ) {
            $director = $data->directors[0]->displayName ?? null;
        }

        $runTime = $data->runtimeSeconds ?? null;

        if (null !== $runTime) {
            $runTime = $runTime / 60;
        }

        return new IMDBMovieDTO(
            $filmTitle,
            $genre,
            $startYear,
            $description,
            $director,
            $runTime
        );
    }
}
