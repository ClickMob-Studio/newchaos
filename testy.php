<?php 
require "headertest.php";
?>
<button type="button" class="btn btn-primary" id="timeButton" data-bs-toggle="tooltip" data-bs-placement="top" title="Click to see server time">
            <i class="fa-solid fa-clock"></i> Check Server Time
        </button>
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl, {
                    trigger: 'click', // Tooltip will show on click
                    title: function() {
                        return new Date().toLocaleTimeString(); // Returning current time as tooltip title
                    }
                });
            });
        });
    </script>