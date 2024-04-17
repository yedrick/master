<?php

namespace yedrick\Master\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class ModelMaster extends Command
{
    protected $signature = 'make:model-master {table}';
    protected $description = 'Generate a master model';

    protected $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    public function handle(){
        // $name = $this->argument('name');
        $table = $this->argument('table');
        $name= str_replace('_','-',Str::studly(Str::singular($this->argument('table'))));
        $this->createFile('Models', $name,$table);
    }

    protected function createFile($type,$name,$table){
        // Determine the file path based on type and name
        $filePath = app_path('Models' . DIRECTORY_SEPARATOR . $name . '.php');

        // Verificar si el archivo ya existe
        if ($this->filesystem->exists($filePath)) {
            $this->error($type . ' already exists.');
            return;
        }
        // Define a template path based on the type
        // $templatePath = __DIR__ . '/stubs/' . $type . '.stub';
        // debemos apuntar al vendro del master
        $templatePath = base_path('vendor/yedrick/master/src/stubs/'.$type.'.stub');
        // $templatePath = base_path('resources/views/stubs/' . $type . '.stub');

        // Load the template content
        $template = $this->filesystem->get($templatePath);

        // Replace placeholders in the template with the provided name
        // Replace placeholders in the template with the provided name
            $placeholders = [
                '{{name}}' => $name,
                '{{table}}' => $table,
            ];

        $content = str_replace(array_keys($placeholders), array_values($placeholders), $template);
        // $content = str_replace('{{name}}', $name, $template);

        // Determine the file path based on type and name
        $filePath = app_path('Models' . DIRECTORY_SEPARATOR . $name . '.php');

        // Write the content to the file
        $this->filesystem->put($filePath, $content);

        // Display a success message
        $this->info($type . ' Modelo Creado.');
    }
}
