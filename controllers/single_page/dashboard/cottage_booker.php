<?php
namespace Concrete\Package\CottageBooker\Controller\SinglePage\Dashboard;
use \Concrete\Core\Page\Controller\DashboardPageController;

defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * Class: DashboardCottageBookerController
 *
 * @see Controller
 */
class CottageBooker extends DashboardPageController
{

    protected $pkgHandle = 'cottage_booker';

    /**
     * view
     *
     */
    public function view()
    {
        $this->set('aBlocks', $this->getBlocks());
    }

    /**
     * settings
     *
     * @param mixed $bId
     * @param string $sMessage
     */
    public function settings($bId, $sMessage = '')
    {
        $oDb = \Loader::db();

        // Fetch general settings
        $sSql = "SELECT * FROM btCottageBooker WHERE bID = ?";
        $aResult = $oDb->getRow($sSql, array(intval($bId)));
        $this->set('aBlockSettings', $aResult);

        $aGroupSelect = array();
        // Fetch all user groups
        $oGroupList = new \Concrete\Core\User\Group\GroupList();
        $aGroups = $oGroupList->getResults();

        foreach ($aGroups as $oGroup) {
            $aGroupSelect[$oGroup->getGroupID()] = $oGroup->getGroupName();
        }

        $this->set('aGroups', $aGroupSelect);

        // Wisseldagen
        $aDays = array(
            0 => t('Geen'),
            1 => t('Maandag'),
            2 => t('Dinsdag'),
            3 => t('Woensdag'),
            4 => t('Donderdag'),
            5 => t('Vrijdag'),
            6 => t('Zaterdag'),
            7 => t('Zondag'),
        );
        $this->set('aDays', $aDays);

        if (!empty($sMessage)) {$this->set('message', $sMessage);}
    }

    /**
     * save
     *
     * @param mixed $bId
     */
    public function save($bId)
    {
        $oDb = \Loader::db();

        $aData = $_POST;

        $aColumns = array('creditsPerWeekDay',
            'creditsPerWeekendDay',
            'userCreditsAnnual',
            'userCreditsMax',
            'cottageName',
            'canBookCancelledBookings',
            'adminGroup',
            'userGroup',
            'changeDay');

        $aParams = array();
        $sSql = "UPDATE btCottageBooker SET ";

        foreach ($aData as $sColumn => $sValue) {
            if (in_array($sColumn, $aColumns)) {
                $sSql .= $sColumn . ' = ?, ';
                $aParams[] = $sValue;
            }
        }

        $sSql = trim($sSql, ', ');
        $sSql .= 'WHERE bID = ?';

        $aParams[] = $bId;

        $oUpdate = $oDb->Execute($sSql, $aParams);

        $this->redirect('/dashboard/cottage_booker/settings', $bId, t('De wijzigingen zijn opgeslagen.'));

    }

    /**
     * reserveringen
     *
     * @param mixed $bId
     * @param string $sMessage
     */
    public function reserveringen($bId, $sMessage = '')
    {
        // Get all bookings
        $oDb = \Loader::db();
        $sSql = "SELECT * FROM btCottageBookerBookings WHERE bID = ? ORDER BY start DESC";
        $aBookings = $oDb->GetAll($sSql, array($bId));
        foreach ($aBookings as $iKey => $aBooking) {
            if (!empty($aBooking['uID'])) {
                $oUserInfo = UserInfo::getByID($aBooking['uID']);
                $aBookings[$iKey]['user'] = $oUserInfo->getUserFullName();
            }
        }
        $this->set('aBookings', $aBookings);

        // Get all cancellations
        $sSql = "SELECT * FROM btCottageBookerCancelled WHERE bID = ? ORDER BY start DESC";
        $aCancelled = $oDb->GetAll($sSql, array($bId));
        foreach ($aCancelled as $iKey => $aBooking) {
            if (!empty($aBooking['uID'])) {
                $oUserInfo = UserInfo::getByID($aBooking['uID']);
                $aCancelled[$iKey]['user'] = $oUserInfo->getUserFullName();
            }
        }
        $this->set('aCancelled', $aCancelled);

        // Get all exceptions
        $sSql = "SELECT * FROM btCottageBookerExceptions WHERE bID = ? ORDER BY start DESC";
        $aExceptions = $oDb->GetAll($sSql, array($bId));
        foreach ($aExceptions as $iKey => $aBooking) {
            if (!empty($aBooking['uID'])) {
                $oUserInfo = UserInfo::getByID($aBooking['uID']);
                $aExceptions[$iKey]['user'] = $oUserInfo->getUserFullName();
            }
        }
        $this->set('aExceptions', $aExceptions);

        $this->set('bID', $bId);

        // JS toevoegen
        $html = \Loader::helper('html');
        $this->addHeaderItem($html->javascript('bootstrap-tab.js', $this->pkgHandle));
        $this->addHeaderItem($html->javascript('dashboard_functions.js', $this->pkgHandle));

        if (!empty($sMessage)) {$this->set('message', $sMessage);}
    }

