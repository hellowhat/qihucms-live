@extends('layouts.wap')

@section('title', '直播')
@section('header_title')
    <span style="padding-left:28px">直播</span>
@endsection

@section('headerRightContent')
    <a class="px-3" href="{{route('live.wap.my')}}" style="font-size: 12px;background-image: linear-gradient(to bottom right, yellow, red);-webkit-background-clip: text;color: transparent;">
        <i class="iconfont icon-02f" aria-hidden="true"></i> 开播
    </a>
@endsection

@section('styles')
    <style type="text/css">
        .rooms a {
            font-size: 12px;
            color: #666666
        }

        .room-one {
            position: relative;
            font-size:12px;
        }

        .room-one img {
            object-fit: cover;
        }

        .user {
            padding: 8px 5px;
            vertical-align: middle;
            display: flex;
            flex-direction: row;
        }

        .user p {
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            line-height:25px;
        }

        .user p span{
            background:#999999;
            color:white;
            padding:2px 2px 1px 2px;
            margin-left:5px;
            border-radius: 3px;
            font-size:10px;
        }

        .user img {
            margin-right: 5px;
        }

        .room-one .peoples {
            position: absolute;
            bottom: 0;
            right: 0;
            padding: 5px;
            color: #cccccc;
        }

        .room-one .room_id {
            position: absolute;
            top: 5px;
            left: 5px;
            padding: 0 5px;
            background: rgba(0, 0, 0, .4);
            color: white;
            border-radius: 5px;
        }

        .room-one .room_status {
            position: absolute;
            top: 5px;
            right: 5px;
            padding: 0 5px;
            border-radius: 5px;
        }

        .room-one .living {
            background: rgba(255,0,0,0.5);
            color: white;
        }

        .room-one .not-living {
            background: rgba(0, 0, 0, .5);
            color: #cccccc;
        }

        .title {
            position: absolute;
            bottom: 0;
            left: 0;
            padding: 5px;
            color: white;
            width: 100%;
            background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 1));
        }

        .categories {
            background: white;
            padding: 5px;
        }

        .categories-space{
            display:none;
            height:40px;
        }

        .categories ul {
            margin: 0;
            display: flex;
            flex-direction: row;
            justify-content: flex-start;
            overflow: auto;
        }

        .categories ul li {
            list-style: none;
            padding: 5px 10px;
            font-size: 14px;
            line-height:20px;
            white-space: nowrap;
            color:#666666;
        }

        .categories ul .current {
            color: #ffffff;
            background-image: linear-gradient(to bottom right, yellow, red);
            border-radius: 20px;
        }

        .categories-fixed-top{
            position: fixed;
            top:46px;
            z-index:1000;
            width:100%;
            opacity: 0.9;
        }

        .weui-search-bar{
            padding:10px 10px 8px 10px;
            background:#f0f1f3;
        }

        .weui-search-bar:after{
            border:none;
        }

        .weui-search-bar__box .weui-search-bar__input{
            line-height: 14px;
            padding:6px 0;
        }

        .weui-icon-clear, .weui-icon-search{
            font-size:14px;
        }

        .weui-search-bar__form{
            background:none;
        }

        .weui-search-bar__form:after{
            border:none;
        }

        .weui-search-bar__label{
            border-radius: 0;
            padding-top:2px;
        }

        .weui-loadmore_line .weui-loadmore__tips{
            background:#f0f1f3;
        }

        [v-cloak] {
            display: none;
        }

        .ordering{
            background:#f0f1f3;
            padding:10px 10px 10px 0;
        }

        .ordering a{
            background-image: linear-gradient(to bottom right, yellow, red);
            padding:0 10px;
            height:32px;
            line-height:32px;
            border-radius: 5px;
            font-size:14px;
            color:#ffffff;
            display: block;
        }

        .weui-search-bar__cancel-btn{
            color:black;
        }
    </style>
