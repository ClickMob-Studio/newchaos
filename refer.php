<?php
include 'header.php';
?>
<div class='box_top'>Referral</div>
						<div class='box_middle'>
							<div class='pad'>
<style>
/* Base styling */
body {
    background-color: #121212;
    color: #c0c0c0;
    font-family: 'Arial', sans-serif;
}

/* General styling for h3 headers to match tables */
/*h3 {*/
/*    background-color: #333; !* Dark background to match tables *!*/
/*    color: #FFD700; !* Gold color to stand out *!*/
/*    padding: 10px;*/
/*    margin: 20px auto; !* Center the header and give some space around it *!*/
/*    text-align: center;*/
/*    border-top: 3px solid #FFD700; !* Gold top border for distinction *!*/
/*    border-bottom: 1px solid #444; !* Dark border to match table styling *!*/
/*    width: 75%; !* Match the width of the tables *!*/
/*    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); !* Soft shadow for depth *!*/
/*    border-radius: 4px; !* Slight rounding of corners *!*/
/*}*/

hr {
    display: none; /* Remove the horizontal rule as the header now has borders */
}

/* Referral box styling */
.referral-box {
    background: #252525;
    padding: 15px;
    border-radius: 4px;
    margin: 10px auto;
    width: 75%;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

/* Table styling */
.table {
    background-color: #333;
    color: #c0c0c0;
    width: 100%;
    border-collapse: collapse;
    margin: 10px auto;
}

.table th, .table td {
    padding: 10px;
    border-bottom: 1px solid #444;
}

.table th {
    background-color: #222;
    color: #9CDCFE;
}

.table tr:hover {
    background-color: #2A2D2E;
}

/* Rewards Milestones styling */
.rewards-milestones {
    background: #333;
    padding: 15px;
    margin: 20px auto;
    border-radius: 4px;
    width: 75%;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.rewards-milestones ul {
    list-style-type: none;
    padding: 0;
}

.rewards-milestones li {
    background: #222;
    padding: 10px;
    margin-bottom: 8px;
    border-left: 4px solid #FFD700;
    color: #c0c0c0;
}

.discord-logo {
      height: 60px; /* Adjust height for better scaling on mobile */
      margin-left: 20px;
    }

/* Responsive styling */
@media only screen and (max-width: 768px) {
    h3, .referral-box, .rewards-milestones {
        width: 95%;
    }
}</style>

<script>
function startCountdown(duration, display) {
    var timer = duration, minutes, seconds;
    setInterval(function () {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        if (--timer < 0) {
            display.textContent = "00:00";
        }
    }, 1000);
}

window.onload = function () {
    var countdownDisplay = document.querySelector('#time');
    startCountdown(countdownSeconds, countdownDisplay);
};
</script>


 <div class="floaty" style="margin:3px;">

<br>


<div class="referral-system">
    <h4>Referral System</h4>
<br>
    <div>
    <h4>Your referral link</h4>
        <input type="text" value="https://chaoscity.co.uk/home.php?referer=<?php echo $user_class->id; ?>" readonly onclick="this.select();" style="width: 50%">
    </div>
    <div class="rewards">
        <b>Reward:</b> <span style="color:red;">50 Gold</span> per referral.
    </div>
    <p>
        Referrals are paid out once the player referred hits level 100. If your referral hasn't been paid, please message an admin.
    </p>
</div>

<div class="rewards-info">
    <h4>Rewards Milestones</h4>
    <ul>
        <li><h4><font color=red>5 Referrals</font> - 150 GOLD</h4></li>
        <li><h4><font color=red>10 Referrals</font> - 500 GOLD</h4></li>
        <li><h4><font color=red>15 Referrals</font> - 1,000 GOLD</h4></li>
        <li><h4><font color=red>25 Referrals</font> - 2,000 GOLD</h4></li>

        <!-- Add more milestones as necessary -->
    </ul>
</div>


<hr>
<div class="rewards-info">
       <h4>Your Referrals</h4>
    <table>
        <tr>
            <th>Mobster</th>
            <th>Status</th>
            <th>Reward [Gold]</th>
        </tr>
        <?php
        // Assuming you have a connection $conn
        $referrer_id = mysql_real_escape_string($user_class->id); // Basic attempt to sanitize input
        $query = "SELECT * FROM referrals WHERE referrer = '$referrer_id' ORDER BY id DESC";
        $result = mysql_query($query);

        if (mysql_num_rows($result) == 0) {
            echo '<tr><td colspan="3">You have no referrals</td></tr>';
        } else {
            while ($row = mysql_fetch_assoc($result)) {
                $credited = ($row['credited'] == 0) ? "Pending" : "Approved";
                $points = ($row['credited'] == 0) ? "0" : "100 + 50 Credits"; // Adjust based on your logic
                echo '<tr>';
				echo'<td>' . formatName($row['referred']) . '</td>';
                echo '<td>' . $credited . '</td>';
                echo '<td>' . $points . '</td>';
                echo '</tr>';
            }
        }
        ?>
    </table>
</div>

<?php
include 'footer.php';
?>
