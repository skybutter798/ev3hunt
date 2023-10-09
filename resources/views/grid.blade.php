@extends('layouts.app')

@section('content')

<div id="" style="position: absolute; left: 80px; bottom: 50px; z-index: 100;">
    <img src="/img/bubble.png" id="bubble" style="height:100px;" onclick="bubbleClicked()">
</div>

<div id="npcContainer" style="position: absolute; left: 0; bottom: 0; z-index: 100;">
    <img src="/img/oldman_standv2.png" id="npc" style="height:500px;" onclick="">
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
    <div class="title-container">
        <h1>EV3 - Blue Code</h1>

        @if(Auth::check())
            <span style="color:white">Welcome, {{ Auth::user()->name }}</span>
            <span id="userWalletAddress" style="display: none;">{{ Auth::user()->wallet_address }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="twitter-login">Logout</button>
            </form>
        @else
            <a href="{{ route('login.twitter') }}" class="twitter-login">Login with Twitter</a>
        @endif
        
        @if(Auth::check() && Auth::user()->share == 1)
            <div id="remainingClicksDiv" style="color:white">
                You have 1 click left for today.
            </div>
        @else
            <div id="remainingClicksDiv" style="color:white">
                You have {{ $remainingClicks }} clicks left for today.
            </div>
        @endif

    </div>

    <div class="grid-container {{ !Auth::check() ? 'disabled' : '' }}">
        <img src="/img/boat.png" id="boat" style="display: none; position: absolute;" onclick="boatClicked()">
        @foreach ($grids as $grid)
            <div class="grid-item {{ $grid->clicked ? 'clicked' : '' }} {{ $grid->reward_item_id ? 'reward' : '' }}" data-id="{{ $grid->id }}">
                @if ($grid->reward_item_id)
                    <!-- Display a gift icon for grid items with a reward -->
                @endif
            </div>
        @endforeach
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="/js/app.js?v={{ filemtime(public_path('/js/app.js')) }}"></script>
@endsection
