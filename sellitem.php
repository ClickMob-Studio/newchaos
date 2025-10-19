<?php
include 'header.php';
?>
<div class='box_top'>Sell Item</div>
<div class='box_middle'>
  <div class='pad'>
    <?php
    if (!isset($_GET['id']) || $_GET['id'] == "") {
      echo Message("You haven't picked an item.");
      include 'footer.php';
      die();
    }
    $worked = Get_Item($_GET['id']);
    if (empty($worked) || $worked['itemname'] == "") {
      echo Message("That isn't a real item.");
      include 'footer.php';
      die();
    }

    if ($worked['buyable'] == 0) {
      echo Message("You can't sell that item.");
      include 'footer.php';
      die();
    }

    $howmany = Check_Item($_GET['id'], $user_class->id); //check how many they have
    $price = $worked['cost'] * .60;
    ?>
    <style>
      button {
        background-color: #2d87f0;
        color: #fff;
        padding: 5px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
      }
    </style>
    <script>
      // JavaScript function to update sell price dynamically
      function updateSellPrice() {
        var amountInput = document.getElementById('amount');
        var sellPrice = document.getElementById('sell-price');
        var amount = parseInt(amountInput.value) || 0;
        if (amount > <?php echo $howmany; ?>) {
          return;
        }
        var newPrice = <?php echo $price; ?> * amount;
        var formatPrice = newPrice.toLocaleString();
        sellPrice.innerText = "Sell Price: $" + formatPrice;
      }
    </script>
    <?php
    if (isset($_POST['submit'])) { //if they confirm they want to sell it
      if (!isset($_POST['amount']) || $_POST['amount'] == "") {
        echo Message("You need to enter an amount to sell.");
        include 'footer.php';
        die();
      }

      $_POST['amount'] = intval($_POST['amount']);
      if ($_POST['amount'] < 1) {
        $error = "You need to sell at least 1";
      }
      $error = ($howmany < $_POST['amount']) ? "You don't have that many of those." : null;
      if (isset($error)) {
        echo Message($error);
        include 'footer.php';
        die();
      }
      $price = $price * $_POST['amount'];
      $newmoney = $user_class->money + $price;
      perform_query("UPDATE `grpgusers` SET `money` = ? WHERE `id` = ?", [$newmoney, $_SESSION['id']]);
      perform_query("INSERT INTO `item_sell` (`userid`, `itemid`, `quantity`, `price`, `when`) VALUES (?, ?, ?, ?, ?)", [$user_class->id, $_GET['id'], $_POST['amount'], $price, time()]);

      Take_Item($_GET['id'], $user_class->id, $_POST['amount']);
      echo Message("You have sold " . $_POST['amount'] . " x " . $worked['itemname'] . " for $" . prettynum($price) . ".<br /><br /><a href='inventory.php'>Back to Inventory</a>");
      include 'footer.php';
      die();
    }
    ?>
    <tr>
      <td class="contentspacer"></td>
    </tr>
    <tr>
      <td class="contenthead">Sell Item</td>
    </tr>
    <tr>
      <td class="contentcontent">
        <table width='100%'>
          <tr>
            How many would you like to sell? You currently have <?php echo $howmany; ?> x
            <?php echo $worked['itemname']; ?>
            <br>
            <div id="sell-price" style="color:red" class="sell-price">Sell Price: $0</div>
            <form method="post">
              <input type="number" name="amount" id="amount" max="<?php echo $howmany; ?>" oninput="updateSellPrice()">
              <br>
              <input type="submit" name="submit" value="submit">
            </form>
          </tr>
        </table>

      </td>
    </tr>
    </table>
    <?php
    include 'footer.php';
    ?>