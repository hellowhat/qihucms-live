<?php
use Illuminate\Routing\Router;
use Encore\Admin\Layout\Content;

//直播
Route::namespace('Qihucms\Live\Controllers')->name('live.')->middleware(['web'])->group(function () {
    Route::domain(config('qihu.wap_domain'))->name('wap.')->group(function () {
        Route::get('live', 'LiveController@index')->name('index');
        //正在进行的直播流
        Route::get('live/rooms', 'LiveController@rooms')->name('rooms');
        //房间数据
        Route::get('live/ajax', 'LiveController@ajax')->name('ajax');
        //直播房间
        Route::get('live/room/{id}', 'LiveController@room')->name('room');
        //更换直播间橱窗商品
        Route::get('live/product/{id}/{product}', 'LiveController@changeProduct')->name('product');
        //小程序获取直播信息
        Route::get('live/mini/{id}', 'LiveController@miniPush')->name('mini');
        //虚拟互动
        Route::get('live/fictitious/{room_id}', 'LiveController@fictitious')->name('fictitious');
        //分类
        Route::get('live/categories', 'LiveController@categories')->name('categories');
        //直播录制回调
        Route::post('live/record', 'LiveController@record')->name('record');
        //直播推流回调
        Route::post('live/start', 'LiveController@start')->name('start');
        //往期直播
        Route::get('live/playback/{id}', 'LiveController@playback')->name('playback');
        //直播榜单
        Route::get('live/ordering', 'LiveController@ordering')->name('ordering');
    });
});

Route::namespace('Qihucms\Live\Controllers')->name('live.')->middleware(['web','auth'])->group(function () {
    Route::domain(config('qihu.wap_domain'))->name('wap.')->group(function () {
        //我的直播
        Route::get('live/my', 'LiveController@my')->name('my');
        //创建直播间
        Route::post('live/create', 'LiveController@create')->name('create');
        //结束直播
        Route::get('live/close', 'LiveController@close')->name('close');
        //发送信息
        Route::post('live/message', 'LiveController@message')->name('message');
        //发红包
        Route::post('live/red/pay', 'LiveRedController@pay')->name('red.pay');
        //抢红包
        Route::post('live/red/rob', 'LiveRedController@rob')->name('red.rob');
        //直播推流
        Route::get('live/push', 'LiveController@push')->name('push');
    });
});

Route::group([
    'prefix' => config('admin.route.prefix'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {
    $router->resource('plugins/qihucms/live/list', '\Qihucms\Live\Admin\Controllers\LiveController');
    $router->resource('plugins/qihucms/live/categories', '\Qihucms\Live\Admin\Controllers\LiveCategoryController');
    $router->get('plugins/qihucms/live/config', function (Content $content) {
        return $content
            ->title('直播配置')
            ->body(new Qihucms\Live\Admin\Forms\Live);
    });
});