<?php
include 'header.php';
?>
<div class='box_top'>Events</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        // Assuming $conn is your PDO connection object
        $userId = $user_class->id;
        $conn->prepare("UPDATE `events` SET `viewed` = '2' WHERE `to` = :userId")->execute([':userId' => $userId]);

        if (isset($_GET['deleteall']) && $_GET['deleteall'] == 1) {
            $conn->prepare("DELETE FROM `events` WHERE `to` = :userId")->execute([':userId' => $userId]);
            echo Message("All your events have been deleted.");
        }

        if (isset($_GET['delete']) && $_GET['delete'] != "") {
            $deleteId = $_GET['delete'];
            $conn->prepare("DELETE FROM `events` WHERE `id` = :deleteId AND `to` = :userId")->execute([':deleteId' => $deleteId, ':userId' => $userId]);
            echo Message("You have deleted that event.");
        }
        ?>

        <form method="GET" class="d-inline float-right">
            <input type="text" id="filterInput" placeholder="Search for an Event" name="search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <input type="submit" value="Search">
        </form>

        <hr>
        <table id="newtables" style="width:100%;">
            <tr>
                <th><b>Description</b></th>
                <th><b>Received</b></th>
            </tr>
            <?php
            $searchString = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : null;
            $stmt = $conn->prepare("SELECT * FROM `events` WHERE `to` = :userId " . ($searchString ? "AND `text` LIKE :searchString " : "") . "ORDER BY `timesent` DESC LIMIT :offset, :rowsperpage");
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
            if ($searchString) {
                $stmt->bindValue(':searchString', $searchString, PDO::PARAM_STR);
            }
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':rowsperpage', $rowsperpage, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($results as $row) {
                $text = str_replace('[-_USERID_-]', formatName($row['extra']), $row['text']);
                // Assuming formatName() is defined elsewhere
                echo "<tr><td width='67%'>" . htmlspecialchars($text) . "</td><td width='31%'>" . date("d F Y, g:ia", $row['timesent']) . "</td><td width='2%'><a href='events.php?delete=" . htmlspecialchars($row['id']) . "'><span class='delete'>&nbsp;X&nbsp;</span></a></td></tr>";
            }
            ?>
        </table>
        <span style="display: none" id="dataHolder" data-offset="<?= htmlspecialchars($offset) ?>" data-rowsPerPage="30" data-userId ="<?= htmlspecialchars($userId) ?>"></span>
        <br />
        <table width="100%">
            <tr>
                <td align="left">
                    <!-- Pagination links -->
                </td>
                <td align="right">[<a href="events.php?deleteall=1">delete all events</a>]</td>
            </tr>
        </table>
    </div>
</div>
<?php
include 'footer.php';
?>
<s