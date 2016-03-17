<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

class CottageBookerBlockController extends BlockController
{

    protected $btTable = "btCottageBooker";
    protected $btInterfaceWidth = "350";
    protected $btInterfaceHeight = "300";
    /**
     * This has to be on otherwise duplicate()
     * kills the reservations.
     */
    protected $btIncludeAll = true;
    protected $btWrapperClass = 'ccm-ui';

    public function getBlockTypeName()
    {
        return t('Cottage Booker');
    }

    public function getBlockTypeDescription()
    {
        return t('A calendar interface to the booking system.');
    }

    public function view()
    {
        $this->_initUser();
        $this->_setAdminPageLink();
    }


    public function formatRequiredFieldsMessage($aRequiredFields)
    {
        $sMessage = '<p>' . t('De volgende velden zijn niet ingevuld:') . '</p>';
        $sMessage .= '<ul>';
        foreach ($aRequiredFields as $sName => $sValue) {
            if (empty($sValue)) {
                $sMessage .= '<li>' . $sName . '</li>';
            }
        }
        $sMessage .= '</ul>';
        return $sMessage;
    }

    public function actionNew()
    {
        $oUser = new user();
        $uId = $oUser->getUserID();

        $oResponse = new stdClass;

        $sStart = $this->get('start');
        $sEnd = $this->get('end');
        $iPersons = $this->get('persons');

        if (!empty($sStart) && !empty($sEnd) && !empty($iPersons)) {
            // Start and end in the right order?
            if (strtotime($sStart) > strtotime($sEnd)) {
                $sTemp = $sStart;
                $sStart = $sEnd;
                $sEnd = $sTemp;
            }
            $aResponse = $this->insertBooking($uId, $sStart, $sEnd, $this->get('notes'), $iPersons);
            $oResponse->message = $aResponse['message'];
            $oResponse->status = $aResponse['status'];
        } else {
            $oResponse->message = t("Er is iets fout gegaan bij het reserveren. Probeer het later nog eens.") . $this->formatRequiredFieldsMessage(
                array(
                    t('Begindatum') => $sStart,
                    t('Einddatum') => $sEnd,
                    t('Aantal personen') => $iPersons,
                )
            );

            $oResponse->status = 'error';
        }

        $oResponse->credits = $this->getUserCottageBookerCredits($uId);

        $js = Loader::helper('json');
        print $js->encode($oResponse);
        exit;
    }

    public function actionUpdate()
    {
        $oUser = new user();
        $uId = $oUser->getUserID();

        $oResponse = new stdClass;

        $sStart = $this->get('start');
        $sEnd = $this->get('end');
        $iId = intval($this->get('id'));
        $iPersons = $this->get('persons');

        if (!empty($sStart) && !empty($sEnd) && !empty($iPersons) && !empty($iId)) {
            // Start and end in right order?
            if (strtotime($sStart) > strtotime($sEnd)) {
                $sTemp = $sStart;
                $sStart = $sEnd;
                $sEnd = $sTemp;
            }
            $aResponse = $this->updateBooking($iId, $uId, $sStart, $sEnd, $this->get('notes'), $iPersons);
            $oResponse->message = $aResponse['message'];
            $oResponse->status = $aResponse['status'];
        } else {
            $oResponse->message = t("Er is iets fout gegaan bij het bewerken van de reservering. Probeer het nog eens.") . $this->formatRequiredFieldsMessage(
                array(
                    t('Begindatum') => $sStart,
                    t('Einddatum') => $sEnd,
                    t('Aantal personen') => $iPersons,
                )
            );
            $oResponse->status = 'error';
        }

        $oResponse->credits = $this->getUserCottageBookerCredits($uId);

        $js = Loader::helper('json');
        print $js->encode($oResponse);
        exit;
    }

    public function actionDelete()
    {
        $oUser = new user();
        $uId = $oUser->getUserID();

        $oResponse = new stdClass;

        $iId = intval($this->get('id'));

        if (!empty($iId)) {
            $aResponse = $this->deleteBooking($iId, $uId);
            $oResponse->message = $aResponse['message'];
            $oResponse->status = $aResponse['status'];
        } else {
            $oResponse->message = t("Er is iets fout gegaan bij het annuleren van de reservering. Probeer het nog eens.");
            $oResponse->status = 'error';
        }

        $oResponse->credits = $this->getUserCottageBookerCredits($uId);

        $js = Loader::helper('json');
        print $js->encode($oResponse);
        exit;
    }

