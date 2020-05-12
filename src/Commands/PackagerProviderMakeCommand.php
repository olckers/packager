<?php

namespace olckerstech\packager\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use olckerstech\packager\traits\packager;

class PackagerProviderMakeCommand extends GeneratorCommand
{
    use packager;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'packager:provider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service provider class for a package';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packager:provider
                    {name : Name of the Service Provider}
                    {--model= : Model name}
                    {--package= : Fully qualified package name the Service Provider belongs to}';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Provider';

    /**
     * Namespace modifier for this generator command instance
     *
     * @var string
     */
    protected $packageNameSpaceModifier = 'src';

    /**
     * Execute the console command.
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $this->info('Creating Service Provider: ' . $this->argument('name'));
        if (!$this->parsePackage()) {
            $this->error('FAILED. Could not create Service Provider');
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
        return $this->resolveStubPath('/stubs/provider.stub');
    }

}
