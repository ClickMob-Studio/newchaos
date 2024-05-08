<?php 
require "headertest.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Display Modal</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Custom styling for the modal */
        .modal-content {
            background-color: #333;  /* Dark background */
            color: #fff;  /* White text color */
        }
        .modal-header, .modal-body {
            border-bottom: none;  /* Remove default borders */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <!-- Link to trigger modal -->
        <a href="#" data-bs-toggle="modal" data-bs-target="#timeModal">
            <i class="fa-solid fa-clock"></i> Check Server Time
        </a>

        <!-- Modal -->
        <div class="modal fade" id="timeModal" tabindex="-1" aria-labelledby="timeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="timeModalLabel">Current Time</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="timeDisplay">
                        <!-- Time will be displayed here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS with Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JavaScript to update and show time -->
    <script>
        var timeModal = document.getElementById('timeModal');
        timeModal.addEventListener('show.bs.modal', function () {
            var now = new Date();
            var formattedTime = now.toLocaleTimeString(); // You can format it to match your needs
            document.getElementById('timeDisplay').textContent = formattedTime;
        });
    </script>
</body>
</html>
