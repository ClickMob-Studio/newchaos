<?php

if (strpos($_SERVER['PHP_SELF'], '.inc.php') !== false || !isset($view)) {
    die('You cannot access this file directly.');
}

if ($view->profile->id == 300) {
    ?>
    <h1><?php echo PROFILE; ?></h1>
    <div class="contentBox">
        <center>
        <img width="150px" src="images/Boss/goon.png">
        <br><br>
        <b>The king doesn't want to see you. Leave at once!</b>
        </center>

    </div>
    <?php
    include_once 'footer.php';
    die();
}
?>                     
<h1><?php echo PROFILE; ?></h1>
<div class="contentBox">
	<table width='100%'>
		<tr>
			<td colspan='4'>

			<table width='100%' height='100%' cellpadding='5' cellspacing='2'>
				<tr>

					<td width='120' align='center'><img height="100" width="100"
						src="images/Boss/Avatars/<?php
                        echo $view->profile->avatar; ?>"></td>
					<td align='center'><b><?php echo PROFILE_FAVORITE_QUOTE; ?>: </b>"<?php
                    echo stripslashes($view->profile->quote);
                    ?>"
<br>
					<br>
		</td>

				</tr>
			</table>

			</td>
		</tr>

		<tr>
			<td width='15%'><b><?php echo COM_NAME; ?></b>:</td>
			<td width='35%'><b><?php echo $view->profile->name; ?></b></td>
			<td width='15%'><b><?php echo COM_HP; ?></b>:</td>
			<td width='35%'><?php echo $view->profile->hp; ?></td>
		</tr>
		<tr>
			<td width='15%'><b><?php echo PROFILE_TYPE; ?></b>:</td>
			<td width='35%'><?php echo 'Boss'; ?></td>
			<td width='15%'><b>Missions</b>:</td>
			<td width='35%'><?php echo $view->profile->crimes; ?></td>
		</tr>

		<tr>
			<td width='15%'><b><?php echo COM_LEVEL; ?></b>:</td>
			<td width='35%'><?php echo $view->profile->level; ?></td>
			<td width='15%'><b><?php echo MONEY; ?></b>:</td>
			<td width='35%'>$<?php echo $view->profile->level * 100; ?> </td>
		</tr>
		<tr>
			<td width='15%'><b><?php echo COM_AGE; ?></b>:</td>
			<td width='35%'>325</td>
			<td width='15%'><b><?php echo LAST_ACTIVE; ?></b>:</td>
			<td width='35%'>1min</td>
		</tr>
		<tr>
			<td width='15%'><b><?php echo ucwords(COM_ONLINE); ?></b>:</td>
			<td width='35%'>Online</td>
			<td width='15%'><b><?php echo GANG; ?></b>:</td>
			<td width='35%'><?php echo $view->profile_class->formattedgang; ?>
            <?php
                echo  !empty($view->member_for) ? '(' . $view->member_for . ')' : '';
            ?>
            </td>
		</tr>
		<tr>
			<td width='15%'><b><?php echo COM_PRISON; ?></b>:</td>
			<td width='35%'><?php $c = new City($view->profile->city_id); echo $c->name; ?></td>
			<td width='15%'><b><?php echo COM_CELL; ?></b>:</td>
			<td width='35%'><?php $c = new House($view->profile->house); echo $c->name; ?></td>
		</tr>
		<tr>
			<td width='15%'><b><?php echo PROFILE_CURRENT_RATING; ?></b>:</td>
			<td width='35%' valign='middle'style="color:red;"> -<?php echo (int) (($view->profile->level * 35) / 8); ?></td>
			
			<td width='15%'>&nbsp;</td>
			<td width='35%'>&nbsp;</td>
		</tr>
		</table>

    <br>
    <br>
 <table width="100%">
<?php
if ($view->profile->currenthospital > time()):?>
<tr><td colspan="4" align='center' style="color: red">This King is in the hospital</td></tr>
<?php endif; ?>
</table>
</div><br>
<h1><?php echo COM_ACTIONS; ?></h1>
<div class="contentBox">
    <table width="100%"><tr>
            <td><a href="kingmaison.php">Attack</a></td>
            <td><a href="kinglogs.php">Logs</a></td>
    </tr>
    </table>
</div>

<h1><?php echo PROFILE_SIGNATURE; ?></h1>
<div class="contentBox">
		<div style='display:block; width:100%;overflow:hidden;'><?php echo $view->bbcode->parse_bbcode(wordwrap(Utility::ReplaceNewline(stripslashes($view->profile->profile)), 140, '<br>', '<br>')); ?></div>
                <?php

                        $rs = KingList::whoDefeatHim($view->profile->id);
                  if (count($rs) > 0):
                      echo '<br><p style="font-family:verdana;font-size:120%;text-align:center;"><b>My sworn enemies</b><p>';
                    echo '<table width="100%">';
                      foreach ($rs as $group) {
                          $user = UserFactory::getInstance()->getUser($group->element_1);
                          $user1 = UserFactory::getInstance()->getUser($group->element_2);
                          $user2 = UserFactory::getInstance()->getUser($group->element_3);

                          echo '<tr><td>' . date('d/m/Y  H:i', $group->time) . '</td><td>' . $user->formattedname . '</td><td>' . $user1->formattedname . '</td><td>' . $user2->formattedname . '</td><tr>';
                      }
                        echo '</table>';
                  endif;

                ?>
                
</div>