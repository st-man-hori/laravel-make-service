<?php

namespace StManHori\LaravelMakeService;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeServiceCommand extends Command
{
    protected $signature = 'make:service {name : create service class}';
    protected $description = 'Create a new service class and interface';

    protected $files;

    /**
     * The constructor initializes the Filesystem instance.
     *
     * @param Filesystem $files The Filesystem instance.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * This method handles the command execution.
     */
    public function handle()
    {
        $name = $this->argument('name');

        $name = str_replace('/', '\\', $name);

        $interfacePath = app_path('Services/' . str_replace('\\', '/', $name) . 'Interface.php');
        $classPath = app_path('Services/' . str_replace('\\', '/', $name) . '.php');

        $this->createDirectory(dirname($interfacePath));
        $this->createDirectory(dirname($classPath));

        $this->files->put($interfacePath, $this->buildInterface($name));
        $this->files->put($classPath, $this->buildClass($name));

        $this->info('Service created successfully.');
        $this->info("Register the service in AppServiceProvider using:");
        $this->line("\$this->app->bind(\App\Services\\{$name}Interface::class, \App\Services\\{$name}::class);");
    }

    /**
     * Replaces the namespace in the provided stub with the correct namespace.
     *
     * @param string $stub The stub with the namespace placeholder.
     * @param string $name The name of the service class.
     * @return string The updated stub with the correct namespace.
     */
    protected function replaceNamespace(string $stub, string $name)
    {
        $namespace = 'App\Services';
        if (strpos($name, '\\') !== false) {
            $subNamespace = substr($name, 0, strrpos($name, '\\'));
            $namespace .= '\\' . $subNamespace;
        }
        return str_replace('DummyNamespace', $namespace, $stub);
    }

    /**
     * Creates a directory with the specified path if it doesn't exist.
     *
     * @param string $path The path of the directory to create.
     */
    protected function createDirectory(string $path)
    {
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true, true);
        }
    }


    /**
     * Builds the service interface using the provided stub.
     *
     * @param string $name The name of the service class.
     * @return string The content of the generated interface.
     */
    protected function buildInterface(string $name)
    {
        $stub = $this->files->get(__DIR__.'/stubs/service-interface.stub');

        $stub = $this->replaceNamespace($stub, $name);
        $stub = $this->replaceInterface($stub, $name);
        
        return $stub;
    }
    
    /**
     * Builds the service class using the provided stub.
     *
     * @param string $name The name of the service class.
     * @return string The content of the generated class.
     */
    protected function buildClass(string $name)
    {
        $stub = $this->files->get(__DIR__.'/stubs/service-class.stub');
        
        $stub = $this->replaceNamespace($stub, $name);
        $stub = $this->replaceInterface($stub, $name);
        $stub = $this->replaceClass($stub, $name);
    
        return $stub;
    }

    /**
     * Replaces the interface name in the provided stub with the correct interface name.
     *
     * @param string $stub The stub with the interface name placeholder.
     * @param string $name The name of the service class.
     * @return string The updated stub with the correct interface name.
     */
    protected function replaceInterface(string $stub, string $name)
    {
        $interfaceName = substr($name, strrpos($name, '\\') + 1) . 'Interface';
        return str_replace('DummyInterface', $interfaceName, $stub);
    }

    /**
     * Replaces the class name in the provided stub with the correct class name.
     *
     * @param string $stub The stub with the class name placeholder.
     * @param string $name The name of the service class.
     * @return string The updated stub with the correct class name.
     */
    protected function replaceClass(string $stub, string $name)
    {
        $className = substr($name, strrpos($name, '\\') + 1);
        return str_replace('DummyClass', $className, $stub);
    }

}
