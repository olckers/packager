<?php

namespace olckerstech\packager\src\Commands;

use Illuminate\Console\GeneratorCommand;
use olckerstech\packager\src\traits\packager;

class PackagerRepositoryInterfaceMakeCommand extends GeneratorCommand
{
    use packager;
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
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packager:repository-interface
                    {name : Name of the Repository}
                    {--package= : Fully qualified package name the Repository-Interface belongs to}';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Interface';

    /**
     * Namespace modifier for this generator command instance
     *
     * @var string
     */
    protected $packageNameSpaceModifier = 'src\\Repositories\\Interfaces';

    /**
     * Execute the console command.
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $this->info('Creating Repository-Interface: ' . $this->argument('name'));
        if (!$this->parsePackage()) {
            $this->error('FAILED. Could not create Repository-Interface');
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
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/repositoryInterface.stub');
    }
}