    public function actionFetchall()
    {
        $oUser = new user();
        $uId = $oUser->getUserID();

        $aEvents = $this->fetchAll();
        $aResponse = array();

        foreach ($aEvents as $iKey => $aEvent) {
            $aResponse[$iKey]['start'] = $aEvent['start'];
            $aResponse[$iKey]['end'] = $aEvent['end'];
            $oUserInfo = UserInfo::getByID($aEvent['uID']);
            $aResponse[$iKey]['title'] = $oUserInfo->getUserFullName();
            $aResponse[$iKey]['id'] = $aEvent['entryID'];

            // Alleen opmerking en bewerken van huidige gebruiker of als gebruiker beheerder is
            if ($this->isAdmin($uId) || $uId == $aEvent['uID']) {
                $aResponse[$iKey]['notes'] = $aEvent['notes'];
                $aResponse[$iKey]['persons'] = $aEvent['persons'];
                $aResponse[$iKey]['authorized'] = true;
            }
        }

        $js = Loader::helper('json');
        print $js->encode($aResponse);
        exit;
    }

    public function actionFetchallexceptions()
    {
        $aEvents = $this->fetchAllExceptions();
        $aResponse = array();

        foreach ($aEvents as $iKey => $aEvent) {
            $aResponse[$iKey]['start'] = $aEvent['start'];
            $aResponse[$iKey]['end'] = $aEvent['end'];

            $sCreditsText = ($aEvent['credits'] == 1) ? t('schelp') : t('schelpen');

            $aResponse[$iKey]['title'] = t('Uitzondering: ') . $aEvent['credits'] . ' ' . $sCreditsText . t(' per dag.');
            $aResponse[$iKey]['id'] = $aEvent['entryID'];
            $aResponse[$iKey]['type'] = 'exception';
            $aResponse[$iKey]['notes'] = $aEvent['notes'];
        }

        $js = Loader::helper('json');
        print $js->encode($aResponse);
        exit;
    }

    public function actionCredits()
    {
        $oResponse = new stdClass;

        $sStart = $this->get('start');
        $sEnd = $this->get('end');

        if (!empty($sStart) && !empty($sEnd)) {
            // Start en end in goede volgorde?
            if (strtotime($sStart) > strtotime($sEnd)) {
                $sTemp = $sStart;
                $sStart = $sEnd;
                $sEnd = $sTemp;
            }
            $oResponse->credits = $this->getCredits($sStart, $sEnd);
        } else {
            $oResponse->credits = 0;
        }
        $js = Loader::helper('json');
        print $js->encode($oResponse);
        exit;
    }

    public function fetchAll()
    {
        $oDb = Loader::db();
        $sSql = "SELECT * FROM btCottageBookerBookings WHERE bID = ?";
        return $oDb->GetAll($sSql, array($this->bID));
    }

    public function fetchAllExceptions()
    {
        $oDb = Loader::db();
        $sSql = "SELECT * FROM btCottageBookerExceptions WHERE bID = ?";
        return $oDb->GetAll($sSql, array($this->bID));
    }

    protected function insertBooking($uId, $sStart, $sEnd, $sNotes, $iPersons)
    {
        date_default_timezone_set('UTC');

        // Mag de gebruiker reserveren?
        $mCanBook = $this->canBook($sStart, $sEnd, $uId);
        if ($mCanBook === true) {
            $oDb = Loader::db();
            $sSql = "INSERT INTO btCottageBookerBookings (bID, uID, start, end, credits, notes, last_modified, persons) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";

            $dStart = date("Y-m-d", strtotime($sStart));
            $dEnd = date("Y-m-d", strtotime($sEnd));

            $iCredits = $this->getCredits($sStart, $sEnd);

            $aParams = array($this->bID, $uId, $dStart, $dEnd, $iCredits, $sNotes, $iPersons);

            $oDb->Execute($sSql, $aParams);

            $this->updateUserCredits($uId, -$iCredits);

            // Mail naar admin
            $iBookingId = $oDb->Insert_ID();

            $this->mailAdmins('mail_insert_booking',
                array(
                    'sStart' => date('d-m-Y', strtotime($sStart)),
                    'sEnd' => date('d-m-Y', strtotime($sEnd)),
                    'sNotes' => $sNotes,
                    'iPersons' => $iPersons,
                    'iBookingId' => $iBookingId,
                ));

            $this->mailUser($uId, 'mail_insert_booking_user',
                array(
                    'sStart' => date('d-m-Y', strtotime($sStart)),
                    'sEnd' => date('d-m-Y', strtotime($sEnd)),
                    'iPersons' => $iPersons,
                    'sNotes' => $sNotes,
                ));

            return array("status" => "success", "message" => t("Uw reservering is geplaatst!"));
        } else {
            return array("status" => "error", "message" => $mCanBook);
        }
    }

