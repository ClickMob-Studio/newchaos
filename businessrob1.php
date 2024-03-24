<?php
include 'header.php';

// Fetching the businesses from the database
$db->query("SELECT * FROM businesses ORDER BY id ASC");
$db->execute();
$rows = $db->fetch_row();
foreach ($rows as $row) {
    echo'<div class="floaty flexcont" style="width:85%;margin:2px;">';
        echo'<div class="flexele" style="border-right:thin solid #333;">';
            echo'<img src="images/' . str_replace(array(' ' , '*'), '', strtolower($row['name'])) . '.png" />';
        echo'</div>';
        echo'<div class="flexele">';
            echo $row['name'] . '<br />';
            echo'<br />';
            echo'Employees: ' . $row['employees'] . '<br />';
            echo'Intelligence: ' . $row['intelligence'] . '<br />';
            echo'Cost: $' . number_format($row['cost'], 2) . '<br />';
            echo'Rating: <span style="color: gold; font-size: 1.5em;">' . str_repeat('&#9733;', $row['rating']) . str_repeat('&#9734;', 5 - $row['rating']) . '</span>';
        echo'</div>';
        echo'<div class="flexele" style="border-left:thin solid #333;line-height:100px;">';
            echo '<a href="business.php?buy=' . $row['id'] . '"><button>Buy</button></a>';
        echo'</div>';
    echo'</div>';
}
include 'footer.php';
?>