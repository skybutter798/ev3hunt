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

        // Only update the grid if the response is not 'Try next time' and not 'You have reached your click limit for today.'
        if (response.data.message !== 'You have reached your click limit for today.') {
            gridElement.classList.add('clicked');
            gridElement.innerHTML = '🎁'; 
            gridElement.onclick = null; 

            // Update the remaining clicks count
            let remainingClicksDiv = document.getElementById('remainingClicksDiv');
            let currentClicks = parseInt(remainingClicksDiv.textContent.match(/\d+/)[0]);
            remainingClicksDiv.textContent = `You have ${currentClicks - 1} clicks left for today.`;
        }
    });
}

window.checkGrid = checkGrid;