    protected function updateBooking($entryID, $uId, $sStart, $sEnd, $sNotes, $iPersons)
    {
        date_default_timezone_set('UTC');

        $mCanEdit = $this->canEdit($entryID, $uId, $sStart, $sEnd);
        if ($mCanEdit === true) {
            // Oude credits ophalen
            $oDb = Loader::db();
            $sSql = "SELECT credits FROM btCottageBookerBookings WHERE bID = ? AND uID = ? AND entryID = ?";
            $iOldCredits = $oDb->GetOne($sSql, array($this->bID, $uId, $entryID));

            $sSql = "UPDATE btCottageBookerBookings SET start = ?, end = ?, credits = ?, notes = ?, last_modified = NOW(), persons = ? WHERE bID = ? AND uID = ? AND entryID = ?";

            $dStart = date("Y-m-d", strtotime($sStart));
            $dEnd = date("Y-m-d", strtotime($sEnd));

            $aParams = array($dStart, $dEnd, $this->getCredits($sStart, $sEnd), $sNotes, $iPersons, $this->bID, $uId, $entryID);

            $oDb->Execute($sSql, $aParams);

            $this->updateUserCredits($uId, $iOldCredits - $this->getCredits($sStart, $sEnd));

            $this->mailAdmins('mail_update_booking',
                array(
                    'sStart' => date('d-m-Y', strtotime($sStart)),
                    'sEnd' => date('d-m-Y', strtotime($sEnd)),
                    'sNotes' => $sNotes,
                    'iPersons' => $iPersons,
                    'iBookingId' => $entryID,
                ));

            $this->mailUser($uId, 'mail_update_booking_user',
                array(
                    'sStart' => date('d-m-Y', strtotime($sStart)),
                    'sEnd' => date('d-m-Y', strtotime($sEnd)),
                    'iPersons' => $iPersons,
                    'sNotes' => $sNotes,
                ));

            return array("status" => "success", "message" => t("Uw reservering is bijgewerkt!"));
        } else {
            return array("status" => "error", "message" => $mCanEdit);
        }
    }

    protected function deleteBooking($entryID, $uId)
    {
        date_default_timezone_set('UTC');

        $mCanDelete = $this->canDelete($entryID, $uId);
        if ($mCanDelete === true) {
            // Oude booking ophalen
            $oDb = Loader::db();
            $sSql = "SELECT * FROM btCottageBookerBookings WHERE bID = ? AND uID = ? AND entryID = ?";
            $aBooking = $oDb->GetRow($sSql, array($this->bID, $uId, $entryID));

            // Verwijderen
            $sSql = "DELETE FROM btCottageBookerBookings WHERE entryID = ? AND uID = ? AND bID = ? LIMIT 1";

            $aParams = array($entryID, $uId, $this->bID);

            $oDb->Execute($sSql, $aParams);

            // Credits bijschrijven
            $this->updateUserCredits($uId, $aBooking['credits']);

            // Toevoegen aan de annuleringen tabel
            $sSql = "INSERT INTO btCottageBookerCancelled (bID, uID, start, end, credits, notes, persons) VALUES (?, ?, ?, ?, ?, ?, ?)";

            $aParams = array($this->bID, $aBooking['uID'], $aBooking['start'], $aBooking['end'], $aBooking['credits'], $aBooking['notes'], $aBooking['persons']);

            $oDb->Execute($sSql, $aParams);

            $iBookingId = $oDb->Insert_ID();
            $this->mailAdmins('mail_delete_booking',
                array(
                    'sStart' => date('d-m-Y', strtotime($aBooking['start'])),
                    'sEnd' => date('d-m-Y', strtotime($aBooking['end'])),
                    'sNotes' => $aBooking['notes'],
                    'iPersons' => $iPersons,
                    'iBookingId' => $iBookingId,
                ));

            $this->mailUser($uId, 'mail_delete_booking_user',
                array(
                    'sStart' => date('d-m-Y', strtotime($aBooking['start'])),
                    'sEnd' => date('d-m-Y', strtotime($aBooking['end'])),
                    'iPersons' => $iPersons,
                    'sNotes' => $aBooking['notes'],
                ));

            return array("status" => "success", "message" => t("Uw reservering is geannuleerd!"));
        } else {
            return array("status" => "error", "message" => $mCanDelete);
        }
    }

