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
    'punch' => array(
        'description' => 'Put a knuckle to his face',
        'impact' => mt_rand(10, 15),
        'response' => 'You land a heavy punch across Phil\'s jaw. He grunts, spits blood, but stays silent — for now.'
    ),
    'burn' => array(
        'description' => 'Burn your cigarette in his face',
        'impact' => mt_rand(20, 25),
        'response' => 'You hold Phil\'s head and press the cherry of your cigarette to Phil\'s left cheek. He screams, his body jerking in the chair.'
    ),
    'spit' => array(
        'description' => 'Spit in his eye',
        'impact' => mt_rand(1, 10),
        'response' => 'You spit in his face. He glares at you, more insulted than afraid — but the cracks are forming.',
    ),
    'whisper' => array(
        'description' => 'Whisper in his ear',
        'impact' => mt_rand(10, 15),
        'response' => 'You lean in, calmly reminding him what happens to rats in this city. His breathing becomes shallow.'
    ),
    'sister' => array(
        'description' => 'Mention his sister by name',
        'impact' => mt_rand(10, 15),
        'response' => 'You casually mention Elena — his sister — and how she\'s been walking home alone lately. Phil\'s eyes widen.'
    ),
    'finger' => array(
        'description' => 'Break a finger',
        'impact' => mt_rand(10, 15),
        'response' => 'You grab his hand and snap a finger clean back. Phil howls in agony, his resolve slipping.'
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

<h1>The Boiler Room</h1>
<hr />
<p>
    You drag Phil into the dimly lit backroom of The Boiler Room. The door slams shut behind you, and the only sound is
    the steady drip from a leaking pipe overhead. Phil's tied to a chair, blood already crusted on his collar — he's
    been softened up, but he's still not talking.
</p>


<center>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div id="success-msg-section"></div>
            <h3>Interrogation progress</h3>
            <div class="progress">
                <div id="progress-bar" class="progress-bar progress-bar-blue" role="progressbar" style="width: 0%;"
                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
    <br />

    <div class="row">
        <div class="col-md-12">
            <p style="font-weight: bold;">What will you do to make Phil start talking?</p>
            <?php foreach ($actions as $action => $details): ?>
                <a href="#" class="btn btn-sm btn-primary threat-btn" data-impact-value="<?php echo $details['impact'] ?>"
                    data-response-msg="<?php echo $details['response'] ?>"><?php echo $details['description'] ?></a> &nbsp;
            <?php endforeach; ?>
        </div>
    </div>
</center>

<script>
    // Generate random success thresholds between 50-60 and 70-80
    const lowerThreshold = Math.floor(Math.random() * (60 - 50 + 1)) + 50;
    const upperThreshold = Math.floor(Math.random() * (80 - 70 + 1)) + 70;
    console.log(`You need to pressure Phil to at least ${lowerThreshold}% to get him to talk. If you go beyond ${upperThreshold}%, he shuts down and says nothing.`);

    document.querySelectorAll('.threat-btn').forEach(button => {
        button.addEventListener('click', function () {
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
                    responseDiv.innerHTML = 'You have failed to intimidate Phil successfully.';
                    successMsgSection.appendChild(responseDiv);
                    return;
                }

                if (newProgress > lowerThreshold && newProgress < upperThreshold) {
                    const successMsgSection = document.getElementById('success-msg-section');
                    successMsgSection.innerHTML = '';

                    // AJAX request to mark the quest as complete
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'quest_ajax.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.send('user_id=<?php echo $user_class->id ?>&field=interrogate_phil&value=1');

                    const responseDiv = document.createElement('div');
                    responseDiv.className = 'alert alert-success';
                    responseDiv.innerHTML = 'Phil breaks and spills everything. <a href="quest.php">Click here to complete the interrogation.</a>';
                    successMsgSection.appendChild(responseDiv);
                    return;
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
