<?php

namespace App\Services\Repositories;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(StudentRepository::class);
        $this->app->singleton(StudentResponseRepository::class);
        $this->app->singleton(AssessmentRepository::class);
        $this->app->singleton(QuestionRepository::class);
    }
}
