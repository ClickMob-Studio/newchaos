<?php
include 'header.php';
?>
<div class='box_top'>Events</div>
<div class='box_middle'>
    <div class='pad'>
        <?php

        perform_query("UPDATE `events` SET `viewed` = '2' WHERE `to` = ?", [$user_class->id]);
        if (isset($_GET['deleteall']) && $_GET['deleteall'] == 1) {
            perform_query("DELETE FROM `events` WHERE `to` = ?", [$user_class->id]);
            echo Message("All your events have been deleted.");
        }
        if (isset($_GET['delete']) && $_GET['delete'] != "") {
            perform_query("DELETE FROM `events` WHERE `id` = ? AND `to` = ?", [$_GET['delete'], $user_class->id]);
            echo Message("You have deleted that event.");
        }
        ?>

        <form method="GET" class="d-inline float-right">
            <input type="text" id="filterInput" placeholder="Search for an Event" name="search" <?php if (isset($_GET['search'])) { ?>value="<?php echo $_GET['search'] ?>" <?php } ?>>
            <input type="submit" value="Search">
        </form>
        <div class="filter-buttons">
            <button class="filter" data-filter="missions">Missions</button>
            <button class="filter" data-filter="mugs">Mugs</button>
            <button class="filter" data-filter="achievements">Achievements</button>
            <button class="filter" data-filter="level">Level</button>
        </div>
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
                $result = mysql_query("SELECT COUNT(*) FROM `events` WHERE `to` = $user_class->id");
                $r = mysql_fetch_row($result);
                $numrows = $r[0];
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
                    $res = mysql_query("SELECT * from `events` WHERE `to` = $user_class->id ORDER BY `timesent` DESC LIMIT $offset, $rowsperpage");
                } else {
                    $res = mysql_query("SELECT * from `events` WHERE `to` = $user_class->id AND `text` like '$searchString' ORDER BY `timesent` DESC LIMIT $offset, $rowsperpage");
                }
                while ($row = mysql_fetch_array($res)) {
                    $text = str_replace('[-_USERID_-]', formatName($row['extra']), $row['text']);
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
                    url: "ajax_event.php",
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
                    url: "ajax_event.php",
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
            $('.filter').click(function () {
                let filterValue = $(this).attr('data-filter');
                let searchValue = $('#filterInput').val() + ' ' + filterValue; // Combine with existing search input if any
                filterEvents(searchValue.trim()); // Function to handle filtering
            });

            function filterEvents(value) {
                let offset = $('#dataHolder').attr('data-offset');
                let rowsPerPage = $('#dataHolder').attr('data-rowsPerPage');
                let uid = $('#dataHolder').attr('data-userId');
                $.ajax({
                    type: "POST",
                    url: "ajax_event.php",
                    data: { search: value, offset: offset, rowsPerPage: rowsPerPage, userId: uid },
                    success: function (response) {
                        let data = JSON.parse(response);
                        let innerHtml = '';
                        $.each(data.events, function (key, item) {
                            innerHtml += item;
                        });
                        $('.eventsTable').html(innerHtml);
                    }
                });
            }

            $('#filterInput').keyup(function () {
                filterEvents($(this).val());
            });

        </script>