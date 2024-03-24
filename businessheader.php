<?php
if ($user_class->current_employer != 0) {
    ?>
    <tr>
        <td class="contentcontent">
            <hr />
            <table id="newtables" class="linkstable" style="width:100%;table-layout:fixed;">
                <tr>
                    <th colspan="4">Business Links</th>
                </tr>
                <tr>
                    <td class"tdHover"><a href="businessdetails.php">Business Details</a></td>
                    <td><a href="businessLog.php">Business Log</a></td>
                    <td><a href="financialReports.php">Financial Reports</a></td>
                    <td><a href="businessmembers.php">Employee Management</a></td>
                </tr>
                <tr>
                    <td><a href="businessEvents.php">Business Events</a></td>
                    <td><a href="businessForum.php">Business Forum</a></td>
                    <td><a href="disbandbusiness.php">Resign</a></td>
                    <td><a href="businessapplication.php">Employee Applications</a></td>
                </tr>
            </table>
        </td>
    </tr>
    <?php
}
?>