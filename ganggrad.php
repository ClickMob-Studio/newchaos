<?php
include 'header.php';
?>

<div class='box_top'>Gang Gradient</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        if ($user_class->gang == 0)
            diefun("You are not in a gang.");
        $gang_class = new Gang($user_class->gang);
        $user_rank = new GangRank($user_class->grank);
        if ($user_rank->ganggrad != 1)
            diefun("You do not have permission to be here.");
        $removeAmnt = 1000;
        if (!array_key_exists('action', $_GET))
            $_GET['action'] = '';
        switch ($_GET['action']) {
            default:
                index();
                break;
            case "submit":
                submit();
                break;
            case "confirm":
                confirm();
                break;
            case "buy":
                buy();
                break;
        }
        function index()
        {
            global $gang_class;
            if ($gang_class->formattedTag == "No")
                diefun("Looks like you dont have a Gang Gradient Tag Click here to buy it for (1,000 Points) <input type='submit' onclick=location.href='?action=buy' value='Buy' />");
            print "
        <script type='text/javascript' data-cfasync='false' src='js/cp/jscolor.js'></script>
        <form action='?action=submit' method='post'>
                  <div class='form-item'>
                      <label for='start'>First Color:</label>
                      <input type='text' id='start' name='first' class='color' value='$gang_class->Color1' />
                  </div>
                  <div class='form-item'>
                      <label for='start'>Second Color:</label>
                      <input type='text' id='middle' name='second' class='color' value='$gang_class->Color2' />
                  </div>
              <div class='form-item'>
                      <label for='stop'>Third Color:</label>
                      <input type='text' id='stop' name='third' class='color' value='$gang_class->Color3' />
              </div>
              <div id='gradient'>
                  <div id='picker'></div>
                  <input type='submit' name='submit' value='Create Username' />
              </div>
        </form>
    ";
        }
        function buy()
        {
            global $gang_class, $removeAmnt;
            if ($gang_class->formattedTag == "Yes")
                diefun("Looks like you already have a Formatted Tag ", "<input type='submit' onclick=location.href='?ganggrad' value='Back' />");
            else if ($gang_class->pointsvault < $removeAmnt)
                diefun("Uh-Oh it looks like you dont have enough to purchase a formatted gang tag you need 1,000 Points in your Gang Vault");
            else {
                perform_query("update gangs set pointsvault = pointsvault - ?, formattedTag = 'Yes' where id = ?", [$removeAmnt, $gang_class->id]);
                diefun("You can now add a gradient to your Gang Tag, Its time to look cool!", "<input type='submit' onclick=location.href='?ganggrad' value='Back' />");
            }
        }
        function submit()
        {
            global $gang_class, $removeAmnt;

            if ($gang_class->formattedTag == "No")
                diefun("Looks like you dont have a Gradient Tag", "Click here to buy it for (1,000 Points) <input type='submit' onclick=location.href='?action=buy' value='Buy' />");

            if ($gang_class->pointsvault < $removeAmnt)
                diefun(NULL, "You do not have enough Points in your Gang Vault!");
            $first = $_POST['first'];
            $second = $_POST['second'];
            $third = $_POST['third'];
            $pattern = "/#?[0-9A-Fa-f]{6}/i";
            if (!preg_match($pattern, $first) || !preg_match($pattern, $second) || !preg_match($pattern, $third))
                diefun("There was an error. Please try again <input type='submit' value='Back' onclick=location.href='ganggrad.php' />");

            perform_query("UPDATE gangs SET tColor1 = ?, tColor2 = ?, tColor3 = ? WHERE id = ?", [$first, $second, $third, $gang_class->id]);
            diefun("Your Formatted Tag has been changed refresh to the page to see the effect! <input type='submit' value='Home' onclick=location.href='index.php' />");
        }
        ?>
    </div>