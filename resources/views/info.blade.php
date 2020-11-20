@extends('layouts.wap')

@section('title', '我的直播')
@section('header_title', '我的直播')

@section('styles')
    <style type="text/css">
        .title{
            text-align: center;
            margin-top:20px;
        }
        .pushUrl{
            padding:10px;
            border-radius: 10px;
            border:1px solid #cccccc;
            word-break: break-all;
            background:white;
            margin:10px 20px;
        }
        .qrcode{
            text-align: center;
        }
    </style>
@endsection
@inject('photo', 'App\Services\PhotoService')
@section('content')
    <div>
        <div class="weui-panel__bd">
            <div>
                <p class="title">推流地址/二维码</p>
                <p class="pushUrl">{{$pushUrl}}</p>
                <p class="qrcode"><img width="60%" src="{{$qrcode}}"/></p>
            </div>
            <div class="weui-btn-area">
                <a class="btn btn-block btn-primary qh-btn-rounded" href="{{route('live.wap.room',['id' => $info->user_id])}}">我的房间</a>
                <a id="pusher" class="btn btn-block btn-primary qh-btn-rounded" href="javascript:;" onclick="livePush()">开始推流</a>
                <a class="btn btn-block btn-primary qh-btn-rounded" href="javascript:;" onclick="liveClose()">结束直播</a>
            </div>
        </div>
    </div>
    @include('components.wap.placeholder_nav')
    @include('components.wap.nav', ['index' => ''])
@endsection

@push('scripts')
    <script>
        if(is_app && is_app !== 'miniprogram' && is_app !== 'plus') {
            @if(Cache::get('config_live_h5_push'))
            if (!window.browser.versions.ios && !window.browser.versions.iPhone && !window.browser.versions.iPad) {
                $('#pusher').show();
            }
            @endif
        }else{
            $('#pusher').show();
        }

        function livePush(){
            if(is_app == 'miniprogram'){
                @if(session()->exists('in_app'))
                uni.navigateTo({
                    url: '/pages/live/pusher?room_id={{$info->user_id}}'
                });
                @else
                var path = "/pages/live/index?room_id={{$info->user_id}}";
                wx.miniProgram.navigateTo({url: path});
                @endif
            }else if(is_app == 'plus'){
                uni.navigateTo({
                    url: '/pages/live/pusher?room_id={{$info->user_id}}'
                });
            }else{
                location.href = '{{route('live.wap.push')}}'
            }
        }

        wx.config( {!! $weChatJsSdk !!} );

        function liveClose(){
            axios.get('{{ route('live.wap.close') }}')
                .then(response => {
                    console.log(response);
                    $.alert('直播时长：' + response.data.hours + '分钟<br/>浏览次数：' + response.data.online + '次</br>礼物价值：' + response.data.jewel + '{{cache('config_jewel_unit').cache('config_jewel_alias')}}', "直播收益", function() {
                        location.href = '{{route('live.wap.my')}}';
                    });
                })
                .catch(error => {
                    $.toptip(error.response.data.message, 'warning');
                })
        }
    </script>
@endpush