@endsection
@section('content')
    <div id="rooms" v-cloak>
        <div class="bg-white" style="display: flex">
            <div class="weui-search-bar" style="flex: 1">
                <form class="weui-search-bar__form" onsubmit="return rooms.searching()">
                    <div class="weui-search-bar__box">
                        <i class="weui-icon-search"></i>
                        <input type="search" v-model="keyword" class="weui-search-bar__input">
                        <a href="javascript:" class="weui-icon-clear"></a>
                    </div>
                    <label class="weui-search-bar__label">
                        <i class="weui-icon-search"></i>
                        <span>搜索主播ID或昵称</span>
                    </label>
                </form>
                <a href="javascript:" class="weui-search-bar__cancel-btn">取消</a>
            </div>
            <div class="ordering">
                <a href="{{route('live.wap.ordering')}}"><i class="iconfont icon-jieshao" aria-hidden="true"></i> 榜单</a>
            </div>
        </div>
        <div class="categories">
            <ul>
                <li class="current" data-category="0">全部</li>
                @foreach($categories as $category)
                <li data-category="{{$category->id}}">{{$category->title}}</li>
                @endforeach
            </ul>
        </div>
        <div class="categories-space"></div>
        <div class="rooms">
            <div class="weui-panel__bd">
                <div class="d-flex flex-wrap pb-2">
                    <div class="w-50" v-for="item in roomList.data">
                        <a v-bind:href="item.link"
                           class="d-block overflow-hidden bg-white">
                            <div class="room-one">
                                <img v-bind:src="item.cover" width="100%" height="180"
                                     v-bind:alt="item.title">
                                <div class="title">
                                    <div style="width: 70%">@{{ item.title }}</div>
                                </div>
                                <div class="peoples">
                                    <i class="iconfont icon-renshu font-size-12"></i> @{{ item.peoples }}
                                </div>
                                <div class="room_id">@{{ item.category }}</div>
                                <div v-bind:class="item.status ? 'room_status living' : 'room_status not-living'"><i v-if="item.product" style="margin-right:5px" class="iconfont icon-gouwuchekong font-size-12"></i>@{{ liveStatus(item) }}</div>
                            </div>
                            <div class="user">
                                <img v-bind:src="item.user.avatar"
                                     v-bind:alt="item.user.nickname"
                                     class="rounded-circle qh-box-shadow" width="25" height="25"
                                     style="border: 1px solid rgba(255,255,255,.5)">
                                <p>@{{ item.user.nickname }}<span>ID:@{{ item.user_id }}</span></p>
                            </div>
                        </a>
                    </div>
                </div>
                <div v-if="roomList.data.length == 0 && !loading">
                    @include('components.wap.no_content', ['content' => '暂无直播'])
                </div>
            </div>
        </div>
    </div>
    <div class="weui-loadmore" id="loading">
        <i class="weui-loading"></i>
        <span class="weui-loadmore__tips"></span>
    </div>
    <div class="weui-loadmore weui-loadmore_line" id="nomore">
        <span class="weui-loadmore__tips">☹️ 木有了</span>
    </div>
    @include('components.wap.placeholder_nav')
    @include('components.wap.nav', ['index' => ''])
@endsection
@push('scripts')
    <script src="https://cdn.bootcdn.net/ajax/libs/vue/2.6.9/vue.min.js"></script>
    <script>
        const rooms = new Vue({
            el: '#rooms',
            data: {
                roomList: {
                    data: [],
                    links: [],
                    meta: []
                },
                page: 1,
                category: 0,
                keyword: '',
                noMore : false,
                loading : false
            },
            methods: {
                liveStatus: function (item) {
                    if(item.status){
                        return str = '直播中';
                    }else if(item.hls){
                        return str = '回放';
                    }else{
                        return str = '未开播';
                    }
                },
                searching: function () {
                    console.log(rooms.keyword);
                    rooms.page = 1;
                    category = 0;
                    rooms.noMore = false;
                    rooms.roomList = {
                        data: [],
                        links: [],
                        meta: []
                    };
                    rooms.getRooms();
                    return false;
                },
                getRooms: function () {
                    if(!rooms.noMore && !rooms.loading){
                        rooms.loading = true;
                        $('#nomore').hide();
                        $('#loading').show();
                        axios.get('{{ route('live.wap.ajax') }}', {params: {page:rooms.page,category:rooms.category,keyword:rooms.keyword}})
                            .then(response => {
                                rooms.roomList.data = [...rooms.roomList.data,...response.data.data];
                                rooms.roomList.links = response.data.links;
                                rooms.roomList.meta = response.data.meta;
                                $('#loading').hide();
                                if(rooms.roomList.links == undefined || !rooms.roomList.links.next){
                                    if(rooms.roomList.data.length > 0){
                                        $('#nomore').show();
                                    }
                                    rooms.noMore = true;
                                }
                                rooms.loading = false;
                            })
                            .catch(error => {
                                console.log(error);
                                $('#loading').hide();
                            })
                    }
                }
            },
            watch: {}
        });

        $(document).ready(function () {
            rooms.getRooms();
            $('.categories li').click(function () {
                $('.categories li').removeClass('current');
                $(this).addClass('current');
                rooms.category = $(this).data('category');
                if(!rooms.category){
                    rooms.keyword = '';
                }
                rooms.page = 1;
                rooms.noMore = false;
                rooms.roomList = {
                    data: [],
                    links: [],
                    meta: []
                };
                rooms.getRooms();
            });
        });

        $(document.body).infinite().on("infinite", function() {
            rooms.page++;
            rooms.getRooms();
        });

        $(document).scroll(function() {
            if($(document).scrollTop() >= 46){
                $('.categories').addClass('categories-fixed-top');
                $('.categories-space').show();
            }else{
                $('.categories').removeClass('categories-fixed-top');
                $('.categories-space').hide();
            }
        });
    </script>
@endpush