<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Events\PeriodoFeriasGozado;
use App\Listeners\{AtualizarFlagsFuncionario, GerarProximoPeriodoAquisitivo};

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */

    protected $listen = [
        PeriodoFeriasGozado::class => [
            AtualizarFlagsFuncionario::class,
            GerarProximoPeriodoAquisitivo::class
        ],
    ];
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
    * Determine se os eventos e os ouvintes devem ser descobertos automaticamente.
    *
    */

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
