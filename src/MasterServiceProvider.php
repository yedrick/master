<?php

namespace yedrick\Master;

use Illuminate\Support\ServiceProvider;

class MasterServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function boot() {
        /* Publicar Elementos */
        $this->publishes([
            __DIR__ . '/config' => config_path()
        ], 'config');

        $this->publishes([
            __DIR__ . '/database/seeders/' => database_path('seeders'),
        ], 'seeders');

        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        /* Cargar Traducciones */
        $this->loadTranslationsFrom(__DIR__.'/lang', 'master');

        /* Cargar Vistas */
        $this->loadViewsFrom(__DIR__ . '/views', 'master');
    }


    public function register() {
        /* Registrar ServiceProvider Internos */
        $this->app->register('Maatwebsite\Excel\ExcelServiceProvider');

        /* Registrar Alias */
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Excel', 'Maatwebsite\Excel\Facades\Excel');
        $loader->alias('PDF', 'Barryvdh\Snappy\Facades\SnappyPdf');
        $loader->alias('FuncNode', '\yedrick\Master\App\Helpers\FuncNode');

        /* Comandos de Consola */
        $this->commands([
            \yedrick\Master\App\Console\Commands\Deploy::class,
            \yedrick\Master\App\Console\Commands\ModelMaster::class,
        ]);

        // $this->mergeConfigFrom(
        //     __DIR__ . '/config/solunes.php', 'solunes'
        // );
    }
    
}
