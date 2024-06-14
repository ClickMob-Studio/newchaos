<?php

class Header
{
    /**
     * Handle replacing values during output buffering.
     *
     * @return string
     */
    public static function outerBufferCallback(string $buffer) : string
    {
        global $effectBar, $hateBar;

        $user_class = UserFactory::getInstance()->getUser($_SESSION['id']);
        $user2 = SUserFactory::getInstance()->getUser($_SESSION['id']);
        $hospital = '[' . User::sCountAllInHospital() . ']';

        $jail = '[' . User::sCountAllInJail() . ']';
        if ($user_class->mods > 0) {
            $unreadMailsMod = $user_class->CountUnreadPmailsMod();

            $unreadMails = $user_class->CountUnreadPmails();

            $mail =
                $unreadMails > 0
                    ? '<span  class="menu-highlight">' .
                    LINK_MAILBOX_TEXT .
                    ' [' .
                    $unreadMails .
                    ']'
                    : 'Mailbox [' . $unreadMails . ']</span>';

            $mailMod =
                $unreadMailsMod > 0
                    ? '<span class="menu-highlight">Mailbox Mod  [' .
                    $unreadMailsMod .
                    ']</span>'
                    : 'Mailbox Mod [' . $unreadMailsMod . ']';
        } else {
            $unreadMails = $user_class->CountUnreadPmails();

            $mail =
                $unreadMails > 0
                    ? '<span class="menu-highlight">' .
                    LINK_MAILBOX_TEXT .
                    ' [' .
                    $unreadMails .
                    ']</span>'
                    : 'Mailbox [' . $unreadMails . ']';
        }

        $unreadEvents = $user_class->CountUnreadEvents();

        $events =
            $unreadEvents > 0
                ? '<span  class="menu-highlight">' .
                LINK_EVENTS_TEXT .
                ' [' .
                $unreadEvents .
                ']</span>'
                : 'Events [' . $unreadEvents . ']';

        $color = '';

        if ($user_class->GetSupportStatus() !== null) {
            $view = 'sopened';

            $requestCount = SupportThread::CountAllOpenedForLevel(
                $user_class->GetSupportStatus()->GetLevel()
            );
        } else {
            $view = 'opened';

            $requestCount = SupportThread::CountAllOpenedForAuthor($user_class->id);
        }

        if ($requestCount > 0) {
            $color = 'class="menu-highlight"';
        }

        $supportMenuEntry =
            '<a href="support.php?view=' .
            $view .
            '"><span ' .
            $color .
            '>' .
            LINK_SUPPORT_CENTER_TEXT .
            ' [' .
            $requestCount .
            ']</span></a>';

        $out = $buffer;

        $out = str_replace(
            '<!_-money-_!>',
            number_format($user_class->money),
            $out
        );
        $out = str_replace(
            '<!_-daily_tasks-_!>',
            self::getDailyTaskBars(),
            $out
        );
        $out = str_replace(
            '<!_-responsiveMoney-_!>',
            Utility::FormatMoney($user_class->money),
            $out
        );

        $out = str_replace(
            '<!_-formhp-_!>',
            $user_class->hp.' / '.$user_class->GetMaxHP(),
            $out
        );

        $out = str_replace(
            '<!_-hpperc-_!>',
            $user_class->hp > 0 ? floor($user_class->hp / $user_class->GetMaxHP() * 100) : 0,
            $out
        );

        $out = str_replace(
            '<!_-formenergy-_!>',
            $user_class->energy.' / '.$user_class->getMaxEnergy(),
            $out
        );

        $out = str_replace(
            '<!_-energyperc-_!>',
            $user_class->energy > 0 ? floor($user_class->energy / $user_class->GetMaxEnergy() * 100) : 0,
            $out
        );

        $out = str_replace(
            '<!_-formawake-_!>',
            $user_class->awake.' / '.$user_class->GetMaxAwake(),
            $out
        );
        $out = str_replace(
            '<!_-awakeperc-_!>',

            $user_class->awake > 0 ? floor($user_class->awake / $user_class->GetMaxAwake() * 100) : 0
            ,
            $out
        );

        $out = str_replace(
            '<!_-formnerve-_!>',
            $user_class->nerve.' / '.$user_class->GetMaxNerve(),
            $out
        );

        $out = str_replace(
            '<!_-nerveperc-_!>',
            $user_class->nerve > 0 ? floor($user_class->nerve / $user_class->GetMaxNerve() * 100) : 0,
            $out
        );

        $out = str_replace('<!_-formxp-_!>',
            number_format($user_class->exp, 0) .' / '. number_format($user_class->maxexp, 0), $out);

        $out = str_replace(
            '<!_-xpperc-_!>',
            $user_class->exppercent ,

            $out
        );

        $out = str_replace(
            '<!_-points-_!>',
            number_format($user_class->points),
            $out
        );

        $out = str_replace('<!_-level-_!>', $user_class->level, $out);

        $out = str_replace('<!_-hospital-_!>', $hospital, $out);

        $out = str_replace('<!_-jail-_!>', $jail, $out);

        if ($user_class->mods > 0) {
            $out = str_replace('<!_-pms_mail-_!>', $mailMod, $out);
        }

        $out = str_replace('<!_-mail-_!>', $mail, $out);

        $out = str_replace('<!_-mailClass-_!>', $unreadMails > 0 ? 'highlight' : null, $out);

        $out = str_replace('<!_-events-_!>', $events, $out);

        $out = str_replace('<!_-eventsClass-_!>', $unreadEvents > 0 ? 'highlight' : null, $out);

        $out = str_replace('<!_-cityname-_!>', $user_class->GetCity()->name, $out);

        $out = str_replace('<!_-awakepercvalue-!>', '&nbsp', $out);

        $out = str_replace('<!_-nervepercvalue-_!>', '&nbsp', $out);

        $out = str_replace('<!_-energypercvalue-_!>', '&nbsp', $out);

        $out = str_replace('<!_-hppercvalue-!>', '&nbsp', $out);

        $out = str_replace('<!_-xppercvalue-_!>', '&nbsp', $out);

        $out = str_replace('<!_-support-_!>', $supportMenuEntry, $out);

        $out = str_replace('<!_-effects-_!>', $effectBar, $out);

        $out = str_replace('<!_-hatebar-_!>', $hateBar, $out);

        // Replace gray scale on body
        $out = str_replace('<!_-grayscale-_!>', ($user2->grayscale ? ' class="grayscale"' : ''), $out);

        $out = str_replace(
            '<!_-downtown-_!>_>',
            '<a href="downtown.php"><img width="16" height="16" src="images/buttons/world.png"> <span ' .
            ($user_class->searchdowntown != 0 ? 'class="highlight"' : '') .
            ';">' .
            LINK_SEARCH_THE_PRISON_YARD .
            '</span></a>',
            $out
        );

        $out = str_replace(
            '<!_-downtown-_!>',
            '<a href="downtown.php"><span ' .
            ($user_class->searchdowntown != 0 ? 'class="highlight"' : '') .
            ';">' .
            LINK_SEARCH_THE_PRISON_YARD .
            '</span> <img width="16" height="16" src="images/buttons/world.png"></a>',
            $out
        );

        return $out;
    }
    public static function getDailyTaskBars()
    {
        $tasks = '';
        $user_class = UserFactory::getInstance()->getUser($_SESSION['id']);
        $todaysTasks = DailyTasks::getTodaysDailyTasks();
        $dailyProgress = DailyTasks::getUserTaskProgress($user_class);
        if ($todaysTasks !== null) {
            foreach ($todaysTasks as $index => $task) {
                $percentage = floor(100 / DailyTasks::getAmountRequired((int)$task, $user_class) * $dailyProgress[$task]['progress']);
                $message = DailyTasks::getMessage((int)$task, $user_class);
                if ($percentage > 100 || $dailyProgress[$task]['complete']) {
                    $percentage = 100;
                }
                $progressText = $dailyProgress[$task]['progress'] . ' / ' . DailyTasks::getAmountRequired((int)$task, $user_class);
                $dDriveMessage = $message . '<br><strong>Progress:</strong> ' . $progressText;
                $tasks .= '<li
                    class="' . ($percentage >= 100 ? 'complete' : '') . '"
                    onmouseover="ddrivetip(\'' . $dDriveMessage . '\', \'\', 180);"
                    onmouseout="hideddrivetip()"
                >
                    <a href="daily_tasks.php">
                        <span>' . $message . '</span>
                        <span>' . ($percentage >= 100 ? '<span class="fas fa-check"></span>' : $progressText) . '</span>
                    </a>
                </li>';
            }
            $tasks = '<section class="tasks">
                        <label><a href="daily_tasks.php">T A S K S</a></label>
                            <div class="tasks-container">
                                    <ul>
                                        ' . $tasks . '
                                    </ul>
                            </div>
                    </section>';
        } else {
            $tasks = false;
        }
        return $tasks;
    }
}
