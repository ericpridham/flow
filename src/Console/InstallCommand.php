<?php


namespace EricPridham\Flow\Console;


use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'flow:install {--force}';
    protected $description = 'Publishes the flow assets into your application';

    public function handle()
    {
        $this->comment('Publishing config ...');
        $this->call('vendor:publish', ['--tag' => 'flow.config', '--force' => $this->option('force')]);

        $this->line('');

        $this->comment('Publishing assets ...');
        $this->call('vendor:publish', ['--tag' => 'flow.views', '--force' => $this->option('force')]);
    }
}
