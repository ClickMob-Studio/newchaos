<?php
require_once("header.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
class Poll {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    private function back() {
        echo '<a href="'.$_SERVER['PHP_SELF'].'">Back</a>';
        exit();
    }

    private function fetchUser($userId) {
        $this->db->query("SELECT * FROM grpgusers WHERE id = ?");
        $this->db->bind(1, $userId);
        return $this->db->fetch_row(true);
    }

    private function fetchPoll() {
        $this->db->query("SELECT * FROM `poll`");
        return $this->db->fetch_row(true);
    }

    public function index() {
        $user = $this->fetchUser($_SESSION['id']);
        $poll = $this->fetchPoll();

        if ($user['admin'] == 1) {
            echo "<a href='?x=admin'>[Admin Panel]</a>";
        }

        if (empty($poll['ID'])) {
            echo 'No poll running at the moment.';
            $this->back();
        }

        if (time() > $poll['end'] && $user['admin'] != 1) {
            echo 'The Poll has ended!';
        } else {
            $ended = (time() > $poll['end']) ? '<font color="red">The Poll has Ended</font>' : '';
            echo "You can only vote for one option on the poll";

            ?>

            <?php echo $ended ?>

            <table width="300px">
                <tr><td><h1>Question:</h1> <?php echo $poll['question'] ?></td></tr>
                <?php if ($poll['1']): ?>
                    <tr><td><?php echo $poll['1'] ?></td><td> <?php echo $poll['1_r'] ?> </td><td> <a href="?x=vote&ID=1">[Vote]</a></td></tr>
                <?php endif; ?>
                <?php if ($poll['2']): ?>
                    <tr><td><?php echo $poll['2'] ?></td><td> <?php echo $poll['2_r'] ?> </td><td> <a href="?x=vote&ID=2">[Vote]</a></td></tr>
                <?php endif; ?>
                <?php if ($poll['3']): ?>
                    <tr><td><?php echo $poll['3'] ?></td><td> <?php echo $poll['3_r'] ?> </td><td> <a href="?x=vote&ID=3">[Vote]</a></td></tr>
                <?php endif; ?>
                <?php if ($poll['4']): ?>
                    <tr><td><?php echo $poll['4'] ?></td><td> <?php echo $poll['4_r'] ?> </td><td> <a href="?x=vote&ID=4">[Vote]</a></td></tr>
                <?php endif; ?>

                <tr><td>Ends: <?php echo date('Y-m-d', $poll['end']); ?></td></tr>
            </table>
            <?php
        }
    }

    public function vote() {
        $poll = $this->fetchPoll();
        if (time() > $poll['end']) {
            echo 'Poll has ended!';
            $this->back();
        }

        $this->db->query("SELECT `id` FROM `poll_votes` WHERE `userid`= ?");
        $this->db->bind(1, $_SESSION['id']);
        $check = $this->db->fetch_row(true);

        if (!empty($check['id'])) {
            echo 'You have already voted!';
            $this->back();
        }

        $ID = ($_GET['ID'] < 1 || $_GET['ID'] > 4) ? 0 : $_GET['ID'] . '_r';

        $this->db->query("UPDATE `poll` SET `$ID` = `$ID` + 1 WHERE `ID` = 1");
        $this->db->execute();

        $this->db->query("INSERT INTO `poll_votes` (`userid`, `option`) VALUES (?, ?)");
        $this->db->bind(1, $_SESSION['id']);
        $this->db->bind(2, $ID);
        $this->db->execute();

        echo sprintf('You have successfully voted for option %u !', $_GET['ID']);
        $this->back();
    }

    public function admin() {
        if (isset($_POST['submit'])) {
            $check = $this->fetchPoll();
            if (!empty($check['ID'])) {
                echo 'Error - A poll already exists!';
                $this->back();
            }

            $end_timestamp = time() + (ctype_digit($_POST['end']) ? $_POST['end'] * 86400 : 86400);
            $this->db->query("INSERT INTO `poll` (`question`, `1`, `2`, `3`, `4`, `1_r`, `2_r`, `3_r`, `4_r`, `end`) VALUES (?, ?, ?, ?, ?, 0, 0, 0, 0, ?)");
            $this->db->bind(1, $_POST['question']);
            $this->db->bind(2, $_POST['1']);
            $this->db->bind(3, $_POST['2']);
            $this->db->bind(4, $_POST['3']);
            $this->db->bind(5, $_POST['4']);
            $this->db->bind(6, $end_timestamp);
            $this->db->execute();

            echo 'Poll Created Successfully';
            $this->back();
        } elseif (isset($_GET['del']) && $_GET['del'] == 1) {
            $this->db->query("TRUNCATE TABLE `poll`");
            $this->db->execute();
            $this->db->query("TRUNCATE TABLE `poll_votes`");
            $this->db->execute();
            echo 'Poll Deleted';
            $this->back();
        } else {
            echo sprintf('<table>
            <tr><td>Create a Poll...</td></tr> 
            <tr><td>
            <form action="%1$s?x=admin" method="POST">
            Question: </td><td> <textarea name="question"></textarea></td></tr>
            <tr><td>
            Option 1: </td><td> <input type="text" name="1" /></td></tr>
            <tr><td>
            Option 2: </td><td> <input type="text" name="2" /></td></tr>
            <tr><td>
            Option 3: </td><td> <input type="text" name="3" /></td></tr>
            <tr><td>
            Option 4: </td><td> <input type="text" name="4" /></td></tr>
            <tr><td>
            End In: </td><td> <input type="text" name="end" />(days)</td></tr>
            <tr><td>
            <input type="submit" name="submit" value="Create" />
            </form>
            <a href="%1$s?x=admin&del=1">[Delete current Poll]</a>
            </td></tr>
            </table>', $_SERVER['PHP_SELF']);
        }
    }
}

$poll = new Poll();

echo '<h1>Poll</h1>';

switch($_GET['x']) {
    case 'vote':
        $poll->vote();
        break;
    case 'admin':
        $poll->admin();
        break;
    default:
        $poll->index();
        break;
}
?>
