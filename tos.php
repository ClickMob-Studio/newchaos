<?php
include('header.php');
session_start();

$csrf = md5(uniqid(rand(), true));
$_SESSION['csrf'] = $csrf;
?>

<div class="contenthead floaty">
    <span style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;"><h4>Game Rules & Regulations - Mafia Lords</h4></span>
    <table id="newtables" style="width:100%;">
        <tr><td class="contentspacer"></td></tr>
        <tr>
            <td>
                <div class="contentcontent" align="left">
                    <ul class="info-list">
                        <li>You are only allowed to have ONE account !!! If anyone plays 2 or more accounts, this will result in a ban of all accounts involved.</li>
                        <li>Having more than 1 account or getting someone else to create an account for the sole purpose of sending you their dailies is strictly forbidden.</li>
                        <li>You cannot enter any other accounts, even if requested by the account owner. Additionally, you may not have non-players enter an account for any reason, even if requested by the account owner. If anyone asks you to do it, report it on the Help Desk.</li>
                        <li>Accounts in the same location (IP) CANNOT: Send money, points, or items between those accounts, join the same gang, exchange anything on the market, 50/50, or any other things, using a middle man player to exchange (middle man player will also be banned).</li>
                        <li>Using serious verbal abuse (including racism) towards other players (whether through mail/forum or on a gangster's profile) is strictly forbidden. The definition of "verbal abuse" will be defined by the members of the staff not the players.</li>
                        <li>No flooding/spamming anywhere (it means continually filling the screen with repetitive text, whether it be advertising or plainly abusing three or more lines of chat causing a disruptive flow of unnecessary material). Massive use of caps can also be considered as spamming.</li>
                        <li>Asking for personal or contact information of any kind is strictly prohibited. Please do not post personal contact information on any part of Mafia Lords. This includes important bank information and/or contact details.</li>
                        <li>Using game-enhancing programs such as any form of Macros plugins, any form of auto-clicker programs, and any form of reloader plugins is forbidden. Players caught or suspected of the use of one or more of these banned programs, plugins, or techniques are subject to banning, or account deletion at the discretion of the game admin.</li>
                        <li>Exploitation of any bugs or holes in the game will be punished with a permanent ban and deletion of the account.</li>
                        <li>Any advertising of other websites is not tolerated and will result in a permanent ban.</li>
                        <li>Scamming other players in-game or through an external tool (website, instant messaging, etc.) is FORBIDDEN! Use the in-game markets to protect yourself from scamming. All scammers that are reported and proven to have scammed will be banned.</li>
                        <li>The use of racism anywhere on this site is prohibited.</li>
                        
                        <li>Always respect Admin decisions. If you have any complaints just open a ticket on the Help Desk.</li>
                        <li>Accounts Sales are forbidden here at Mafia Lords.</li>
                    </ul>
                    <p>These rules are subject to change and staff interpretation at any time.</p>
                </div>
            </td>
        </tr>
    </table>
</div>

<?php
include('footer.php');
?>
