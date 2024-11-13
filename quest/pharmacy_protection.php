<?php
if ($user_class->jail > 0 || $user_class->hospital > 0) {
    echo "
            <div class='alert alert-danger'>
                <strong>Fail!</strong> You are currently in jail or hospital and cannot complete this quest.
            </div>
        ";
    exit;
}


$actions = array(
    'knock_over_shelves' => array(
        'description' => 'Knock over shelves',
        'impact' => mt_rand(16,20),
        'response' => 'You knock over the shelves and Marco looks at you in shock.'
    ),
    'smash_counter' => array(
        'description' => 'Smash counter',
        'impact' => mt_rand(20,25),
        'response' => 'You smash the counter and Marco looks at you in horror.'
    ),
    'calmly_warn' => array(
        'description' => 'Calmly warn',
        'impact' => mt_rand(1,10),
        'response' => 'You calmly warn Marco that he needs to pay up or else. He looks at you with a confused look on his face.',
    ),
    'talk_about_don_luca' => array(
        'description' => 'Talk about Don Luca',
        'impact' => mt_rand(10,15),
        'response' => 'You talk about Don Luca and how he is not happy with Marco. Marco looks at you with a confused look on his face.'
    )
);
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


<center>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div id="success-msg-section"></div>
            <h3>Threat Level</h3>
            <div class="progress">
                <div id="progress-bar" class="progress-bar progress-bar-blue" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
    <br />

    <div class="row">
        <div class="col-md-12">
            <p style="font-weight: bold;">What's your next action?</p>
            <?php foreach ($actions as $action => $details): ?>
                <a href="#" class="btn btn-sm btn-primary threat-btn" data-impact-value="<?php echo $details['impact'] ?>" data-response-msg="<?php echo $details['response'] ?>"><?php echo $details['description'] ?></a> &nbsp;
            <?php endforeach; ?>
        </div>
    </div>
</center>

<script>
    // Generate random success thresholds between 40-50 and 60-70
    const lowerThreshold = Math.floor(Math.random() * (50 - 40 + 1)) + 40;
    const upperThreshold = Math.floor(Math.random() * (70 - 60 + 1)) + 60;

    document.querySelectorAll('.threat-btn').forEach(button => {
        button.addEventListener('click', function() {
            const impactValue = parseInt(this.getAttribute('data-impact-value'));
            const responseMsg = this.getAttribute('data-response-msg');

            // Update the progress bar
            const progressBar = document.getElementById('progress-bar');
            let currentProgress = parseInt(progressBar.getAttribute('aria-valuenow'));
            let newProgress = currentProgress + impactValue;
            if (newProgress > 100) newProgress = 100;

            // Check if progress is at least 30
            if (newProgress >= 30) {
                // If new progress is not between the success thresholds, display failure message
                if (newProgress > upperThreshold) {
                    const successMsgSection = document.getElementById('success-msg-section');
                    successMsgSection.innerHTML = '';

                    const responseDiv = document.createElement('div');
                    responseDiv.className = 'alert alert-danger';
                    responseDiv.innerHTML = 'You have failed to intimidate Marco successfully.';
                    successMsgSection.appendChild(responseDiv);
                    return;
                }

                if (newProgress > lowerThreshold && newProgress > upperThreshold) {
                    const successMsgSection = document.getElementById('success-msg-section');
                    successMsgSection.innerHTML = '';

                    const responseDiv = document.createElement('div');
                    responseDiv.className = 'alert alert-success';
                    responseDiv.innerHTML = 'You have successfully intimidated Marco.';
                    successMsgSection.appendChild(responseDiv);
                }
            }

            updateProgressBar(newProgress);

            // Clear old response messages
            const successMsgSection = document.getElementById('success-msg-section');
            successMsgSection.innerHTML = '';

            // Show the response message
            const responseDiv = document.createElement('div');
            responseDiv.className = 'alert alert-info';
            responseDiv.innerHTML = `${responseMsg}`;
            successMsgSection.appendChild(responseDiv);
        });
    });

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


</script>

<?php
exit;
