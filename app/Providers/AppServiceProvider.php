<?php

namespace App\Providers;

use App\Models\User;
use GuzzleHttp\Client;
use App\Models\Comment;
use App\IMDB\IMDBRepository;
use App\Repositories\FilmRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Gate;
use Psr\Http\Client\ClientInterface;
use App\Repositories\GenreRepository;
use App\IMDB\IMDBRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\FilmRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\GenreRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(FilmRepositoryInterface::class, FilmRepository::class);
        $this->app->bind(IMDBRepositoryInterface::class, IMDBRepository::class);
        $this->app->bind(GenreRepositoryInterface::class, GenreRepository::class);
        $this->app->bind(ClientInterface::class, Client::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Gate::define('manage-comment', function (User $user, Comment $comment) {
            if ($user->hasRole('moderator') || $user->id === $comment->user?->id) {
                return true;
            }

            return false;
        });
    }
}
