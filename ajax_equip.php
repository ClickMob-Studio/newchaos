<?php
include 'ajax_header.php';

$user_class = new User($_SESSION['id']);
$response = array("success" => false, "message" => "");

if (isset($_GET['loaned']) && $_GET['loaned'] == 1) {
    if (empty($_GET['id'])) {
        $response['message'] = "No item picked.";
        echo json_encode($response);
        exit;
    }

    $howmany = Check_Loan($_GET['id'], $user_class->id);
    $db->query("SELECT * FROM items WHERE id = ?");
    $db->execute(array($_GET['id']));
    if (!$db->num_rows()) {
        $response['message'] = "Item not found.";
        echo json_encode($response);
        exit;
    }

    $row = $db->fetch_row(true);
    if ($howmany <= 0) {
        $response['message'] = "You don't have any of those.";
        echo json_encode($response);
        exit;
    }
    if ($row['level'] > $user_class->level) {
        $response['message'] = "You aren't high enough level to use this.";
        echo json_encode($response);
        exit;
    }

    // Equip/loan item
    switch ($_GET['eq']) {
        case "weapon":
            if ($row['offense'] <= 0) {
                $response['message'] = "This item is not a weapon.";
                echo json_encode($response);
                exit;
            }
            equipLoanedItem('weapon', $user_class, $row, $db, $response);
            break;

        case "armor":
            if ($row['defense'] <= 0) {
                $response['message'] = "This item is not an armor.";
                echo json_encode($response);
                exit;
            }
            equipLoanedItem('armor', $user_class, $row, $db, $response);
            break;

        case "shoes":
            if ($row['speed'] <= 0) {
                $response['message'] = "This item is not a shoe.";
                echo json_encode($response);
                exit;
            }
            equipLoanedItem('shoes', $user_class, $row, $db, $response);
            break;
    }

    echo json_encode($response);
    exit;
} elseif (isset($_GET['unequip'])) {
    // Handle unequip logic
    unequipItem($user_class, $_GET['unequip'], $db, $response);
    echo json_encode($response);
    exit;
} elseif (isset($_GET['eq']) && isset($_GET['id'])) {
    // Handle general equipment process if item is not loaned
    if (empty($_GET['id'])) {
        $response['message'] = "No item picked.";
        echo json_encode($response);
        exit;
    }

    $howmany = Check_Item($_GET['id'], $user_class->id);
    $db->query("SELECT * FROM items WHERE id = ?");
    $db->execute(array($_GET['id']));
    $row = $db->fetch_row(true);

    if ($howmany == 0) {
        $response['message'] = "You don't have any of those.";
        echo json_encode($response);
        exit;
    }
    if ($row['level'] > $user_class->level) {
        $response['message'] = "You aren't high enough level to use this.";
        echo json_encode($response);
        exit;
    }

    // Equip item based on type
    switch ($_GET['eq']) {
        case "weapon":
            if ($row['offense'] <= 0) {
                $response['message'] = "This item is not a weapon.";
                echo json_encode($response);
                exit;
            }
            equipItem('weapon', $user_class, $row, $db, $response);
            break;

        case "armor":
            if ($row['defense'] <= 0) {
                $response['message'] = "This item is not an armor.";
                echo json_encode($response);
                exit;
            }
            equipItem('armor', $user_class, $row, $db, $response);
            
            break;

        case "shoes":
            if ($row['speed'] <= 0) {
                $response['message'] = "This item is not a shoe.";
                echo json_encode($response);
                exit;
            
            }
            equipItem('shoes', $user_class, $row, $db, $response);
            Take_Item($_GET['id'], $user_class->id);
            break;
    }

    echo json_encode($response);
    exit;
}

// Helper function for equipping loaned items
function equipLoanedItem($type, $user_class, $item, $db, &$response) {
    $column = "eq" . $type;
    if($type == 'weapon'){
        $type = 'wep';
    }
    $loanedColumn = $type . "loaned";
    
    if ($user_class->$column != 0) {
        if ($user_class->$loanedColumn == 1) {
            
            Loan_Item($user_class->gang, $user_class->$column, $user_class->id);
        } else {
            Give_Item($user_class->$column, $user_class->id);
        }
    }

    $db->query("UPDATE grpgusers SET $column = ?, `$loanedColumn` = 1 WHERE id = ?");
    $db->execute(array($item['id'], $user_class->id));
   
    $db->query("SELECT id FROM gang_loans WHERE item = ? AND idto = ?");
    $db->execute(array($item['id'], $user_class->id));
    $takeid = $db->fetch_single();
    Take_Loan($takeid, $user_class->id);
    
    $response['success'] = true;
    $response['message'] = ucfirst($type) . " equipped successfully!";
}

// Helper function for unequipping items
function unequipItem($user_class, $type, $db, &$response) {
    $column = "eq" . $type;
    $loanedColumn = $type . "loaned";
    if ($loanedColumn === "weaponloaned") {
        $loanedColumn = "weploaned";
    }
    if ($loanedColumn == "armorloaned") {
        $loanedColumn = "armloaned";
    }
    if ($user_class->$column != 0) {
        if ($user_class->$loanedColumn == 1) {
            Loan_Item($user_class->gang, $user_class->$column, $user_class->id);
        } else {
            Give_Item($user_class->$column, $user_class->id);
        }
    }

    $db->query("UPDATE grpgusers SET $column = 0, `$loanedColumn` = 0 WHERE id = ?");
    $db->execute(array($user_class->id));

    $response['success'] = true;
    $response['message'] = ucfirst($type) . " unequipped successfully!";
}

// Helper function for equipping non-loaned items
function equipItem($type, $user_class, $item, $db, &$response) {
    $column = "eq" . $type;
    $loanedColumn = $type . "loaned";
    if ($loanedColumn == "weaponloaned") {
        $loanedColumn = "weploaned";
    
    }if ($loanedColumn == "armorloaned") {
        $loanedColumn = "armloaned";
    }
    if ($user_class->$column != 0) {
        if ($user_class->$loanedColumn == 1) {
            Loan_Item($user_class->gang, $user_class->$column, $user_class->id);
        } else {
            Give_Item($user_class->$column, $user_class->id);
        }
    }

    $db->query("UPDATE grpgusers SET $column = ?, `$loanedColumn` = 0 WHERE id = ?");
    $db->execute(array($item['id'], $user_class->id));

    Take_Item($item['id'], $user_class->id);

    $response['success'] = true;
    $response['message'] = ucfirst($type) . " equipped successfully!";
}
?>
