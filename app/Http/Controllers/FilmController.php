<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\FilmsRequestData;
use App\Data\FilmCreateData;
use App\Http\Responses\BaseResponse;
use App\IMDB\IMDBRepository;
use App\Http\Responses\SuccessResponse;
use App\Services\FilmService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FilmController extends Controller
{
    public function index(FilmsRequestData $data, FilmService $service, IMDBRepository $repository)
    {
        return new SuccessResponse(
            $service->getAll(
                $data,
                Auth::guard('sanctum')->user()?->hasRole('moderator') ?? false
            )
        );
    }

    public function show(int $id, FilmService $service): BaseResponse
    {
        return new SuccessResponse(
            $service->getById(
                $id,
                Auth::guard('sanctum')->user()?->id
            )
        );
    }

    public function create(FilmCreateData $data, FilmService $service): BaseResponse
    {
        return new SuccessResponse(
            $service->createFilm(
                $data->imdbId
            ),
            Response::HTTP_CREATED
        );
    }

    public function update(): BaseResponse
    {
    }
}
