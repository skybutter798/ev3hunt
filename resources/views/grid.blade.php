@extends('layouts.app')

@section('content')

<style>
    /* Reset default styles and fill the viewport */
    html, body {
        margin: 0;
        padding: 0;
        height: 100%;
        width: 100%;
        overflow: hidden; /* Optional: to avoid scrolling */
    }

    /* Adjust grid-container to fill the screen */
    .grid-container {
        display: grid;
        grid-template-columns: repeat(50, 2vw); /* This will create columns of 2% of viewport width */
        grid-template-rows: repeat(50, 2vh); /* This will create rows of 2% of viewport height */
        gap: 1px;
        width: 100vw; /* Full viewport width */
        height: 100vh; /* Full viewport height */
        position: relative;
        background: url('/img/island.png') no-repeat center center;
        background-size: cover;  // Make sure the image covers the entire grid.
    }
    
    .grid-item {
        width: 100%;
        height: 100%;
        border: 1px solid #ddd;
        background-color: rgba(255,255,255,0.5);  // semi-transparent white to show the island image behind
        transition: background-color 0.3s;
    
        &:hover {
            background-color: rgba(238,238,238,0.5);
            cursor: pointer;
        }
    
        &.clicked {
            background-color: rgba(204,204,204,0.5);
            pointer-events: none;  // Makes the grid box unclickable
        }
    }
</style>


<div class="grid-container">
    @foreach ($grids as $grid)
        <div class="grid-item {{ $grid->clicked ? 'clicked' : '' }}" data-id="{{ $grid->id }}">
        <div class="grid-item {{ $grid->clicked ? 'clicked' : '' }}" data-id="{{ $grid->id }}"  onclick="checkGrid({{ $grid->id }})">
            
            @if ($grid->reward_item_id)
                🎁
            @endif
            
        </div>
    @endforeach
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    import axios from 'axios';
    
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

</script>
@endsection
