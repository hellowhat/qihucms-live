<?php

namespace Qihucms\Live\Admin\Forms;

use App\Plugins\Plugin;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Live extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '直播配置';

    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        $data = $request->all();
        $message = '保存成功';

        $plugin = new Plugin();

        // 授权激活
        if ($request->has('qihucms-liveLicenseKey') && Cache::store('file')->get('qihucms-liveLicenseKey') != $data['qihucms-liveLicenseKey']) {
            $result = $plugin->registerPlugin('qihucms-live', $data['qihucms-liveLicenseKey']);
            if ($result) {
                $message .= '；授权激活成功';
            } else {
                $message .= '；授权激活失败';
            }
        }

        unset($data['qihucms-liveLicenseKey']);

        foreach ($data as $key => $value) {
            Cache::put($key, $value);
        }

        admin_success('保存成功');

        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('qihucms-liveLicenseKey', '插件授权')->help('购买授权地址：<a href="http://ka.qihucms.com/product/" target="_blank">http://ka.qihucms.com</a>');
        $this->textarea('config_live_notice', '公告内容')->help('进入直播间时提示，多公告条请换行。');
        $this->select('config_live_authentication', '开启直播认证')->options(['关闭', '开启'])->help('需要用户通过认证后才能开启直播');
        $this->number('config_live_authentication_id', '认证ID')->help('请在"用户管理"=>"认证项目"获取认证项目的ID');

        $this->divider('腾讯云直播配置');
        $this->text('config_live_tencent_secretid', '腾讯SecretId')
            ->help('<a href="https://cloud.tencent.com/document/product/267/13551" target="_blank">点击查看开通流程</a>');
        $this->text('config_live_tencent_secretkey', '腾讯SecretKey');
        $this->text('config_live_tencent_pushkey', '推流防盗链Key');
        $this->text('config_live_tencent_pullkey', '播放防盗链Key');
        $this->text('config_live_tencent_pushurl', '直播推流域名');
        $this->text('config_live_tencent_pullurl', '直播播放域名');
        $this->text('config_live_tencent_notify_key', '回调秘钥')->help('直播录制回调地址：'.route('live.wap.record').'直播推流毁掉地址：'.route('live.wap.start'));
        $this->select('config_live_h5_push', '开启H5推流')->options(['关闭', '开启'])->help('h5推流仅兼容android手机，苹果手机不显示');
        $this->text('config_live_tencent_sdkappid', '实时音视频SDKAppID');
        $this->text('config_live_tencent_usersigkey', '实时音视频UserSigKey');

        $this->divider('虚拟数据');
        $this->select('config_live_fictitious_peoples', '开启虚拟在线人数')->options(['关闭', '开启']);
        $this->number('config_live_fictitious_base_mix', '随机在线人数最小值');
        $this->number('config_live_fictitious_base_max', '随机在线人数最大值')->help('房间在线人数基数将在最小值和最大值之间随机生成');
        $this->text('config_live_fictitious_peoples_multiple', '实际在线人数倍数')->help('房间实际在线人数x倍数，可以是小数。0表示显示真实数据');
        $this->select('config_live_fictitious_actity', '开启虚拟互动')->options(['关闭', '开启'])->help('开启后将从平台随机挑选用户模拟浏览直播间、刷礼物(仅显示送礼效果，没有实际意义)。开启后将增加服务器压力。');
        $this->text('config_live_fictitious_user', '虚拟用户用户名前缀')->help('留空则表示从所有用户中随机。');
        $this->text('config_live_fictitious_gift', '虚拟送礼礼物ID')->help('多个礼物ID请用英文逗号隔开，留空则表示从所有礼物中随机。');
        $this->number('config_live_fictitious_actity_space', '虚拟互动最大时间间隔')->help('单位:秒，推荐配置30-60，间隔时间太短容易暴露，而且会增加服务器压力。');
        $this->textarea('config_live_fictitious_actity_content', '虚拟互动文字消息内容')->help('请用英文逗号隔开,系统会随机抽取一条显示。');

        $this->divider('互动服务配置');
        $this->text('config_live_socket_domain', '服务域名');
        $this->text('config_live_socket_appid', 'appId');
        $this->text('config_live_socket_key', 'Key');
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        return [
            'qihucms-liveLicenseKey' => Cache::store('file')->get('qihucms-liveLicenseKey'),
            'config_live_notice' => Cache::get('config_live_notice'),

            'config_live_authentication' => Cache::get('config_live_authentication'),
            'config_live_authentication_id' => Cache::get('config_live_authentication_id'),
            'config_live_h5_push' => Cache::get('config_live_h5_push'),
            'config_live_tencent_sdkappid' => Cache::get('config_live_tencent_sdkappid'),
            'config_live_tencent_usersigkey' => Cache::get('config_live_tencent_usersigkey'),

            'config_live_tencent_secretid' => Cache::get('config_live_tencent_secretid'),
            'config_live_tencent_secretkey' => Cache::get('config_live_tencent_secretkey'),
            'config_live_tencent_pushkey' => Cache::get('config_live_tencent_pushkey'),
            'config_live_tencent_pullkey' => Cache::get('config_live_tencent_pullkey'),
            'config_live_tencent_pushurl' => Cache::get('config_live_tencent_pushurl'),
            'config_live_tencent_pullurl' => Cache::get('config_live_tencent_pullurl'),
            'config_live_tencent_notify_key' => Cache::get('config_live_tencent_notify_key'),

            'config_live_fictitious_base_mix' => Cache::get('config_live_fictitious_base_mix'),
            'config_live_fictitious_base_max' => Cache::get('config_live_fictitious_base_max'),
            'config_live_fictitious_peoples' => Cache::get('config_live_fictitious_peoples'),
            'config_live_fictitious_peoples_multiple' => Cache::get('config_live_fictitious_peoples_multiple'),
            'config_live_fictitious_actity' => Cache::get('config_live_fictitious_actity'),
            'config_live_fictitious_user' => Cache::get('config_live_fictitious_user'),
            'config_live_fictitious_gift' => Cache::get('config_live_fictitious_gift'),
            'config_live_fictitious_actity_space' => Cache::get('config_live_fictitious_actity_space'),
            'config_live_fictitious_actity_content' => Cache::get('config_live_fictitious_actity_content'),
            'config_live_socket_domain' => Cache::get('config_live_socket_domain'),
            'config_live_socket_appid' => Cache::get('config_live_socket_appid'),
            'config_live_socket_key' => Cache::get('config_live_socket_key')
        ];
    }
}