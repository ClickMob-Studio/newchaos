<?php
include 'header.php';
?>
<div class='box_top'>Events</div>
<div class='box_middle'>
    <div class='pad'>
        <?php


        $db->query("UPDATE `events` SET `viewed` = '2' WHERE `to` = ?");
        $db->execute([$user_class->id]);
        if (isset($_GET['deleteall']) && $_GET['deleteall'] == 1) {
            $db->query("DELETE FROM `events` WHERE `to` = ?");
            $db->execute([$user_class->id]);
            echo Message("All your events have been deleted.");
        }
        if (isset($_GET['deleteattacks']) && $_GET['deleteattacks'] == 1) {
            $db->query("DELETE FROM `events` WHERE `to` = ? AND `text` LIKE '%attacked you%'");
            $db->execute([$user_class->id]);
            echo Message("All your attack events have been deleted.");
        }
        if (isset($_GET['deletemugs']) && $_GET['deletemugs'] == 1) {
            $db->query("DELETE FROM `events` WHERE `to` = ? AND `text` LIKE '%mugged%'");
            $db->execute([$user_class->id]);
            $db->query("DELETE FROM `events` WHERE `to` = ? AND `text` LIKE '%mug you%'");
            $db->execute([$user_class->id]);
            echo Message("All your mug events have been deleted.");
        }
        if (isset($_GET['deletebusts']) && $_GET['deletebusts'] == 1) {
            $db->query("DELETE FROM `events` WHERE `to` = ? AND `text` LIKE '%busted out%'");
            $db->execute([$user_class->id]);
            echo Message("All your bust events have been deleted.");
        }
        if (isset($_GET['delete']) && $_GET['delete'] != "") {
            $db->query("DELETE FROM `events` WHERE `id` = ? AND `to` = ?");
            $db->execute([$_GET['delete'], $user_class->id]);
            echo Message("You have deleted that event.");
        }
        ?>

        <form method="GET" class="d-inline float-right">
            <input type="text" id="filterInput" placeholder="Search for an Event" name="search" <?php if (isset($_GET['search'])) { ?>value="<?php echo $_GET['search'] ?>" <?php } ?>>
            <input type="submit" value="Search">
        </form>

        <hr>
        <div class="contenthead floaty">
            <table id="newtables" style="width:100%; text-align: left;">
                <tr>
                    <th><b>Description</b></th>
                    <th><b>Recieved</b></th>
                </tr>
                <style type="text/css">
                    background-color: #333;
                    /* Dark background */
                    color: white;
                    /* Light text */
                    padding: 20px;
                    border-radius: 10px;
                    /* Rounded corners */
                    box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.2);
                    /* Subtle shadow at the top */
                    padding: 15px 5px 10px;
                    text-align: center;

                    }
                </style>
                <?php
                $db->query("SELECT COUNT(*) FROM `events` WHERE `to` = ?");
                $db->execute([$user_class->id]);
                $numrows = $db->fetch_single();
                $rowsperpage = 30;
                $totalpages = ceil($numrows / $rowsperpage);
                $searchString = null;
                if ($totalpages <= 0)
                    $totalpages = 1;
                else
                    $totalpages = ceil($numrows / $rowsperpage);
                if (isset($_GET['page']) && is_numeric($_GET['page']))
                    $currentpage = (int) $_GET['page'];
                else
                    $currentpage = 1;
                if ($currentpage > $totalpages)
                    $currentpage = $totalpages;
                if ($currentpage < 1)
                    $currentpage = 1;
                if (isset($_GET['search']) && $_GET['search'] != '') {
                    $searchString = '%' . $_GET['search'] . '%';
                }
                $offset = ($currentpage - 1) * $rowsperpage;
                if ($searchString == null) {
                    $db->query("SELECT * FROM `events` WHERE `to` = ? ORDER BY `timesent` DESC LIMIT {$offset}, {$rowsperpage}");
                    $db->execute([$user_class->id]);
                    $res = $db->fetch_row();
                } else {
                    $db->query("SELECT * FROM `events` WHERE `to` = ? AND `text` LIKE ? ORDER BY `timesent` DESC LIMIT {$offset}, {$rowsperpage}");
                    $db->execute([$user_class->id, $searchString]);
                    $res = $db->fetch_row();
                }

                foreach ($res as $row) {
                    $text = str_replace('[-_USERID_-]', formatName($row['extra']), $row['text']);
                    //$text       = str_replace('[-_GANGID_-]', gangName($row['extra']), $text);
                    echo "<tr style='height: 30px; border-bottom: solid 1px;'><td width='67%'>" . $text . "</td><td width='31%'>" . date("d F Y, g:ia", $row['timesent']) . "</td><td width='2%'><a href='events.php?delete={$row['id']}'><span class='delete'>&nbsp;X&nbsp;</span></a></td></tr>";
                }
                ?>
            </table>
            <span style="display: none" id="dataHolder" data-offset="<?php echo $offset ?>" data-rowsPerPage="30"
                data-userId="<?php echo $user_class->id ?>"></span>
            <br />
            <table width="100%">
                <tr>
                    <td align="left">
                        <?php
                        $range = 2;
                        if ($currentpage > 1)
                            echo " <a href='?page=1'><<</a> ";
                        for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++)
                            if (($x > 0) && ($x <= $totalpages))
                                if ($x == $currentpage)
                                    echo " [<b>$x</b>] ";
                                else
                                    echo " <a href='?page=$x'>$x</a> ";
                        if ($currentpage < $totalpages)
                            echo " <a href='?page=$totalpages'>>></a> ";
                        ?>
                    </td>
                    <td align="right">[<a href="events.php?deleteall=1">delete all events</a>]</td>
                </tr>
                <tr>
                    <td colspan="2">
                        [<a href="events.php?deletemugs=1">delete mug events</a>] |
                        [<a href="events.php?deleteattacks=1">delete attack events</a>]
                        [<a href="events.php?deletebusts=1">delete bust events</a>]
                    </td>
                </tr>
            </table>
            </td>
            </tr>
        </div>
        <?php
        include 'footer.php';
        function gangName($id)
        {
            $gang = new Gang($id);
            return $gang->formattedname;
        }
        ?>
        <script>
            $(document).ready(function () {
                let offset = $('#dataHolder').attr('data-offset');
                let rowsPerPage = $('#dataHolder').attr('data-rowsPerPage');
                let uid = $('#dataHolder').attr('data-userId');
                $.ajax({
                    type: "POST",
                    url: "ajax_event_search.php",
                    data: { offset: offset, rowsPerPage: rowsPerPage, userId: uid },
                    success: function (response) {
                        let data = JSON.parse(response);
                        let innerHtml;
                        $.each(data.events, function (key, item) {
                            innerHtml = innerHtml + item
                        })
                        $('.eventsTable').html(innerHtml);
                    }
                });
            });
            $('#filterInput').keyup(function () {
                let value = $(this).val();
                let offset = $('#dataHolder').attr('data-offset');
                let rowsPerPage = $('#dataHolder').attr('data-rowsPerPage');
                let uid = $('#dataHolder').attr('data-userId');
                $.ajax({
                    type: "POST",
                    url: "ajax_event_search.php",
                    data: { search: value, offset: offset, rowsPerPage: rowsPerPage, userId: uid },
                    success: function (response) {
                        let data = JSON.parse(response);
                        let innerHtml;
                        $.each(data.events, function (key, item) {
                            innerHtml = innerHtml + item
                        })
                        $('.eventsTable').html(innerHtml);
                    }
                });
            })
        </script>