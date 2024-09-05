<?php
require('sec_inc.php');
header('Content-Type: text/javascript');

echo $addons->get_hooks(
    array(),
    array(
        'page'     => 'includes/auto_chat.php',
        'location' => 'page_start'
    )
);

$time = time();

// Fetch live chat data
$cq = $pdo->query("SELECT * FROM " . DB_LIVECHAT . " WHERE gameID = " . (int)$gameID);
$cr = $cq->fetch(PDO::FETCH_ASSOC);
$lastLog = '';

if ($cr && $cr['updatescreen'] > $time) {
    $i = 1;
    $chat = '';

    while ($i < 6) {
        $cThis = isset($cr['c' . $i]) ? $cr['c' . $i] : '';

        if (empty($cThis)) {
            $i++;
            continue;
        }

        $lMsg = "<log>" . htmlspecialchars($cThis, ENT_QUOTES, 'UTF-8') . "</log>";
        $lXml = simplexml_load_string($lMsg);

        $opsTheme->addVariable('chatter', array(
            'id'     => (int)$lXml->user->id,
            'name'   => (string)$lXml->user->name,
            'avatar' => (string)$lXml->user->avatar,
        ));
        $opsTheme->addVariable('message', (string)$lXml->message);
        $lastLog = $opsTheme->viewPart('poker-log-message');

        $chat .= $lastLog;
        $i++;
    }
    ?>
    var chatxt = '<?php echo addslashes($chat); ?>';
    document.getElementById('chatdiv').innerHTML = chatxt;

    <?php if (!empty($cThis)) { ?>
    if (typeof document.getElementById('tablelog') != 'undefined') {
        document.getElementById('tablelog').innerHTML = '<?php echo addslashes($lastLog); ?>';
    }
    <?php } ?>
<?php
}

// Fetch user chat data
$ucq = $pdo->query("SELECT * FROM " . DB_USERCHAT . " WHERE gameID = " . (int)$gameID);
$ucr = $ucq->fetch(PDO::FETCH_ASSOC);

if ($ucr && $ucr['updatescreen'] > $time) {
    $i = 1;
    $chat = '';

    while ($i < 6) {
        $cThis = isset($ucr['c' . $i]) ? $ucr['c' . $i] : '';

        if (empty($cThis)) {
            $i++;
            continue;
        }

        $uMsg = "<chat>" . htmlspecialchars($cThis, ENT_QUOTES, 'UTF-8') . "</chat>";
        $uXml = simplexml_load_string($uMsg);

        $opsTheme->addVariable('chatter', array(
            'id'     => (int)$uXml->user->id,
            'name'   => (string)$uXml->user->name,
            'avatar' => (string)$uXml->user->avatar,
        ));
        $opsTheme->addVariable('message', (string)$uXml->message);

        if ((string)$uXml->user->name == $plyrname) {
            $chat .= $opsTheme->viewPart('poker-chat-message-me');
        } else {
            $chat .= $opsTheme->viewPart('poker-chat-message-other');
        }

        $i++;
    }
    ?>
    var userchatxt = '<?php echo addslashes($chat); ?>';
    document.getElementById('userchatdiv').innerHTML = userchatxt;

    if (userchatxt != '') {
        document.getElementById("chataudio").play();
        if (!$('.poker__chat').hasClass('active')) {
            $("#chatButton").addClass("newMsg");
        }
    }
    <?php
}
?>
