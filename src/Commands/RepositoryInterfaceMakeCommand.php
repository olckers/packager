<?php

namespace olckerstech\packager\src\Commands;

use Illuminate\Console\GeneratorCommand;

class RepositoryInterfaceMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'packager:repository-interface';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class interface';
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Interface';
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../resources/stubs/repositoryInterface.stub';//__DIR__ . '/stubs/repositoryInterface.stub';
    }
    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Repositories\Interfaces';
    }
}