    /**
     * editBooking
     *
     * @param mixed $entryID
     */
    public function editBooking($entryID)
    {
        date_default_timezone_set('UTC');

        \Loader::model('user_list');
        $ul = new UserList();

        $oUsers = $ul->get();

        $aUsers = array();
        foreach ($oUsers as $oUser) {
            $aUsers[$oUser->getUserID()] = $oUser->getUserFullName();
        }

        $this->set('aUsers', $aUsers);

        // Oude booking ophalen
        $oDb = \Loader::db();
        $sSql = "SELECT * FROM btCottageBookerBookings WHERE entryID = ?";
        $aBooking = $oDb->GetRow($sSql, array($entryID));
        $this->set('aBooking', $aBooking);
    }

    /**
     * addBooking
     *
     * @param mixed $bId
     */
    public function addBooking($bId)
    {
        date_default_timezone_set('UTC');

        \Loader::model('user_list');
        $ul = new UserList();

        $oUsers = $ul->get();

        $aUsers = array();
        foreach ($oUsers as $oUser) {
            $aUsers[$oUser->getUserID()] = $oUser->getUserFullName();
        }

        $this->set('aUsers', $aUsers);

        $this->set('bID', $bId);
    }

    /**
     * saveBooking
     *
     * @param mixed $bID
     */
    public function saveBooking($bID)
    {
        date_default_timezone_set('UTC');

        $oDb = \Loader::db();

        $aData = $_POST;

        $aColumns = array(
            'bID',
            'uID',
            'start',
            'end',
            'credits',
            'notes',
            'last_modified',
            'persons',
        );

        $aParams = array();
        $sSql = "INSERT INTO btCottageBookerBookings (`" . implode($aColumns, '`,`') . "`) VALUES (";

        $aParams[] = $bID;
        $aParams[] = $aData['uID'];
        $aParams[] = date('Y-m-d', strtotime($aData['start']));
        $aParams[] = date('Y-m-d', strtotime($aData['end']));
        $aParams[] = $aData['credits'];
        $aParams[] = $aData['notes'];
        $aParams[] = date('Y-m-d');
        $aParams[] = $aData['persons'];

        foreach ($aParams as $sValue) {
            $sSql .= '?, ';
        }

        $sSql = trim($sSql, ', ') . ')';

        $oInsert = $oDb->Execute($sSql, $aParams);

        $this->redirect('/dashboard/cottage_booker/reserveringen', $bID, t('De reservering is toegevoegd.'));

        $this->set('aBooking', array());
    }

    /**
     * updateBooking
     *
     * @param mixed $entryID
     */
    public function updateBooking($entryID)
    {
        // Oude booking ophalen
        $oDb = \Loader::db();
        $sSql = "SELECT * FROM btCottageBookerBookings WHERE entryID = ?";
        $aBooking = $oDb->GetRow($sSql, array($entryID));

        $aData = $_POST;

        $aData['last_modified'] = date('Y-m-d');

        $aColumns = array(
            'uID',
            'start',
            'end',
            'credits',
            'notes',
            'last_modified',
            'persons',
        );

        $aParams = array();
        $sSql = "UPDATE btCottageBookerBookings SET ";

        foreach ($aData as $sColumn => $sValue) {
            if (in_array($sColumn, $aColumns)) {
                $sSql .= $sColumn . ' = ?, ';

                if ($sColumn == 'start' || $sColumn == 'end') {
                    $aParams[] = date('Y-m-d', strtotime($sValue));
                } else {
                    $aParams[] = $sValue;
                }
            }
        }

        $sSql = trim($sSql, ', ');
        $sSql .= ' WHERE entryID = ?';

        $aParams[] = $entryID;

        $oUpdate = $oDb->Execute($sSql, $aParams);

        $this->redirect('/dashboard/cottage_booker/reserveringen', $aBooking['bID'], t('De wijzigingen zijn opgeslagen.'));
    }

