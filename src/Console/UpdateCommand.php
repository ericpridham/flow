<?php


namespace EricPridham\Flow\Console;


use Illuminate\Console\Command;

class UpdateCommand extends Command
{
    protected $signature = 'flow:update';
    protected $description = 'Force pulls the flow assets into your application';

    public function handle()
    {
        $this->comment('Publishing assets ...');
        $this->call('vendor:publish', ['--tag' => 'flow.views', '--force' => true]);
    }
}
