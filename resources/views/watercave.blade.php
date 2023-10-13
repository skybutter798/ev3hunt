<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Join the Mission</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body style="background-color:black">
    @if($hasJoined)
    <div class="alert alert-danger">
        You have already joined the mission. Thank you for your commitment!
    </div>
@else
    <div class="d-flex flex-column align-items-center justify-content-center" style="height: 100vh;width:80%; margin:auto">
        <div class="text-center mb-4">
            You are one of the select few. Out of a hundred, only 10 have been chosen, and you are among them. Your mission, should you choose to accept, is to protect King Coral during the Peralihan Monsun onslaught. Click 'I'm in' to confirm your commitment. Tread lightly and remain inconspicuous. If you choose to decline, this opportunity will be passed on to another.
        </div>
        <div>
            <button id="updateButton" class="btn btn-danger">I'M IN</button>
        </div>
    </div>
@endif
    <script>
        $('#updateButton').on('click', function() {
            $.ajax({
                url: '/update-watercave',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // For CSRF protection
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                    } else {
                        alert('Failed to update status.');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        });
    </script>
</body>
</html>
