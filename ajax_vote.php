<?php
include "ajax_header.php";

$user_class = new User($_SESSION['id']);

$pollId = $db->real_escape_string($_POST['pollid']);
$vote = $db->real_escape_string($_POST['radioq']);

$db->query("SELECT * FROM voters WHERE `user_id` = ? AND `poll_id` = ?");
$db->execute(
    array(
        $user_class->id,
        $pollId
    )
);
$voted = $db->fetch_row(true);

$db->query("SELECT * FROM polls WHERE id = ?");
$db->execute(
    array(
        $pollId
    )
);
$poll = $db->fetch_row(true);

$choices = unserialize($poll['options']);
$votes = unserialize($poll['votes']);
$end = $poll['finish'];

if (!$voted && (time() < $end)) {

    $votes[$vote] = $votes[$vote] + 1;
    $poll['voters'] = $poll['voters'] + 1;
    $_votes = serialize($votes);

    $db->query("UPDATE polls SET voters = voters + 1, votes = ? WHERE id = ?");
    $db->execute(
        array(
            $_votes,
            $pollId
        )
    );

    $db->query("INSERT INTO voters (`user_id`, `poll_id`, `choice`) VALUES (?, ?, ?)");
    $db->execute(
        array(
            $user_class->id,
            $pollId,
            $vote
        )
    );
}

$html = '<h3>Results</h3>';
for ($i = 0; $i < count($choices); $i++) {
    $votePercent = round(($votes[$i] / $poll['voters']) * 100);
    $votePercent = !empty($votePercent) ? $votePercent . '%' : '0%';
    $html .= '<p>' . $choices[$i] . '</p>';
    $html .= '<div class="progress" style="margin: auto;max-width:80%">';
    $html .= '<div class="poll-progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:' . $votePercent . '">' . $votePercent . '</div></div>';
}
$html .= '</div>';
print $html;

?>