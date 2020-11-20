    @extends('layouts.wap_no_header')

    @section('title', '直播')
    @inject('photo', 'App\Services\PhotoService')
    @inject('qrcode', 'App\Services\QrCodeService')
    @section('styles')
        <style type="text/css">
            .live-main {
                position: relative;
                background: black;
                font-size: 1rem;
            }

            .live-main video{
                position:absolute;
            }

            .live-main .play_shadow {
                position: fixed;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                z-index: 8;
                background: url('{{ $photo->getImgUrl($room->cover,300) }}') no-repeat center center;
                background-size: cover;
                overflow: hidden;
            }

            .live-main .shadow {
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                z-index: 9;
                background: url('{{ $photo->getImgUrl($room->cover,300) }}') no-repeat center center;
                background-size: cover;
                filter: blur(20px);
                margin: -30px;
            }

            .live_pause {
                position: fixed;
                z-index: 999;
                width: 100%;
                color: white;
                text-align: center;
                margin-top: 50%;
            }

            .live_pause button {
                border: none;
                background: rgba(0, 0, 0, 0.2);
                padding: 5px;
                width: 40px;
                border-radius: 10px;
                color: #ffffff;
            }

            .ios_play {
                position: absolute;
                width: 40px;
                height: 40px;
                left: 50%;
                top: 50%;
                z-index: 20;
                transform: translate(-1rem, -1.5rem);
                display: none;
            }

            .ios_play button {
                border: none;
                background: none;
                font-weight: bold;
                color: #ffffff;
            }

            .paused {
                position: fixed;
                top: 40%;
                left: 20%;
                bottom: 40%;
                right: 20%;
                display: flex;
                flex-direction: column;
                z-index: 20;
                align-items: center;
                justify-content: center;
            }

            .paused button {
                background: rgba(0, 0, 0, 0.2);
                border-radius: 10px;
                line-height: 30px;
                padding: 5px 10px;
                color: #ffffff;
                width: 130px;
                height: 50px;
                border: 1px solid white;
            }

            .live-main .live-content {
                position: fixed;
                flex: 1;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 10;
                overflow: hidden;
            }

            .live-main .live-content .live-red{
                width:3rem;
                position: relative;
                margin: 0 0.5rem;
                animation: 3s move infinite;
                transform-style: preserve-3d;
            }

            @keyframes move {
                0% {
                    transform: rotateY(0deg);
                }
                100% {
                    transform: rotateY(360deg);
                }
            }

            .live-main .live-content .live-top {
                display: flex;
                padding: 0.6rem;
            }

            .live-main .live-content .live-user {
                display: flex;
                padding: 0.2rem;
                border-radius: 3rem;
                background: rgba(0, 0, 0, 0.2);
            }

            .live-main .live-content .live-user .avatar img {
                width: 2.2rem;
                height: 2.2rem;
            }

            .live-main .live-content .live-user .nickname {
                color: white;
                line-height: 1.4rem;
                margin: 0 0.5rem;
                font-size: 0.8rem;
                font-weight: bold;
                width: 4.7rem;
                overflow: hidden;
                white-space:nowrap;
                text-overflow: ellipsis;
            }

            .live-main .live-content .live-user .nickname p {
                margin: 0;
                line-height: 0.6rem;
                font-size: 0.6rem;
                color: #ededed;
                font-weight: normal;
            }

            .live-main .live-content .live-user .follow {
                padding: 0.1rem;
            }

            .live-main .live-content .live-user .follow button {
                border: none;
                border-radius: 3rem;
                background: white;
                color: #ff0064;
                padding: 0 0.5rem;
                height: 2rem;
                line-height: 2.2rem;
                font-weight: bold;
            }

            .live-main .live-content .live-gift-list {
                display: flex;
                overflow: auto;
                flex: 1;
                margin: 0 0.6rem;
                padding-top: 0.3rem;
            }

            .live-main .live-content .live-gift-list-user {
                width: 30px;
                height: 30px;
                position: relative;
                margin-right: 0.4rem;
            }

            .live-main .live-content .live-gift-list-user-number {
                position: absolute;
                bottom: 0;
                font-size: 0.6rem;
                width: 100%;
                border-radius: 0.3rem;
                background: rgb(100, 100, 0, 0.5);
                color: white;
                flex: 1;
                line-height: 0.6rem;
                padding: 0.1rem;
                text-align: center;
            }

            .live-main .live-content .live-numbers {
                background: rgba(0, 0, 0, 0.2);
                border-radius: 2rem;
                padding: 0 0.6rem;
                color: white;
                font-size: 0.8rem;
                height: 35px;
                line-height: 35px;
            }

            .live-main .live-content .live-more {
                text-align: right;
                @if($room->screen > 0)
                position: relative;
                margin-top:260px;
                z-index:10;
                @endif
            }

            .live-main .live-content .live-more button {
                background: rgba(0, 0, 0, 0.2);
                border: none;
                border-radius: 0.8rem;
                padding: 0.2rem 1.6rem 0.2rem 0.8rem;
                font-size: 0.8rem;
                color: white;
                margin-right: -1rem;
            }

            .live-main .live-content .live-message-product {
                position: absolute;
                bottom: 3rem;
                padding: 0.6rem;
                display: flex;
                width: 100%;
            }

            .live-main .live-content .live-product {
                width: 30%;
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
            }

            .live-main .live-content .live-product .live-product-info {
                padding: 0.4rem 0.4rem 0.1rem 0.4rem;
                background: rgba(255, 255, 255, 0.8);
                border-radius: 0.3rem;
                position: relative;
            }

            .live-main .live-content .live-product .live-product-info img {
                width: 100%;
                border-radius: 0.2rem;
            }

            .live-main .live-content .live-product .live-product-info span {
                color: #ff0064;
                font-weight: bold;
            }

            .live-main .live-content .live-product .live-product-info .live-product-close {
                position: absolute;
                left: -0.6rem;
                top: -0.7rem;
            }

            .live-main .live-content .live-product .live-product-info .live-product-close button {
                border: none;
                background: rgba(0, 0, 0, 0.3);
                padding: 0 0.3rem;
                line-height: 1.3rem;
                color: white;
                border-radius: 1rem;
            }

            .live-main .live-content .live-messages {
                display: flex;
                flex: 1;
                margin-right: 0.6rem;
            }

            .live-main .live-content .live-messages .live-message-box {
                display: flex;
                overflow: auto;
                flex-flow: column nowrap;
            }

            .live-main .live-content .live-messages .live-message-box div {
                font-size: 0.8rem;
                padding: 0.3rem 0 0 0;
            }

            .live-message-first {
                margin-top: auto;
            }

            .live-main .live-content .live-messages div p {
                padding: 0.2rem 0.5rem;
                line-height: 1.2rem;
                border-radius: 1rem;
                background: rgba(0, 0, 0, 0.2);
                color: white;
                float: left;
                word-break: break-all;
            }

            .live-main .live-content .live-messages div .message-content span {
                color: #00ffff;
                margin-right: 0.3rem
            }

            .live-main .live-content .live-messages div .message-join span {
                color: #00ffaa;
                margin-right: 0.3rem
            }

            .live-main .live-content .live-messages div .message-gift {
                background: rgba(255, 0, 100, 0.3);
            }

            .live-main .live-content .live-messages div .message-gift span {
                color: #ffff00;
            }

            .live-main .live-content .live-messages div .message-notice {
                color: #007bff;
            }

            .live-main .live-content .live-messages div .message-red {
                background: rgba(255, 0, 0, 0.3);
            }

            .live-main .live-content .live-messages div .message-red span {
                color: #ffff00;
                margin-right: 0.3rem
            }

            .live-main .live-content .live-bottom {
                position: absolute;
                bottom: 0;
                padding: 0.6rem;
                display: flex;
                width: 100%;
            }

            .live-main .live-content .live-bottom .live-message-input {
                flex: 1;
            }

            .live-main .live-content .live-bottom .live-message-input input {
                border-radius: 1.5rem;
                height: 2.5rem;
                line-height: 2.5rem;
                width: 100%;
                padding: 0.3rem 0.8rem;
                background: rgba(0, 0, 0, 0.2);
                color: white;
            }

            .live-main .live-content .live-bottom .live-bottom-button {
                margin-left: 0.5rem;
            }

            .live-main .live-content .live-bottom .live-bottom-button button {
                border: none;
                background: rgba(0, 0, 0, 0.2);
                padding: 0 0.8rem;
                height: 2.5rem;
                line-height: 2.5rem;
                color: #ffffff;
                border-radius: 1.5rem;
            }

            .live-main .live-content .live-gift {
                width: 100%;
                max-height: 15rem;
                display: flex;
                flex-direction: column;
                margin-top: 0.6rem;
                z-index: 900;
            }

            .live-main .live-content .live-gift .gift {
                line-height: 3rem;
                display: flex;
                justify-content: center;
            }

            .live-main .live-content .live-gift .gift .box {
                background: rgba(0, 0, 0, 0.2);
                color: white;
                display: flex;
                border-radius: 3rem;
                margin-bottom: 0.6rem;
                padding: 0 0.6rem;
            }

            .live-main .live-content .live-gift .gift .nickname {
                color: #ffff00;
                font-size: 0.8rem;
                padding: 0 0.6rem;
            }

            .live-main .live-content .live-gift .gift .nickname span {
                color: white;
            }

            .live-main .live-modal {
                position: fixed;
                bottom: 0;
                width: 100%;
                height: 60%;
                background: white;
                z-index: 20;
                border-radius: 0.6rem;
                margin-bottom: -0.6rem;
                padding: 0.8rem 1rem;
                display: flex;
                flex-direction: column;
            }

            .live-main .live-modal-title {
                padding-bottom: 0.8rem;
                font-weight: bold;
                font-size: 0.8rem;
            }

            .live-main .live-modal-close {
                position: absolute;
                right: 0.8rem;
                top: 0.6rem;
            }

            .live-main .live-modal-close button {
                border: none;
                background: #999999;
                padding: 0 0.3rem;
                line-height: 1.3rem;
                color: white;
                border-radius: 1rem;
            }

            .live-main .live-modal-list {
                flex: 1;
                overflow: auto;
            }

            .live-main .live-modal-list .live-modal-good {
                display: flex;
                padding-top: 0.8rem;
                border-top: 1px solid #efefef;
                margin-bottom: 0.8rem;
            }

            .live-main .live-modal-list .live-modal-good img {
                border-radius: 0.2rem;
                object-fit: cover;
            }

            .live-main .live-modal-list .live-modal-good-info {
                margin-left: 0.8rem;
                flex: 1;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }

            .live-main .live-modal-list .live-modal-good-title {
                font-size: 0.8rem;
            }

            .live-main .live-modal-list .live-modal-good-title span {
                padding: 0 0.2rem;
                border: 1px solid #ff0064;
                border-radius: 0.2rem;
                color: #ff0064;
            }

            .live-main .live-modal-list .live-modal-good-price {
                display: flex;
                justify-content: space-between;
                font-weight: bold;
                color: #ff0064;
            }

            .live-main .live-modal-list .live-modal-good-price button {
                border: none;
                color: white;
                background: #ff0064;
                border-radius: 0.2rem;
                padding: 0.1rem 1rem;
                font-size: 0.8rem;
            }

            .live-main .live-modal-list .live-modal-ordering {
                display: flex;
                justify-content: space-between;
                line-height: 2.5rem;
                padding-top: 0.8rem;
                border-top: 1px solid #efefef;
                margin-bottom: 0.8rem;
                font-size: 0.8rem;
                font-weight: bold;
            }

            .live-main .live-modal-list .live-modal-ordering-index {
                text-align: left;
                margin-right: 1rem;
                color: #999999;
            }

            .live-main .live-modal-list .live-modal-ordering img {
                border-radius: 2.5rem;
            }

            .live-main .live-modal-list .live-modal-ordering .live-modal-ordering-user {
                text-align: center;
                margin: 0 1rem;
            }

            .live-main .live-modal-list .live-modal-ordering .live-modal-ordering-jewel {
                flex: 1;
                text-align: right;
                color: #ff0064;
            }

            .list-item {
                display: inline-block;
                margin-right: 10px;
            }

            .list-enter-active, .list-leave-active {
                transition: all 1s;
            }

            .list-enter, .list-leave-to {
                opacity: 0;
                transform: translateX(100px);
            }

            .fade-enter-active, .fade-leave-active {
                transition: all 0.3s;
            }

            .fade-enter, .fade-leave-to {
                opacity: 0;
                transform: translateY(200px);
            }

            [v-cloak] {
                display: none;
            }

            .weui-grid:after, .weui-grid:before {
                content: none
            }

            .live-red-package-list .weui-grid__icon {
                border: 2px solid #666666;
                width: 100%;
                text-align: center;
                border-radius: 5px;
                padding: 5px;
                color: #666666;
                font-weight: bold;
            }

            .live-red-package-list .live-read-package-current {
                border: 2px solid #ffc107;
                color: #ffc107;
            }

            .live-red-package-list .weui-grid {
                padding: 10px;
            }

            .live-red-package-list .weui-grid__icon {
                height: auto;
            }

            .live-red-package-list .weui-grids {
                padding: 5px;
            }

            .live-red-package-list .weui-cell:before {
                content: none;
            }

            .live-red-package-list .weui-grids:after{
                border:none;
            }

            .live-red-package-list .weui-cell {
                color: #ffc107;
            }

            .live-red-package-list .live-read-package-submit {
                margin: 10px 15px;
                padding-bottom: 5px;
            }

            .live-red-package-list .live-read-package-submit a {
                background: #ff0064;
                color: white;
                font-size: 16px;
            }

            .live-red-box{
                z-index:999;
                position: fixed;
                left:0;
                top:0;
                right:0;
                bottom:0;
                display:flex;
                flex-direction: column;
                justify-content: center;
                align-items: center
            }
            .live-red-center{
                width:70%;
                max-height:50%;
                min-height:50px;
                overflow: auto;
                position: relative;
            }
            .live-red-list{
                display: flex;
                margin:5px 0;
                background: white;
                border-radius: 10px;
            }

            .live-red-list .red_price{
                width:70px;
                height:50px;
                background:#ff0064;
                color:yellow;
                text-align: center;
                line-height: 50px;
                font-size: 16px;
                font-weight: bold;
                border-right:3px dotted white;
            }

            .live-red-list .red_info{
                font-size: 14px;
                line-height: 20px;
                padding:5px 10px;
                flex: 1;
                color:#999999;
            }
            .live-red-list .red_info p{
                color:#ff0064;
                font-weight: bold;
            }
            .live-red-list .red_get button{
                width:60px;
                height:50px;
                background:#ff0064;
                color:yellow;
                border:none;
            }

            .live-red-close{
                padding:5px 10px;
                border-radius: 20px;
                margin-top:10px;
                background:white;
            }
        </style>
    @endsection
    @section('content')
        <div class="live-main" id="LiveMain" v-cloak>
            <video id="LivePlayer"
               @if($room->screen)
               controls
               @endif
               webkit-playsinline="true"
               x-webkit-airplay="allow"
               playsinline="true"
               x5-video-player-type="h5-page"
               x5-video-orientation="portrait"
               x5-video-player-fullscreen="true"
               src=""
               loop
               width="100%"
            >
            </video>
            <div class="play_shadow" id="play_shadow">
                <div class="shadow"></div>
                @if($state)
                    <div class="qh-video-loading" style="z-index: 30">
                        <div></div>
                        <div></div>
                    </div>
                @endif
            </div>
            @if(($room->status and $state) or (!empty($room->hls) and !$room->status))
                <div class="ios_play">
                    <button v-on:click="ios_play"><i class="iconfont icon-bofang1 font-size-30"></i></button>
                </div>
            @endif
            <transition name="fade">
            <div class="live-red-box" v-if="red_show">
                <div class="live-red-center">
                    <div class="live-red-list" v-for="item in red_packages">
                        <div class="red_price">
                            @{{ item.amount }}￥
                        </div>
                        <div class="red_info">
                            <p>@{{ item.user_nickname }}</p>
                            @{{ item.total }}/@{{ item.surplus }}
                        </div>
                        <div class="red_get">
                            <button v-if="item.get" style="background: #EFEFEF;color:#666666">已抢</button>
                            <button v-else-if="!item.get && item.surplus == 0" style="background: #EFEFEF;color:#666666">没了</button>
                            <button v-else v-on:click="redRob(item.red_id)">抢</button>
                        </div>
                    </div>
                </div>
                <div class="live-red-close" v-on:click="red_show = false"><i class="iconfont icon-guanbi font-size-16"></i></div>
            </div>
            </transition>
            <div class="paused" v-if="tryTimes > 5">
                <button onclick="location.reload()">
                    直播暂停中 <i class="iconfont icon-shuaxin1 font-size-16"></i>
                </button>
            </div>
            <div class="live-content">
                @if($room->status and !$state)
                    <div class="live_pause">
                        <button onclick="location.reload()">
                            主播马上就来 <i class="iconfont icon-shuaxin1 font-size-16"></i>
                        </button>
                    </div>
                @elseif($room->status == 0 and empty($room->hls))
                    <div class="live_pause">
                        <button onclick="location.reload()">
                            未开播 <i class="iconfont icon-shuaxin1 font-size-16"></i>
                        </button>
                    </div>
                @endif
                <div class="live-top">
                    <div class="live-user">
                        <div class="avatar" v-on:click="homePage(room)">
                            @empty($room->user->avatar)
                                <img src="{{ $photo->getImgUrl(cache('config_default_avatar'), 50) }}"
                                     alt="{{ $room->user->nickname }}"
                                     class="rounded-circle"/>
                            @else
                                <img src="{{ $photo->getImgUrl($room->user->avatar, 50) }}"
                                     alt="{{ $room->user->nickname }}"
                                     class="rounded-circle"/>
                            @endempty
                        </div>
                        <div class="nickname" v-on:click="homePage(room)">
                            {{$room->user->nickname ? $room->user->nickname : $room->user->username}}
                            <p>@{{numberChange(ordering.total,1)}}本场礼物</p>
                        </div>
                        <div class="follow">
                            <button v-on:click="follow">
                                <i v-if="!followed" class="iconfont icon-iconweiguanzhu font-size-16"></i>
                                <i v-if="followed" class="iconfont icon-iconyiguanzhu font-size-16"></i>
                            </button>
                        </div>
                    </div>
                    <div class="live-gift-list" v-on:click="modal_show = true;modal_show_index = 2">
                        <div class="live-gift-list-user" v-for="(list,index) in ordering.list.slice(0,10)">
                            <img v-bind:src="list.avatar"
                                 class="rounded-circle qh-box-shadow" width="30" height="30"/>
                            <div class="live-gift-list-user-number">@{{numberChange(ordering.rank[index],1)}}</div>
                        </div>
                    </div>
                    <div class="live-numbers">
                        @{{ numberChange(online,1) }}
                    </div>
                </div>
                <div class="live-more">
                    @if($room->status == 0 and !empty($room->hls))
                        <button style="margin-bottom:10px" id="playBack">
                            <i class="iconfont icon-bofang font-size-16"></i> 直播回放
                        </button>
                        <br/>
                    @endif
                    @if($user and $room->user->id == $user->id)
                        @if($room->status == 1)
                        <button style="margin-bottom:10px" v-on:click="live_close">
                            <i class="iconfont icon-guanbi font-size-16"></i> 结束直播
                        </button>
                        <br/>
                        @endif
                        <button v-on:click="modal_show = true;modal_show_index = 1">
                            <i class="iconfont icon-dianpu font-size-16"></i> 更换橱窗
                        </button>
                    @endif
                </div>
                <div class="live-red" v-on:click="redShow" v-if="red_packages.length > 0">
                    <img src="{{asset('asset/live/red.png')}}" width="100%" />
                </div>
                <div class="live-gift">
                    <transition-group name="list" tag="p">
                        <div class="gift" v-for="gift in gifts" v-bind:key="gift">
                            <div class="box">
                                <div class="avatar">
                                    <img v-bind:src="gift['user'].avatar"
                                         v-bind:alt="gift['user'].nickname"
                                         class="rounded-circle qh-box-shadow" width="30" height="30"/>
                                </div>
                                <div class="nickname">
                                    @{{gift['user'].nickname ? gift['user'].nickname : gift['user'].username}}
                                    <span>送出了</span> @{{gift['gift'].name}}
                                </div>
                                <div class="gif">
                                    <img v-bind:src="gift['gift'].thumbnail"
                                         v-bind:alt="gift['gift'].name"
                                         width="50" height="50"
                                    />
                                </div>
                            </div>
                        </div>
                    </transition-group>
                </div>
                <div class="live-message-product">
                    <div class="live-messages">
                        <div class="live-message-box">
                            <div v-for="(message,index) in messages" v-bind:class="index === 0 ? 'live-message-first' : ''">
                                <p v-if="message.type==='join'" class="message-join"><span>@{{message.nickname}}</span>来了
                                </p>
                                <p v-else-if="message.type==='gift'" class="message-gift"><span>@{{message.nickname}}</span>
                                    送出了 <span>@{{message.gift}}</span></p>
                                <p v-else-if="message.type==='notice'" class="message-notice">@{{message.content}}</p>
                                <p v-else-if="message.type==='red'" class="message-red"><span>@{{message.nickname}}</span>发了一个红包</p>
                                <p v-else class="message-content"><span>@{{message.nickname}}</span>@{{message.content}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="live-product">
                        <transition name="list">
                            <div class="live-product-info" v-if="product_show">
                                <a v-bind:href="'/mall/goods/' + product_info.id">
                                    <img v-bind:src="product_info.thumbnail"/>
                                    <span>￥@{{product_info.price}}</span>
                                </a>
                                <div class="live-product-close">
                                    <button v-on:click="product_show = false">
                                        <i class="iconfont icon-guanbi font-size-12"></i>
                                    </button>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>
                <div class="live-bottom">
                    <div class="live-message-input">
                        <input class="weui-input" v-model="content" type="text" placeholder="说点啥吧" onclick="this.focus();"
                               v-on:click="inputFocus" v-on:focusout="hideKeyBord">
                    </div>
                    <div class="live-bottom-button" v-if="!texting">
                        <button v-on:click="redPackage">
                            <i class="iconfont icon-hongbao1 font-size-16"></i>
                        </button>
                    </div>
                    <div class="live-bottom-button" v-if="!texting">
                        <button v-on:click="gift">
                            <i class="iconfont icon-liwu1 font-size-16"></i>
                        </button>
                    </div>
                    <div class="live-bottom-button" v-if="!texting">
                        <button v-on:click="modal_show = true;modal_show_index = 1">
                            <i class="iconfont icon-gouwucheman font-size-16"></i>
                        </button>
                    </div>
                    <div class="live-bottom-button" v-if="!texting">
                        <button v-on:click="share">
                            <i class="iconfont icon-zhuanfa font-size-16"></i>
                        </button>
                    </div>
                    <div class="live-bottom-button" v-if="!texting">
                        <button v-on:click="location.href = '{{route('live.wap.index')}}'">
                            <i class="iconfont icon-guanbi font-size-16"></i>
                        </button>
                    </div>
                    <div class="live-bottom-button" v-if="texting">
                        <button v-on:click="send">
                            <i class="iconfont icon-zhifeiji font-size-16"></i>
                        </button>
                    </div>
                </div>
            </div>
            <transition name="fade">
                <div class="live-modal" v-if="modal_show">
                    <div class="live-modal-title">@{{ modal_show_index == 1 ? '共' + mall.length + '件商品' : '本场榜单（TOP100）'
                        }}
                    </div>
                    <div class="live-modal-close">
                        <button v-on:click="modal_show = false">
                            <i class="iconfont icon-guanbi font-size-12"></i>
                        </button>
                    </div>
                    <div class="live-modal-list">
                        <div class="live-modal-good" v-if="modal_show_index == 1" v-for="(good,index) in mall">
                            <img width="100" height="100" v-bind:src="good.thumbnail"/>
                            <div class="live-modal-good-info">
                                <div class="live-modal-good-title">
                                    @{{good.title}}
                                    <span v-if="good.is_new">@{{good.is_new}}</span>
                                    <span v-if="good.is_hot">@{{good.is_hot}}</span>
                                    <span v-if="good.link">链接</span>
                                </div>
                                <div class="live-modal-good-price">
                                    <p>￥@{{good.price}}</p>
                                    @if($user and $room->user->id == $user->id)
                                        <button v-on:click="change_product(index)">上橱窗</button>
                                    @else
                                        <button v-on:click="go_shopping(index)">去看看</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="live-modal-ordering" v-if="modal_show_index == 2"
                             v-for="(list,index) in ordering.list.slice(0,100)" v-on:click="homePage(list.id)">
                            <div class="live-modal-ordering-index">@{{index+1}}</div>
                            <img width="40" height="40" v-bind:src="list.avatar"/>
                            <div class="live-modal-ordering-user">@{{list.nickname}}</div>
                            <div class="live-modal-ordering-jewel">
                                @{{numberChange(ordering.rank[index],1)}}{{cache('config_jewel_alias')}}</div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>

        <div class="live-red-package d-none">
            <div class="qh-mask"></div>
            <div class="qh-popup">
                <div class="d-flex align-items-center p-3">
                    <div class="text-warning font-size-14 flex-grow-1">
                        发红包
                        <span class="text-999 pl-1 pr-2">
                            当前{{ cache('config_jewel_alias') }}：{{ $user['account']['jewel'] }}{{ cache('config_jewel_unit') }}
                            <a href="{{ route('member.wap.wallet.jewel.recharge') }}" class="text-warning">
                                充值<i class="iconfont icon-gengduo1 text-999 font-size-14"></i>
                            </a>
                        </span>
                    </div>
                    <div id="liveRedPackageClose" class="text-light font-size-14 iconfont icon-guanbi"></div>
                </div>
                <div class="live-red-package-list">
                    <div class="weui-grids">
                        <a data-price="10" href="javascript:;" class="weui-grid js_grid">
                            <div class="weui-grid__icon live-read-package-current">
                                10￥
                                <div class="font-size-12 text-999">{{10 * cache('config_recharge_jewel_rate')}}{{ cache('config_jewel_alias') }}</div>
                            </div>
                        </a>
                        <a data-price="20" href="javascript:;" class="weui-grid js_grid">
                            <div class="weui-grid__icon">
                                20￥
                                <div class="font-size-12 text-999">{{20 * cache('config_recharge_jewel_rate')}}{{ cache('config_jewel_alias') }}</div>
                            </div>
                        </a>
                        <a data-price="50" href="javascript:;" class="weui-grid js_grid">
                            <div class="weui-grid__icon">
                                50￥
                                <div class="font-size-12 text-999">{{50 * cache('config_recharge_jewel_rate')}}{{ cache('config_jewel_alias') }}</div>
                            </div>
                        </a>
                        <a data-price="100" href="javascript:;" class="weui-grid js_grid">
                            <div class="weui-grid__icon">
                                100￥
                                <div class="font-size-12 text-999">{{100 * cache('config_recharge_jewel_rate')}}{{ cache('config_jewel_alias') }}</div>
                            </div>
                        </a>
                        <a data-price="200" href="javascript:;" class="weui-grid js_grid">
                            <div class="weui-grid__icon">
                                200￥
                                <div class="font-size-12 text-999">{{200 * cache('config_recharge_jewel_rate')}}{{ cache('config_jewel_alias') }}</div>
                            </div>
                        </a>
                        <a data-price="500" href="javascript:;" class="weui-grid js_grid">
                            <div class="weui-grid__icon">
                                500￥
                                <div class="font-size-12 text-999">{{500 * cache('config_recharge_jewel_rate')}}{{ cache('config_jewel_alias') }}</div>
                            </div>
                        </a>
                    </div>
                    <div class="weui-cell">
                        <div class="weui-cell__hd">
                            <label class="weui-label">红包个数</label>
                        </div>
                        <div class="weui-cell__bd">
                            <input id="redPackageNumbers" class="weui-input" type="number" placeholder="请输红包个数" value="10">
                        </div>
                        <div class="weui-cell__ft">
                            个
                        </div>
                    </div>
                    <div class="weui-cells__tips">红包个数不能小于10个或者大于红包金额数。</div>
                    <div class="live-read-package-submit">
                        <a href="javascript:;" class="weui-btn weui-btn_primary"><i class="iconfont icon-hongbao1"></i> 塞进红包</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-none" id="qhVideoGift" style="z-index: 1000">
            <div class="qh-mask"></div>
            <div class="qh-popup">
                <div class="d-flex align-items-center p-3">
                    <div class="text-warning font-size-14 flex-grow-1">送给他一个小礼物吧〜</div>
                    <div id="qhVideoGiftClose" class="text-light font-size-14 iconfont icon-guanbi"></div>
                </div>
                <div id="qhVideoGiftList" class="d-flex flex-wrap text-light text-center px-2 pb-2"
                     style="max-height: 30vh; overflow-y: auto">
                    @foreach($gifts as $gift)
                        <div class="w-25" data-gift-id="{{ $gift['id'] }}">
                            <img class="d-block img-fluid w-50 mx-auto"
                                 src="{{ Storage::url($gift['thumbnail']) }}" alt="红胖子">
                            <div class="font-size-14 mt-2">{{ $gift['name'] }}</div>
                            <div class="font-size-12 text-999 pb-2">
                                @if($gift['pay_balance'] > 0)
                                    {{ $gift['pay_balance'] }}{{ cache('config_balance_unit') }}
                                @elseif($gift['pay_jewel'] > 0)
                                    {{ $gift['pay_jewel'] }}{{ cache('config_jewel_alias') }}
                                @elseif($gift['pay_integral'] > 0)
                                    {{ $gift['pay_integral'] }}{{ cache('config_integral_alias') }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex align-items-center font-size-14 px-3 py-2"
                     style="border-top: 1px solid rgba(0,0,0,.1)">
                    @auth
                        <i class="iconfont icon-yue text-primary" aria-hidden="true"></i>
                        <div class="text-999 pl-1 pr-2">
                            {{ $user['account']['balance'] }}{{ cache('config_balance_unit') }}
                        </div>
                        <i class="iconfont icon-zuanshi text-info" aria-hidden="true"></i>
                        <div class="text-999 pl-1 pr-2">
                            {{ $user['account']['jewel'] }}{{ cache('config_jewel_unit') }}
                        </div>
                        <i class="iconfont icon-jifen1 text-light" aria-hidden="true"></i>
                        <div class="text-999 pl-1 mr-auto">
                            {{ $user['account']['integral'] }}{{ cache('config_integral_unit') }}
                        </div>
                    @else
                        <div class="flex-grow-1">
                            <a class="text-secondary" href="{{ route('auth.login') }}">前往登录</a>
                        </div>
                    @endauth
                    <a href="{{ route('member.wap.wallet.jewel.recharge') }}" class="text-warning">
                        充值
                        <i class="iconfont icon-gengduo1 text-999 font-size-14"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="d-none" id="qhVideoShareQrCode">
            <div class="qh-mask"></div>
            <div class="qh-alert">
                <div class="bg-dark rounded p-3">
                    <div class="text-999">截图二维码发送给好友：</div>
                    <div class="text-center pt-2 pb-3">
                        <img id="qhVideoShareImg" width="100%" src="" alt="二维码">
                    </div>
                    <div class="text-999">复制链接分享给好友：</div>
                    <label class="d-block pt-2">
                    <textarea id="qhVideoShareTextarea"
                              class="w-100 rounded font-size-12 p-2 border-0"
                              data-clipboard-action="copy"
                              data-clipboard-target="#qhVideoShareTextarea">{{ route('wap.home') }}
                    </textarea>
                    </label>
                </div>
                <div class="d-flex justify-content-center">
                    <div id="qhVideoShareClose" class="text-light rounded-circle font-size-30 px-3">&times;</div>
                </div>
            </div>
        </div>
        <div id="miniPoster" class="weui-popup__container popup-bottom">
            <div class="weui-popup__overlay"></div>
            <div class="weui-popup__modal">
                <img src="" width="100%" />
            </div>
        </div>
    @endsection
    @push('scripts')
        <script src="https://cdn.bootcdn.net/ajax/libs/vue/2.6.9/vue.min.js"></script>
        <script src="https://cdn.bootcdn.net/ajax/libs/hls.js/0.13.2/hls.min.js"></script>
        <script>
            const Live = new Vue({
                el: '#LiveMain',
                data: {
                    auth: {{$user ? 'true' : 'false'}},
                    room: {{$room->user->id}},
                    content: '',
                    online: 0,
                    height: $(document).height(),
                    width: $(document).width(),
                    product_show: false,
                    hls: '{{$room->hls}}',
                    product_info: {!! $room->product ? json_encode($room->product) : 'null' !!},
                    messages: [
                            @if(!empty(cache('config_live_notice')))
                            @foreach(explode(PHP_EOL,cache('config_live_notice')) as $notice)
                        {
                            type: 'notice', content: '{{trim($notice)}}'
                        },
                        @endforeach
                        @endif
                    ],
                    gifts: [],
                    mall: {!! $mall ? json_encode($mall) : 'null' !!},
                    modal_show_index: 1,
                    modal_show: false,
                    followed: {{$follow}},
                    ordering: {!! $ordering ? json_encode($ordering) : '{list: [],rank: [],total: 0}' !!},
                    tryTimes: 0,
                    texting: false,
                    screen: null,
                    red_package_price: 10,
                    red_package_numbers: 10,
                    red_packages : {!! json_encode($red_data) !!},
                    red_show:false
                },
                methods: {
                    homePage: function (id) {
                        location.href = '{{route('user.homepage')}}?uid=' + id;
                    },
                    inputFocus: function () {
                        Live.texting = true;
                    },
                    hideKeyBord: function () {
                        window.scrollTo(0, 0);
                        setTimeout('Live.texting = false', 300);
                    },
                    send: function () {
                        Live.texting = false;
                        if (!Live.auth) {
                            Live.needLogin();
                            return;
                        }
                        if (this.content === '') {
                            $.toast("说点什么吧！", "text");
                            return;
                        }
                        Live.sendToLive({content: this.content, type: 'content'});
                        this.content = '';
                    },
                    joinLive: function () {
                        Live.sendToLive({type: 'join'});
                    },
                    redPackage: function () {
                        if (!Live.auth) {
                            Live.needLogin();
                            return;
                        }
                        $('.live-red-package').removeClass('d-none');
                        $('#liveRedPackageClose').click(function () {
                            $('.live-red-package .qh-mask').attr('class', 'qh-mask qh-mask-hide');
                            $('.live-red-package .qh-popup').attr('class', 'qh-popup qh-popup-hide');
                            setTimeout(function () {
                                $('.live-red-package').addClass('d-none');
                                $('.live-red-package .qh-mask').attr('class', 'qh-mask');
                                $('.live-red-package .qh-popup').attr('class', 'qh-popup');
                            }, 300);
                        });
                        $('.live-red-package-list .weui-grids a').click(function () {
                            $('.live-red-package-list .weui-grid__icon').removeClass('live-read-package-current');
                            $(this).find('.weui-grid__icon').addClass('live-read-package-current');
                            Live.red_package_price = $(this).data('price');
                        });
                    },
                    redShow: function(){
                        if (!Live.auth) {
                            Live.needLogin();
                            return;
                        }
                        Live.red_show = true;
                    },
                    redRob: function(red_id){
                        $.showLoading();
                        axios.post('{{route('live.wap.red.rob')}}', {
                            live_id: Live.room,
                            red_id: red_id
                        })
                            .then(response => {
                                $.hideLoading();
                                if(response.data.status){
                                    Live.red_packages = response.data.reds;
                                    $.toast(response.data.message, "text");
                                }else{
                                    $.alert(response.data.message);
                                }
                            })
                            .catch(function (error) {
                                console.log(error);
                            });
                    },
                    gift: function () {
                        if (!Live.auth) {
                            Live.needLogin();
                            return;
                        }
                        const qhVideoGift = document.getElementById('qhVideoGift');
                        const qhVideoGiftClose = document.getElementById('qhVideoGiftClose');
                        qhVideoGift.className = '';
                        qhVideoGiftClose.onclick = qhVideoGift.children[0].onclick = function () {
                            qhVideoGift.children[0].className = 'qh-mask qh-mask-hide';
                            qhVideoGift.children[1].className = 'qh-popup qh-popup-hide';
                            setTimeout(function () {
                                qhVideoGift.className = 'd-none';
                                qhVideoGift.children[0].className = 'qh-mask';
                                qhVideoGift.children[1].className = 'qh-popup';
                            }, 300);
                        };
                        // 送礼物
                        const giftItems = document.getElementById('qhVideoGiftList').children;
                        const giftCount = document.getElementById('qhVideoGiftList').childElementCount;
                        for (let i = 0; i < giftCount; i++) {
                            giftItems[i].onclick = e => {
                                const giftId = e.currentTarget.getAttribute('data-gift-id');
                                axios.get('{{ route('gift_api') }}', {params: {id: giftId, uid: {{$room->user->id}}}})
                                    .then(response => {
                                        $.toast(response.data.message);
                                        Live.sendToLive({gift: giftId, type: 'gift'});
                                    })
                                    .catch(error => {
                                        $.toast(error.response.data.message, "text");
                                    })
                            }
                        }
                    },
                    sendToLive: function (data) {
                        data.room = this.room;
                        axios.post('{{route('live.wap.message')}}', data)
                            .then(response => {
                                console.log(response.statusText);
                            })
                            .catch(function (error) {
                                console.log(error);
                            });
                    },
                    go_shopping: function (index) {
                        location.href = Live.mall[index].link ? Live.mall[index].link : '/mall/goods/' + Live.mall[index].id;
                    },
                    follow: function () {
                        if (!Live.auth) {
                            Live.needLogin();
                            return;
                        }
                        axios.get('{{ route('update_follow_api') }}', {params: {id: {{$room->user->id}}}})
                            .then(response => {
                                Live.followed = response.data.data.is_follow;
                                $.toast(Live.followed ? "已关注" : "已取消关注");
                            })
                            .catch(error => {
                                $.toast(error.response.data.message, "text");
                            })
                    },
                    numberChange: function (num, fix) {
                        return num >= 10000 ? parseFloat((num / 10000).toFixed(fix)) + 'w' : num;
                    },
                    ios_play: function () {
                        $('.ios_play').hide();
                        $('.qh-video-loading').show();
                        document.getElementById('LivePlayer').play();
                    },
                    live_close: function () {
                        axios.get('{{ route('live.wap.close') }}')
                            .then(response => {
                                $.alert('直播时长：' + response.data.hours + '分钟<br/>浏览次数：' + response.data.online + '次</br>礼物价值：' + response.data.jewel + '{{cache('config_jewel_unit').cache('config_jewel_alias')}}', "直播收益", function () {
                                    location.href = '{{route('live.wap.my')}}';
                                });
                            })
                            .catch(error => {
                                $.toast(error.response.data.message, "text");
                            })
                    },
                    needLogin: function () {
                        $.confirm({
                            title: '温馨提示',
                            text: '您需要登陆后才能操作哦，点击确定前往登陆。',
                            onOK: function () {
                                location.href = '{{route('auth.wap.login')}}';
                            }
                        });
                    },
                    change_product: function (index) {
                        axios.get('/live/product/' + Live.room + '/' + Live.mall[index].id)
                            .then(response => {
                                Live.modal_show = false;
                                $.toast('橱窗商品已更换', "text");
                            })
                            .catch(error => {
                                $.toast(error.response.data.message, "text");
                            })
                    },
                    fictitious: function () {
                        axios.get('/live/fictitious/' + Live.room)
                            .then(response => {
                                if (response.data.seconds > 0) {
                                    setTimeout('Live.fictitious()', response.data.seconds * 1000);
                                }
                            })
                            .catch(error => {
                                $.toast(error.response.data.message, "text");
                            })
                    },
                    appReset: function(platform,statusBarHeight){
                        $(".live-content").css("padding-top",statusBarHeight + 'px');
                        if(Live.screen){
                            $(".live-main video").css("padding-top",statusBarHeight + 'px');
                        }else{
                            $(".live-main video").height(Live.height + statusBarHeight);
                        };
                        switch(platform){
                            case 'android':
                                $(".live-message-product").height($(".live-message-product").height() - statusBarHeight);
                                break;
                            case 'iosX':
                                $(".qh-popup").css("padding-bottom","10px");
                                $(".live-bottom").css("margin-bottom","10px");
                                $(".live-message-product").css("bottom","3.5rem");
                                $(".live-message-product").height($(".live-message-product").height() - 50);
                                break;
                            case 'ios':
                                $(".live-message-product").height($(".live-message-product").height() - statusBarHeight);
                                break;
                        }
                    },
                    playBackList: function(){
                        @if($room->backs)
                        let backs = {!!json_encode($room->backs) !!};
                        let displayValues = [],values = [];
                        for(i in backs){
                            displayValues.push(backs[i].start_time + ' - ' + backs[i].end_time);
                            values.push(backs[i].url);
                        }
                        $('#playBack').picker({
                            title: "请选择往期视频",
                            toolbarCloseText: "确定",
                            cols: [
                                {
                                    textAlign: 'center',
                                    values,
                                    displayValues
                                },
                            ],
                            onClose: function(data){
                                if(Live.hls != values[Math.abs(data.cols[0].activeIndex)]){
                                    Live.hls = values[Math.abs(data.cols[0].activeIndex)];
                                    if (Hls.isSupported()) {
                                        const hls = new Hls();
                                        hls.loadSource(Live.hls);
                                        hls.attachMedia(document.getElementById('LivePlayer'));
                                    }else{
                                        player.src = Live.hls;
                                    }
                                }
                            }
                        });
                        @endif
                    },
                    share: function () {
                        const text = '【正在直播】{{$room->title}}';
                        const img = '{{ url($photo->getImgUrl($room->cover,300))}}';
                        const url = '{{ cache('config_share_domain') . '/sharing_page' }}?module=live&id=' + Live.room + '&uid=' + {{$user ? $user->id : 0}};
                        const desc = '点击进入观看精彩内容！';
                        if (window.Qihu) {
                            window.Qihu.share({title: text, text: desc, img, url});
                        } else {
                            let isWechat = false;
                            window.wx.ready(function () {
                                // 如果是小程序
                                window.wx.miniProgram.getEnv(function (res) {
                                    if (res.miniprogram) {
                                        $.actions({
                                            actions: [{
                                                text: "发送给朋友",
                                                onClick: function() {
                                                    // 小程序分享
                                                    window.wx.miniProgram.postMessage({
                                                        data: {
                                                            title: text,
                                                            imageUrl: img,
                                                            url
                                                        }
                                                    });
                                                    $.toast("请点击右上角（…）进行分享", "text");
                                                }
                                            },{
                                                text: "生成图片海报",
                                                onClick: function() {
                                                    $.showLoading('海报生成中');
                                                    axios.post('{{ route('api.mini.share.poster') }}', {
                                                        type: 'live',
                                                        title: text,
                                                        imageUrl: '{{ url($photo->getImgUrl($room->cover,600))}}',
                                                        url
                                                    })
                                                        .then(response => {
                                                            $.hideLoading();
                                                            $('#miniPoster img').attr('src',response.data.poster + '?s=' + Math.random());
                                                            $("#miniPoster").popup();
                                                        })
                                                        .catch(error => {
                                                            $.toast("海报生成失败", "text");
                                                            $.hideLoading();
                                                        })
                                                }
                                            }]
                                        });

                                        isWechat = true;
                                    }
                                });

                                if (!isWechat) {
                                    // 微信分享好友
                                    window.wx.updateAppMessageShareData({
                                        title: text,
                                        desc,
                                        link: url,
                                        imgUrl: img,
                                        success: function (res) {
                                        }
                                    });

                                    // 微信分享朋友圈
                                    window.wx.updateTimelineShareData({
                                        title: text,
                                        link: url,
                                        imgUrl: img,
                                        success: function (res) {
                                        }
                                    });

                                    isWechat = true;
                                    $.toast("请点击右上角（…）进行分享", "text");
                                }

                            });

                            if (!isWechat) {
                                const qhVideoShareQrCode = document.getElementById('qhVideoShareQrCode');
                                const qhVideoShareImg = document.getElementById('qhVideoShareImg');
                                qhVideoShareImg.src = '{{'data:image/png;base64,' . base64_encode($qrcode->urlQrCode(cache('config_share_domain') . '/sharing_page?module=live&id='.$room->user->id.'&uid='.($user ? $user->id : 0)))}}';
                                const qhVideoShareTextarea = document.getElementById('qhVideoShareTextarea');
                                qhVideoShareTextarea.value = url;
                                const qhVideoShareClose = document.getElementById('qhVideoShareClose');
                                qhVideoShareQrCode.className = '';
                                // 关闭礼物窗口
                                qhVideoShareQrCode.children[0].onclick = qhVideoShareClose.onclick = function () {
                                    qhVideoShareQrCode.children[0].className = 'qh-mask qh-mask-hide';
                                    qhVideoShareQrCode.children[1].className = 'qh-alert qh-alert-hide';
                                    setTimeout(function () {
                                        qhVideoShareQrCode.className = 'd-none';
                                        qhVideoShareQrCode.children[0].className = 'qh-mask';
                                        qhVideoShareQrCode.children[1].className = 'qh-alert';
                                    }, 300);
                                };
                            }
                        }
                    }
                },
                watch: {
                    screen: function (val) {
                        if (val) {
                            $('#LivePlayer').css({
                                'height': 'auto',
                                'top': '60px',
                                'object-fit': 'contain'
                            });
                            $('.live-message-product').height((Live.height - $('#LivePlayer').height() - 153) + 'px');
                        } else {
                            $('#LivePlayer').css({
                                'height': Live.height,
                                'top': 0,
                                'object-fit': 'cover'
                            });
                            $('.live-message-product').height('15rem');
                        }
                    }
                }
            });

            $(document).ready(function () {
                @if($room->status == 0 and !empty($room->hls))
                Live.playBackList();
                @endif
                $('.live-read-package-submit a').click(function () {
                    if ($('#redPackageNumbers').val() > Live.red_package_price) {
                        $.alert("红包个数必须小于等于红包金额数");
                        return;
                    } else if ($('#redPackageNumbers').val() < 10) {
                        $.alert("红包个数必须大于等于10个");
                        return;
                    } else {
                        Live.red_package_numbers = $('#redPackageNumbers').val();
                    }

                    $.confirm("您将被扣除" + (Live.red_package_price * {{cache('config_recharge_jewel_rate')}}) + "{{ cache('config_jewel_alias') }}用于发红包", function() {
                        $.showLoading();
                        axios.post('{{route('live.wap.red.pay')}}', {
                            live_id: Live.room,
                            amount: Live.red_package_price,
                            total: Live.red_package_numbers
                        })
                            .then(response => {
                                $.hideLoading();
                                if(response.data.status){
                                    $.toast(response.data.message);
                                    $('#liveRedPackageClose').click();
                                }else{
                                    $.alert(response.data.message);
                                }
                            })
                            .catch(function (error) {
                                console.log(error);
                            });
                    }, function() {
                        $('#liveRedPackageClose').click();
                    });
                });
                $('.live-main').height(Live.height);
                Live.screen = {{$room->screen}};
                Live.joinLive();
                @if($room->status == 1 and cache('config_live_fictitious_actity'))
                setTimeout('Live.fictitious()', 5000);
                @endif
                if (Live.product_info != null) {
                    setTimeout('Live.product_show = true', 2000);
                }
                const player = document.getElementById('LivePlayer');
                @if($room->status == 0 and !empty($room->hls))
                let m3u8 = '{{$room->hls}}';
                @else
                let m3u8 = '{!! $playUrl !!}';
                @endif

                if(m3u8 != ''){
                    if (Hls.isSupported()) {
                        const hls = new Hls();
                        hls.loadSource(m3u8);
                        hls.attachMedia(player);
                        hls.on(Hls.Events.MANIFEST_PARSED, function () {
                            Live.tryTimes = 0;
                            player.addEventListener('canplay', function (e) {
                                player.pause();
                                if (!window.Qihu) {
                                    $('.ios_play').show();
                                }
                                player.play();
                            });
                            player.addEventListener('play',function (e) {
                                $('.ios_play').hide();
                                $('.qh-video-loading').hide();
                                $('#LivePlayer').css('z-index', @if($room->screen) 20 @else 10 @endif);
                            });
                        });
                        @if($room->status > 0)
                        hls.on(Hls.Events.ERROR, function (e) {
                            Live.tryTimes++;
                        });
                        @endif
                    } else if (player.canPlayType('application/vnd.apple.mpegurl')) {
                        if (!window.Qihu) {
                            $('.ios_play').show();
                        }
                        player.src = m3u8;
                        player.addEventListener('loadedmetadata', function () {
                            Live.tryTimes = 0;
                            $('.ios_play').hide();
                            $('.qh-video-loading').hide();
                            player.play();
                            $('#LivePlayer').css('z-index', @if($room->screen) 20 @else 10 @endif);
                        });
                        @if($room->status > 0)
                        player.addEventListener('error', function () {
                            Live.tryTimes++;
                        });
                        @endif
                    }
                }else{
                    $('.ios_play').hide();
                    $('.qh-video-loading').hide();
                }
                var clipboard = new ClipboardJS('#qhVideoShareTextarea');
                // 显示用户反馈/捕获复制/剪切操作后选择的内容
                clipboard.on('success', function (e) {
                    $.toast("复制成功");
                    e.clearSelection();
                });
                clipboard.on('error', function () {
                    $.toast("复制失败", "cancel");
                });
            })
            window.Echo = new window.Echo({
                broadcaster: 'socket.io',
                host: '{{cache('config_live_socket_domain')}}'
            });
            //直播间互动
            window.Echo.channel('live-message-{{$room->user->id}}')
                .listen('\\LiveMessage', (e) => {
                    let nickname = '';
                    if (e.user) {
                        nickname = e.user.nickname ? e.user.nickname : e.user.username;
                    }
                    switch (e.type) {
                        case 'content':
                            Live.messages.push({type: 'message', nickname: nickname, content: e.content});
                            break;
                        case 'gift':
                            Live.messages.push({type: 'gift', nickname: nickname, gift: e.gift.name});
                            Live.gifts.unshift({user: e.user, gift: e.gift});
                            Live.ordering = e.ordering;
                            setTimeout(function () {
                                Live.gifts.pop();
                            }, 5000);
                            break;
                        case 'join':
                            Live.online = e.online;
                            if (e.user) {
                                Live.messages.push({type: 'join', nickname: nickname});
                            }
                            break;
                        case 'product':
                            Live.product_info = e.product;
                            Live.product_show = true;
                            break;
                        case 'red':
                            Live.messages.push({type: 'red', nickname: nickname});
                            Live.red_show = true;
                            Live.red_packages.unshift(e);
                            break;
                    }

                    if (Live.messages.length > 100) {
                        Live.messages.splice(0, 1);
                    }

                    $(".live-message-box").animate({scrollTop: $(".live-message-box")[0].scrollHeight}, 300);
                });

            wx.config( {!! $weChatJsSdk !!} );
        </script>
    @endpush