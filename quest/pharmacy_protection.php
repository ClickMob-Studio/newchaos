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
        'impact' => mt_rand(3,4)
    ),
    'smash_counter' => array(
        'description' => 'Smash counter',
        'impact' => mt_rand(5,7)
    ),
    'calmly_warn' => array(
        'description' => 'Calmly warn',
        'impact' => mt_rand(1,2)
    ),
    'talk_about_don_luca' => array(
        'description' => 'Talk about Don Luca',
        'impact' => mt_rand(2,3)
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
            <h3>Threat Level</h3>
            <div class="progress">
                <div id="progress-bar" class="progress-bar progress-bar-blue" role="progressbar" style="width: 10%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <p style="font-weight: bold;">What's your next action?</p>
            <?php foreach ($actions as $action => $details): ?>
                <a href="#" class="btn btn-primary threat-btn" data-impact-value="<?php echo $details['impact'] ?>"><?php echo $details['description'] ?></a> &nbsp;
            <?php endforeach; ?>
        </div>
        <div class="col-md-2"></div>
    </div>
</center>

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


</script>

<?php
exit;
