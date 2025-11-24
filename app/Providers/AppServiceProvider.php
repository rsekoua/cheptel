<?php

namespace App\Providers;

use App\Models\Animal;
use App\Models\DetailPreparation;
use App\Models\MouvementAliment;
use App\Models\PreparationAliment;
use App\Observers\AnimalObserver;
use App\Observers\DetailPreparationObserver;
use App\Observers\MouvementAlimentObserver;
use App\Observers\PreparationAlimentObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Animal::observe(AnimalObserver::class);
        MouvementAliment::observe(MouvementAlimentObserver::class);
        DetailPreparation::observe(DetailPreparationObserver::class);
        PreparationAliment::observe(PreparationAlimentObserver::class);
    }
}