    protected function mailAdmins($sTemplate, $aParams)
    {
        // Alle admins ophalen
        Loader::model('user_list');
        $userList = new UserList();
        $userList->filterByGroup(Group::getByID($this->adminGroup)->getGroupName());
        $users = $userList->get();

        $mh = Loader::helper('mail');
        $mh->from('info@familie-kramer.nl');

        // Parameters
        $oUser = new user();
        $uId = $oUser->getUserID();
        $oUserInfo = UserInfo::getByID($uId);
        $aStandardParams = array(
            'sFullName' => $oUserInfo->getUserFullName(),
            'sEmail' => $oUserInfo->getUserEmail(),
        );
        $aParams = array_merge($aParams, $aStandardParams);
        foreach ($aParams as $sKey => $sParam) {
            $mh->addParameter($sKey, $sParam);
        }
        $mh->load($sTemplate, 'cottage_booker');
        foreach ($users as $user) {
            $mh->to($user->getUserEmail(), $user->getUserFullName());
        }
        $mh->sendMail();
    }

    protected function mailUser($uId, $sTemplate, $aParams)
    {
        $mh = Loader::helper('mail');
        $mh->from('info@familie-kramer.nl');

        // Parameters
        $oUserInfo = UserInfo::getByID($uId);
        $aStandardParams = array(
            'sFullName' => $oUserInfo->getUserFullName(),
            'sEmail' => $oUserInfo->getUserEmail(),
        );
        $aParams = array_merge($aParams, $aStandardParams);
        foreach ($aParams as $sKey => $sParam) {
            $mh->addParameter($sKey, $sParam);
        }
        $mh->load($sTemplate, 'cottage_booker');
        $mh->to($oUserInfo->getUserEmail(), $oUserInfo->getUserFullName());

        $mh->sendMail();
    }

    protected function canBook($sStart, $sEnd, $uId)
    {
        if ($this->isAdmin()) {
            return true;
        } else {
            // Ingelogd?
            if ($this->isLoggedIn() !== true) {
                return $this->isLoggedIn();
            }

            // Klopt de tijd?
            if (strtotime($sStart) < time() || strtotime($sEnd) < time()) {
                return t('U kunt alleen toekomstige reserveringen maken.');
            }

            // Kloppen de credits?
            if ($this->getCredits($sStart, $sEnd) > $this->getUserCottageBookerCredits($uId)) {
                return t('U heeft helaas niet voldoende schelpen voor deze reservering.');
            }

            if (!$this->canBookCancelledBookings) {
                // Deze periode al eens geannuleerd?
                $mIsCancelled = $this->getCancelledBooking($uId, $sStart, $sEnd);
                if ($mIsCancelled !== false) {
                    return t('U heeft de periode ') . date('d-m-Y', strtotime($mIsCancelled['start']))
                    . t(' t/m ') . date('d-m-Y', strtotime($mIsCancelled['end']))
                    . t(' al eens geannuleerd. U kunt niet opnieuw reserveren.');
                }
            }

            // Gaan we niet dubbel boeken?
            $mIsAlreadyBooked = $this->isAlreadyBooked($sStart, $sEnd);
            if ($mIsAlreadyBooked !== false) {
                return t('Deze periode is al door iemand geboekt van ' . date('d-m-Y', strtotime($mIsAlreadyBooked['start']))
                    . t(' t/m ') . date('d-m-Y', strtotime($mIsAlreadyBooked['end'])) . '.');
            }

            // Kan je alleen een hele week boeken?
            $mWeekOnly = $this->getAllowedByExceptions($sStart, $sEnd);
            if ($mWeekOnly !== false) {
                return $mWeekOnly;
            }

            return true;
        }
        return t('U kunt deze periode helaas niet reserveren.');
    }

