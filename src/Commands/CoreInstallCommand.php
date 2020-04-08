<?php

namespace olckerstech\core\src\Commands;

use Illuminate\Console\Command;

class CoreInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'core:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Core Package components';

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
        $this->info('Starting Core Package install...');

        $config = config('tmp_core_install');

        if (isset($config)) {
            $this->comment('Using "' . $config['provider'] . '" as the service provider...');

            $this->line('Running install commands...');

            $commands = collect(array_values(config('tmp_core_install.artisan_commands')));

            if(config('tmp_core_install.silent') === true) {
                $bar = $this->output->createProgressBar(count($commands));
            }

            foreach ($commands as $command) {
                /*
                 *  Array Keys:
                 *  0 => Command
                 *  n => additional options
                 */
                $collection = collect(explode(' ', $command));
                $options_size = count($collection)-1;

                $build_command = $collection[0];

                $options_array = [];
                if($options_size !== 0) {
                    for ($i = 1; $i <= $options_size; $i++) {
                        $options_array = $this->parseOptions($collection[$i]);
                    }
                    if(config('tmp_core_install.silent') === true) {
                        $this->callSilent($build_command, $options_array);
                    }else{
                        $this->call($build_command, $options_array);
                    }
                }else{
                    if(config('tmp_core_install.silent') === true) {
                        $this->callSilent($build_command);
                    }else{
                        $this->call($build_command);
                    }
                }

                if(config('tmp_core_install.silent') === true) {
                    $bar->advance();
                }
            }
            if(config('tmp_core_install.silent') === true) {
                $bar->finish();
                $this->line(' Done');
            }

            $this->info('Completed Core Package install. Please check the output above for any errors.');
        } else {
            $this->error('Initialize this package by running "core:init". All operations aborted');
        }

    }

    public function parseOptions($option)
    {
        //dd($option);
        $return = [];
        if (strpos($option, '=') !== false) {
            $parse = explode('=', $option);
            if ($parse[0] === '--tag') {
                $return[$parse[0]] = $parse[1];
            } else if ($parse[1] === 'provider') {
                $return[$parse[0]] = config('tmp_core_install.provider');
            }
        } else {
            $return[$option] = true;
        }
        return $return;
    }
}
