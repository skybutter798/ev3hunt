document.addEventListener('DOMContentLoaded', function () {
        const music = document.getElementById('backgroundMusic');
        const muteButton = document.getElementById('muteButton');
        const playMusicButton = document.getElementById('playMusicButton');
    
        playMusicButton.addEventListener('click', function() {
            music.play();
            playMusicButton.style.display = 'none'; // Hide the play button after clicking
            muteButton.style.display = 'block';
        });
        
        let clickSound = new Audio('/img/click.wav');
    
        // Add event listener to elements with the 'play-sound' class
        const soundElements = document.querySelectorAll('.play-sound');
        soundElements.forEach(element => {
            element.addEventListener('click', function() {
                clickSound.currentTime = 0;
                clickSound.play();
            });
        });
    
        muteButton.addEventListener('click', function() {
            if (music.muted) {
                music.muted = false;
                muteButton.textContent = 'Mute';
            } else {
                music.muted = true;
                muteButton.textContent = 'Unmute';
            }
        });
    
        const gridItems = document.querySelectorAll('.grid-item');
    
        gridItems.forEach(item => {
            item.addEventListener('click', function() {
                const gridId = this.getAttribute('data-id');
                checkGrid(gridId);
            });
        });
        
        const userWalletAddress = document.getElementById('userWalletAddress');
        
       /*if (userWalletAddress && userWalletAddress.textContent) {
            // Display the SweetAlert message
            Swal.fire({
                title: 'Whitelisted!',
                html: `
                    You've already found your spot! Make sure to follow EV3 or join discord to get updates! 
                    Your wallet address is: <br><br> <strong>${userWalletAddress.textContent}</strong>
                    <br><br>
                    
                `,
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false
            })
        
            // Fade out the main container
            const mainContainer = document.querySelector('.main-container');
            mainContainer.style.opacity = '0.5';
            mainContainer.style.pointerEvents = 'none'; // Disable all interactions
        
            // Enable only the logout button
            const logoutButton = document.querySelector('.twitter-login');
            if (logoutButton) {
                logoutButton.style.pointerEvents = 'auto';
            }
        } else {*/
        const npcContainer = document.getElementById('npcContainer');
        const closeNpcButton = document.getElementById('closeNpc');
        const mainContainer = document.querySelector('.main-container');
        //const userWinStatus = await checkUserWinStatus(window.userId);
    
        // Initially, disable the main content
        mainContainer.style.opacity = '0.2';
        mainContainer.style.pointerEvents = 'none';
    
        closeNpcButton.addEventListener('click', function() {
            // Hide the NPC and enable the main content
            npcContainer.style.display = 'none';
            mainContainer.style.opacity = '1';
            mainContainer.style.pointerEvents = 'auto';
        });
    
        // Check if userId exists (i.e., the user is authenticated)
        if (window.userId) {
            let shouldShowBoat = false;
        
            const randomNumber = Math.floor(Math.random() * 100) + 1; // This will give a number between 1 and 100
            
            if (randomNumber <= 10) {
                shouldShowBoat = true;
            }
        
            if (shouldShowBoat) {
                showBoat();
            } else {
                //if (!userWinStatus) {
                    // Display the password button
                    document.getElementById('passwordButton').style.display = 'block';
                //}
            }
        }

        bubbleClicked();
});

document.getElementById('passwordButton').addEventListener('click', function() {
    const correctPassword = document.querySelector('.main-container').getAttribute('data-password');

    Swal.fire({
        title: 'Special Reward Code',
        input: 'password',
        inputPlaceholder: '03******63',
        showCancelButton: true,
        confirmButtonText: 'Submit',
        confirmButtonColor: '#f53636',
        background: 'black',
        customClass: {
            title: 'custom-title-color',
            htmlContainer: 'custom-text-color',
        },
        preConfirm: (password) => {
            Swal.showLoading(); // Show the loader manually
            return new Promise((resolve, reject) => {
                setTimeout(() => { // Simulate async operation
                    if (password === correctPassword) {
                        showBoat();
                        document.getElementById('passwordButton').style.display = 'none'; // Hide the password button
                        resolve();
                    } else {
                        Swal.hideLoading(); // Hide the loader manually
                        Swal.showValidationMessage('Incorrect "music" code, find the code somewhere "behind" the island. Or ask for community help, code change everyday');
                        reject();
                    }
                }, 1000); // Simulate a delay of 1 second
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                confirmButtonColor: '#f53636',
                background: 'black',
                customClass: {
                    title: 'custom-title-color',
                    htmlContainer: 'custom-text-color',
                },
                icon: 'success',
                title: 'Success!',
                text: 'Something has appeared on the map!'
            });
        }
    });
});





