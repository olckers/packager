<?php

namespace olckerstech\packager\src\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Str;
use olckerstech\packager\src\traits\packager;

class PackagerComponentMakeCommand extends GeneratorCommand
{
    use packager;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'packager:component';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new view component class for a package';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packager:component
            {name : Name of the Component}
            {--package= : Fully qualified package name the Component belongs to}
            {--force : Create the class even if the component already exists}
            {--inline : Create a component that renders an inline view}';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Component';

    /**
     * Namespace modifier for this generator command instance
     *
     * @var string
     */
    protected $packageNameSpaceModifier = 'src\\Components';

    /**
     * Execute the console command.
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $this->info('Creating Component: '.$this->argument('name'));
        if (!$this->parsePackage()) {
            $this->error('FAILED. Could not create Component');
            return false;
        }

        if (parent::handle() === false && ! $this->option('force')) {
            return false;
        }

        if (! $this->option('inline')) {
            $this->writeView();
        }

        if(!$this->copyAndDelete($this->argument('name'))){
            $this->error('FAILED. Could either not move and/or delete the created files');
            return false;
        }
        return true;
    }

    /**
     * Write the view for the component.
     *
     * @return void
     */
    protected function writeView()
    {
        $view = $this->getView();

        $path = base_path('packages\\'.str_replace('/', '\\', $this->packageNameSpace)).'src\\resources\\views\\components\\'.$view;
        //$path = resource_path('views').'/'.str_replace('.', '/', 'components.'.$view);

        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        file_put_contents(
            $path.'.blade.php',
            '<div>
    <!-- '.Inspiring::quote().' -->
</div>'
        );
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        if ($this->option('inline')) {
            return str_replace(
                'DummyView',
                "<<<'blade'\n<div>\n    ".Inspiring::quote()."\n</div>\nblade",
                parent::buildClass($name)
            );
        }

        return str_replace(
            'DummyView',
            'view(\'components.'.$this->getView().'\')',
            parent::buildClass($name)
        );
    }

    /**
     * Get the view name relative to the components directory.
     *
     * @return string view
     */
    protected function getView()
    {
        return collect(explode('/', $this->argument('name')))
            ->map(function ($part) {
                return Str::kebab($part);
            })
            ->implode('.');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/view-component.stub');
    }

}
