<?php

class SchelpenUser {
	
	public function __construct(){
		
	}
	
	public function setupUser($ui){
		$oDb = Loader::db();
        
        // Fetch general settings
        $sSql = "SELECT * FROM btCottageBooker WHERE userCreditsMax > 0 AND userCreditsAnnual > 0 LIMIT 1";
        $aConfig = $oDb->getRow($sSql);
		
		$iCredits = ($aConfig['userCreditsAnnual'] > 0) ? intval($aConfig['userCreditsAnnual']) : 42;
	
		$ui->setAttribute('cottage_booker_credits', $iCredits);
		$ui->setAttribute('cottage_booker_credits_last_update', date('Y-m-d'));
	}
	
}
