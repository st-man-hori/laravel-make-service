<?php

namespace StManHori\LaravelMakeService;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeServiceCommand extends Command
{
    protected $signature = 'make:service {name : create service class}';
    protected $description = 'Create a new service class and interface';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function handle()
    {
        $name = $this->argument('name');

        $interfacePath = app_path('Services/' . $name . 'Interface.php');
        $classPath = app_path('Services/' . $name . '.php');

        $this->createDirectory($interfacePath);
        $this->createDirectory($classPath);

        $this->files->put($interfacePath, $this->buildInterface($name));
        $this->files->put($classPath, $this->buildClass($name));

        $this->info('Service created successfully.');
        $this->info("Register the service in AppServiceProvider using:");
        $this->line("\$this->app->bind(App\Services\\{$name}Interface::class, App\Services\\{$name}::class);");
    }

    protected function createDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true, true);
        }
    }

    protected function buildInterface($name)
    {
        $stub = $this->files->get(__DIR__.'/stubs/service-interface.stub');

        $stub = $this->replaceNamespace($stub, $name);
        $stub = $this->replaceInterface($stub, $name);
        
        return $stub;
    }
    
    protected function buildClass($name)
    {
        $stub = $this->files->get(__DIR__.'/stubs/service-class.stub');
        
        $stub = $this->replaceNamespace($stub, $name);
        $stub = $this->replaceInterface($stub, $name);
        $stub = $this->replaceClass($stub, $name);
    
        return $stub;
    }
    


    protected function replaceNamespace($stub, $name)
    {
        return str_replace('DummyNamespace', 'App\Services', $stub);
    }

    protected function replaceInterface($stub, $name)
    {
        return str_replace('DummyInterface', $name . 'Interface', $stub);
    }
    
    protected function replaceClass($stub, $name)
    {
        return str_replace('DummyClass', $name, $stub);
    }

}
