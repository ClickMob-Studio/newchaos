<?php
include('header.php');
?>

<div class='box_top'>Terms Of Servvice</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        require_once 'includes/functions.php';

        start_session_guarded();

        $csrf = md5(uniqid(rand(), true));
        $_SESSION['csrf'] = $csrf;
        ?>

        <div class="contenthead floaty">
            <table id="newtables" style="width:100%;">
                <tr>
                    <td class="contentspacer"></td>
                </tr>
                <tr>
                    <td>
                        <div class="contentcontent" align="left">
                            <ul class="info-list">
                                <li>Each user is permitted to possess only one account. Any attempt to operate multiple
                                    accounts will result in the suspension of all associated accounts.</li>
                                <li>The creation of additional accounts for the purpose of collecting daily rewards or
                                    engaging in any form of account sharing is strictly prohibited.</li>
                                <li>Users are strictly forbidden from accessing or allowing access to any account other
                                    than their own, even upon request from the account holder. Any such requests should
                                    be reported to the Help Desk immediately.</li>
                                <li>Accounts originating from the same IP address are prohibited from engaging in
                                    financial transactions, joining the same gang, or participating in any form of
                                    exchange. The involvement of intermediary players in facilitating such transactions
                                    will result in their banning as well.</li>
                                <li>Any form of serious verbal abuse, including instances of racism, directed towards
                                    other players is strictly prohibited. The determination of what constitutes verbal
                                    abuse shall be at the discretion of the administrative team.</li>
                                <li>Spamming or flooding any chat platform with repetitive or unnecessary content,
                                    including excessive use of capital letters, is not allowed.</li>
                                <li>Solicitation of personal or contact information is strictly forbidden, including the
                                    sharing of banking details or any form of private communication.</li>
                                <li>The use of third-party software or plugins designed to enhance gameplay, such as
                                    macros, auto-clickers, or reloaders, is expressly forbidden. Violators may face
                                    account deletion or suspension.</li>
                                <li>Exploitation of any bugs or vulnerabilities within the game will result in permanent
                                    account suspension.</li>
                                <li>Any form of external website advertising is strictly prohibited and may result in
                                    permanent banning.</li>
                                <li>Scamming other players, whether within the game or through external means, is
                                    strictly forbidden. Utilize in-game marketplaces for safe transactions.</li>
                                <li>The use of racist language anywhere on the platform is prohibited.</li>
                                <li>Users are expected to respect administrative decisions at all times. Complaints or
                                    issues should be addressed through the Help Desk.</li>
                                <li>The sale of accounts is strictly prohibited within Chaos City.</li>
                            </ul>

                            <p>Please note that these rules are subject to change and interpretation by the
                                administrative team at any time.</p>

                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <?php
        include('footer.php');
        ?>