<?php

final class Fertilizer
{
    private $objUser;

    public function __construct($objUser)
    {
        $this->objUser = $objUser;
    }

    public function GetOwnedFertilizer()
    {
        $strSQL = "SELECT * FROM `fertilizer` WHERE `owner`='" . $this->objUser->id . "'";
        $result = DBi::$conn->query($strSQL);
        $arrResult = mysqli_fetch_array($result);

        return $arrResult;
    }

    public function GetFertilizer($intFertilizerId)
    {
        $strSQL = "SELECT * FROM `fertilizer` WHERE `fertilizer_id`='" . $intFertilizerId . "'";
        $result = DBi::$conn->query($strSQL);
        $arrResult = mysqli_fetch_array($result);

        return $arrResult;
    }

    public function AddFertilizer($arrRecord, $intStock, $intPrice)
    {
        $message = '';

        $this->objUser->RemoveFromAttribute('fertilizer', $intStock);

        if (count($arrRecord) > 1) {
            $strSQL = 'UPDATE  `fertilizer` SET `price`=' . $intPrice . ', `stock`=`stock`+' . $intStock . " where `fertilizer_id`='" . $arrRecord['fertilizer_id'] . "'";
            $message = FERTILIZERMARKET_FERTILIZER_UPDATED;
        } else {
            $strSQL = 'INSERT INTO  `fertilizer` SET `price`=' . $intPrice . ', `stock`=' . $intStock . ",`owner`='" . $this->objUser->id . "'";
            $message = FERTILIZERMARKET_ADDED_LAND;
        }

        $result = DBi::$conn->query($strSQL);
        throw new SuccessResult($message);
    }

    public function BuyFertilizer($arrRecord, $intQty)
    {
        $intAmount = $arrRecord['price'] * $intQty;

        //if buyer has less money
        if ($this->objUser->getAttribute('money') < $intAmount) {
            throw new SoftException(FERTILIZERMARKET_INSUFFICIENT_MONEY);
        }
        //update buyer money
        $this->objUser->RemoveFromAttribute('money', $intAmount);

        //update stock of seller
        $sellerUser = UserFactory::getInstance()->getUser($arrRecord['owner']);

        //Add to bank account 50% amount
        $sellerUser->DepositMoney($intAmount / 2);

        //Update remaining 50% amount to seller a/c
        $sellerUser->AddtoAttribute('money', ($intAmount / 2));

        $strSQL = 'UPDATE  `fertilizer` SET `stock`=`stock`-' . $intQty . " WHERE `fertilizer_id`='" . $arrRecord['fertilizer_id'] . "'";
        $result = DBi::$conn->query($strSQL);

        //update buyer fertilizer
        $this->objUser->AddtoAttribute('fertilizer', $intQty);
        $strSeller = $this->objUser->getAttribute('username');

        throw new SuccessResult(sprintf(FERTILIZERMARKET_PURCHASED, $strSeller));
    }

    public function RemoveFertilizer($intId, $intQty)
    {
        $strSQL = 'UPDATE  `fertilizer` SET `stock`=`stock`-' . $intQty . " WHERE `fertilizer_id`='" . $intId . "'";
        $result = DBi::$conn->query($strSQL);
        $this->objUser->AddtoAttribute('fertilizer', $intQty);
        throw new SuccessResult(FERTILIZERMARKET_REMOVED);
    }

    public function UpdatePrice($intId, $intPrice)
    {
        $strSQL = 'UPDATE  `fertilizer` SET `price`=' . $intPrice . " WHERE `fertilizer_id`='" . $intId . "'";
        $result = DBi::$conn->query($strSQL);
        $this->objUser->AddtoAttribute('fertilizer', $intQty);
        throw new SuccessResult(FERTILIZERMARKET_PRICE_UPDATED);
    }

    public function UpdateFertilizer($intId, $intFertilizer)
    {
        $strSQL = 'UPDATE  `fertilizer` SET `stock`=`stock`+' . $intFertilizer . " WHERE `fertilizer_id`='" . $intId . "'";
        $result = DBi::$conn->query($strSQL);
        $this->objUser->RemoveFromAttribute('fertilizer', $intFertilizer);
        throw new SuccessResult(FERTILIZERMARKET_FERTILIZER_UPDATED);
    }
}
