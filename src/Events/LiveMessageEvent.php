<?php

namespace Qihucms\Live\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use App\Models\Gift;
use App\Models\Goods;
use App\Models\User;
use App\Http\Resources\User\UserCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

class LiveMessageEvent implements ShouldBroadcastNow
{
    use SerializesModels;

    public $data;

    /**
     * SendLiveMessageEvent constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('live-message-' . $this->data['id']);
    }

    # 或者可以指定返回的格式和数据
    public function broadcastWith()
    {
        if (isset($this->data['user'])) {
            $this->data['user']->avatar = Storage::url($this->data['user']->avatar);
            unset($this->data['user']->email, $this->data['user']->gender, $this->data['user']->last_login_ip, $this->data['user']->last_login_time);
        }
        switch ($this->data['type']) {
            case 'join':
                if(Cache::has('live-room-online-cache-' . $this->data['id'])){
                    $this->data['online'] = Cache::get('live-room-online-' . $this->data['id']);
                }else{
                    $http = new \GuzzleHttp\Client(['base_uri' => Cache::get('config_live_socket_domain') . '/apps/' . Cache::get('config_live_socket_appid') . '/channels/']);
                    $response = $http->request('GET', 'live-message-' . $this->data['id'], [
                        'headers' => [
                            'Authorization' => 'Bearer ' . Cache::get('config_live_socket_key')
                        ]
                    ]);
                    $response = json_decode($response->getBody()->getContents());
                    $this->data['online'] = $response->subscription_count;
                    Cache::put('live-room-online-cache-' . $this->data['id'], true,now()->addSeconds(30));
                    if (Cache::get('config_live_fictitious_peoples')) {
                        $this->data['online'] = bcmul(Cache::get('config_live_fictitious_peoples_multiple'),$this->data['online'],0);
                        $this->data['online'] += rand(Cache::get('config_live_fictitious_base_mix'),Cache::get('config_live_fictitious_base_max'));
                    }
                    Cache::put('live-room-online-' . $this->data['id'], $this->data['online']);
                }
                break;
            case 'gift':
                $gift = Gift::find($this->data['gift']);
                $gift->thumbnail = Storage::url($gift->thumbnail);
                $gift->image = Storage::url($gift->image);
                $this->data['gift'] = $gift;

                //更新礼物排行
                $total = 0;
                if ($gift->pay_balance > 0) {
                    $total += $gift->pay_balance * Cache::get('config_jewel_exchange_balance_rate');
                }
                if ($gift->pay_jewel > 0) {
                    $total += $gift->pay_jewel;
                }
                if ($gift->pay_integral > 0) {
                    $total += $gift->pay_integral / Cache::get('config_integral_exchange_jewel_rate');
                }
                Redis::zincrby('live-gift-' . $this->data['id'], $total, $this->data['user']->id);
                //缓存榜单数据
                $ordering = Redis::zrevrange('live-gift-' . $this->data['id'], 0, -1, 'WITHSCORES');
                if (isset($ordering[0])) {
                    $ordering = Redis::zrevrange('live-gift-' . $this->data['id'], 0, -1, ['WITHSCORES' => true]);
                }
                $ids = array_keys($ordering);
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                if (Cache::get('live-gifter-ids-' . $this->data['id']) === implode($ids)) {
                    $list = Cache::get('live-gifter-list-' . $this->data['id']);
                } else {
                    $list = new UserCollection(User::whereIn('id', $ids)
                        ->orderByRaw("field(id,{$placeholders})", $ids)
                        ->limit(100)
                        ->get());
                    Cache::put('live-gifter-ids-' . $this->data['id'], implode($ids));
                    Cache::put('live-gifter-list-' . $this->data['id'], $list);
                }
                $this->data['ordering'] = [
                    'rank' => array_values($ordering),
                    'list' => $list,
                    'total' => array_sum($ordering)
                ];
                Cache::put('live-gift-ordering-' . $this->data['id'], $this->data['ordering']);
                break;
            case 'content':
                //做敏感信息处理或直接返回
                break;
            case 'product':
                $good = Goods::find($this->data['product']);
                $good->thumbnail = Storage::url($good->thumbnail);
                $this->data['product'] = $good;
                break;
            case 'red':
                //将红包信息存入redis，
                Redis::sadd('live-red-'.$this->data['id'],$this->data['red_id']);
                $this->data['user_id'] = $this->data['user']->id;
                $this->data['user_nickname'] = $this->data['user']->nickname ? $this->data['user']->nickname : $this->data['user']->username;
                $this->data['user_avatar'] = Storage::url($this->data['user']->avatar);
                Redis::hmset('live-red-'.$this->data['id'].'-'.$this->data['red_id'], $this->data);
                break;
        }
        return $this->data;
    }

    /**
     * 广播的事件名称.如果未定义则默认为事件名称即 App\Events\PublicBroadcastEvent
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'LiveMessage';
    }
}
