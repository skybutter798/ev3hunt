@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Reset default styles and fill the viewport */
    html, body {
        margin: 0;
        padding: 0;
        height: 100%;
        width: 100%;
        background-color: black; /* Set background to black */
        display: flex; /* Flexbox for centering */
        align-items: center; /* Vertical centering */
        justify-content: center; /* Horizontal centering */
    }
    
    /* Adjust grid-container to fill the screen */
    .grid-container {
        display: grid;
        grid-template-columns: repeat(50, 20px); /* Set to a fixed value for square boxes */
        grid-template-rows: repeat(50, 20px); /* Same fixed value for square boxes */
        gap: 1px;
        max-width: 1000px; /* 50 boxes * 20px each */
        max-height: 1000px; /* 50 boxes * 20px each */
        overflow-y: auto; /* Make it vertically scrollable */
        background: url('/img/islandv2.png') no-repeat center center;
        background-size: cover;
        background-attachment: local;
    }
    
    .grid-item {
        width: 100%;
        height: 100%;
        border: 1px solid #ddd;
        background-color: rgba(255,255,255,0.1);
        transition: background-color 0.3s;
    
        &:hover {
            background-color: rgba(238,238,238,0.5);
            cursor: pointer;
        }
    
        &.clicked {
            background-color: black;
            pointer-events: none;
        }
    
        &.reward {
            background-color: rgba(255,217,102,0.5);
        }
    }
    /* Styling for the main container */
    .main-container {
        display: flex;
        flex-direction: column;
        align-items: center; /* Center children horizontally */
        justify-content: center; /* Center children vertically */
        height: 100vh; /* Take full viewport height */
    }

    /* Modify the title-container to use Flexbox */
    .title-container {
        display: flex;
        flex-direction: column;
        align-items: center; /* Center the title and tagline */
        position: relative; /* Set to relative to position the Twitter login button */
        width: 100%;
    }

    /* Style for the Twitter login button */
    .twitter-login {
        position: absolute; /* Absolute positioning */
        top: 50%; /* Center it vertically */
        right: 0; /* Push it to the right */
        transform: translateY(-50%); /* Adjust for perfect centering */
        background-color: #1DA1F2;
        color: white;
        padding: 10px 20px;
        border-radius: 20px;
        text-decoration: none;
        font-weight: bold;
        transition: background-color 0.3s;
    }

    .twitter-login:hover {
        background-color: #0c85d0;
    }

    h1 {
        font-size: 2em;
        color: #FFFFFF;
        margin-bottom: 10px; /* Add some space between the title and the tagline */
    }

    h3 {
        font-size: 1.2em;
        color: #888; /* A lighter color for the tagline */
    }

</style>

<!-- Title and Tagline -->
<div class="main-container">
    <div class="title-container">
        <h1>Your Game Title</h1>
        <h3>Your Game Tagline</h3>
        
        @if(Auth::check())
            <span>Welcome, {{ Auth::user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="twitter-login">Logout</button>
            </form>
        @else
            <a href="{{ route('login.twitter') }}" class="twitter-login">Login with Twitter</a>
        @endif
        
        <div id="remainingClicksDiv">
            You have {{ $remainingClicks }} clicks left for today.
        </div>


        

    </div>

    <div class="grid-container">
        @foreach ($grids as $grid)
            {{--<div class="grid-item {{ $grid->clicked ? 'clicked' : '' }} {{ $grid->reward_item_id ? 'reward' : '' }}" data-id="{{ $grid->id }}" onclick="checkGrid({{ $grid->id }})">--}}
            <div class="grid-item {{ $grid->clicked ? 'clicked' : '' }} {{ $grid->reward_item_id ? 'reward' : '' }}" data-id="{{ $grid->id }}">
                @if ($grid->reward_item_id)
                    🎁  <!-- Display a gift icon for grid items with a reward -->
                @endif
            </div>
        @endforeach
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const gridItems = document.querySelectorAll('.grid-item');

        gridItems.forEach(item => {
            item.addEventListener('click', function() {
                const gridId = this.getAttribute('data-id');
                checkGrid(gridId);
            });
        });
    });

    function checkGrid(gridId) {
        const gridElement = document.querySelector(`.grid-item[data-id="${gridId}"]`);

        axios.post('/checkGrid', { id: gridId })

        .then(response => {
            let swalConfig = {
                title: 'Notification',
                text: response.data.message,
                icon: response.data.message === 'You have reached your click limit for today.' ? 'error' : 'success',
                confirmButtonText: 'OK',
            };

            if (response.data.message === 'You have reached your click limit for today.') {
                const twitterShareUrl = `https://twitter.com/intent/tweet?text=Your%20preset%20message%20here&url=https://yourwebsite.com`;
                swalConfig.footer = `<a href="${twitterShareUrl}" target="_blank" class="btn btn-primary">Share on Twitter</a>`;
            }

            Swal.fire(swalConfig);

            if (response.data.message !== 'Try next time') {
                gridElement.classList.add('clicked');
                gridElement.innerHTML = '🎁'; 
            } else {
                gridElement.classList.add('clicked');
            }
            gridElement.onclick = null; 
        });
        
        // Update the remaining clicks count
        let remainingClicksDiv = document.getElementById('remainingClicksDiv');
        let currentClicks = parseInt(remainingClicksDiv.textContent.match(/\d+/)[0]);
        remainingClicksDiv.textContent = `You have ${currentClicks - 1} clicks left for today.`;
    }

    window.checkGrid = checkGrid;

</script>
@endsection
