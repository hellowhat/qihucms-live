<?php

namespace Qihucms\Live\Console;

use App\Plugins\Plugin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qihucms-live:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'qihucms live plugin install command.';

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

        $this->call('migrate');
        $root = DB::table('admin_menu')->insertGetId([
            'title' => '直播',
            'parent_id' => 46,
            'order' => DB::table('admin_menu')->count() + 1,
            'icon' => 'fa-video-camera',
            'uri' => null
        ]);
        DB::table('admin_menu')->insert([
            'title' => '直播配置',
            'parent_id' => $root,
            'order' => DB::table('admin_menu')->count() + 1,
            'icon' => 'fa-cog',
            'uri' => 'plugins/qihucms/live/config'
        ]);
        DB::table('admin_menu')->insert([
            'title' => '直播分类',
            'parent_id' => $root,
            'order' => DB::table('admin_menu')->count() + 1,
            'icon' => 'fa-columns',
            'uri' => 'plugins/qihucms/live/categories'
        ]);
        DB::table('admin_menu')->insert([
            'title' => '直播列表',
            'parent_id' => $root,
            'order' => DB::table('admin_menu')->count() + 1,
            'icon' => 'fa-bars',
            'uri' => 'plugins/qihucms/live/list'
        ]);

        $plugin->setPluginVersion('qihucms-live', 100);
        $this->info('install successed.');
    }
}
