<?php

namespace olckerstech\packager\Commands;

use Illuminate\Console\GeneratorCommand;

class RepositoryMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:repository';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../resources/stubs/repository_for_make_command.stub';//__DIR__ . '/stubs/repository.stub';
    }
    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Repositories';
    }
}
