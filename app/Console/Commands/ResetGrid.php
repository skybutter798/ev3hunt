<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetGrid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-grid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Logic to reset the grid
        Grid::query()->update(['clicked' => false, 'user_id' => null]);
        // Add more reset logic as required
        $this->info('Grid has been reset!');
    }

}
