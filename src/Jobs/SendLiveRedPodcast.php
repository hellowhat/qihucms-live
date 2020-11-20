<?php

namespace Qihucms\Live\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Qihucms\Live\Models\LiveRedLog;
use Illuminate\Support\Facades\Cache;
use App\Repositories\AccountRepository;
use App\Models\User;
use Yansongda\Pay\Pay;

class SendLiveRedPodcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (Cache::get('config_live_red_package_immediate')) {
            $user = User::find($this->data['user']);
            if(isset($user->open_id['wechat']['web'])){
                $pay = Pay::wechat([
                    'app_id' => config('qihu.wechat_mp_appid'),
                    'mch_id' => config('qihu.wechat_pay_mch_id'),
                    'key' => config('qihu.wechat_pay_key'),
                    'cert_client' => storage_path('cert/apiclient_cert.pem'),
                    'cert_key' => storage_path('cert/apiclient_key.pem'),
                ]);
                $pay->redpack($order = [
                    'mch_billno' => time().rand(10000,99999),
                    'send_name' => Cache::get('config_site_name'),
                    'total_num' => 1,
                    'total_amount' => $this->data['amount'] * 100,
                    're_openid' => $user->open_id['wechat']['web'],
                    'wishing' => '恭喜发财，大吉大利！',
                    'act_name' => '直播红包',
                    'remark' => '看直播，抢红包！'
                ]);
            }else{
                (new AccountRepository)->updateBalance($this->data['user'], $this->data['amount'], 'live_red_rob', ['red_id' => $this->data['red_id']]);
            }
        } else {
            (new AccountRepository)->updateBalance($this->data['user'], $this->data['amount'], 'live_red_rob', ['red_id' => $this->data['red_id']]);
        }
    }
}
