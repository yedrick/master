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
        Artisan::call('migrate:reset');
        $this->info('Migrate...');
        Artisan::call('migrate', ['--path'=>'/vendor/yedrick/master/src/database/migrations']);
        Artisan::call('migrate', ['--path'=>'/database/migrations']);

        $this->info('Migrate:refresh...');
        // llamar a la seeder apra vaciar tablas
        Artisan::call('db:seed', ['--class' => 'TruncateNodeSeeder']);
        // Artisan::call('migrate:fresh');
        \yedrick\Master\App\Helpers\FuncNode::getTables();
        $this->info('Creacion de modelos...');
        \yedrick\Master\App\Helpers\FuncNode::createModels();
        $this->info('Creacion de fileds...');
        \yedrick\Master\App\Helpers\FuncNode::creationNodeFields();
        if(config('master.deploy_seed')){
            $this->info('Ejecutando el seeder...');
            Artisan::call('db:seed', ['--class' => 'MasterSeeder']);
        }
        //importar datos de excel
        // \Func::importExcel();

        $this->info('Deploy ejecutado.');
    }
}
