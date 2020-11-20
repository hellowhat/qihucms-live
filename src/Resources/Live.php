<?php

namespace Qihucms\Live\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\PhotoService;

class Live extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $img = new PhotoService();
        return [
            'user_id' => $this->user_id,
            'link' => route('live.wap.room',['id'=>$this->user_id]),
            'user' => [
                'nickname' => $this->user->nickname ? $this->user->nickname : $this->user->username,
                'avatar' => empty($this->user->avatar) ? $img->getImgUrl(cache('config_default_avatar'), 50) : $img->getImgUrl($this->user->avatar, 50)
            ],
            'hls' => empty($this->hls) ? false : true,
            'product' => $this->product,
            'category_id' => $this->category_id,
            'category' => $this->category->title,
            'title' => $this->title,
            'cover' => $img->getImgUrl($this->cover, 300,300),
            'peoples' => cache('live-room-online-'.$this->user_id,0),
            'status' => $this->status
        ];
    }
}
