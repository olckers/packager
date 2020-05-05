<?php

namespace olckerstech\packager\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use olckerstech\packager\traits\packager;

class PackagerListenerMakeCommand extends GeneratorCommand
{
    use packager;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'packager:listener';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new event listener class for a package';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packager:listener
            {name : Name of the Listener}
            {--package= : Fully qualified package name the Listener belongs to}
            {--event= : The event class being listened for}
            {--queued : Indicates the event listener should be queued}';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Listener';

    /**
     * Namespace modifier for this generator command instance
     *
     * @var string
     */
    protected $packageNameSpaceModifier = 'src\\Listeners';

    /**
     * Execute the console command.
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $this->info('Creating Listener: ' . $this->argument('name'));
        if (!$this->parsePackage()) {
            $this->error('FAILED. Could not create Listener');
            return false;
        }

        parent::handle();

        if (!$this->copyAndDelete($this->argument('name'))) {
            $this->error('FAILED. Could either not move and/or delete the created files');
            return false;
        }
        return true;

    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $event = $this->option('event');

        if (!Str::startsWith($event, [
            $this->laravel->getNamespace(),
            'Illuminate',
            '\\',
        ])) {
            $event = str_replace('/', '\\', $this->packageNameSpace) . '\\src\\Events\\' . $event;
        }

        $stub = str_replace(
            'DummyEvent', class_basename($event), parent::buildClass($name)
        );

        return str_replace(
            'DummyFullEvent', trim($event, '\\'), $stub
        );
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('queued')) {
            return $this->option('event')
                ? $this->resolveStubPath('/stubs/listener-queued.stub')
                : $this->resolveStubPath('/stubs/listener-queued-duck.stub');
        }

        return $this->option('event')
            ? $this->resolveStubPath('/stubs/listener.stub')
            : $this->resolveStubPath('/stubs/listener-duck.stub');
    }

    /**
     * Determine if the class already exists.
     *
     * @param string $rawName
     * @return bool
     */
    protected function alreadyExists($rawName)
    {
        return class_exists($rawName);
    }

}
