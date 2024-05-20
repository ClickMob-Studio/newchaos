<?php

include 'header.php';

if ($user_class->gang < 1) {
    diefun('You are not in a gang.');
}

$gangCompLeaderboard = getGangCompLeaderboard($user_class->gang);

?>

<div class='box_top'><h1>Gang Double EXP Challenge</h1></div>
<div class='box_middle'>
    <div class='pad'>
        <br />
        <center>
            <p>
                Mobsters, it's time to gang up and show what you & the homies are made of! Complete the missions below with 4 x Gang Double EXP Pills up for grabs!
            </p>
            <p>
                Each mission complete will earn your 1 x Gang Double EXP Pill. Once the mission is complete, your gang leader will be able to claim the prize on this page
                and the pill will be added to their inventory, ready for them to activate double EXP for your gang at anytime!
            </p>

            <p>Enjoy!</p>
            <br /><br /><hr />
        </center>


        <table id="newtables" style="width:100%; text-align: left;">
            <tr>
                <th><b>Mission</b></th>
                <th width="50%"><b>Progress</b></th>
                <th><b>Claim</b></th>
            </tr>
            <tr>
                <td>
                    <center>
                        <strong>Crimes</strong><br />
                        Complete 7,000,000 Crimes
                    </center>
                </td>
                <td>
                    <div class="progress pb-star-holder" style="height:2rem;" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" title="700,000/7,000,000">
                        <div class="progress-bar bg-success pb-star-bar" style="background-color: #ff6218 !important; width: 10%">
                            700,000/7,000,000
                        </div>
                    </div>
                </td>
                <td>
                    <center>
                        <a class="btn btn-success" href="#">Claim Prize</a>
                    </center>
                </td>
            </tr>
        </table>
    </div>
</div>

<?php
include 'footer.php';
?>
