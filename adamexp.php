<?php
if ($user_class->admin != 1) {
    exit;
}

require "header.php";
?>
<div class='box_top'>Upgrade Store Sale</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        $monthStartDate = new \DateTime('first day of this month');
        $monthEndDate = new \DateTime('last day of this month');

        $db->query("SELECT SUM(paymentamount) AS totalSpent FROM ipn WHERE date >= ? AND date <= ?");
        $db->execute([$monthStartDate->getTimestamp(), $monthEndDate->getTimestamp()]);
        $rowMonthDonations = $db->fetch_row(true, );
        $monthDonations = $rowMonthDonations["totalSpent"];

        // Fetch month by month total income
        $db->query("SELECT DATE_FORMAT(FROM_UNIXTIME(date), '%Y-%m') AS month, SUM(paymentamount) AS totalIncome 
                    FROM ipn 
                    GROUP BY month 
                    ORDER BY month DESC");
        $rows = $db->fetch_row();

        // Check if there are any rows
        if (!empty($rows)) {
            // Output table header
            echo "<br /><hr /><br /><h1>Month by Month Total Income</h1>";
            echo "<table border='1'><tr><th>Month</th><th>Total Income</th></tr>";

            // Output data from rows
            foreach ($rows as $row) {
                echo "<tr><td>" . $row["month"] . "</td><td>$" . number_format($row["totalIncome"], 2) . "</td></tr>";
            }

            // Output table footer
            echo "</table>";
        } else {
            echo "No monthly income data available.";
        }
        ?>

        <br />
        <h1>Total Income</h1>
        <?php

        $db->query("SELECT user_id, SUM(paymentamount) AS totalSpent FROM ipn GROUP BY user_id ORDER BY totalSpent DESC LIMIT 1");
        $db->execute();
        $rowBiggestDonor = $db->fetch_row(true);
        // Check if there are any rows
        if (!empty($rowBiggestDonor)) {
            $biggestDonor = $rowBiggestDonor["user_id"];
            $highestAmount = $rowBiggestDonor["totalSpent"];
        } else {
            $biggestDonor = "No data";
            $highestAmount = 0;
        }

        // Fetch data from the database
        $db->query("SELECT * FROM ipn ORDER BY `id` ASC");
        $db->execute();
        $rows = $db->fetch_row();

        // Check if there are any rows
        if (!empty($rows)) {
            // Initialize variables for total income and fees
            $totalIncome = 0;

            // Output table header
            echo "<table border='1'><tr><th>ID</th><th>Date</th><th>Credits Bought</th><th>Payment Amount</th><th>Transaction ID</th><th>Payer Email</th><th>User</th></tr>";

            // Output data from rows
            foreach ($rows as $row) {
                echo "<tr><td>" . $row["id"] . "</td><td>" . date('Y-m-d H:i:s', $row["date"]) . "</td><td>" . $row["creditsbought"] . "</td><td>$" . $row["paymentamount"] . "</td><td>" . $row["txnid"] . "</td><td>" . $row["payeremail"] . "</td><td>" . formatName($row["user_id"]) . "</td></tr>";

                // Update total income and fees
                $totalIncome += $row["paymentamount"];
            }

            // Output table footer with totals
            echo "<tr><td colspan='6'>Total Income</td><td>$" . $totalIncome . "</td><td colspan='7'></td></tr></table>";
            echo "<table><tr><td colspan='8'></td><td>Biggest Donor</td><td>" . formatName($biggestDonor) . "</td><td>Amount Spent</td><td>$" . number_format($highestAmount, 0) . "</td></tr></table>";
            echo "<table><tr><td colspan='8'></td><td>Month Donations</td><td colspan='2'>Amount Spent</td><td>$" . number_format($monthDonations, 0) . "</td></tr></table>";

        } else {
            echo "0 results";
        }

        require "footer.php";

