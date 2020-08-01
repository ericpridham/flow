<?php

namespace EricPridham\Flow\Console;

use Carbon\Carbon;
use EricPridham\Flow\Recorder\DatabaseRecorder;
use Illuminate\Console\Command;

class PurgeCommand extends Command
{
    protected $signature = 'flow:purge {days} {--dry}';
    protected $description = 'Removes old flow records';

    public function handle(DatabaseRecorder $recorder): void
    {
        $days = Carbon::now()->subDays($this->argument('days'))->setTime(0, 0, 0);
        $query = $recorder->retrieve(null, $days);

        $count = $query->count();
        if ($this->option('dry')) {
            $this->comment($count . ' records would have been purged');
            return;
        }

        $this->line($count . ' records found');
        if ($this->confirm('Are you sure?')) {
            $deleteCount = $query->delete();
            $this->comment($deleteCount . ' records purged');
        } else {
            $this->comment('Purge canceled!');
        }
    }
}