    protected function canEdit($entryID, $uId, $sStart, $sEnd)
    {
        if ($this->isAdmin()) {
            return true;
        } else {
            // Ingelogd?
            if ($this->isLoggedIn() !== true) {
                return $this->isLoggedIn();
            }

            // Klopt de gebruiker?
            $oDb = Loader::db();
            $sSql = "SELECT uID, credits FROM btCottageBookerBookings WHERE bID = ? AND entryID = ?";
            $aResult = $oDb->GetRow($sSql, array($this->bID, $entryID));
            if ($aResult['uID'] != $uId) {
                return t('U kunt alleen uw eigen reservering bewerken.');
            }

            // Klopt de datum?
            if (strtotime($sStart) < time() || strtotime($sEnd) < time()) {
                return t('U kunt alleen toekomstige reserveringen maken.');
            }

            // Kloppen de credits?
            if (($this->getCredits($sStart, $sEnd) - $aResult['credits']) > $this->getUserCottageBookerCredits($uId)) {
                return t('Je hebt helaas niet voldoende schelpen voor deze reservering.');
            }

            // Deze periode al eens geannuleerd?
            if (!$this->canBookCancelledBookings) {
                $mIsCancelled = $this->getCancelledBooking($uId, $sStart, $sEnd);
                if ($mIsCancelled !== false) {
                    return t('U heeft de periode ') . date('d-m-Y', strtotime($mIsCancelled['start']))
                    . t(' t/m ') . date('d-m-Y', strtotime($mIsCancelled['end']))
                    . t(' al eens geannuleerd. U kunt niet opnieuw reserveren.');
                }
            }

            // Gaan we niet dubbel boeken?
            $mIsAlreadyBooked = $this->isAlreadyBooked($sStart, $sEnd, $uId);
            if ($mIsAlreadyBooked !== false) {
                return t('Deze periode is al door iemand geboekt van' . date('d-m-Y', strtotime($mIsAlreadyBooked['start']))
                    . t(' t/m ') . date('d-m-Y', strtotime($mIsAlreadyBooked['end'])) . '.');
            }

            // Kan je alleen een hele week boeken?
            $mWeekOnly = $this->getAllowedByExceptions($sStart, $sEnd);
            if ($mWeekOnly !== false) {
                return $mWeekOnly;
            }

            return true;
        }
        return t('U kunt deze reservering helaas niet bewerken.');
    }

    protected function canDelete($entryID, $uId)
    {
        if ($this->isAdmin()) {
            return true;
        } else {
            // Ingelogd?
            if ($this->isLoggedIn() !== true) {
                return $this->isLoggedIn();
            }

            $oDb = Loader::db();
            $sSql = "SELECT uID, start, end FROM btCottageBookerBookings WHERE bID = ? AND entryID = ?";
            $aResult = $oDb->GetRow($sSql, array($this->bID, $entryID));
            if ($aResult['uID'] != $uId) {
                return t('U kunt alleen uw eigen reservering annuleren.');
            }

            // Klopt de datum?
            if (strtotime($aResult['start']) < time() || strtotime($aResult['end']) < time()) {
                return t('U kunt alleen toekomstige reserveringen annuleren.');
            }

            return true;
        }
        return t('U kunt deze reservering helaas niet annuleren.');
    }

    /**
     * @param $first
     * @param $last
     * @param string $step
     * @param string $output_format
     * @return array
     */
    protected function dateRangeToArray($first, $last, $step = '+1 day', $output_format = 'd-m-Y')
    {
        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);

