@extends('layouts.wap')

@section('title', '我的直播')
@section('header_title', '我的直播')

@section('styles')
    <style type="text/css">

    </style>
@endsection
@inject('photo', 'App\Services\PhotoService')
@section('content')
    <div style="transform: translateY(-11px)">
        <div class="weui-panel__bd">
            <div class="weui-cells weui-cells_form">
                <form id="liveForm" method="POST" action="{{route('live.wap.create')}}" onsubmit="return submitForm()">
                    <div class="weui-cells__title">直播间名称</div>
                    <div class="weui-cells">
                        <div class="weui-cell">
                            <div class="weui-cell__bd">
                                <input id="liveTitle" name="title" class="weui-input" value="{{$title}}" type="text" placeholder="取一个有趣的名字吧！">
                            </div>
                        </div>
                    </div>
                    <div class="weui-cells__title">直播分类</div>
                    <div class="weui-cell weui-cell_select">
                        <div class="weui-cell__bd">
                            <select class="weui-select" name="category_id">
                                @foreach($categories as $category)
                                <option value="{{$category->id}}" {{$category_id === $category->id ? 'selected' : ''}}>{{$category->title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="weui-cells__title">屏幕方向</div>
                    <div class="weui-cell weui-cell_select">
                        <div class="weui-cell__bd">
                            <select class="weui-select" name="screen">
                                <option value="0" {{$screen ? '' : 'selected'}}>竖屏</option>
                                <option value="1" {{$screen ? 'selected' : ''}}>横屏</option>
                            </select>
                        </div>
                    </div>
                    <div class="weui-cells__title">推广商品</div>
                    <div class="weui-cell weui-cell_select">
                        <div class="weui-cell__bd">
                            <select class="weui-select" name="product">
                                <option value="">不推广</option>
                                @foreach($mall as $good)
                                <option @if($product == $good['id']) selected @endif value="{{$good['id']}}">{{$good['title']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <input type="hidden" value="{{$cover}}" name="cover" id="liveCover">
                    @csrf
                </form>
                <div class="weui-cells__title">直播间封面</div>
                <div class="weui-cell">
                    <div class="weui-cell__bd">
                        <div class="weui-uploader">
                            <ul id="coverView" class="weui-uploader__files" style="float:left;margin-bottom:0">
                                @if(!empty($cover))
                                <li class="weui-uploader__file" style="background-image:url({{ $photo->getImgUrl($cover, 100) }})"></li>
                                @endif
                            </ul>
                            <div class="weui-uploader__bd">
                                <div class="weui-uploader__input-box">
                                    <input id="coverInput" name="coverInput" class="weui-uploader__input" type="file" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="weui-btn-area">
                <a class="btn btn-block btn-primary qh-btn-rounded" onclick="$('#liveForm').submit()" href="javascript:">开始直播</a>
            </div>
        </div>
    </div>
    @include('components.wap.placeholder_nav')
    @include('components.wap.nav', ['index' => ''])
@endsection
@push('scripts')
    @if(config('filesystems.default') == 'oss')
        <script src="//gosspublic.alicdn.com/aliyun-oss-sdk-6.4.0.min.js"></script>
    @endif
    <script src="{{ asset('js/upload.js') }}"></script>
    <script>
        function submitForm(){
            if($('#liveTitle').val() === ''){
                $.toast("直播间名称必须填写", "text");
                return false;
            }
            if($('#liveCover').val() === ''){
                $.toast("直播间封面必须上传", "text");
                return false;
            }
            return true;
        }
        $('#coverInput').on('change', function () {
            let files = $(this).get(0).files;

            @switch(config('filesystems.default'))
            @case('qiniu')
            $.qn({
                path: 'live',
                file: files[0],
                tokenUrl: "{{ route('upload.qiniu.token') }}",
                success: function (res) {
                    console.log(res);
                    $('#coverView').html('<li class="weui-uploader__file" style="background-image:url({{ config('filesystems.disks.qiniu.domain') }}/'+res.key+')"></li>');
                    $('#liveCover').val(res.key);
                },
                fail: function (err) {
                    $.toast("上传失败", "cancel");
                }
            });
            @break
            @case('oss')
            $.oss({
                path: 'live',
                file: files[0],
                accessKeyId: "{{ config('filesystems.disks.oss.access_key') }}",
                accessKeySecret: "{{ config('filesystems.disks.oss.secret_key') }}",
                bucket: "{{ config('filesystems.disks.oss.bucket') }}",
                endpoint: "{{ config('filesystems.disks.oss.endpoint') }}",
                cname: {{ config('filesystems.disks.oss.isCName') }},
                success: function (res) {
                    $('#coverView').html('<li class="weui-uploader__file" style="background-image:url('+res.url+')"></li>');
                    $('#liveCover').val(res.name);
                    console.log(res);
                },
                fail: function (err) {
                    $.toast("上传失败", "cancel");
                }
            });
            @break
            @default
            $.bd({
                path: 'live',
                input: 'image',
                file: files[0],
                uploadUrl: "{{ route('upload') }}",
                success: function (res) {
                    $('#coverView').html('<li class="weui-uploader__file" style="background-image:url('+res.data.url+')"></li>');
                    $('#liveCover').val(res.data.name);
                    console.log(res);
                },
                fail: function (err) {
                    $.toast("上传失败", "cancel");
                }
            });
            @endswitch
        });
    </script>
@endpush