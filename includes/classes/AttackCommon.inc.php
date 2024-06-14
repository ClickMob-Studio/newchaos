<?php

class AttackCommon
{
    public function Validation(&$attackingUser, &$targetUser, $jointAttackers = [])
    {
        $inactive = time() - 259200;
        $isRiot = ((float) Variable::GetValue('riotStarted') > time());
        $isVirusEvent = Utility::IsEventRunning('virus');
        $isAdminEvent = Utility::IsEventRunning('attackadmin');
         if (Utility::GetPercent($attackingUser->energy, $attackingUser->GetMaxEnergy()) < 25) {
            if ($attackingUser->GetLovePotionTime() > time()) {
                // Active love potion, energy not needed
            } else {
                throw new SoftException(ATK_LESS_ENERGY);
            }
        } elseif ($attackingUser->IsInJail()) {
            throw new SoftException(ATK_USER_IN_SHOWER);
        } elseif ($attackingUser->IsInHospital()) {
            throw new SoftException(ATK_USER_IN_HOSPITAL);
        } elseif ($attackingUser->id == $targetUser->id) {
            throw new SoftException(ATK_CANT_YOURSELF);
        } elseif ($targetUser->IsAdmin() && !$isRiot && !$isVirusEvent && !$isAdminEvent) { // Comment for events
            throw new SoftException(ATK_CANT_PG);
        } // Comment for events
        elseif ($targetUser->IsAdmin() && !empty($jointAttackers)) { // Do not comment this for events
            throw new SoftException(ATK_CANT_PG);
        } // Do not comment this for events
        //elseif ($attackingUser->GetGang()->id == $targetUser->GetGang()->id && $attackingUser->GetGang()->id != 0) {
            //throw new SoftException(ATK_CANT_GANG_MEMBER);
        //}
        if ($targetUser->IsInJail()) {
            throw new SoftException(ATK_CANT_IN_SHOWER);
        } elseif ($targetUser->IsInHospital()) {
            throw new SoftException(ATK_CANT_IN_HOSPITAL);
        } elseif ($targetUser->IsProtectedByGuards()) {
            throw new SoftException(ATK_CANT_PROTECTED_BY_GUARD);
        } elseif ($targetUser->hp == 0) {
            throw new SoftException(ATK_CANT_UNCONSCIOUS);
        } elseif ($attackingUser->level >= 4 && $targetUser->level < 4 && $targetUser->lastactive > $inactive) {
            //throw new SoftException(ATK_CANT_LESS_LEVEL);
        }
        try {
            $attackingUser->CheckPrisons($targetUser);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
