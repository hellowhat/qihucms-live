<?php

namespace Qihucms\Live\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Live\V20180801\LiveClient;
use TencentCloud\Live\V20180801\Models\DescribeLiveStreamStateRequest;

use TencentCloud\Vod\V20180717\VodClient;
use TencentCloud\Vod\V20180717\Models\SearchMediaRequest;
use TencentCloud\Vod\V20180717\Models\DeleteMediaRequest;

class TencentLiveService
{
    private $cred;
    private $httpProfile;
    private $clientProfile;

    public function __construct()
    {
        $this->cred = new Credential(Cache::get('config_live_tencent_secretid'), Cache::get('config_live_tencent_secretkey'));
        $this->httpProfile = new HttpProfile();
        $this->clientProfile = new ClientProfile();
    }
    /**
     * 获取推流地址
     * 如果不传key和过期时间，将返回不含防盗链的url
     * @param domain 您用来推流的域名
     *        streamName 您用来区别不同推流地址的唯一流名称
     *        key 安全密钥
     *        time 过期时间 sample 2016-11-12 12:00:00
     * @return String url
     */

    public function PushUrl($streamName)
    {
        $txTime = strtoupper(base_convert(Carbon::now()->addMinutes(600)->timestamp, 10, 16));
        $txSecret = md5(Cache::get('config_live_tencent_pushkey') . $streamName . $txTime);
        $ext_str = "?" . http_build_query(array(
                "txSecret" => $txSecret,
                "txTime" => $txTime
            ));
        return "rtmp://" . Cache::get('config_live_tencent_pushurl') . "/live/" . $streamName . (isset($ext_str) ? $ext_str : "");
    }

    public function PlayUrl($streamName)
    {
        $txTime = strtoupper(base_convert(time(), 10, 16));
        $txSecret = md5('qihucms' . $streamName . $txTime);
        return Cache::get('config_live_tencent_pullurl') . "/live/" . $streamName . '.m3u8' . '?txTime='.$txTime.'&txSecret='.$txSecret;
    }

    public function liveStatus($streamName){
        $this->httpProfile->setEndpoint("live.tencentcloudapi.com");
        $this->clientProfile->setHttpProfile($this->httpProfile);
        $client = new LiveClient($this->cred, "", $this->clientProfile);

        $req = new DescribeLiveStreamStateRequest();
        $params = '{"AppName":"live","DomainName":"'.Cache::get('config_live_tencent_pushurl').'","StreamName":"'.$streamName.'"}';
        $req->fromJsonString($params);
        $resp = $client->DescribeLiveStreamState($req);
        return $resp->StreamState == 'active' ? true : false;
    }

    public function delBacks($room_id,$field_id)
    {
        /*
        $this->httpProfile->setEndpoint("vod.tencentcloudapi.com");
        $this->clientProfile->setHttpProfile($this->httpProfile);
        $client = new VodClient($this->cred, "", $this->clientProfile);
        $req = new SearchMediaRequest();
        $params = '{\"StreamId\":\"room-' . $room_id . '\"}';
        $req->fromJsonString($params);
        $resp = $client->SearchMedia($req);
        if($resp->TotalCount > 0){
            $req = new DeleteMediaRequest();
            foreach($resp->MediaInfoSet as $item){
                if($field_id != $item->FileId){
                    $params = '{"FileId":"'.$item->FileId.'"}';
                    $req->fromJsonString($params);
                    $client->DeleteMedia($req);
                }
            }
        }
        */
    }
}