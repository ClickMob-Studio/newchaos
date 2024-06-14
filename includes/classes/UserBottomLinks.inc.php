<?php

final class UserBottomLinks
{
    public static $idField = 'id'; //id field
    public static $dataTable = 'bottomlinks'; // table implemented
    public static $iconDir = 'images/buttons/'; //menu icon directory

    /**
     * Funtions return the left links for given user.
     *
     * @throws SQLException
     * @throws SoftException
     *
     * @return array
     */
    public static function GetUserBottomLinks(User $user_class)
    {
        if (!is_numeric($user_class->id)) {
            throw new SoftException('User id is not valid.');
        }

        $links = [];
        if ($user_class->IsInJail() || User::sCountAllInJail()) {
            $links[] = (object) [
                'name' => 'LINK_SHOWERS_JAIL',
                'link' => 'jail.php',
                'newwindow' => 'N',
                'highlight' => true,
            ];
        }
        if ($user_class->IsInHospital()) {
            $links[] = (object) [
                'name' => 'LINK_HOSPITAL_HOSPITAL',
                'link' => 'hospital.php',
                'newwindow' => 'N',
                'highlight' => false,
            ];
        }

        $ll_result = DBi::$conn->query('SELECT name, link, image, new_window, jail, hospital, IFNULL((SELECT link_order FROM `user_leftlinks` WHERE leftlinks.id = user_leftlinks.link_id AND user_id = ' . $user_class->id . ' ),9999) as link_order FROM `leftlinks` WHERE mandt = "Y" OR id IN ( SELECT link_id FROM user_leftlinks WHERE user_id = ' . $user_class->id . ' ORDER BY link_order) ORDER BY link_order, id');

        while ($ll_row = mysqli_fetch_object($ll_result)) {
            if ((!$user_class->IsInJail() || $user_class->IsInJail() && $ll_row->jail === 'Y') && (!$user_class->IsInHospital() || $user_class->IsInHospital() && $ll_row->hospital === 'Y')) {
                if ($ll_row->link == '<!_-checkgang-_!>') {
                    $ll_row->link = ($user_class->IsInAGang() === false) ? 'creategang.php' : 'gang.php';
                }
                $links[] = $ll_row;
            }
        }

//        $links[] = (object) [
//            'name' => 'LINK_DISCORD',
//            'link' => 'https://discord.gg/reAZfpgz9t',
//            'newwindow' => 'Y',
//            'highlight' => false,
//        ];

        return $links;
    }

    public static function GetUserLeftLinks1(User $user_class)
    {
        if (!is_numeric($user_class->id)) {
            throw new SoftException('User id is not valid.');
        }

        $links = [];

        $ll_result = DBi::$conn->query('SELECT  name, link, image, new_window,    
                            IFNULL( (SELECT link_order FROM `user_leftlinks1` WHERE leftlinks.id = user_leftlinks1.link_id AND user_id = ' . $user_class->id . ' ),9999) as link_order FROM `leftlinks` WHERE
                             id IN ( SELECT link_id FROM user_leftlinks1 WHERE user_id = ' . $user_class->id . ' ORDER BY link_order) ORDER BY link_order, id');

        while ($ll_row = mysqli_fetch_object($ll_result)) { //loop through return result set
            if ($ll_row->link == '<!_-checkgang-_!>') {
                $ll_row->link = ($user_class->IsInAGang() === false) ? 'creategang.php' : 'gang.php';
            }
            $links[] = $ll_row;
        }

        return $links;
    }

    /**
     * Funtions return all left links with previous settings for given user.
     *
     * @throws SQLException
     * @throws SoftException
     *
     * @return array
     */
    public static function GetAllLeftLinks(User $user_class)
    {
        if (!is_numeric($user_class->id)) {
            throw new SoftException(USER_INVALID_ID);
        }

        $links = [];

        $ll_result = DBi::$conn->query('SELECT id, name, image, mandt, link_id, IFNULL(link_order,9999) as link_order 
            FROM `leftlinks`
            LEFT JOIN `user_leftlinks`
            ON leftlinks.id = user_leftlinks.link_id
            AND user_leftlinks.user_id = ' . $user_class->id . '
            ORDER BY link_order, id');

        while ($ll_row = mysqli_fetch_object($ll_result)) {
            $links[] = $ll_row;
        }

        return $links;
    }

    public static function GetAllLeftLinks1(User $user_class)
    {
        if (!is_numeric($user_class->id)) {
            throw new SoftException(USER_INVALID_ID);
        }

        $links = [];

        $ll_result = DBi::$conn->query('SELECT id, name, image, mandt, link_id, IFNULL(link_order,9999) as link_order FROM `leftlinks` LEFT JOIN `user_leftlinks1` ON leftlinks.id = user_leftlinks1.link_id AND user_leftlinks1.user_id = ' . $user_class->id . ' ORDER BY link_order, id');

        while ($ll_row = mysqli_fetch_object($ll_result)) { //loop through return result set
            $links[] = $ll_row;
        }

        return $links;
    }

    public function SaveUserPreferences(User $user_class, $links)
    {
        DBi::$conn->query('DELETE FROM `user_leftlinks` WHERE user_id = ' . $user_class->id); //delete all existing record for current user

        $coma = '';
        $sql = 'INSERT INTO `user_leftlinks` (`user_id`, `link_id`, `link_order`) VALUES ';

        if (!is_array($links)) {
            return null;
        } //No link is checked

        $i = 1;

        foreach ($links as $link_id) {
            $sql .= $coma . "('" . $user_class->id . "', '$link_id', '$i')";
            ++$i;
            $coma = ', ';
        }

        return DBi::$conn->query($sql);
    }
}
