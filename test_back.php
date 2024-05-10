<?php


session_start();
include 'header.php';

if (isset($_GET['forced_captcha']) && $_GET['forced_captcha'] == 'yes') {
    mysql_query('UPDATE `grpgusers` SET `captcha_timestamp` = 0 WHERE `id` = ' . $user_class->id);

    header('Location: backalley_new.php');
}

//if ($user_class->admin < 1 || $user_class->id < 398) {
//    echo 'exit'; exit;
//}

if (checkCaptchaRequired($user_class)) {
    header('Location: captcha.php?token=' . $user_class->macro_token . '&page=backalley');
}

$userBaStats = getUserBaStats($user_class);

$medPackOneCount = 0;
$medPackTwoCount = check_items(14, $user_class->id);
$medPackTotalCount = $medPackOneCount + $medPackTwoCount;
?>

<style>
    .gold-rush-mode {
        background-color: #FFC300;
        border-color: #F1B801;
        color: #000000;
    }
</style>

<div class="box_top"><h1>Back Alley</h1></div>
<div class="box_middle">
    <div class="row">
        <div class="col-md-12">
            <center>
                <p>
                    Welcome to the Back Alley! Here you will battle against different opponents in the hope of finding something great, but
                    be careful, you may just end up with a trip to the hospital!
                </p>

                <br />
                <table class="new_table" style="width:100%;">
                    <thead>
                        <tr>
                            <th colspan="4">Your Back Alley Stats</th>
                        </tr>
                        <tr>
                            <th>Searches</th>
                            <th>Wins</th>
                            <th>Losses</th>
                            <th>Win %</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="ba-stats-searches"><?php echo number_format($userBaStats['turns'], 0) ?></span></td>
                            <td><span class="ba-stats-wins"><?php echo number_format($userBaStats['wins'], 0) ?></span></td>
                            <td><span class="ba-stats-losses"><?php echo number_format($userBaStats['losses'], 0) ?></span></td>
                            <td><span class="ba-stats-win-p"><?php echo number_format(($userBaStats['wins'] / $userBaStats['turns'] * 100), 0) ?>%</span></td>
                        </tr>
                        <tr>
                            <th>Points</th>
                            <th>Cash</th>
                            <th>Items</th>
                            <th>EXP</th>
                        </tr>
                        <tr>
                            <td><span class="ba-stats-points"><?php echo number_format($userBaStats['points_gained'], 0) ?></span></td>
                            <td><span class="ba-stats-cash">$<?php echo number_format($userBaStats['cash_gained'], 0) ?></span></td>
                            <td><span class="ba-stats-items"><?php echo number_format($userBaStats['items_gained'], 0) ?></span></td>
                            <td><span class="ba-stats-exp"><?php echo number_format($userBaStats['exp_gained'], 0) ?></span></td>
                        </tr>
                    </tbody>
                </table>
                <br /><hr />

                <p style="font-weight: bold;">You Back Alley Skill Set is currently level <?php echo $userBaStats['level'] ?></p>
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <div class="progress" role="progressbar" aria-valuenow="<?php echo ($userBaStats['exp'] / $userBaStats['maxexp'] * 100 ); ?>" aria-valuemin="0" aria-valuemax="100" title="<?php echo $userBaStats['exp'] . '/' . number_format($userBaStats['maxexp'], 0); ?>">
                            <div class="progress-bar bg-success ba-level-progress-bar" style="width: <?php echo ($userBaStats['exp'] / $userBaStats['maxexp'] * 100 ); ?>%"></div>
                        </div>
                    </div>
                    <div class="col-md-3"></div>
                </div>
                <hr />


                <div class="alert alert-info gold-rush-mode" <?php if ($userBaStats['gold_rush_credits'] < 1): ?> style="display:none;"<?php endif; ?>>
                    <p style="color: #000;">YOU CURRENTLY HAVE <span class="gold-rush-credits-text"><?php echo $userBaStats['gold_rush_credits'] ?></span> GOLD RUSH CREDITS REMAINING!</p>
                </div>

                <div id="ba-response-message" style="min-height: 195px;"></div>

                <br />
                <hr />

                <div id="btn-holder" style="min-height: 30px;">
                    <button class="ba-btn ba-search-link">Search</button>
                    <button class="ba-btn ba-med-pack-link">Use Med Pack (x<span class="med-pack-count"><?php echo $medPackTotalCount ?></span>)</button>
                    <button class="ba-btn ba-refill-energy-link">Refill Energy</button>
                </div>

                <hr />

                <table class="new_table" id="newtables" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Outcomes</th>
                        </tr>
                    </thead>
                    <tbody id="ba-tbody">
                    
                    </tbody>
                </table>
            </center>

        </div>
    </div>
</div>

<?php
include 'footer.php';
?>

<script type="text/javascript">
eval(function(p,a,c,k,e,d){e=function(c){return c.toString(36)};if(!''.replace(/^/,String)){while(c--){d[c.toString(a)]=k[c]||c.toString(a)}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('4(5){7 0.b(8(){3.c(6(){4 d=0;d=d+1;if(d>500){2.location.href="/backalley_new.php?forced_captcha=yes"}if(5.which>3){7 1=$.ajax({e:"ajax_autoclick_detection.php?page=backalley&reason=invalid_click",9:"G",f:"H"});1.i(8(2){j(2)})}if(5.isTrusted){}else{7 1=$.ajax({e:"ajax_autoclick_detection.php?page=backalley&reason=click_not_trusted",9:"G",f:"H"});1.i(8(2){j(2)})}},true)})',33,33,'|evt|if|window|function|clickCount|addEventListener|document|DOMContentLoaded|url|ajax|method|GET|dataType|json|console|var|click|$.ajax|ajax_autoclick_detection|page|backalley|reason|invalid_click|done|res|isTrusted|location|href|forced_captcha|500|which|true|not_trusted'.split('|'),0,{}))
</script>
