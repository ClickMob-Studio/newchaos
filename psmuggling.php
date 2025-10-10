<?php
include "header.php";

?>

<div class='box_top'>Points Smuggling</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        $places = array(
            'America',
            'Canada',
            'Brazil',
            'Germany',
            'Japan',
            'Argentina'
        );
        if ($user_class->jail > 0)
            diefun("You cant go points smuggling while in prison.");
        if ($user_class->hospital > 0)
            diefun("You cant go points smuggling while in hospital.");
        if ($user_class->psmuggling == 0)
            diefun("You have smuggled as many points as you can today.");

        $resultMessage = '';

        if (isset($_GET['smug'])) {
            if (!in_array($_GET['smug'], $places))
                $resultMessage = "Where the FUCK did this country come from?";
            else {
                $stole = rand(500, 1000);
                $db->query("UPDATE grpgusers SET points = points + ?, psmuggling = psmuggling - 1 WHERE id = ?");
                $db->execute(array(
                    $stole,
                    $user_class->id
                ));

                $resultMessage = '<div style="text-align: center; padding: 15px; border: 1px solid #ccc; box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.1); font-size: 15px; display: flex; align-items: center; justify-content: center;">You went to ' . $_GET['smug'] . ' and stole ' . $stole . ' points.</div><br>';

            }
        }
        ?>

        <style>
            /* Set initial filter to grayscale (100%) for all images */
            img {
                filter: grayscale(100%);
                transition: filter 0.3s ease;
                /* Add a smooth transition effect */
            }

            /* Remove the grayscale filter when the image is hovered over */
            img:hover {
                filter: none;
            }

            /* Tooltip styles */
            .custom-tooltip {
                position: relative;
                display: inline-block;
                cursor: pointer;
            }

            .custom-tooltip .tooltip-text {
                visibility: hidden;
                width: auto;
                /* Make the width dynamic based on content */
                background-color: #333;
                color: #fff;
                text-align: center;
                border-radius: 5px;
                padding: 5px;
                position: absolute;
                z-index: 1;
                opacity: 0;
                transition: opacity 0.3s;
            }

            .custom-tooltip:hover .tooltip-text {
                visibility: visible;
                opacity: 1;
            }

            /* Hide the background table and table rows */
            table.smuggling-table {
                border-collapse: collapse;
                border: none;
                margin: 0;
                width: 100%;
                padding: 0;
            }

            table.smuggling-table td {
                padding: 0;
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let tooltips = document.querySelectorAll('.custom-tooltip');

                tooltips.forEach(tooltip => {
                    tooltip.addEventListener('mousemove', function (e) {
                        let tooltipText = this.querySelector('.tooltip-text');
                        tooltipText.style.left = (e.pageX + 15) + 'px';  // Adjusted to position tooltip right next to the cursor
                        tooltipText.style.top = (e.pageY - 15) + 'px';   // Adjusted to vertically center tooltip with cursor
                    });
                });
            });
        </script>


        <div class="contenthead floaty">
            <div id="resultMessage">
                <?php echo $resultMessage; ?>
            </div>

            <!-- Add the class 'smuggling-table' to the table to hide the background -->
            <table class="smuggling-table">
                <?php
                $rowCount = 0;
                foreach (array_chunk($places, 2) as $placesRow) {
                    echo '<tr>';
                    foreach ($placesRow as $place) {
                        echo '<td style=\'text-align: center;\'><div class="custom-tooltip"><a href="?smug=' . urlencode($place) . '"><span onmouseover="updateTooltipPosition(event)" onmouseout="resetTooltipPosition(event)"><img src="/images/' . strtolower($place) . '.png" width=\'150\' height=\'150\' alt="' . $place . '" /></span></a></div></td>';
                    }
                    echo '</tr>';
                    $rowCount++;
                }
                // Add empty cells to fill the remaining table rows if needed
                while ($rowCount < 6) {
                    echo '<tr><td style=\'text-align: center;\' colspan="2"></td></tr>';
                    $rowCount++;
                }
                ?>
            </table>
        </div>

        <?php
        include 'footer.php';
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let tooltips = document.querySelectorAll('.custom-tooltip');

                tooltips.forEach(tooltip => {
                    tooltip.addEventListener('mousemove', function (e) {
                        let tooltipText = this.querySelector('.tooltip-text');
                        tooltipText.style.left = e.pageX + 10 + 'px';
                        tooltipText.style.top = e.pageY + 10 + 'px';
                    });
                });
            });
        </script>

        <script>
            function showTooltip(event, text) {
                let tooltipText = event.currentTarget.querySelector('.tooltip-text');
                tooltipText.textContent = text;
                tooltipText.style.left = event.pageX + 10 + 'px';
                tooltipText.style.top = event.pageY + 10 + 'px';
                tooltipText.style.visibility = 'visible';
                tooltipText.style.opacity = '1';
            }

            function hideTooltip(event) {
                let tooltipText = event.currentTarget.querySelector('.tooltip-text');
                tooltipText.style.visibility = 'hidden';
                tooltipText.style.opacity = '0';
            }
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let tooltips = document.querySelectorAll('.custom-tooltip');

                tooltips.forEach(tooltip => {
                    tooltip.addEventListener('mousemove', function (e) {
                        let tooltipText = this.querySelector('.tooltip-text');
                        tooltipText.style.left = (e.pageX + 15) + 'px';  // Adjusted to position tooltip right next to the cursor
                        tooltipText.style.top = (e.pageY - 15) + 'px';   // Adjusted to vertically center tooltip with cursor
                    });
                });
            });
        </script>