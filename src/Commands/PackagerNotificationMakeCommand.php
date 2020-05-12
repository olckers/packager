<?php

namespace olckerstech\packager\Commands;

use Illuminate\Console\GeneratorCommand;
use olckerstech\packager\traits\packager;

class PackagerNotificationMakeCommand extends GeneratorCommand
{
    use packager;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'packager:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new notification class for a package';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packager:notification
            {name : Name of the Notification}
            {--package= : Fully qualified package name the Notification belongs to}
            {--markdown= : Create a new Markdown template for the notification}
            {--model= : Model name}
            {--force : Create the class even if the notification already exists}';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Notification';

    /**
     * Namespace modifier for this generator command instance
     *
     * @var string
     */
    protected $packageNameSpaceModifier = 'src\\Notifications';

    /**
     * Execute the console command.
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $this->info('Creating Notification: ' . $this->argument('name'));
        if (!$this->parsePackage()) {
            $this->error('FAILED. Could not create Notification');
            return false;
        }

        if (parent::handle() === false && !$this->option('force')) {
            return false;
        }

        if ($this->option('markdown')) {
            $this->writeMarkdownTemplate();
        }

        if (!$this->copyAndDelete($this->argument('name'))) {
            $this->error('FAILED. Could either not move and/or delete the created files');
            return false;
        }
        return true;

    }

    /**
     * Write the Markdown template for the mailable.
     *
     * @return void
     */
    protected function writeMarkdownTemplate()
    {
        $path = base_path('packages/' . str_replace('\\', '/', $this->packageNameSpace) . '/resources/views/Notifications/' . str_replace('.', '/', $this->option('markdown'))) . '.blade.php';

        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true);
        }

        $this->files->put($path, file_get_contents($this->resolveStubPath('/stubs/markdown.stub')));
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
        $class = parent::buildClass($name);

        if ($this->option('markdown')) {
            $class = str_replace('DummyView', $this->option('markdown'), $class);
        }

        return $class;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->option('markdown')
            ? $this->resolveStubPath('/stubs/markdown-notification.stub')
            : $this->resolveStubPath('/stubs/notification.stub');
    }


}