function checkGrid(gridId) {
    const gridElement = document.querySelector(`.grid-item[data-id="${gridId}"]`);
    const remainingClicksDiv = document.getElementById('remainingClicksDiv');
    let remainingClicks = parseInt(remainingClicksDiv.textContent.match(/\d+/)[0]);
    let shareSound = new Audio('/img/share.wav');
    let winSound = new Audio('/img/win.wav');
    let coinSound = new Audio('/img/coin.wav');

    axios.post('/checkGrid', { 
        id: gridId,
        hasWalletAddress: !!document.getElementById('userWalletAddress').textContent
    })

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
                        <button class="swal2-confirm swal2-styled" style="background-color: black;">Earn Extra Click</button>
                    </a>`
            };
        
            Swal.fire(swalConfig);
        
            document.querySelector('.swal2-confirm').addEventListener('click', function() {
                remainingClicks++;
                remainingClicksDiv.innerHTML = `You have ${remainingClicks} clicks left for today.`;
                shareSound.play();
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
        }

        
        if (response.data.message === 'shared') {
            const twitterShareUrl = `https://twitter.com/intent/tweet?text=Unleash%20the%20Hunt:%20Secure%20Your%20Whitelist%20Spot%20Now!%20%23EV3%20%23BLUECODE&url=https://hunt.ev3nft.xyz/`;
            
            let swalConfig = {
                title: 'EV3',
                showConfirmButton: false,
                background: 'black',
                customClass: {
                    title: 'custom-title-color',
                    //htmlContainer: 'custom-text-color',
                },
                html: `
                    You have reached your click limit for today. Help us to share out the fun!
                    <br><br>
                    <a href="${twitterShareUrl}" target="_blank">
                        <button class="swal2-confirm swal2-styled" style="background-color: black;">Share on Twitter</button>
                    </a>`
            };

            Swal.fire(swalConfig);
        }
        
        if (response.data.message === 'reward') {
            const userWalletAddress = document.getElementById('userWalletAddress');
            
            if (userWalletAddress && userWalletAddress.textContent) {
                Swal.fire({
                    title: 'Whitelisted!',
                    html: `
                        You've already found your spot! Make sure to follow EV3 or join discord to get updates! 
                        Your wallet address is: <br><br> <strong>${userWalletAddress.textContent}</strong>
                        <br><br>
                        
                    `,
                    icon: 'info',
                    showConfirmButton: true
                })
            } else {
                const twitterShareUrl = `https://twitter.com/intent/tweet?text=I%20found%20my%20spot%20on%20EV3%20hunting!!%20%23EV3%20%23BLUECODE&url=https://hunt.ev3nft.xyz/`;
                gridElement.classList.add('reward-found');
                remainingClicks--;
                remainingClicksDiv.innerHTML = `You have ${remainingClicks} clicks left for today.`;
                winSound.play();
            
                let swalConfig = {
                    title: 'Congratulations! You found a reward! Please bind your wallet address to secure your whitelist spot!',
                    input: 'text',
                    inputPlaceholder: 'Enter your wallet address',
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    allowOutsideClick: false,
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
                                    <button class="swal2-confirm swal2-styled" style="background-color: black;">Share on Twitter</button>
                                </a>`
                        }).then(() => {
                            // Disable clicks on the main container after the wallet address has been saved
                            const mainContainer = document.querySelector('.main-container');
                            mainContainer.style.opacity = '0.5';
                            mainContainer.style.pointerEvents = 'none'; // Disable all interactions
                        });
                    }
                });
            }
        }
        
        if (response.data.message === 'cash') {
            const twitterShareUrl = `https://twitter.com/intent/tweet?text=I%20found%20gold%20in%20EV3%20hunting!!%20%23EV3%20%23BLUECODE&url=https://hunt.ev3nft.xyz/`;
            gridElement.classList.add('reward-found');
            // Decrement and update the remaining clicks
            remainingClicks--;
            remainingClicksDiv.innerHTML = `You have ${remainingClicks} clicks left for today.`;
            coinSound.play();
            let swalConfig = {
                title: 'Cash Grabber',
                showConfirmButton: false,
                html: `Congratulations! You have snagged a cash grab of $5! Keep hunting for a whitelist spot to secure your wallet. And dont fret if you dont find it immediately; we will prompt you to provide your wallet address at the game conclusion.`
            };
            Swal.fire(swalConfig);
        }

        
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
    //const isMobile = window.innerWidth <= 768; // You can adjust this value based on your mobile breakpoint
    //const boatElementId = isMobile ? 'boat-mobile' : 'boat-desktop';
    //const boatElement = document.getElementById(boatElementId);
    const boatElement = document.getElementById('boat');
    const gridSize = 20; // Assuming each grid item is 20px by 20px

    // Calculate the boat's top and left position based on its x and y values
    const topPosition = boatPosition.y * gridSize;
    const leftPosition = boatPosition.x * gridSize;

    boatElement.style.top = `${topPosition}px`;
    boatElement.style.left = `${leftPosition}px`;
    boatElement.style.display = 'block'; // Show the boat
}

function checkUserWinStatus(userId) {
    return axios.get(`/checkWinStatus`, {
        params: {
            user_id: userId
        }
    })
    .then(response => {
        console.log("User win status response:", response.data);  // Logging the response
        return response.data;
    })
    .catch(error => {
        console.error("Error checking user win status:", error);
    });
}


async function boatClicked() {
    const rewardChance = Math.floor(Math.random() * 100) + 1;
    const twitterShareUrl = `https://twitter.com/intent/tweet?text=Yes%20!%20I%20got%20my%20raffle%20ticket%20for%20the%20EV3%20exclusive%20prize!!%20%23EV3%20%23BLUECODE&url=https://hunt.ev3nft.xyz/`;
    let message = '';
    let icon = 'info';
    let imageUrl = null; 
    let winSound = new Audio('/img/win.wav');
    let rewardReceived = false; 
    
    const userWinStatus = await checkUserWinStatus(window.userId);
    
    if (userWinStatus && userWinStatus.alreadyWon) {
        message = 'Your ID is already registered in the database, and we will notice you on game day.';
        icon = 'info';
    } else if (rewardChance < 99) {
        message = 'Congratulations! You found a special reward! You have secured a ticket for the grand raffle, granting you a chance to win any of the items listed above! We have noted your entry. You dont need to do anything further for now. Continue your search for the whitelist!';
        icon = 'success';
        imageUrl = '/img/special2.png';
        winSound.play();
        rewardReceived = true;
    } else {
        message = 'Sorry, no reward this time.';
        icon = 'error';
    }
    
    console.log("Logging user_id:", window.userId); 
    console.log("Logging number:", rewardChance); 

    if (rewardReceived) {
        axios.post('/recordReward', {
            user_id: window.userId, 
            reward_type: "special",
        })
        .then(response => {
            if (response.data.success) {
                console.log("Reward recorded successfully!");
            } else {
                console.error("Failed to record the reward.");
            }
        })
        .catch(error => {
            console.error("Error recording the reward:", error);
        });
        
        // Display the popout message
        Swal.fire({
            title: 'Flying Code',
            text: message,
            imageUrl: imageUrl,
            background: 'black',
            showConfirmButton: false,  // This will hide the default OK button
            customClass: {
                title: 'custom-title-color',
                htmlContainer: 'custom-text-color',
            },
            html: `
                Congratulations! You found a special reward! You have secured a ticket for the grand raffle, granting you a chance to win any of the items listed above! We have noted your entry. You don't need to do anything further for now. Continue your search for the whitelist!
                <br><br>
                <a href="${twitterShareUrl}" target="_blank">
                    <button class="swal2-confirm swal2-styled" style="background-color: red;">Share</button>
                </a>`
        });
    } else {
        // Display the popout message
        Swal.fire({
            title: 'Flying Code',
            text: message,
            imageUrl: imageUrl,
            background: 'black',
            showConfirmButton: false,  // This will hide the default OK button
            customClass: {
                title: 'custom-title-color',
                htmlContainer: 'custom-text-color',
            },
            html: `
                Sorry, no reward this time..You don't need to do anything further for now. Continue your search for the whitelist!
                <br><br>
                <a href="${twitterShareUrl}" target="_blank">
                    <button class="swal2-confirm swal2-styled" style="background-color: red;">Share</button>
                </a>`
        });
    }

    // Hide the boat
    const boatElement = document.getElementById('boat');
    boatElement.style.display = 'none';

    // Remove the boat's movement
    clearInterval(boatInterval);
}



function bubbleClicked() {
    // Start the first message
    firstMessage();
    const npcContainer = document.getElementById('npcContainer');
    const mainContainer = document.querySelector('.main-container');
    
    npcContainer.style.display = 'block';
    mainContainer.style.opacity = '0.2';
    mainContainer.style.pointerEvents = 'auto';
}

function firstMessage() {
    let closeSound = new Audio('/img/close.wav');
    
    Swal.fire({
        title: 'Treasure Hunt',
        text: 'Ahoy, adventurer! Welcome to the Island Treasure Hunt. Set your sights on our vast 50 x 50 grid and brace yourself for a journey like no other.',
        imageUrl: '/img/whitelist.png?v1',
        imageAlt: 'EV3 Hunt',
        showCancelButton: true,
        confirmButtonText: 'Next',
        confirmButtonColor: '#f53636',
        cancelButtonText: 'Exit',
        cancelButtonColor: '#000000',
        background: 'black',
        customClass: {
            title: 'custom-title-color',
            htmlContainer: 'custom-text-color',
        },
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            secondMessage();
        } else if (result.isDismissed) {
            // This will run when the "Exit" button is clicked
            closeSound.play();
            const npcContainer = document.getElementById('npcContainer');
            const mainContainer = document.querySelector('.main-container');
            
            npcContainer.style.display = 'none';
            mainContainer.style.opacity = '1';
            mainContainer.style.pointerEvents = 'auto';
            const music = document.getElementById('backgroundMusic');
            music.play();
            playMusicButton.style.display = 'none'; // Hide the play button after clicking
            muteButton.style.display = 'block';
        }
    });
}

