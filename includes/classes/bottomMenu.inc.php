<?php

class bottomMenu extends BaseObject
{
    public function __construct()
    {
    }
    public static function getAllLinks(){
        $result = DBi::$conn->query("SELECT * FROM `bottom_menu_links`");
        $links = [];
        while($row = mysqli_fetch_object($result)){
            $links[] = $row;
        }
        return $links;
    }
    public function getDefault() :array {
        //get default links from bottom_menu_links table
        $defaultLinks = DBi::$conn->query("SELECT * FROM bottom_menu_links WHERE firstlink = '1' ORDER BY id");
        $defaultLinksArray = [];
        while ($defaultLink = mysqli_fetch_assoc($defaultLinks)) {
            $defaultLinksArray[] = $defaultLink;
        }
        return $defaultLinksArray;
}
    public function getBottomMenu($id): array
    {
        $fetch = DBi::$conn->query('SELECT * FROM `bottom_menu` WHERE `userid` = '.$id);

       if(mysqli_num_rows($fetch) == 0){
         //if no links are set for user, return default links
            return $this->getDefault();
        }else{
           //if there is links retreive them and retrieve the details from bottom_menu_links
            $links = [];
            while ($link = mysqli_fetch_assoc($fetch)) {
                $linkDetails = DBi::$conn->query('SELECT * FROM `bottom_menu_links` WHERE `id` = '.$link['link']);
                $linkDetails = mysqli_fetch_assoc($linkDetails);
                $links[] = $linkDetails;
            }
            return $links;
       }
    }

    protected function GetIdentifierFieldName()
    {
        // TODO: Implement GetIdentifierFieldName() method.
    }

    protected function GetClassName()
    {
        // TODO: Implement GetClassName() method.
    }

    protected static function GetDataTable()
    {
        // TODO: Implement GetDataTable() method.
    }

    protected static function GetDataTableFields()
    {
        // TODO: Implement GetDataTableFields() method.
    }
}