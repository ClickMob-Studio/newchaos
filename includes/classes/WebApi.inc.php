<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class WebApi
{
    public static function TopRefill()
    {
        $string = 'SELECT RefillReward from best';
        $result =DBi::$conn->query($string);
        $line = mysqli_fetch_array($result);

        return $line['RefillReward'];
    }

    public static function ImageGenerationRefill()
    {
        $id = WebApi::TopRefill();
        if ($id == 0) {
            return 'images/top-information-thumb.jpg';
        }

        $user = UserFactory::getInstance()->getUser($id);
        if ($user->avatar == '') {
            return 'images/top-information-thumb.jpg';
        }

        return $user->avatar;
    }

    public static function Tip($Msg)
    {
        return " onmouseover=\"ddrivetip('" . $Msg . "', '#96926f', 140)\" ; onmouseout=\"hideddrivetip()\"";
    }

    public static function ShowHospitalDetails($id)
    {
        $UH = new UserHospital($id);

        return WebApi::Tip(sprintf(HOSP_TIP, $UH->getCause(), $UH->getAttacker(), $UH->getTime()));
    }

    public static function Question($question, $True, $False)
    {
        //echo "<script>";
        //echo "function ask(){";
        echo "var answer = confirm ('" . $question . "');";
        echo ' if (answer) {';
        echo $True;
        echo '} else {';
        echo $False;
        echo  '}';
    }
}
