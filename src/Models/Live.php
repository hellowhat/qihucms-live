<?php

namespace Qihucms\Live\Models;

use Illuminate\Database\Eloquent\Model;

class Live extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'title', 'screen', 'cover', 'hls','backs', 'product', 'times', 'status'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function category()
    {
        return $this->belongsTo('Qihucms\Live\Models\LiveCategory');
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'backs' => 'array',
    ];
}
