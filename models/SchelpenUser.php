<?php
namespace Concrete\Package\CottageBooker\Models;

/**
 * Model for user who can book the cottage
 */
class SchelpenUser
{

    /**
     * Fetch credits for this user
     * @param $ui
     */
    public static function setupUser($ui)
    {
        $oDb = Loader::db();

        // Fetch general settings
        $sSql = "SELECT * FROM btCottageBooker WHERE userCreditsMax > 0"
            . "AND userCreditsAnnual > 0 LIMIT 1";
        $aConfig = $oDb->getRow($sSql);

        $iUserCredits = $aConfig['userCreditsAnnual'];
        $iCredits = ($iUserCredits > 0) ? intval($iUserCredits) : 42;

        $ui->setAttribute('cottage_booker_credits', $iCredits);
        $ui->setAttribute('cottage_booker_credits_last_update', date('Y-m-d'));
    }

}
