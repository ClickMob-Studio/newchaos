<?php
include_once('header.php');

if ($user_class->admin > 0)
{
    echo '<table class="table">
        <tr>
            <th colspan="4" class="heading">Staff Options</th>
        </tr>
        <tr>
            <th width="25%"><a href="tickets.php?action=stafflist">List Tickets</a></th>
            <th width="25%"><a href="tickets.php?action=yourlist">Your Tickets</a></th>
            <th width="25%"><a href="tickets.php?action=closedtickets">Your Closed Tickets</a></th>
            <th width="25%">
                ' . ($user_class->admin == 1 ? '<a href="tickets.php?action=alltickets">View All</a>' : '') . '
            </th>
        </tr>
    </table>';
}

$action = (array_key_exists('action', $_GET) && strlen($_GET['action']) > 0 && ctype_alnum($_GET['action'])) ? substr($_GET['action'], 0, 20) : FALSE ;
switch ($action)
{
    case "alltickets":      ($ir['user_level'] == 2 ? view_all() : support_index());        break;
    case "closeticket":     ($ir['user_level'] > 1 ? close_ticket() : support_index());     break;
    case "pushticket":      ($ir['user_level'] > 1 ? push_ticket() : support_index());      break;
    case "viewticket":      ($ir['user_level'] > 1 ? view_ticket() : support_index());      break;
    case "closedtickets":   ($ir['user_level'] > 1 ? closed_tickets() : support_index());   break;
    case "stafflist":       ($ir['user_level'] > 1 ? staff_list() : support_index());       break;
    case "yourlist":        ($ir['user_level'] > 1 ? your_list() : support_index());        break;
    case "view":            support_view();                                                 break;
    case "open":            support_open();                                                 break;
    case "closed":          support_closed();                                               break;
    default:                support_index();                                                break;
}
function support_index()
{
    global $db,$user_class;

    echo '<table>
        <tr>
            <th class="heading">Welcome to the support system, ' . $user_class->formattedname. '</th>
        </tr>
    </table>';

    $subject = '';
    $message = '';

    if ($_SERVER['REQUEST_METHOD'] == "POST")
    {
        $error = '';

        $subject_error = (array_key_exists('subject', $_POST) && strlen($_POST['subject']) >= 5)  ? FALSE : TRUE ;
        $message_error = (array_key_exists('message', $_POST) && strlen($_POST['message']) >= 10) ? FALSE : TRUE ;
        
        $subject = $_POST['subject'];
        $message = $_POST['message'];
        $type    = (array_key_exists('type', $_POST) && (ctype_digit($_POST['type']))) ? substr($_POST['type'], 0, 1) : 0 ;
        if ($subject_error == TRUE)
        {
            $error .= '<p>You did not enter a subject. It needs to be 5 characters or more.</p>';
        }
        
        if ($message_error == TRUE)
        {
            $error .= '<p>You did not enter a message. It needs to be at least 10 characters in length.</p>';
        }

        if (strlen($error) > 0)
        {
            echo '<h4 style="color: red;">There seems to have been an error in your submission.</h4>'.
            $error;
        }
        else
        {
            $subject = mysql_real_escape_string($subject);
            $message = mysql_real_escape_string($message);

            $sql = "INSERT INTO `support_tickets` (`user`,`subject`,`message`, `time`, `admin`) VALUES ('".$user_class->id."', '" . $subject . "', '" . $message . "', UNIX_TIMESTAMP(), '{$type}')";
mysql_query($sql) or die(mysql_error());


            if (mysql_insert_id() > 0) {
                echo Message('<h4>Your ticket has been successfully sent.</h4>
                <p>You will get an event once it has been read and assigned.</p>
                <p>Thanks for getting in touch.</p>');
            }
            else {
                echo '<h4 style="color: red;">There seems to have been an error in your submission.</h4>
                <p>You will get an event once it has been read and assigned.</p>
                <p>Thanks for getting in touch.</p>';
            }
     
            exit;
        }
    }

    echo '<p>Please post your inquiry, bug report, suggestions etc below.</p>
    <form action="supporttickets.php" method="post">
    <table>
        <tr>
            <th width="20%">Subject</th>
            <td><input type="text" name="subject" value="' . $subject . '" class="input_select">
        </tr>
        <tr>
            <th>Type</th>
            <td>
                <select name="type" class="input_select">
                    <option value="0">Select One</option>
                    <option value="1">Game Bug</option>
                    <option value="1">Report Player</option>
                    <option value="1">Complaint about staff</option>
                    <option value="1">Report Cheating</option>
                    <option value="1">Suggestions</option>
                    <option value="0">Questions about game play</option>
                    <option value="0">Question about a feature</option>
                    <option value="0">Account Issues</option>
                    <option value="1">Other</option>
                </select>
            </td>
        </tr>
        <tr>
            <th>Message</th>
            <td><textarea name="message" class="input_textarea">' . $message . '</textarea></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: left;">
                <button style="margin-left: 20%;" class="input_button">Submit</button>
            </td>
        </tr>
    </table>
    </form>';
    $sql    = "SELECT COUNT(*) FROM `support_tickets` WHERE `user` = '".$user_class->id."' AND `closed` = 0";
    $active = mysql_num_rows($sql);
    $sql    = "SELECT COUNT(*) FROM `support_tickets` WHERE `user` = '".$user_class->id."' AND `closed` = 1";
    $closed = mysql_num_rows($sql);
    echo '<table class="table">
        <tr>
            <th colspan="2" class="heading">Previous Tickets</th>
        </tr>
        <tr>
            <th width="50%"><a href="tickets.php?action=open">Open (' . $active . ')</a></th>
            <th><a href="tickets.php?action=closed">Closed (' . $closed . ')</a></th>
        </tr>
    </table>';
}
function support_closed()
{
    global $db, $c, $ir, $h;

    echo '<table class="table">
        <tr>
            <th class="heading">Your closed tickets</th>
        </tr>
    </table>
    <table class="table">';

    $sql = "SELECT * FROM `support_tickets` WHERE `user` = '{$ir['userid']}' AND `closed` = 1 ORDER BY `id` DESC";
    if ( ($res = $db->fetchAll($sql)) == false ) {
        echo '<tr>
            <td>You do not have any closed tickets.</td>
        </tr>';
    }
    else {
        foreach ($res AS $ticket) {
            echo '<tr>
                <th colspan="2">' . htmlentities($ticket['subject'], ENT_QUOTES, "UTF-8") . '</th>
            </tr>
            <tr>
                <td width="25%">Ticket <a href="tickets.php?action=view&id=' . $ticket['id'] . '">#' . $ticket['id'] . '</td>
                <td>' . str_replace("\n", "<br />", htmlentities($ticket['message'], ENT_QUOTES, "UTF-8")) . '</td>
            </tr>';
        }
    }
    echo '</table>';
}
function support_open()
{
    global $db, $c, $ir, $h;

    echo '<h3>Your open tickets</h3>
    <table class="table" style="width: 100%;">';

    $sql = "SELECT * FROM `support_tickets` WHERE `user` = '{$ir['userid']}' AND `closed` = 0 ORDER BY `id` DESC";
    if ( ($res = $db->fetchAll($sql)) == false ) {
        echo '<tr>
            <td colspan="2">You do not have any open tickets.</td>
        </tr>';
    }
    else {
        foreach ($res AS $ticket) {
            echo '<tr>
                <th colspan="2">' . htmlentities($ticket['subject'], ENT_QUOTES, "UTF-8") . '</th>
            </tr>
            <tr>
                <td width="25%">Ticket <a href="tickets.php?action=view&id=' . $ticket['id'] . '">#' . $ticket['id'] . '</td>
                <td>' . str_replace("\n", "<br />", stripslashes(htmlentities($ticket['message'], ENT_QUOTES, "UTF-8"))) . '</td>
            </tr>';
        }
    }
    echo '</table>';
}
function support_view()
{
    global $db, $c, $ir, $h;

    $id   = (array_key_exists('id', $_GET) && (is_int($_GET['id']) || ctype_digit($_GET['id']))) ? substr($_GET['id'], 0, 12) : FALSE ;
    if ($id)
    {
        echo '<h3>Viewing ticket #' . $id . '</h3>';
        $sql = "SELECT u.`userid`, u.`username`, t.* FROM `support_tickets` t LEFT JOIN `users` u ON t.`user` = u.`userid` WHERE `id` = '{$id}' AND t.`user` = '{$ir['userid']}' LIMIT 1";
        if ( ($ticket = $db->fetchRow($sql)) == true ) {
            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                $reply = (array_key_exists('reply', $_POST) && is_string($_POST['reply']) && strlen($_POST['reply']) > 0) ? $db->escapeString($_POST['reply']) : FALSE ;
                if ($reply) {
                    $sql = "INSERT INTO `support_replies` (`ticket`, `user`,`message`,`time`) VALUES ('{$ticket['id']}', '{$ir['userid']}', " . $reply . ", UNIX_TIMESTAMP())";
                    $db->execute($sql);
                    if ($ticket['assigned'] > 0)
                    {
                        $text = '<a href="viewuser.php?u=' . $ir['userid'] . '">' . htmlentities($ir['username'], ENT_QUOTES, "UTF-8") . '</a> replied to one of the tickets you are assigned to: <a href="tickets.php?action=viewticket&id=' . $ticket['id'] . '">Here</a>';
                        event_add($ticket['assigned'], $text, $c);
                    }
                }
            }

            echo '<table class="table">
                <tr>
                    <th colspan="2">' . htmlentities($ticket['subject'], ENT_QUOTES, "UTF-8") . '</th>
                </tr>
                <tr>
                    <td width="25%">
                        <p><a href="viewuser.php?u=' . $ticket['userid'] . '" style="font-weight: bold;">' . htmlentities($ticket['username'], ENT_QUOTES, "UTF-8") . '</a></p>
                        ' . date("jS M g:i:sa", $ticket['time']) . '
                    </td>
                    <td>' . str_replace("\n", "<br />", htmlentities($ticket['message'], ENT_QUOTES, "UTF-8")) . '</td>
                </tr>
            </table>
            <h4>Conversation</h4>
            <table class="table">';

            $sql = "SELECT u.`userid`, u.`username`, u.`user_level`, r.* FROM `support_replies` r LEFT JOIN `users` u ON r.`user` = u.`userid` WHERE r.`ticket` = '{$ticket['id']}' ORDER BY r.`time` ASC";
            if ( ($res = $db->fetchAll($sql)) == false ) {
                echo '<tr>
                    <td>There have been no replies yet.</td>
                </tr>';
            }
            else {
                foreach ($res AS $replies) {
                    echo '<tr>
                       <td width="25%">
                            <p><a href="viewuser.php?u=' . $replies['userid'] . '" style="font-weight: bold;">' . htmlentities($replies['username'], ENT_QUOTES, "UTF-8") . '</a></p>
                            <p>' . date("jS M g:i:sa", $replies['time']) . '</p>
                            <p>' . ($replies['user_level'] > 1 ? '<p style="color: orange;">Staff Member</p>' : '') . '</p>
                        </td>
                        <td>';
                        
                        if ($replies['user_level'] > 1) {
                            echo str_replace("\n", "<br />", $replies['message']);
                        }
                        else {
                            echo str_replace("\n", "<br />", htmlentities(stripslashes($replies['message']), ENT_QUOTES, "UTF-8"));
                        }

                        echo '</td>
                    </tr>';
                }
            }
            echo '</table>';

            if ($ticket['closed'] == 0) {
                echo  '<h4>Reply</h4>
                <form action="tickets.php?action=view&id=' . $ticket['id'] . '" method="post">
                    <table class="table">
                        <tr>
                            <td><textarea name="reply" style="width: 98%; height: 200px;"></textarea>
                        </tr>
                        <tr>
                            <td><button>Reply</button></td>
                        </tr>
                    </table>
                </form>';
            }
            else {
                echo '<p>This ticket is now closed.</p>';
            }
        }
        else {
            echo '<p>No valid support ticket selected</p>';
        }
    }
    else {
        echo '<p>No valid ticket selected.</p>';
    }
}

