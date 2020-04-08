<?php

namespace olckerstech\core\src\Commands;

use Illuminate\Console\Command;

class CoreDeleteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'core:delete
                            {--before : Delete files before install as specified in manifest}
                            {--after : Delete files after install as specified in manifest}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Core package: Delete files.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $options = $this->options();
        $this->info('Preparing to delete files...');

        //Parse options
        if ($options['before']) {
            $option_key = 'delete_before';
        } elseif ($options['after']) {
            $option_key = 'delete_after';
        } else {
            $option_key = false;
        }

        //Delete files

        if ($option_key) {
            //Parse file list
            $files = config('tmp_core_install.'.$option_key);

            $parse = $files;
            $count = 0;
            $this->line('Parsing files...');
            $parse_bar = $this->output->createProgressBar(count($parse));
            foreach($parse as $file){
                if (strpos($file, '*') !== false) {
                    $dir = glob($file);
                    //unset($files[$count]);
                    foreach($dir as $item){
                        $files[] = $item;
                    }
                }
                $parse_bar->advance();
                $count++;
            }
            $parse_bar->finish();
            $this->line(' Done');
            $file = null; //reset file counter

            $headers = ['File Path', 'Result'];
            $table = [];
            $this->line('Deleting files...');
            $file_bar = $this->output->createProgressBar(count($files));
            foreach($files as $file){
                $file_path = base_path(). '/' .$file;
                if(file_exists($file_path)){
                    unlink($file_path);
                    $table[] = [$file, 'Deleted'];
                }else{
                    $table[] = [$file, 'Not Found'];
                }
                $file_bar->advance();
            }
            $file_bar->finish();
            $this->line(' Done');
            $this->table($headers, $table);


        }else{
            $this->line('No list provided... 0 files deleted');
        }
        $this->line('Note: Remember to check output above for any errors or inconsistencies');

        $this->info('File deletions completed...');

    }
}