function secondMessage() {
    let closeSound = new Audio('/img/close.wav');
    Swal.fire({
        title: 'Island Secrets',
        text: 'Within this expansive realm, the unexpected awaits you. Some squares might hide coveted whitelist spots, while others guard hidden treasures or elusive tickets. And sometimes, the grid may just test your patience with an empty spot, leaving your fate in the hands of luck.',
        imageUrl: '/img/reward.png?v1',
        imageAlt: 'Rewards',
        showCancelButton: true,
        confirmButtonText: 'Next',
        confirmButtonColor: '#f53636',
        cancelButtonText: 'Exit',
        cancelButtonColor: '#000000',
        background: 'black',
        customClass: {
            title: 'custom-title-color',
            htmlContainer: 'custom-text-color',
        },
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            thirdMessage();
        } else if (result.isDismissed) {
            // This will run when the "Exit" button is clicked
            closeSound.play();
            const npcContainer = document.getElementById('npcContainer');
            const mainContainer = document.querySelector('.main-container');
            
            npcContainer.style.display = 'none';
            mainContainer.style.opacity = '1';
            mainContainer.style.pointerEvents = 'auto';
            const music = document.getElementById('backgroundMusic');
            music.play();
            playMusicButton.style.display = 'none'; // Hide the play button after clicking
            muteButton.style.display = 'block';
        }
    });
}

