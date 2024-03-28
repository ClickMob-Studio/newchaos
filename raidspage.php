<?php
include "header.php";
?>
<div class='box_top'>Raids Page</div>
						<div class='box_middle'>
							<div class='pad'>
                                <?php
if ($user_class->jail > 0) {
    echo Message("You cant visit the raids page when in Jail");
    include 'footer.php';
    die();
}
if ($user_class->hospital > 0) {
    echo Message("You cant visit the raids page when in Hospital.");
    include 'footer.php';
    die();
}

?>
<h3>Raids section</h3>
<hr>
<tr><td class='contentcontent'>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<div class="tab-section">
    <div class="tabs">
        <button class="tab-button active" onclick="loadTabContent('raid_stats_page')">Raid Home</button>
        <button class="tab-button" onclick="loadTabContent('raids')">Active Raids</button>
        <button class="tab-button" onclick="loadTabContent('raid_profile')">Raid Profile</button>
        <button class="tab-button" onclick="loadTabContent('raid_character')">Raids Character</button>
        <button class="tab-button" onclick="loadTabContent('raid_upgrades')">Raid Upgrades</button>
        <button class="tab-button" onclick="loadTabContent('raid_account')">Raid Prefrences</button>

    </div>
    <div class="tab-content">
        <!-- Content from PHP pages will be loaded here via AJAX -->
    </div>
</div>

<script>
    function loadTabContent(pageName) {
        $.ajax({
            url: pageName + ".php",
            success: function(data) {
                $(".tab-content").html(data);
            }
        });
    }

    // Load the content of the first tab (Raid Stats) by default when the page loads
    $(document).ready(function() {
        loadTabContent('raid_stats_page');
    });
</script>



</center></td></tr>
<?php
include 'footer.php';
?>
