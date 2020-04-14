<?php

namespace olckerstech\packager\src\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use olckerstech\packager\src\traits\packager;

class PackagerTestMakeCommand extends GeneratorCommand
{
    use packager;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'packager:test';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'packager:test
        {name : The name of the Test}
        {--package= : Fully qualified package name the Resource belongs to}
        {--unit : Create a unit test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new test class for a package';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Test';

    /**
     * Namespace modifier for this generator command instance
     *
     * @var string
     */
    protected $packageNameSpaceModifier = 'tests';

    /**
     * Execute the console command.
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $this->info('Creating Test: ' . $this->argument('name'));
        if (!$this->parsePackage()) {
            $this->error('FAILED. Could not create Test');
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
        return $this->option('unit')
            ? $this->resolveStubPath('/stubs/test.unit.stub')
            : $this->resolveStubPath('/stubs/test.stub');
    }


    /**
     * Get the destination class path.
     *
     * @param string $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);
        return str_replace('\\', '/', 'App\\' . $name . '.php');
    }

    /**
     * Copies the created files from the app directory to packages. Files in
     * app directory is deleted after successful copy.
     *
     * @return bool
     */
    public function copyAndDelete($name = false)
    {
        if ($name) {
            $from = base_path(str_replace('\\', '/', $this->getDefaultNamespace('App') . '/' . $name . '.php'));
            $to = $this->str_replace_once('App', 'packages', $from);
            $to = $this->str_replace_once('/temp_packages', '', $to);
            $package_dir = $this->str_replace_once('/' . $name . '.php', '', $to);
            /*
             * Override to provide the Feature / Unit namespace
             */
            if ($this->option('unit')) {
                $package_dir .= '/Unit';

            } else {
                $package_dir .= '/Feature';
            }
            $to = $package_dir . '/' . $name . '.php';

            $this->line('Moving created files to package...');
            if (!file_exists($package_dir)) {
                if (!mkdir($package_dir, 0777, true) && !is_dir($package_dir)) {
                    //throw new \RuntimeException(sprintf('Directory "%s" was not created', $package_dir));
                }
            }
            copy($from, $to);
            $this->line('Deleting temporary file...');
            unlink($from);
            $this->line('Deleting temporary directory: ' . base_path('App/temp_packages'));
            $this->rrmdir(base_path('App/temp_packages'));
            $this->line('Done');
        }
        return true;
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param string $stub
     * @param string $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $searches = [
            ['DummyNamespace', 'DummyRootNamespace', 'NamespacedDummyUserModel', 'DummyPackageNameSpace'],
            ['{{ namespace }}', '{{ rootNamespace }}', '{{ namespacedUserModel }}', '{{ packageNameSpace }}'],
            ['{{namespace}}', '{{rootNamespace}}', '{{namespacedUserModel}}', '{{packageNameSpace}}'],
        ];

        /*
         * Override to provide the Feature / Unit namespace
         */
        if ($this->option('unit')) {
            $tempNameSpace = $this->packageNameSpace . '\\' . $this->packageNameSpaceModifier . '\Unit';
        } else {
            $tempNameSpace = $this->packageNameSpace . '\\' . $this->packageNameSpaceModifier . '\Feature';
        }

        foreach ($searches as $search) {
            $stub = str_replace(
                $search,
                [$this->getNamespace($name), $this->rootNamespace(), $this->userProviderModel(), $tempNameSpace],
                $stub
            );
        }

        return $this;
    }

}
