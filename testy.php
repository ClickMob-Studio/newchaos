<?php
include 'header.php';
?>

<div class="container my-4">
    <!-- Message Box for Success/Error -->
    <div id="messageBox" class="alert" style="display: none;"></div>

    <h2>Equipped Items</h2>
    <div class="row text-center">
        <?php
        // Array to manage equipped items with respective properties
        $equippedItems = [
            'weapon' => [
                'id' => $user_class->eqweapon,
                'img' => $user_class->weaponimg,
                'name' => $user_class->weaponname,
                'placeholder' => 'You are not holding a weapon.'
            ],
            'armor' => [
                'id' => $user_class->eqarmor,
                'img' => $user_class->armorimg,
                'name' => $user_class->armorname,
                'placeholder' => 'You are not wearing armor.'
            ],
            'shoes' => [
                'id' => $user_class->eqshoes,
                'img' => $user_class->shoesimg,
                'name' => $user_class->shoesname,
                'placeholder' => 'You are not wearing boots.'
            ]
        ];

        // Display equipped items with placeholders if not equipped
        foreach ($equippedItems as $type => $item) {
            echo '<div class="col-md-4 mb-3">';
            if ($item['id'] != 0) {
                echo image_popup($item['img'], $item['id']);
                echo '<br />';
                echo item_popup($item['name'], $item['id']);
                echo '<br />';
                echo '<button class="btn btn-sm btn-warning mt-2 unequip-btn" data-type="' . $type . '">Unequip</button>';
            } else {
                echo '<img width="100" height="100" src="/css/images/empty.jpg" alt="Empty Slot"><br />';
                echo $item['placeholder'];
            }
            echo '</div>';
        }
        ?>
    </div>
</div>

<!-- jQuery for AJAX and AJAX-based Unequip functionality -->
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script>
    function showMessage(message, isSuccess) {
        const messageBox = $("#messageBox");
        messageBox
            .text(message)
            .removeClass("alert-success alert-danger")
            .addClass(isSuccess ? "alert-success" : "alert-danger")
            .fadeIn();

        // Hide message after 3 seconds
        setTimeout(() => messageBox.fadeOut(), 3000);
    }

    $(document).on('click', '.unequip-btn', function () {
        const type = $(this).data('type');

        $.ajax({
            url: 'ajax_unequip.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'unequip', type: type },
            success: function (response) {
                if (response.status === 'success') {
                    showMessage(response.message, true);
                    // Reload items to reflect unequipped status
                    location.reload();
                } else {
                    showMessage(response.message, false);
                }
            },
            error: function () {
                showMessage('Error processing the request.', false);
            }
        });
    });
</script>


