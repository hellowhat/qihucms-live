@extends('layouts.wap')

@section('title', '直播榜')
@section('header_title', '直播榜')
@inject('photo', 'App\Services\PhotoService')
@section('styles')
    <style type="text/css">
        .ordering .order_list_three{
            width:100%;
            display:flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            text-align: center;
            padding:30px 25px 25px 25px;
        }

        .ordering .order_list_three img{
            width:100%;
            height:100%;
            object-fit: cover;
        }
        .ordering .order_list_three p{
            width:100%;
            font-size:12px;
            padding-top:5px;
        }

        .ordering .order_list_three .order_two img{
            border-radius: 50%;
            border:3px solid transparent;
            background-origin:border-box; /*从边框开始背景图*/
            background-clip: padding-box, border-box; /*设置第一个背景和第二个背景的范围*/
            background-size: cover;
            /*由于背景图像不能设置纯色，所以可以使用下面的方式设置纯色*/
            background-image:linear-gradient(#fff, #fff),linear-gradient(white, grey);
        }
        .ordering .order_list_three .order_one img{
            border-radius: 50%;
            border:3px solid transparent;
            background-origin:border-box; /*从边框开始背景图*/
            background-clip: padding-box, border-box; /*设置第一个背景和第二个背景的范围*/
            background-size: cover;
            /*由于背景图像不能设置纯色，所以可以使用下面的方式设置纯色*/
            background-image:linear-gradient(#fff, #fff),linear-gradient(yellow, orangered);
        }
        .ordering .order_list_three .order_three img{
            border-radius: 50%;
            border:3px solid transparent;
            background-origin:border-box; /*从边框开始背景图*/
            background-clip: padding-box, border-box; /*设置第一个背景和第二个背景的范围*/
            background-size: cover;
            /*由于背景图像不能设置纯色，所以可以使用下面的方式设置纯色*/
            background-image:linear-gradient(#fff, #fff),linear-gradient(oldlace, sienna);
        }

        .ordering .order_list_three .order_th{
            position: relative;
        }

        .ordering .order_list_three .order_th .order_number{
            display:block;
            position: absolute;
            top:-5px;
            width:100%;
            font-size:14px;
            line-height: 14px;
        }
        .ordering .order_list_three .order_two .order_number span{
            background-image: linear-gradient(to bottom right, white, grey);
            color:white;
            padding:2px 5px;
            border-radius: 5px;
        }
        .ordering .order_list_three .order_one .order_number span{
            background-image: linear-gradient(to bottom right, yellow, red);
            color:white;
            padding:2px 5px;
            border-radius: 5px;
        }
        .ordering .order_list_three .order_three .order_number span{
            background-image: linear-gradient(to bottom right, oldlace, sienna);
            color:white;
            padding:2px 5px;
            border-radius: 5px;
        }

        .ordering .order_list_three .order_two p{
            white-space:nowrap;
            overflow: hidden;
            text-overflow : ellipsis;
            color:grey;
        }
        .ordering .order_list_three .order_one p{
            font-weight: bold;
            white-space:nowrap;
            text-overflow : ellipsis;
            overflow: hidden;
            color:red;
        }
        .ordering .order_list_three .order_three p{
            white-space:nowrap;
            text-overflow : ellipsis;
            overflow: hidden;
            color:sienna;
        }

        .ordering .order_list_other{}
        .ordering .order_list_other .list_one{
            background:white;
            display:flex;
            flex-direction: row;
            margin-bottom:1px;
            padding:10px;
            line-height:40px;
        }
        .ordering .order_list_other .list_one .list_one_number{
            width:40px;
        }
        .ordering .order_list_other .list_one .list_one_number span{
            padding:2px 5px;
            background:#efefef;
            border-radius: 20%;
            font-size: 12px;
            color:#999999;
        }
        .ordering .order_list_other .list_one .list_one_avatar{
            width:40px;
            height:40px;
        }
        .ordering .order_list_other .list_one .list_one_avatar img{
            border-radius: 50%;
        }
        .ordering .order_list_other .list_one .list_one_nickname{
            flex: 1;
            padding:0 10px;
            font-size:14px;
            color:black;
        }
        .ordering .order_list_other .list_one .list_one_rank{
            font-size:12px;
        }
    </style>
@endsection
@section('content')
    <div class="ordering">
        <div class="order_list_three">
            @if(count($data) > 1)
            <div class="order_th order_two" style="width: 27%">
                <a href="{{route('live.wap.room',['id'=>$data[1]['user_id']])}}">
                    <div class="order_number"><span>2</span></div>
                    <img src="{{ $photo->getImgUrl($data[1]['avatar'], 100) }}" />
                    <p>{{$data[1]['nickname']}}<br/>{{$data[1]['total']}}{{cache('config_jewel_alias')}}</p>
                </a>
            </div>
            @endif
            @if(count($data) > 0)
            <div class="order_th order_one" style="width:30%;">
                <a href="{{route('live.wap.room',['id'=>$data[0]['user_id']])}}">
                    <div class="order_number"><span>1</span></div>
                    <img src="{{ $photo->getImgUrl($data[0]['avatar'], 100) }}" />
                    <p>{{$data[0]['nickname']}}<br/>{{$data[0]['total']}}{{cache('config_jewel_alias')}}</p>
                </a>
            </div>
            @endif
            @if(count($data) > 2)
            <div class="order_th order_three" style="width: 25%">
                <a href="{{route('live.wap.room',['id'=>$data[2]['user_id']])}}">
                    <div class="order_number"><span>3</span></div>
                    <img style="min-height: 80px" src="{{ $photo->getImgUrl($data[2]['avatar'], 100) }}" />
                    <p>{{$data[2]['nickname']}}<br/>{{$data[2]['total']}}{{cache('config_jewel_alias')}}</p>
                </a>
            </div>
            @endif
        </div>
        <div class="order_list_other">
            @foreach($data as $one)
                @if($loop->index > 2)
                    <a href="{{route('live.wap.room',['id'=>$one['user_id']])}}">
                        <div class="list_one">
                            <div class="list_one_number"><span>{{$loop->index+1}}</span></div>
                            <div class="list_one_avatar">
                                <img src="{{ $photo->getImgUrl($one['avatar'], 50) }}" width="100%" height="100%" />
                            </div>
                            <div class="list_one_nickname">{{$one['nickname']}}</div>
                            <div class="list_one_rank"> {{$one['total']}} {{cache('config_jewel_alias')}}</div>
                        </div>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
    @include('components.wap.placeholder_nav')
    @include('components.wap.nav', ['index' => ''])
@endsection
@push('scripts')
    <script>

    </script>
@endpush