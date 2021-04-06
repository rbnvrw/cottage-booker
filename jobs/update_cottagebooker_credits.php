<?php 
/**
*
* Credits elk jaar bijwerken
*/

defined('C5_EXECUTE') or die("Access Denied.");
class UpdateCottagebookerCredits extends Job {
	
	public function getJobName() {
		return t("Update Cottage Booker credits.");
	}
	
	public function getJobDescription() {
		return t("Periodically update the Cottage Booker credits.");
	}
	
	public function run() {
		// Get all users
		Loader::model('user_list'); 
		$userList = new UserList();
		$users = $userList->get();
		
		$iCount = 0;
		
		$iOneJan = mktime(0, 0, 0, 1, 1);
		
		foreach($users as $userInfo){
			$lastUpdate = $userInfo->getUserCottageBookerCreditsLastUpdate();
			$iLastUpdate = strtotime($lastUpdate);
			
			// Each year, add 40 credits to a max of 120 credits
			if(($iOneJan - $iLastUpdate) > 31536000){
			
				// Fetch general settings
				$sSql = "SELECT * FROM btCottageBooker WHERE userCreditsMax > 0 AND userCreditsAnnual > 0 LIMIT 1";
				$aConfig = $oDb->getRow($sSql);
				
				$iCredits = ($aConfig['userCreditsAnnual'] > 0) ? intval($aConfig['userCreditsAnnual']) : 42;
				$iCreditsMax = ($aConfig['userCreditsMax'] > 0) ? intval($aConfig['userCreditsMax']) : 126;
			
				$iCreditsNew = $userInfo->getUserCottageBookerCredits();
				$iCreditsNew = $iCreditsNew + $iCredits;
				if($iCreditsNew > $iCreditsMax){ $iCreditsNew = $iCreditsMax; }
				
				$userInfo->setAttribute('cottage_booker_credits', $iCreditsNew);
				$userInfo->setAttribute('cottage_booker_credits_last_update', date('Y-m-d'));
				
				$iCount++;
			}
		}

		return t('Bijwerken geslaagd, %s gebruikers hebben nieuwe credits gekregen.', $iCount);		
	}

}