<?php

namespace olckerstech\packager\src\Commands;

use Illuminate\Console\GeneratorCommand;
use olckerstech\packager\src\traits\packager;

class PackagerMailMakeCommand extends GeneratorCommand
{
    use packager;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'packager:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new email class for a package';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packager:mail
            {name : Name of the Mail}
            {--package= : Fully qualified package name the Mail belongs to}
            {--markdown= : Create a new Markdown template for the Mail}
            {--force : Create the class even if the mail item already exists}';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Mail';

    /**
     * Namespace modifier for this generator command instance
     *
     * @var string
     */
    protected $packageNameSpaceModifier = 'src\\Mails';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $this->info('Creating Mail: ' . $this->argument('name'));
        if (!$this->parsePackage()) {
            $this->error('FAILED. Could not create Mail');
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
        $path = base_path('packages/' . str_replace('\\', '/', $this->packageNameSpace) . '/resources/views/Mails/' . str_replace('.', '/', $this->option('markdown'))) . '.blade.php';

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
            ? $this->resolveStubPath('/stubs/markdown-mail.stub')
            : $this->resolveStubPath('/stubs/mail.stub');
    }

}
