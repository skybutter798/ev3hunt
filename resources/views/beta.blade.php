@extends('layouts.app')

@section('content')
<div id="npcContainer" style="position: absolute; left: 10%; bottom: 0; z-index: 100;">
    <img src="/img/oldman_standv3.png" id="npc" style="height:500px;" onclick="">
    <button id="closeNpc" style="
    position: absolute; 
    top: 10px; 
    right: 10px; 
    background-color: #4b4b4b;
    color: white;
    cursor: pointer;
    width: 30px;
    height: 30px;
    border-style: solid;
    border-color: #ffffff;
    display:none;
    cursor: pointer;">X</button>
</div>

<div class="main-container">
    <audio id="backgroundMusic" loop>
        <source src="/img/Pixel-Island.mp3" type="audio/mp3">
        Your browser does not support the audio element.
    </audio>

    <div class="title-container" style="margin-bottom:10px">
        <h1>EV3 - Blue Code</h1>
        @if(Auth::check())
            <span style="color:white">Welcome, {{ Auth::user()->name }}</span>
            <span id="userWalletAddress" style="display: none;">{{ Auth::user()->wallet_address }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="twitter-login">Logout</button>
            </form>
        @else
            <a href="{{ route('login.twitter') }}" class="twitter-login">Twitter Login</a>
        @endif
        
        <div style="display: flex; align-items: center;">
            @if(Auth::check() && Auth::user()->share == 1)
                <div id="remainingClicksDiv" style="color:white; margin-right: 10px;">
                    You have 1 click left for today.
                </div>
            @else
                <div id="remainingClicksDiv" style="color:white; margin-right: 10px;">
                    You have {{ $remainingClicks }} clicks left for today.
                </div>
            @endif
            <button id="bubble" onclick="bubbleClicked()" class="play-sound" style="background-color: #2778c4; color: white; border: solid; border-width: thin;">Info</button>
            <button id="playMusicButton" style="background-color: #2778c4; color: white; border: solid; border-width: thin;">Music</button>
            <button id="muteButton" style="background-color: #2778c4; color: white; border: solid; border-width: thin; display:none">Mute</button>
        </div>
    </div>

    <div class="grid-container {{ !Auth::check() ? 'disabled' : '' }}">
        <img src="/img/boat.png?v1" id="boat" style="display: none; position: absolute; width:10px" onclick="boatClicked()">
        @foreach ($grids as $grid)
            <div class="grid-item play-sound {{ $grid->clicked ? 'clicked' : '' }} " data-id="{{ $grid->id }}">
            
            </div>
        @endforeach
    </div>
    {{--<div>.... - - .--. ... ---... -..-. -..-. .... ..- -. - .-.-.- . ...- ...-- -. ..-. - .-.-.- -..- -.-- --.. -..-. .-- .- - . .-. -.-. .- ...- .</div>--}}
    
</div>
<script>window.logoutRoute = '{{ route('logout') }}';</script>

@if(Auth::check())
<script>window.userId = @json(Auth::user()->id);</script>
@endif

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="/js/app.js?v={{ filemtime(public_path('/js/app.js')) }}"></script>
@endsection
