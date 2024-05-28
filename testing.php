<?php 
include_once "header.php";
?>

<style>
.selected-option {
    border: 3px solid #007bff;
    box-shadow: 0 0 15px #007bff;
    transition: all 0.3s ease;
}
</style>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$radiobutton = isset($_POST['radiobutton']) ? $_POST['radiobutton'] : 0;
$chance = explode("-", $user_class->gtachance);
?>
<div class="container mt-5" id="result-container"></div>
<form name="form1" onsubmit="return submitGTAForm();">
    <input type="hidden" name="radiobutton" id="select" value="0">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-center">
                Grand Theft Auto
            </div>
            <div class="card-body">
                <div class="row d-flex justify-content-center align-items-center text-center">
                    <div class="col-md-2 select" id="1" onclick="SelectOption(this.id);">
                        <img src="images/images/richhouse.png" class="img-fluid">
                        <p>Steal from rich house</p>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo "$chance[0]"; ?>%" aria-valuenow="<?php echo "$chance[0]"; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="col-md-2 select" id="2" onclick="SelectOption(this.id);">
                        <img src="images/images/street.png" class="img-fluid">
                        <p>Steal from the streets</p>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo "$chance[1]"; ?>%" aria-valuenow="<?php echo "$chance[1]"; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="col-md-2 select" id="3" onclick="SelectOption(this.id);">
                        <img src="images/images/dealership.png" class="img-fluid">
                        <p>Steal from Dealership</p>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo "$chance[2]"; ?>%" aria-valuenow="<?php echo "$chance[2]"; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="col-md-2 select" id="4" onclick="SelectOption(this.id);">
                        <img src="images/images/showroom.png" class="img-fluid">
                        <p>Steal from Showroom</p>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo "$chance[3]"; ?>%" aria-valuenow="<?php echo "$chance[3]"; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="col-md-2 select" id="5" onclick="SelectOption(this.id);">
                        <img src="images/images/garage.png" class="img-fluid">
                        <p>Break into a Garage</p>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo "$chance[4]"; ?>%" aria-valuenow="<?php echo "$chance[4]"; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <button type="submit" class="btn btn-primary">Commit GTA!</button>
            </div>
        </div>
    </div>
</form>

<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-1">
                    <img src="../images/questionmark.jpg" width="49" height="46" class="img-fluid">
                </div>
                <div class="col-md-11">
                    <p>This page is the Grand Theft Auto. Here you can commit a "GTA" which is where you try and rob a car from some unsuspecting person. When you start your percentages are on 0 but the more practice you do the higher the percentages go. You have to lay low for 2 minutes between each GTA to avoid attention from the pigs. When you steal a car it may have been damaged as you tried to get away. After you successfully steal a car it goes into your garage.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function SelectOption(id) {
    document.getElementById('select').value = id;

    const options = document.querySelectorAll('.select');
    options.forEach(option => {
        option.classList.remove('selected-option');
    });

    document.getElementById(id).classList.add('selected-option');
}

function submitGTAForm() {
    const radiobuttonValue = document.getElementById('select').value;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax_gta.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById("result-container").innerHTML = xhr.responseText;
        }
    };

    xhr.send("radiobutton=" + radiobuttonValue);

    return false;
}
</script>