function thirdMessage() {
    let closeSound = new Audio('/img/close.wav');
    Swal.fire({
        title: 'The Island’s Generosity',
        text: 'By connecting with your Twitter, the island grants you the power of 2 clicks each day. As the clock resets at GMT+8 00:00, so do your chances. And if you ever find yourself eager for just one more chance, spread word of our land on Twitter, and an additional click shall be bestowed upon you.',
        imageUrl: '/img/twittershare.png?v1',
        imageAlt: 'Clicking Life',
        showCancelButton: true,
        confirmButtonText: 'Next',
        confirmButtonColor: '#f53636',
        cancelButtonText: 'Exit',
        cancelButtonColor: '#000000',
        background: 'black',
        customClass: {
            title: 'custom-title-color',
            htmlContainer: 'custom-text-color',
        },
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            fourthMessage();
        } else if (result.isDismissed) {
            // This will run when the "Exit" button is clicked
            closeSound.play();
            const npcContainer = document.getElementById('npcContainer');
            const mainContainer = document.querySelector('.main-container');
            
            npcContainer.style.display = 'none';
            mainContainer.style.opacity = '1';
            mainContainer.style.pointerEvents = 'auto';
            
            const music = document.getElementById('backgroundMusic');
            music.play();
            playMusicButton.style.display = 'none'; // Hide the play button after clicking
            muteButton.style.display = 'block';
        }
    });
}

function fourthMessage() {
    let closeSound = new Audio('/img/close.wav');
    Swal.fire({
        title: 'Whispers of the Wind',
        text: 'Always be on your guard, adventurer. The winds whisper of surprise events that might come your way. Our islands story unfolds on Twitter, so stay close and listen well. So, are you ready to test your mettle and seek out the treasures that await? The Island beckons! 🏝🔍🎁',
        imageUrl: '/img/bluecode.png?v1',
        imageAlt: 'EV3 Blue Code',
        confirmButtonText: 'Ahoy!',
        confirmButtonColor: '#f53636',
        background: 'black',
        customClass: {
            title: 'custom-title-color',
            htmlContainer: 'custom-text-color',
        },
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            // This will run when the "Exit" button is clicked
            closeSound.play();
            const npcContainer = document.getElementById('npcContainer');
            const mainContainer = document.querySelector('.main-container');
            
            npcContainer.style.display = 'none';
            mainContainer.style.opacity = '1';
            mainContainer.style.pointerEvents = 'auto';
            
            const music = document.getElementById('backgroundMusic');
            music.play();
            playMusicButton.style.display = 'none'; // Hide the play button after clicking
            muteButton.style.display = 'block';
        }
    });
}

window.onload = function () {
  var countEL = document.getElementById("count");
  var waterEl = document.getElementById("water");
};