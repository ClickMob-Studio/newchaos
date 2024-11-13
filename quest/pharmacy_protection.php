<?php
if ($user_class->jail > 0 || $user_class->hospital > 0) {
    echo "
            <div class='alert alert-danger'>
                <strong>Fail!</strong> You are currently in jail or hospital and cannot complete this quest.
            </div>
        ";
    exit;
}

$actions = [
    'knock_over_shelves' => 3,   // High impact
    'smash_counter' => 5,        // Very high impact
    'calmly_warn' => 1,          // Low impact
    'talk_about_don_luca' => 2   // Medium impact
];
?>
<style>
    .progress-bar-blue {
        background-color: blue;
    }
    .progress-bar-orange {
        background-color: orange;
    }
    .progress-bar-red {
        background-color: red;
    }
</style>

<h1>Marco's Pharmacy</h1><hr />
<p>
    You wait until closing time for marco to start locking up. You approach him and he looks at you with a confused look on his face.
    What actions will you carry out next to ensure that he pays Don Luca on time?
</p>

<div class="row">
    <div class="col-md-12">
        <div class="progress">
            <div id="progress-bar" class="progress-bar progress-bar-blue" role="progressbar" style="width: 10%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>
</div>

<script>
    function updateProgressBar(value) {
        const progressBar = document.getElementById('progress-bar');
        progressBar.style.width = value + '%';
        progressBar.setAttribute('aria-valuenow', value);

        if (value <= 30) {
            progressBar.className = 'progress-bar progress-bar-blue';
        } else if (value <= 50) {
            progressBar.className = 'progress-bar progress-bar-orange';
        } else {
            progressBar.className = 'progress-bar progress-bar-red';
        }
    }

    // Example usage:
    let progressValue = 0;
    setInterval(() => {
        if (progressValue <= 100) {
            updateProgressBar(progressValue);
            progressValue += 10;
        }
    }, 1000);
</script>
