<?php

namespace yedrick\Master\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;


class Deploy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(){
        $this->info('Iniciando...');
        Artisan::call('config:cache');
        $this->info('Migrate...');
        Artisan::call('migrate:fresh');
        \Func::getTables();
        $this->info('Creacion de modelos...');
        \Func::createModels();
        $this->info('Creacion de fileds...');
        \Func::creationNodeFields();
        $this->info('Ejecutando el seeder...');
        Artisan::call('db:seed', ['--class' => 'MasterSeeder']);
        //importar datos de excel
        // \Func::importExcel();

        $this->info('Deploy ejecutado.');
    }
}