    /**
     * cancelBooking
     *
     * @param mixed $entryID
     */
    public function cancelBooking($entryID)
    {
        date_default_timezone_set('UTC');

        // Oude booking ophalen
        $oDb = \Loader::db();
        $sSql = "SELECT * FROM btCottageBookerBookings WHERE entryID = ?";
        $aBooking = $oDb->GetRow($sSql, array($entryID));

        // Verwijderen
        $sSql = "DELETE FROM btCottageBookerBookings WHERE entryID = ? LIMIT 1";
        $aParams = array($entryID);
        $oDb->Execute($sSql, $aParams);

        // Credits bijschrijven
        $this->updateUserCredits($aBooking['uID'], $aBooking['credits']);

        // Toevoegen aan de annuleringen tabel
        $sSql = "INSERT INTO btCottageBookerCancelled (bID, uID, start, end, credits, notes, persons) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $aParams = array($aBooking['bID'], $aBooking['uID'], $aBooking['start'], $aBooking['end'], $aBooking['credits'], $aBooking['notes'], $aBooking['persons']);
        $oDb->Execute($sSql, $aParams);

        $this->set('bID', $aBooking['bID']);

        $this->redirect('/dashboard/cottage_booker/reserveringen', $aBooking['bID'], t('De reservering is verwijderd.'));
    }

    /**
     * removeCancellation
     *
     * @param mixed $entryID
     */
    public function removeCancellation($entryID)
    {
        date_default_timezone_set('UTC');

        // Oude booking ophalen
        $oDb = \Loader::db();
        $sSql = "SELECT * FROM btCottageBookerCancelled WHERE entryID = ?";
        $aBooking = $oDb->GetRow($sSql, array($entryID));

        // Verwijderen
        $oDb = \Loader::db();
        $sSql = "DELETE FROM btCottageBookerCancelled WHERE entryID = ? LIMIT 1";
        $aParams = array($entryID);
        $oDb->Execute($sSql, $aParams);

        header('Location: /dashboard/cottage_booker/reserveringen/' . $aBooking['bID'] . '/' . t('De annulering is verwijderd.') . '/#annuleringen');
        exit;
    }

    /**
     * addException
     *
     * @param mixed $bID
     */
    public function addException($bID)
    {

        $aBooking = array(
            'start' => date('Y-m-d'),
            'end' => date('Y-m-d'),
            'credits' => 0,
            'notes',
            'bookOnlyWeeks' => 0,
            'maxNumberOfDays' => 0,
            'entryID' => null,
            'bID' => $bID,
        );
        $this->set('aBooking', $aBooking);
    }

    /**
     * editException
     *
     * @param mixed $entryID
     */
    public function editException($entryID)
    {
        date_default_timezone_set('UTC');

        // Oude booking ophalen
        $oDb = \Loader::db();
        $sSql = "SELECT * FROM btCottageBookerExceptions WHERE entryID = ?";
        $aBooking = $oDb->GetRow($sSql, array($entryID));

        $this->set('aBooking', $aBooking);
    }

