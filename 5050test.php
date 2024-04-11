<?php
require "header.php";
$db->query("SELECT * FROM fiftyfifty WHERE currency = 'cash'");
$db->execute();
$cash = $db->fetch_row();
?>
<h1>50/50</h1>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-12">
            <h1>Cash Bets</h1>
            <table>
            <?php foreach ($cash as $cas): ?>
            <tr>
                <td><?= formatName($cas['userid'])?></td>
                <td>$<?= prettynum($cas['amnt'], 1)?></td>
                <td>LINK</td>
            </tr>
            <?php endforeach; ?>
            </table>
        </div>

        <div class="col-md-4 col-12">
            testing cols
        </div>

        <div class="col-md-4 col-12">
            testing cols
        </div>
    </div>
</div>
