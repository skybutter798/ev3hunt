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
        background: url('/img/island.png') no-repeat center center;
        background-size: cover;
        background-attachment: local;
        position: relative;
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
            pointer-events: auto;
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
    
    .custom-swal {
        background-image: url('/img/island.png');
        background-size: cover;
    }
    
    .grid-container.disabled .grid-item {
        pointer-events: none; /* Disables all click events */
        opacity: 0.5; /* Makes the grid items look faded */
    }
    
    .swal2-actions {
        flex-direction: row !important;  /* Make buttons stack horizontally */
        align-items: center;             /* Vertically center align buttons */
        justify-content: center;        /* Horizontally center align buttons */
    }
    
    .swal2-styled {
        margin-right: 10px;  /* Add some space between the buttons */
    }
    
    #boat {
        transition: top 2s, left 2s; /* This will ensure the boat moves smoothly over 2 seconds */
    }
    
    .grid-item:active {
        transform: scale(0.95); /* Slightly reduce the size of the grid when clicked */
        transition: transform 0.1s; /* Quick transition for the click effect */
    }

    .grid-item.reward-found {
        animation: rewardFound 0.5s forwards; /* Apply the animation named 'rewardFound' */
    }
    
    @keyframes rewardFound {
        0% {
            transform: scale(0.5);
            opacity: 0;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
    
    .main-container.disabled {
        opacity: 0.5;
        pointer-events: none;
    }

    .npc-icon {
        width: 100%;
        height: 100%;
        cursor: pointer;
        transition: transform 0.2s;
        pointer-events: auto; /* Always allow clicking on the NPC */
    }
    
    .npc-icon:hover {
        transform: scale(1.1); /* Slightly enlarge the NPC when hovered */
    }


</style>

<!-- Title and Tagline -->
<div class="main-container" style="margin-bottom : 50px;">
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
    <img src="/img/boat.png" id="npc" style="display: none; position: absolute;" onclick="npcClicked()">
    @foreach ($grids as $grid)
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
        const remainingClicksDiv = document.getElementById('remainingClicksDiv');
        let remainingClicks = parseInt(remainingClicksDiv.textContent.match(/\d+/)[0]);
    
        axios.post('/checkGrid', { id: gridId })
        .then(response => {
            if (response.data.message !== 'limit' && response.data.message !== 'shared' && response.data.message !== 'repeat') {
                gridElement.classList.add('clicked');
            }
            
            if (response.data.message === 'limit') {
                const twitterShareUrl = `https://twitter.com/intent/tweet?text=Unleash%20the%20Hunt:%20Secure%20Your%20Whitelist%20Spot%20Now!%20%23EV3%20%23BLUECODE&url=https://hunt.ev3nft.xyz/`;
                
                let swalConfig = {
                    title: 'EV3',
                    showConfirmButton: false,
                    html: `
                        You have reached your click limit for today. Share on twitter to get one more click!
                        <br><br>
                        <a href="${twitterShareUrl}" target="_blank">
                            <button class="swal2-confirm swal2-styled">Earn Extra Click</button>
                        </a>`
                };
            
                Swal.fire(swalConfig);
            
                document.querySelector('.swal2-confirm').addEventListener('click', function() {
                    remainingClicks++;
                    remainingClicksDiv.innerHTML = `You have ${remainingClicks} clicks left for today.`;
                    axios.post('/updateStatus')
                    .then(response => {
                        console.log(response.data.message); // Log the response for debugging
            
                        // Display a message to the user about the extra click
                        Swal.fire({
                            title: 'Success!',
                            text: 'You have earned one more click. Use it wisely!',
                            icon: 'success',
                            confirmButtonText: 'Got it!'
                        });
                    })
                    .catch(error => {
                        console.error("Error updating share status:", error);
                    });
                });
            };

            
            if (response.data.message === 'shared') {
                const twitterShareUrl = `https://twitter.com/intent/tweet?text=Unleash%20the%20Hunt:%20Secure%20Your%20Whitelist%20Spot%20Now!%20%23EV3%20%23BLUECODE&url=https://hunt.ev3nft.xyz/`;
                
                let swalConfig = {
                    title: 'EV3',
                    showConfirmButton: false,
                    html: `
                        You have reached your click limit for today. Help us to share out the fun!
                        <br><br>
                        <a href="${twitterShareUrl}" target="_blank">
                            <button class="swal2-confirm swal2-styled">Share on Twitter</button>
                        </a>`
                };

                Swal.fire(swalConfig);
            };
            
            if (response.data.message === 'reward') {
                const twitterShareUrl = `https://twitter.com/intent/tweet?text=I%20found%20my%20spot%20on%20EV3%20hunting!!%20%23EV3%20%23BLUECODE&url=https://hunt.ev3nft.xyz/`;
                gridElement.classList.add('reward-found');
                // Decrement and update the remaining clicks
                remainingClicks--;
                remainingClicksDiv.innerHTML = `You have ${remainingClicks} clicks left for today.`;
            
                let swalConfig = {
                    title: 'Congratulations! You found a reward! Please bind your wallet address to secure your whitelist spot!',
                    input: 'text',
                    inputPlaceholder: 'Enter your wallet address',
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    showLoaderOnConfirm: true,
                    preConfirm: (walletAddress) => {
                        return axios.post('/wallet', { wallet_address: walletAddress })
                            .then(response => {
                                if (!response.data.success) {
                                    throw new Error(response.data.message);
                                }
                                return response.data;
                            })
                            .catch(error => {
                                Swal.showValidationMessage(`Request failed: ${error}`);
                            });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                };
            
                Swal.fire(swalConfig).then((result) => {
                    if (result.value) {
                        Swal.fire({
                            title: 'Saved!',
                            text: 'Your wallet address has been saved.',
                            icon: 'success',
                            showConfirmButton: false,
                            html: `
                                <br><br>
                                <a href="${twitterShareUrl}" target="_blank">
                                    <button class="swal2-confirm swal2-styled">Share on Twitter</button>
                                </a>`
                        });
                    }
                });
            };

            
            if (response.data.message === 'none') {
                // Decrement and update the remaining clicks
                remainingClicks--;
                remainingClicksDiv.innerHTML = `You have ${remainingClicks} clicks left for today.`;
                let swalConfig = {
                    title: 'NO WAY !',
                    showConfirmButton: false,
                    html: `
                        Hi, try another luck we have whitelist spot left !`
                };

                Swal.fire(swalConfig);
            }
            
            if (response.data.message === 'repeat') {
                let swalConfig = {
                    title: 'Bzzzzt',
                    showConfirmButton: false,
                    html: `
                        Grid already clicked.`
                };

                Swal.fire(swalConfig);
            }

        });
    }
    
    window.checkGrid = checkGrid;
    
    document.addEventListener('DOMContentLoaded', function () {
        const userWalletAddress = document.getElementById('userWalletAddress');
        
        if (userWalletAddress && userWalletAddress.textContent) {
            // Display the SweetAlert message
            Swal.fire({
                title: 'Whitelisted!',
                html: `You've already found your spot! Make sure to follow EV3 or join discord to get updates! Your wallet address is: <br> <strong>${userWalletAddress.textContent}</strong>`,
                icon: 'info',
                allowOutsideClick: false, // Prevent closing the alert by clicking outside
                allowEscapeKey: false,    // Prevent closing the alert using the escape key
                showConfirmButton: false
            });

    
            // Fade out the main container
            const mainContainer = document.querySelector('.main-container');
            mainContainer.style.opacity = '0.5';
            mainContainer.style.pointerEvents = 'none'; // Disable all interactions
    
            // Enable only the logout button
            const logoutButton = document.querySelector('.twitter-login');
            if (logoutButton) {
                logoutButton.style.pointerEvents = 'auto';
            }
        }
    });

    
    
    let boatInterval;
    let boatPosition = { x: 0, y: 0 }; // Starting position
    
    // Define the grid dimensions
    const gridWidth = 50;  // Assuming you have 50 columns
    const gridHeight = 50; // Assuming you have 50 rows
    
    function showBoat() {
        // Set the boat's initial position
        boatPosition = { x: Math.floor(Math.random() * gridWidth), y: Math.floor(Math.random() * gridHeight) };
        console.log("Boat initial position:", boatPosition); // Log the initial position
        updateBoatPosition();
    
        // Start moving the boat
        boatInterval = setInterval(moveBoat, 2000); // Move every 2 seconds
    }
    
    function moveBoat() {
        // Randomly determine the boat's next direction
        const direction = Math.floor(Math.random() * 4);
        switch (direction) {
            case 0: // Up
                boatPosition.y = Math.max(0, boatPosition.y - 5);
                break;
            case 1: // Down
                boatPosition.y = Math.min(gridHeight - 5, boatPosition.y + 5);
                break;
            case 2: // Left
                boatPosition.x = Math.max(0, boatPosition.x - 5);
                break;
            case 3: // Right
                boatPosition.x = Math.min(gridWidth - 5, boatPosition.x + 5);
                break;
        }
        //console.log("Boat moved to position:", boatPosition); // Log the new position
        updateBoatPosition();
    }
    
    function updateBoatPosition() {
        const boatElement = document.getElementById('boat');
        const gridSize = 20; // Assuming each grid item is 20px by 20px
    
        // Calculate the boat's top and left position based on its x and y values
        const topPosition = boatPosition.y * gridSize;
        const leftPosition = boatPosition.x * gridSize;
    
        boatElement.style.top = `${topPosition}px`;
        boatElement.style.left = `${leftPosition}px`;
        boatElement.style.display = 'block'; // Show the boat
    
        //console.log("Boat position updated on grid to:", { top: topPosition, left: leftPosition }); // Log the updated position on the grid
    }
    
    function boatClicked() {
        // Determine the outcome when the boat is clicked
        const rewardChance = Math.random();
        let message = '';
        let icon = 'info';
    
        if (rewardChance < 0.5) {
            message = 'Congratulations! You found a special reward!';
            icon = 'success';
            // Give a special reward
            // ... your code to handle the reward ...
        } else {
            message = 'Sorry, no reward this time.';
            icon = 'error';
            // No reward
            // ... your code to handle the absence of a reward ...
        }
    
        // Display the popout message
        Swal.fire({
            title: 'Boat Clicked!',
            text: message,
            icon: icon,
            confirmButtonText: 'OK'
        });
    
        // Hide the boat
        const boatElement = document.getElementById('boat');
        boatElement.style.display = 'none';
    
        // Remove the boat's movement
        clearInterval(boatInterval);
    }
    
    showBoat();
    
    function showNpc() {
        // Set the boat's initial position
        npcPosition = { x: Math.floor(Math.random() * gridWidth), y: Math.floor(Math.random() * gridHeight) };
        console.log("Npc initial position:", npcPosition);
        
        updateNpcPosition();
    }
    
    function updateNpcPosition() {
        const npcElement = document.getElementById('npc');
        const gridSize = 20; // Assuming each grid item is 20px by 20px
    
        const topPosition = npcPosition.y * gridSize;
        const leftPosition = npcPosition.x * gridSize;
    
        npcElement.style.top = `${topPosition}px`;
        npcElement.style.left = `${leftPosition}px`;
        npcElement.style.display = 'block'; // Show the boat
    }
    
    function npcClicked() {
        // Start the first message
        firstMessage();
    }
    
    function firstMessage() {
        Swal.fire({
            title: 'Mysterious NPC',
            text: 'Ahoy, adventurer! Welcome to the Island Treasure Hunt. Set your sights on our vast 250 x 250 grid and brace yourself for a journey like no other.',
            imageUrl: '/img/pixel-frame.png',
            imageWidth: 400,
            imageHeight: 200,
            imageAlt: 'Pixel Frame',
            showCancelButton: true,
            confirmButtonText: 'Next',
            cancelButtonText: 'Exit'
        }).then((result) => {
            if (result.isConfirmed) {
                secondMessage();
            }
        });
    }
    
    function secondMessage() {
        Swal.fire({
            title: 'Island Secrets',
            text: 'Within this expansive realm, the unexpected awaits you. Some squares might hide coveted whitelist spots, while others guard hidden treasures or elusive tickets. And sometimes, the grid may just test your patience with an empty spot, leaving your fate in the hands of luck.',
            imageUrl: '/img/pixel-frame.png',
            imageWidth: 400,
            imageHeight: 200,
            imageAlt: 'Pixel Frame',
            showCancelButton: true,
            confirmButtonText: 'Next',
            cancelButtonText: 'Exit'
        }).then((result) => {
            if (result.isConfirmed) {
                thirdMessage();
            }
        });
    }
    
    function thirdMessage() {
        Swal.fire({
            title: 'The Island’s Generosity',
            text: 'By connecting with your Twitter, the island grants you the power of 2 clicks each day. As the clock resets at GMT+8 00:00, so do your chances. And if you ever find yourself eager for just one more chance, spread word of our land on Twitter, and an additional click shall be bestowed upon you.',
            imageUrl: '/img/pixel-frame.png',
            imageWidth: 400,
            imageHeight: 200,
            imageAlt: 'Pixel Frame',
            showCancelButton: true,
            confirmButtonText: 'Next',
            cancelButtonText: 'Exit'
        }).then((result) => {
            if (result.isConfirmed) {
                fourthMessage();
            }
        });
    }
    
    function fourthMessage() {
        Swal.fire({
            title: 'Whispers of the Wind',
            text: 'Always be on your guard, adventurer. The winds whisper of surprise events that might come your way. Our islands story unfolds on Twitter, so stay close and listen well. So, are you ready to test your mettle and seek out the treasures that await? The Island beckons! 🏝🔍🎁',
            imageUrl: '/img/pixel-frame.png',
            imageWidth: 400,
            imageHeight: 200,
            imageAlt: 'Pixel Frame',
            confirmButtonText: 'Ahoy!'
        });
    }

    showNpc();

</script>
@endsection