    /**
     * saveException
     *
     * @param mixed $entryID
     */
    public function saveException($entryID = null)
    {
        $oDb = \Loader::db();

        $aData = $_POST;

        if (!isset($aData['bookOnlyWeeks'])) {
            $aData['bookOnlyWeeks'] = 0;
        }

        $aData['maxNumberOfDays'] = (intval($aData['maxNumberOfDays']) < 0) ? 0 : intval($aData['maxNumberOfDays']);

        if (!isset($aData['uID']) || empty($aData['uID'])) {
            $oUser = new user();
            $aData['uID'] = $oUser->getUserID();
        }

        $aColumns = array(
            'bID',
            'uID',
            'start',
            'end',
            'credits',
            'notes',
            'bookOnlyWeeks',
            'maxNumberOfDays',
        );

        $aParams = array();

        if (!empty($entryID)) {
            // Oude booking ophalen
            $sSql = "SELECT * FROM btCottageBookerExceptions WHERE entryID = ?";
            $aBooking = $oDb->GetRow($sSql, array($entryID));

            $sSql = "UPDATE btCottageBookerExceptions SET ";

            foreach ($aData as $sColumn => $sValue) {
                if (in_array($sColumn, $aColumns)) {
                    $sSql .= $sColumn . ' = ?, ';

                    if ($sColumn == 'start' || $sColumn == 'end') {
                        $aParams[] = date('Y-m-d', strtotime($sValue));
                    } else {
                        $aParams[] = $sValue;
                    }
                }
            }

            $sSql = trim($sSql, ', ');
            $sSql .= ' WHERE entryID = ?';

            $aParams[] = $entryID;

            $oUpdate = $oDb->Execute($sSql, $aParams);

            header('Location: /dashboard/cottage_booker/reserveringen/' . $aBooking['bID'] . '/' . t('De wijzigingen zijn opgeslagen.') . '/#uitzonderingen');
            exit;
        } else {
            // Inserten
            $sSql = "INSERT INTO btCottageBookerExceptions (start, end, credits, notes, bookOnlyWeeks, maxNumberOfDays, bID, uID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $oInsert = $oDb->Execute($sSql, array(
                date('Y-m-d', strtotime($aData['start'])),
                date('Y-m-d', strtotime($aData['end'])),
                $aData['credits'],
                $aData['notes'],
                $aData['bookOnlyWeeks'],
                $aData['maxNumberOfDays'],
                $aData['bID'],
                $aData['uID'],
            ));

            header('Location: /dashboard/cottage_booker/reserveringen/' . $aData['bID'] . '/' . t('De wijzigingen zijn opgeslagen.') . '/#uitzonderingen');
            exit;
        }

    }

    /**
     * deleteException
     *
     * @param mixed $entryID
     */
    public function deleteException($entryID)
    {
        date_default_timezone_set('UTC');

        // Oude booking ophalen
        $oDb = \Loader::db();
        $sSql = "SELECT * FROM btCottageBookerExceptions WHERE entryID = ?";
        $aBooking = $oDb->GetRow($sSql, array($entryID));

        // Verwijderen
        $oDb = \Loader::db();
        $sSql = "DELETE FROM btCottageBookerExceptions WHERE entryID = ? LIMIT 1";
        $aParams = array($entryID);
        $oDb->Execute($sSql, $aParams);

        $this->set('bID', $aBooking['bID']);

        header('Location: /dashboard/cottage_booker/reserveringen/' . $aBooking['bID'] . '/' . t('De uitzondering is verwijderd.') . '/#uitzonderingen');
        exit;
    }

    /**
     * getBlocks
     *
     */
    protected function getBlocks()
    {
        $aBlocks = array();

        $oDb = \Loader::db();
        $sSql = "SELECT * FROM btCottageBooker ORDER BY cottageName ASC";
        $aBlockRows = $oDb->GetAll($sSql);

        $oNav = \Loader::helper('navigation');

        foreach ($aBlockRows as $iKey => $aBlockRow) {
            $sSql = "SELECT "
                . "COUNT(*) AS totalBookings, "
                . "last_modified "
                . "FROM btCottageBookerBookings "
                . "WHERE bID = ? "
                . "ORDER BY last_modified DESC ";
            $aResult = $oDb->GetRow($sSql, array($aBlockRow['bID']));
            $aBlocks[$iKey] = $aBlockRow;
            $aBlocks[$iKey]['totalBookings'] = $aResult['totalBookings'];
            $aBlocks[$iKey]['last_modified'] = $aResult['last_modified'];
            // Look for page the block is on
            $sSql = "SELECT `cID` FROM `CollectionVersionBlocks` WHERE `bID` = ?";
            $iPage = $oDb->getOne($sSql, array($aBlockRow['bID']));
            $oPage = \Page::getById(intval($iPage));
            $aBlocks[$iKey]['page'] = $oNav->getCollectionURL($oPage);
        }

        return $aBlocks;
    }

    /**
     * updateUserCredits
     *
     * @param mixed $uId
     * @param mixed $iAmount
     */
    protected function updateUserCredits($uId, $iAmount)
    {
        $oUserInfo = UserInfo::getByID($uId);
        $oUserInfo->setAttribute('cottage_booker_credits', $oUserInfo->getUserCottageBookerCredits() + $iAmount);
    }

    /**
     * getCurrentUrl
     *
     */
    protected function getCurrentUrl()
    {
        $currentPage = \Page::getCurrentPage();
        \Loader::helper('navigation');
        return NavigationHelper::getLinkToCollection($currentPage, true);
    }
}