function staff_list()
{
    global $db, $c, $ir, $h;

    echo '<table class="table">
        <tr>
            <th class="heading">New Tickets</th>
        </tr>
    </table>
    <table class="table">
        <tr>
            <th width="10%">Ticket ID</th>
            <th width="15%">Sender</th>
            <th>Subject</th>
        </tr>';
        
        $sql = "SELECT u.`userid`, u.`username`, t.`id`, t.`subject` FROM `support_tickets` t LEFT JOIN `users` u ON t.`user` = u.`userid` WHERE `admin` = 0 AND `assigned` = 0 AND `closed` = 0;";
        if ( ($res = $db->fetchRow($sql)) == true ) {
            foreach ($res AS $tickets) {
                echo '<tr>
                    <td><a href="tickets.php?action=viewticket&id=' . $tickets['id'] . '">Ticket #' . $tickets['id'] . '</a></td>
                    <td><a href="viewuser.php?u=' . $tickets['userid'] . '">' . htmlentities($tickets['username'], ENT_QUOTES, "UTF-8") . '</a></td>
                    <td>' . htmlentities($tickets['subject'], ENT_QUOTES, "UTF-8") . '</td>
                </tr>';
            }
        }
        else{
            echo '<tr>
                <td colspan="3">There are no new tickets that haven\'t been assigned.</td>
            </tr>';
        }
    echo '</table>';

    if ($ir['user_level'] == 2)
    {
        echo '<table class="table">
            <tr>
                <th class="heading">Pending for Admin</th>
            </tr>
        </table>
        <table class="table">
            <tr>
                <th width="10%">Ticket ID</th>
                <th width="15%">Sender</th>
                <th>Subject</th>
            </tr>';
        
            $sql = "SELECT u.`userid`, u.`username`, t.`id`, t.`subject` FROM `support_tickets` t LEFT JOIN `users` u ON t.`user` = u.`userid` WHERE `assigned` = 0 AND `admin` = 1 AND `closed` = 0;";
            if ( ($res = $db->fetchAll($sql)) == true ) {
                foreach ($res AS $tickets) {
                    echo '<tr>
                        <td><a href="tickets.php?action=viewticket&id=' . $tickets['id'] . '">Ticket #' . $tickets['id'] . '</a></td>
                        <td><a href="viewuser.php?u=' . $tickets['userid'] . '">' . htmlentities($tickets['username'], ENT_QUOTES, "UTF-8") . '</a></td>
                        <td>' . htmlentities($tickets['subject'], ENT_QUOTES, "UTF-8") . '</td>
                    </tr>';
                }
            }
            else {
                echo '<tr>
                    <td colspan="3">There are no new tickets that haven\'t been assigned.</td>
                </tr>';
            }
        echo '</table>';
    }
}
function your_list()
{
    global $db, $c, $ir, $h;

    echo '<table class="table">
        <tr>
            <th class="heading">Your Tickets</th>
        </tr>
    </table>
    <table class="table">
        <tr>
            <th width="10%">Ticket ID</th>
            <th width="15%">Sender</th>
            <th>Subject</th>
        </tr>';
        
        $sql = "SELECT u.`userid`, u.`username`, t.`id`, t.`subject` FROM `support_tickets` t LEFT JOIN `users` u ON t.`user` = u.`userid` WHERE `assigned` = '{$ir['userid']}' AND `closed` = 0;";
        if ( ($res = $db->fetchAll($sql)) == true ) {
            foreach ($res AS $tickets) {
                echo '<tr>
                    <td><a href="tickets.php?action=viewticket&id=' . $tickets['id'] . '">Ticket #' . $tickets['id'] . '</a></td>
                    <td><a href="viewuser.php?u=' . $tickets['userid'] . '">' . htmlentities($tickets['username'], ENT_QUOTES, "UTF-8") . '</a></td>
                    <td>' . htmlentities($tickets['subject'], ENT_QUOTES, "UTF-8") . '</td>
                </tr>';
            }
        }
        else {
            echo '<tr>
                <td colspan="3">You have no open tickets you are assigned to.</td>
            </tr>';
        }
    echo '</table>';
}
function closed_tickets()
{
    global $db, $c, $ir, $h;

    echo '<table class="table">
        <tr>
            <th class="heading">Your closed tickets</th>
        </tr>
    </table>
    <table class="table">
        <tr>
            <th width="10%">Ticket ID</th>
            <th width="15%">Sender</th>
            <th>Subject</th>
        </tr>';
        
        $sql = "SELECT u.`userid`, u.`username`, t.`id`, t.`subject` FROM `support_tickets` t LEFT JOIN `users` u ON t.`user` = u.`userid` WHERE `assigned` = '{$ir['userid']}' AND `closed` = 1;";
        if ( ($res = $db->fetchAll($sql)) == true ) {
            foreach ($res AS $tickets) {
                echo '<tr>
                    <td>Ticket #' . $tickets['id'] . '</td>
                    <td><a href="viewuser.php?u=' . $tickets['userid'] . '">' . htmlentities($tickets['username'], ENT_QUOTES, "UTF-8") . '</a></td>
                    <td>' . htmlentities($tickets['subject'], ENT_QUOTES, "UTF-8") . '</td>
                </tr>';
            }
        }
        else {
            echo '<tr>
                <td colspan="3">You have no open tickets you are assigned to.</td>
            </tr>';
        }
    echo '</table>';
}
function view_ticket()
{
    global $db, $c, $ir, $h;

    $id   = (array_key_exists('id', $_GET) && (is_int($_GET['id']) || ctype_digit($_GET['id']))) ? substr($_GET['id'], 0, 12) : FALSE ;
    if ($id)
    {
        $code = MD5($id . $ir['userid']);
        $take = (array_key_exists('code', $_GET) && ctype_alnum($_GET['code'])) ? substr($_GET['code'], 0, 32) : FALSE ;
        if ($take == $code) {
            $sql = "UPDATE `support_tickets` SET `assigned` = '{$ir['userid']}' WHERE `id` = '{$id}' AND `assigned` = 0";
            $db->execute($sql);
        }
        echo '<table class="table">
            <tr>
                <th class="heading">Viewing ticket #' . $id . '</th>
            </tr>
        </table>';
        $sql = "SELECT u.`userid`, u.`username`, t.* FROM `support_tickets` t LEFT JOIN `users` u ON t.`user` = u.`userid` WHERE `id` = '{$id}' LIMIT 1";
        if ( ($ticket = $db->fetchRow($sql)) == true ) {
            if (($ticket['assigned'] !== "0" && $ticket['assigned'] !== $ir['userid']) && $ir['user_level'] !== "2") {
                echo '<p>We\'re sorry, but you are not assigned to this task, so can not work on it.</p>';
         
                exit;
            }
            if ($ticket['admin'] == 1 && $ir['user_level'] !== "2") {
                echo '<p>You can not view this ticket as it is assigned to admin.</p>';
         
                exit;
            }
            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                $reply = (array_key_exists('reply', $_POST) && is_string($_POST['reply']) && strlen($_POST['reply']) > 0) ? $db->escapeString($_POST['reply']) : FALSE ;
                if ($reply) {
                    $sql  = "INSERT INTO `support_replies` (`ticket`, `user`,`message`,`time`) VALUES ('{$ticket['id']}', '{$ir['userid']}', " . $reply . ", UNIX_TIMESTAMP())";
                    $db->execute($sql);
                    $text = '<a href="viewuser.php?u=' . $ir['userid'] . '">' . htmlentities($ir['username'], ENT_QUOTES, "UTF-8") . '</a> just replied to your support ticket: <a href="tickets.php?action=view&id=' . $ticket['id'] . '">Here</a>';
                    event_add($ticket['user'], $text, $c);
                }
            }
            echo '<table class="table">
                <tr>
                    <th colspan="2">' . htmlentities($ticket['subject'], ENT_QUOTES, "UTF-8") . '</th>
                </tr>
                <tr>
                    <td width="25%">
                        <p><a href="viewuser.php?u=' . $ticket['userid'] . '" style="font-weight: bold;">' . htmlentities($ticket['username'], ENT_QUOTES, "UTF-8") . '</a></p>
                        ' . date("jS M g:i:sa", $ticket['time']) . '
                    </td>
                    <td>' . stripslashes(str_replace(array("\n", '\\'), array("<br />", ""), htmlentities($ticket['message'], ENT_QUOTES, "UTF-8"))) . '</td>
                </tr>
            </table>
            <table class="table">
                <tr>
                    <th class="heading">Conversation</th>
                </tr>
            </table>
            <table class="table">';

            $sql = "SELECT u.`userid`, u.`username`, u.`user_level`, r.* FROM `support_replies` r LEFT JOIN `users` u ON r.`user` = u.`userid` WHERE r.`ticket` = '{$ticket['id']}' ORDER BY r.`time` ASC";
            if ( ($res = $db->fetchAll($sql)) == false ) {
                echo '<tr>
                    <td>There have been no replies yet.</td>
                </tr>';
            }
            else {
                foreach ($res AS $replies) {
                    echo '<tr>
                       <td width="25%">
                            <p><a href="viewuser.php?u=' . $replies['userid'] . '" style="font-weight: bold;">' . htmlentities($replies['username'], ENT_QUOTES, "UTF-8") . '</a></p>
                            <p>' . date("jS M g:i:sa", $replies['time']) . '</p>
                            <p>' . ($replies['user_level'] > 1 ? '<p style="color: orange;">Staff Member</p>' : '') . '</p>
                        </td>
                        <td>';

                        if ($replies['user_level'] > 1) {
                            echo str_replace(array("\n", '\\'), array("<br />", ''), $replies['message']);
                        }
                        else {
                            echo str_replace("\n", "<br />", htmlentities(str_replace('\\', '', $replies['message']), ENT_QUOTES, "UTF-8"));
                        }

                        echo '</td>
                    </tr>';
                }
            }
            echo '</table>
            <table class="table">
                <tr>
                    <th class="heading">Reply</th>
                </tr>
            </table>';

            if ($ticket['closed'] == 0 || $ir['user_level'] == 2) {
                if ($ticket['assigned'] == 0) {
                    echo '<table class="table">
                        <tr>
                            <th colspan="2">Nobody has claimed this ticket yet. Would you like to take it?</th>
                        </tr>
                        <tr>
                            <td width="50%"><a href="tickets.php?action=viewticket&id=' . $ticket['id'] . '&code=' . $code . '">I\'ll take it</a></td> 
                            <td><a href="tickets.php">No, not for me</a></td>
                        </tr>
                    </table>';
                }
                else {
                    echo '<form action="tickets.php?action=viewticket&id=' . $ticket['id'] . '" method="post">
                        <table class="table">
                            <tr>
                                <td><textarea name="reply" class="input_textarea"></textarea></td>
                            </tr>
                            <tr>
                                <td>
                                    <button class="input_button">Reply</button> [<a href="tickets.php?action=closeticket&id=' . $ticket['id'] . '">Close Ticket</a>] [<a href="tickets.php?action=pushticket&id=' . $ticket['id'] . '">Push Ticket to Admin</a>]
                                </td>
                            </tr>
                        </table>
                    </form>';
                }
            }
            else {
                echo '<p>This ticket has been closed.</p>';
            }
        }
        else {
            echo '<p>No valid support ticket selected</p>';
        }
    }
    else {
        echo '<p>No valid ticket selected.</p>';
    }
}
function close_ticket()
{
    global $db, $c, $ir, $h;

    echo '<h3>Closing Ticket</h3>';
    $id = (array_key_exists('id', $_GET) && (is_int($_GET['id']) || ctype_digit($_GET['id']))) ? substr($_GET['id'], 0, 12) : FALSE ;
    if ($id) {
        $sql = "SELECT * FROM `support_tickets` WHERE `id` = '{$id}' AND `closed` = 0 LIMIT 1";
        if ( ($ticket = $db->fetchRow($sql)) == true ) {
            if ($ticket['assigned'] == $ir['userid'] || $ir['user_level'] == 2) {
                $sql  = "UPDATE `support_tickets` SET `closed` = 1 WHERE `id` = '{$ticket['id']}'";
                $db->execute($sql);
                echo '<p>Ticket Closed.</p>';
                $text = '<a href="viewuser.php?u=' . $ir['userid'] . '">' . htmlentities($ir['username'], ENT_QUOTES, "UTF-8") . '</a> just closed your ticket: <a href="tickets.php?action=view&id=' . $ticket['id'] . '">Here</a>';
                event_add($ticket['user'], $text, $c);
            }
            else {
                echo '<p>You do not have permission to close this ticket.</p>';
            }
        }
        else {
            echo '<p>Invalid ticket selected.</p>';
        }
    }
    else {
        echo '<p>No valid ticket selected.</p>';
    }
}
function push_ticket()
{
    global $db, $c, $ir, $h;

    echo '<h3>Pushing Ticket</h3>';
    $id = (array_key_exists('id', $_GET) && (is_int($_GET['id']) || ctype_digit($_GET['id']))) ? substr($_GET['id'], 0, 12) : FALSE ;
    if ($id) {
        $sql = "SELECT * FROM `support_tickets` WHERE `id` = '{$id}' AND `closed` = 0 LIMIT 1";
        if ( ($ticket = $db->fetchRow($sql)) == true ) {
            if ($ticket['assigned'] == $ir['userid'] || $ir['user_level'] == 2) {
                $sql = "UPDATE `support_tickets` SET `assigned` = 0, `admin` = 1 WHERE `id` = '{$ticket['id']}'";
                $db->execute($sql);
                echo '<p>Ticket Pushed to Admin.</p>';
                $text = '<a href="viewuser.php?u=' . $ir['userid'] . '">' . htmlentities($ir['username'], ENT_QUOTES, "UTF-8") . '</a> just passed your ticket to admin: <a href="tickets.php?action=view&id=' . $ticket['id'] . '">Here</a>. They will be in contact shortly.';
                event_add($ticket['user'], $text, $c);
            }
            else {
                echo '<p>You do not have permission to push this ticket.</p>';
            }
        }
        else {
            echo '<p>Invalid ticket selected.</p>';
        }
    }
    else {
        echo '<p>No valid ticket selected.</p>';
    }
}
function view_all()
{
    global $db, $c, $ir, $h;

    $limit = 0;
    $page  = (array_key_exists('page', $_GET) && (is_int($_GET['page']) || ctype_digit($_GET['page'])) && $_GET['page'] > 0) ? substr($_GET['page'], 0, 12) : 1 ;
    $limit = (($page - 1) * 20);
    echo '<table class="table">
        <tr>
            <th class="heading">All Tickets</th>
        </tr>
    </table>
    <table class="table">
        <tr>
            <th colspan="5">
                <div style="float: left;">' . ($page > 1 ? '<a href="tickets.php?action=alltickets&page=' . ($page - 1) . '" class="btn btn-small btn-info span12">Previous</a>' : '') . '</div>
                <div style="float: right;"><a href="tickets.php?action=alltickets&page=' . ($page + 1) . '" class="btn btn-small btn-info span12">Next</a></div>
            </th>
        </tr>
        <tr>
            <th width="10%">Ticket ID</th>
            <th width="15%">Sender</th>
            <th>Subject</th>
            <th width="10%">Assigned</th>
            <th width="10%">Status</th>
        </tr>';
        $sql = "SELECT u.`userid` AS `adder_id`, u.`username` AS `adder_name`, u2.`userid` AS `assign_id`, u2.`username` AS `assign_name`, ".
               "t.`id`, t.`subject`, t.`assigned`, t.`closed` FROM `support_tickets` t LEFT JOIN `users` u ON t.`user` = u.`userid` ".
               "LEFT JOIN `users` u2 ON t.`assigned` = u2.`userid` ORDER BY t.`closed` ASC, t.`id` ASC LIMIT $limit, 20";
        if ( ($res = $db->fetchAll($sql)) == true ) {
            foreach ($res AS $tickets) {
                echo '<tr>
                    <td><a href="tickets.php?action=viewticket&id=' . $tickets['id'] . '">Ticket #' . $tickets['id'] . '</a></td>
                    <td><a href="viewuser.php?u=' . $tickets['adder_id'] . '">' . htmlentities($tickets['adder_name'], ENT_QUOTES, "UTF-8") . '</a></td>
                    <td>' . htmlentities($tickets['subject'], ENT_QUOTES, "UTF-8") . '</td>
                    <td>' . (strlen($tickets['assign_name']) > 0 ? '<a href="viewuser.php?u=' . $tickets['assign_id'] . '">' . htmlentities($tickets['assign_name'], ENT_QUOTES, "UTF-8") . '</a>' : '---') . '</td>
                    <td>' . ($tickets['closed'] == 0 ? 'Open' : 'Closed') . '</td>
                </tr>';
            }
        }
        else {
            echo '<tr>
                <td colspan="5">You have no open tickets you are assigned to.</td>
            </tr>';
        }
    echo '</table>';
}
$h->endpage();