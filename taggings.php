<?php
// Main include File 
include("UserTag.inc");
include("classes.php");
$tag = new EntityTag();
$user = mysql_query("SELECT * FROM grpgusers WHERE id='" . $_GET['uid'] . "'");
$GangTag = ($user->GangTag != "") ? "[$user->GangTag] " : "";
$GangLeader = (isset($user->GangLeader) && $user->GangLeader == 1);
$DisplayName = $user->Name;
$Colour = $tag->LowLife;
$Font = $tag->Verdana;

if($user->Banned != 0)
{
switch($user->Banned)
{
case 1: $Colour = $tag->Frozen;
break;
case 2: $Colour = $tag->Banned;
break;
}
}
else
{
switch($user->EliteStatus)
{
case 0: $Colour = $tag->Thug;
break;
case 1: $Colour = $tag->Hitman;
break;
case 2: $Colour = $tag->Boss;
break;
}

switch(intval($user->ModStatus))
{
case 'FM': $Font = $tag->VerdanaBold;
$Colour = $tag->FM;
break;
case 'GM': $Font = $tag->VerdanaBold;
$Colour = $tag->GM;
break;
case 'AD': $Font = $tag->VerdanaBold;
$Colour = $tag->Admin;
break;
}
}

if(/* User has coloured name, need an array of hex colours then can call the following */)
{
$tag->WriteFirstSegment(($GangLeader ? $tag->VerdanaBold : $tag->Verdana), $GangTag, $tag->LowLife,
 $Font, $DisplayName, $Colours, true);
}
else
$tag->WriteFirstSegment(($GangLeader ? $tag->VerdanaBold : $tag->Verdana), $GangTag, $tag->LowLife,
 $Font, $DisplayName, $Colour);

// Level
$Level = sprintf("LVL: %6s", strval($user->Level));
$tag->WriteSecondSegment($tag->ArialBold, $Level, $tag->white);

// HP
$HP = percentage(intval($user->Health), intval($user->MaxHealth));
$tag->WriteThirdSegment($tag->ArialBold, "HP: ", $tag->white,
 $tag->ArialBold, $HP, ((intval($HP) < 75) ? $tag->red : $tag->green));

// City
$tag->WriteFourthSegment($tag->ArialBold, $user->Cityname, $tag->white);

// Online 
$tag->WriteOnlineIndicator((time() - intval($user['LAST_ACTIVE'])) < $setting->seconds_before_offline);

// Or Unknown user
// $tag->WriteAllSegments($tag->ArialBold, 'Unknown User', $tag->white);
}
elseif(isset($_GET['gid']))
{
//-----------------------------------------------------------------------   
// A Gang
//-----------------------------------------------------------------------
$gang = GetGangTagDetailsFromDB($_GET['gid']);

// [Gang tag] Gang Name 
$tag->WriteFirstSegment($tag->ArialBold, "[".$gang->Tag."]", $tag->white,
 $tag->ArialBold, $gang->Name, $tag->white);

// Level
$Level = sprintf("LVL: %6s", strval($gang->Level));
$tag->WriteSecondSegment($tag->ArialBold, $Level, $tag->white);

// Members
$Members = sprintf("MEM: %5s", strval($gang->Members));
$tag->WriteThirdSegment($tag->ArialBold, $Members, $tag->white,
 $tag->ArialBold, "", $tag->white);

// HQ City
if(isset($gang['CITY_NAME']))
$tag->WriteFourthSegment($tag->ArialBold, "HQ: ".$gang->BaseCity, $tag->white, false);

// Or Unknown gang
$tag->WriteAllSegments($tag->ArialBold, 'Unknown gang', $tag->white);
}

// Outputs the tag as an image 
$tag->OutputTag();

// Called via the below:
// <a href=\"userprofile.php?id=1\"><img border='0' src=\"CreateTag.php?uid=1\"></a>
}
?>