<?php

namespace Qihucms\Live\Console;

use App\Plugins\Plugin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class Upgrade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qihucms-live:upgrade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'qihucms live plugin upgrade command.';

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
        $plugin = new Plugin();
        $upgradeResult = $plugin->upgradePlugin('qihucms-live');
        switch ($upgradeResult) {
            case 401:
                $this->info('Extension file download failed. Please download it manually according to the tutorial.');
                break;
            case 400:
                $this->info('Check failed.');
                break;
            case 201:
                $this->info('Is currently the latest version.');
                break;
            default:
                $this->call('migrate');
                $this->info('Upgrade success.');
        }
    }
}