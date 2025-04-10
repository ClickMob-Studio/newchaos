<?php
include 'header.php';


if (!isset($user_class) or $user_class->admin < 1) {
    die("Unauthorized access.");
}

echo '<div class="container mt-5">';
echo '<div class="result" style="margin-bottom: 20px;"></div>'; // Global result container
echo '<form method="post" class="mb-3">';
echo '<div class="mb-3">';
echo '<label for="subject" class="form-label">Subject:</label>';
echo '<input type="text" class="form-control custom-input" id="subject" name="subject" value="" maxlength="75">';
echo '<label for="message" class="form-label">Message:</label>';
echo '<textarea class="form-control custom-input" id="message" name="message" style="height: 125px;" autofocus></textarea>';
echo '</div>';
echo '<button type="submit" name="action" value="send" class="btn btn-primary">Send</button>';
echo '</form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'send') {
    $message = $_POST['message'];
    $subject = $_POST['subject'];

    if (!$message || !$subject) {
        die('Please fill in all fields.');
    }

    $db->query("SELECT id FROM grpgusers WHERE id != ?");
    $db->execute([$user_class->id]);
    $users = $db->fetch_row();

    foreach ($users as $user) {
        $db->query("INSERT INTO pms (parent, to, from, timesent, subject, msgtext, bomb) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $db->execute([0, $user['id'], $user_class->id, time(), $subject, $message, 0]);
    }
}

echo '</div>';

?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        $('.save-form').on('submit', function (e) {
            console.log("Form submitted");  // Check if this logs when you submit the form
            e.preventDefault();


            var form = $(this);

            console.log("Serialized Data: ", form.serialize());
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8', // This is the default, but setting it explicitly can help
                data: form.serialize(),
                success: function (response) {
                    form.find('.form-result').html('<p>' + response + '</p>');
                },
                error: function (xhr, status, error) {
                    form.find('.form-result').html('<p>Error updating item.</p>');
                }
            });

        });
    });
</script>