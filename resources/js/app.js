function checkGrid(gridId) {
    const gridElement = document.querySelector(`.grid-item[data-id="${gridId}"]`);

    axios.post('/path/to/checkGrid/route', { id: gridId })
    .then(response => {
        alert(response.data.message);

        if (response.data.message !== 'Try next time') {
            gridElement.classList.add('clicked');
            gridElement.innerHTML = '🎁'; 
        } else {
            gridElement.classList.add('clicked');
        }
        gridElement.onclick = null; 
    });
}

window.checkGrid = checkGrid;