        while ($current <= $last) {

            $dates[] = date($output_format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }

    /**
     * @param $sStart
     * @param $sEnd
     * @return int
     */
    protected function getCredits($sStart, $sEnd)
    {
        $iCredits = 0;

        $aExceptions = $this->getAllExceptions($sStart, $sEnd);
        $aExceptionDays = array();
        foreach ($aExceptions as $aException) {
            $aTempExDays = $this->dateRangeToArray($aException['start'], $aException['end']);
            foreach ($aTempExDays as $sExDay) {
                $aExceptionDays[$sExDay] = $aException['credits'];
            }
        }

        $aDays = $this->dateRangeToArray($sStart, $sEnd);
        $sFirst = date('d-m-Y', strtotime($sStart));
        $sLast = date('d-m-Y', strtotime($sEnd));
        foreach ($aDays as $sDay) {
            $iWeekDay = date("w", strtotime($sDay));

            if (isset($aExceptionDays[$sDay])) {
                if ($iWeekDay == 6 && $sDay == $sLast && $sDay != $sFirst) {
                    // Don't count the last Saturday
                    // It's the change day
                    continue;
                }

                $iCredits += $aExceptionDays[$sDay];
                continue;
            }

            // Difference in months between now and current day
            $oNow = new DateTime(date('Y-m-d 00:00:00'));
            $oCurrent = new DateTime(date('Y-m-d 00:00:00', strtotime($sDay)));
            $iMonthDifference = $oNow->diff($oCurrent)->m + ($oNow->diff($oCurrent)->y * 12);

            $fFactor = 1;
            if ($iMonthDifference == 1) {
                // 50% off
                $fFactor = 0.5;
            } elseif ($iMonthDifference == 0) {
                // Free!
                $fFactor = 0;
            }

            if ($iWeekDay == 0 || $iWeekDay == 6) {
                // Weekend
                if ($iWeekDay == 6 && $sDay == $sLast && $sDay != $sFirst) {
                    // Don't count the last Saturday
                    // It's the change day
                    continue;
                }
                $iCredits += $this->creditsPerWeekendDay * $fFactor;
            } else {
                // Weekday
                $iCredits += $this->creditsPerWeekDay * $fFactor;
            }

        }

        if ($iCredits < 0) {
            $iCredits = 0;
        }

        return (int) round($iCredits);
    }

    protected function getAllExceptions($sStart, $sEnd)
    {
        $dStart = date("Y-m-d", strtotime($sStart));
        $dEnd = date("Y-m-d", strtotime($sEnd));

        $oDb = Loader::db();
        $sSql = "SELECT * "
        . "FROM btCottageBookerExceptions "
        . "WHERE bID = ? "
        . "AND (((start <= ?) AND (end >= ?))" // Start tussen start en end
         . "OR  ((start <= ?) AND (end >= ?))" // Of end tussen start en end
         . "OR  ((start >= ?) AND (end <= ?)))"; // Of periode tussen start en end
        return $oDb->GetAll($sSql, array($this->bID,
            $dStart,
            $dStart,
            $dEnd,
            $dEnd,
            $dStart,
            $dEnd));
    }

    protected function getAllowedByExceptions($sStart, $sEnd)
    {
        $aResults = $this->getAllExceptions($sStart, $sEnd);

        foreach ($aResults as $aResult) {
            if ($aResult['bookOnlyWeeks']) {
                // Meer dan 1 dag en start en eind op zaterdag?
                if (floor((strtotime($sEnd) - strtotime($sStart)) / 3600 / 24) <= 1
                    || date('N', strtotime($sStart)) != 6
                    || date('N', strtotime($sEnd)) != 6) {
                    return t('Voor deze periode kunt u alleen van zaterdag tot zaterdag boeken.');
                }
            }

            $maxNumberOfDays = intval($aResult['maxNumberOfDays']);
            $iDays = intval(floor(abs(strtotime($sEnd) - strtotime($sStart)) / 86400));

            if (($maxNumberOfDays > 0) && ($maxNumberOfDays < $iDays)) {
                return t('U kunt in deze periode een verblijf van maximaal %s aaneengesloten dagen boeken.', $maxNumberOfDays);
            }
        }

        return false;
    }

    protected function isAdmin()
    {
        $u = new user();

        $gBeheer = Group::getByName('Schelpenbeheerders');

        return false;

        if ($u->inGroup($gBeheer) || $u->isSuperUser()) {
            return true;
        } else {
            return false;
        }
    }

    protected function isLoggedIn()
    {
        $u = new user();
        if ($u->IsLoggedIn()) {
            if (!$this->isAdmin()) {
                $gUsers = Group::getById(intval($this->userGroup));
                if ($u->inGroup($gUsers)) {
                    return true;
                } else {
                    return t('U moet toegevoegd zijn aan de gebruikersgroep om te kunnen reserveren.');
                }
            } else {
                return true;
            }
        } else {
            return t('U moet ingelogd zijn om te kunnen reserveren.');
        }
    }

    protected function getUserCottageBookerCredits($uId)
    {
        $oUserInfo = UserInfo::getByID($uId);
        $iCredits = $oUserInfo->getUserCottageBookerCredits();
        return $iCredits;
    }

    protected function updateUserCredits($uId, $iAmount)
    {
        $oUserInfo = UserInfo::getByID($uId);
        $oUserInfo->setAttribute('cottage_booker_credits', $oUserInfo->getUserCottageBookerCredits() + $iAmount);
    }

    protected function getCancelledBooking($uId, $sStart, $sEnd)
    {
        $dStart = date("Y-m-d", strtotime($sStart));
        $dEnd = date("Y-m-d", strtotime($sEnd));

        $oDb = Loader::db();
        $sSql = "SELECT start, end "
        . "FROM btCottageBookerCancelled "
        . "WHERE bID = ? "
        . "AND uID = ?"
        . "AND (((start <= ?) AND (end >= ?))" // Start tussen start en end
         . "OR  ((start <= ?) AND (end >= ?))" // Of end tussen start en end
         . "OR  ((start >= ?) AND (end <= ?)))"; // Of geannuleerde periode tussen start en end
        $aResult = $oDb->GetRow($sSql, array($this->bID,
            $uId,
            $dStart,
            $dStart,
            $dEnd,
            $dEnd,
            $dStart,
            $dEnd));
        if (!empty($aResult)) {
            return $aResult;
        } else {
            return false;
        }
    }

    protected function isAlreadyBooked($sStart, $sEnd, $uId = 0)
    {
        $dStart = date("Y-m-d", strtotime($sStart));
        $dEnd = date("Y-m-d", strtotime($sEnd));

        $oDb = Loader::db();
        $sSql = "SELECT start, end "
        . "FROM btCottageBookerBookings "
        . "WHERE bID = ? "
        . "AND (((start <= ?) AND (end >= ?))" // Start tussen start en end
         . "OR  ((start <= ?) AND (end >= ?))" // Of end tussen start en end
         . "OR  ((start >= ?) AND (end <= ?)))"; // Of geannuleerde periode tussen start en end
        $aParams = array($this->bID,
            $dStart,
            $dStart,
            $dEnd,
            $dEnd,
            $dStart,
            $dEnd);
        // Allow edits by the same user
        if ($uId > 0) {
            $sSql .= "AND (uID <> ?)";
            $aParams[] = $uId;
        }
        $aResults = $oDb->GetAll($sSql, $aParams);
        if (!empty($aResults)) {

            // Dubbelboeken mag wel als start of eind zaterdag is, dat is de wisseldag
            $iNewStart = date('N', strtotime($sStart));
            $iNewEnd = date('N', strtotime($sEnd));

            // Maximaal 2 boekingen op zaterdag
            $aCounts = array();
            $aCounts[strtotime($sStart)] = 0;
            $aCounts[strtotime($sEnd)] = 0;

            foreach ($aResults as $aResult) {

                $iCurrentStart = date('N', strtotime($aResult['start']));
                $iCurrentEnd = date('N', strtotime($aResult['end']));

                if (strtotime($sStart) == strtotime($aResult['end'])
                    && $iNewStart == $this->changeDay && $iCurrentEnd == $this->changeDay) {
                    $aCounts[strtotime($sStart)]++;
                    continue;
                } elseif (strtotime($sEnd) == strtotime($aResult['start'])
                    && $iNewEnd == $this->changeDay && $iCurrentStart == $this->changeDay) {
                    $aCounts[strtotime($sEnd)]++;
                    continue;
                } else {

                    return $aResult;
                }
            }

            if ($aCounts[strtotime($sStart)] >= 2 || $aCounts[strtotime($sEnd)] >= 2) {
                return $aResults[0];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Set admin page link if user has access
     */
    private function _setAdminPageLink(){
        $adminPage = Page::getByPath('/dashboard/cottage_booker',
                                     $version = 'active');
        $p = new Permissions($adminPage);

        if ($p->canRead()) {
            $this->set('adminPageLink', '/dashboard/cottage_booker');
        } else {
            $this->set('adminPageLink', false);
        }
    }

    /**
     * Setup user properties
     */
    private function _initUser(){
        $oUser = new user();
        $uId = $oUser->getUserID();
        $oUserInfo = UserInfo::getByID($uId);
        if ($this->isLoggedIn() === true) {
            $this->set('loggedIn', true);
            $this->set('userFullName', $oUserInfo->getUserFullName());
            $this->set('userCredits', $oUserInfo->getUserCottageBookerCredits());
            $this->set('schelpenPerDag', $this->creditsPerWeekDay);
            $this->set('schelpenPerWeekendDag', $this->creditsPerWeekendDay);
        } else {
            $this->set('loggedIn', false);
        }
    }
}
