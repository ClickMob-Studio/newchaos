<?php
require 'header_m.php';
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Training</title>
  <style>
    /* Global styles */
    input, button {
      background: #0e0e0e;
      color: #FFF;
      border: #303030 medium solid;
      text-align: center;
      margin-bottom: 10px;
    }

    .refills {
      background: #0e0e0e;
      color: #FFF;
      border: #303030 medium solid;
      color: #FFF;
      padding: 3px;
    }

    /* Responsive styles */
    @media only screen and (max-width: 600px) {
      .responsive-container {
        display: flex;
        flex-direction: column;
        align-items: center;
      }

      .responsive-column {
        width: 100%;
        box-sizing: border-box;
      }
    }
  </style>
</head>
<body>

<div class="responsive-container">
  <div class="responsive-column">
    <h3>Your Training</h3>
    <hr>

    <br /><span class='notice'></span><br /><br />

    <!-- Strength Training -->
    <input id='strength' type='text' name='energy1' size='4' value="<?php echo $user_class->energy ?>" onKeyPress="return numbersonly(this, event)">
    <button onclick="train('strength')">Strength</button>
    <button onclick="trainrefill('strength')">Strength + Refills</button>
    <span id='strengthamnt'><?php echo prettynum($user_class->strength); ?></span> [Ranked: <?php echo getRank("$user_class->id", "strength"); ?>]

    <hr>
    <button onclick="refill('energy')">Refill Energy</button>
    <button onclick="refill('awake')">Refill Awake</button>
    <button onclick="refill('both')">Refill Both</button>
    <span style='color:white;font-weight:bold;'>Super Trains: Click and hold on your desired train, then hold <font color=red>[Enter]</font> button for Super fast trains.<br>You can turn auto refills on</font> <font color=red><a href="preferences.php?refills">[Here]</a></font><br>
    <center><span style="color:white;">  Click <a href="gym2.php">[Here]</a> for Mobile Gym use</span></a></center>
  </div>

  <div class="responsive-column">
    <!-- Defense Training -->
    <input id='defense' type='text' name='energy2' size='4' value="<?php echo $user_class->energy ?>" onKeyPress="return numbersonly(this, event)">
    <button onclick="train('defense')">Defense</button>
    <button onclick="trainrefill('defense')">Defense + Refills</button>
    <span id='defenseamnt'><?php echo prettynum($user_class->defense); ?></span> [Ranked: <?php echo getRank("$user_class->id", "defense"); ?>]

    <!-- Additional content for defense training goes here -->
  </div>

  <div class="responsive-column">
    <!-- Speed Training -->
    <input id='speed' type='text' name='energy3' size='4' value="<?php echo $user_class->energy ?>" onKeyPress="return numbersonly(this, event)">
    <button onclick="train('speed')">Speed</button>
    <button onclick="trainrefill('speed')">Speed + Refills</button>
    <span id='speedamnt'><?php echo prettynum($user_class->speed); ?></span> [Ranked: <?php echo getRank("$user_class->id", "speed"); ?>]

    <!-- Additional content for speed training goes here -->
  </div>
</div>

</body>
</html>
