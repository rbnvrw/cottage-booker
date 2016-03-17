<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * Main controller
 */
class CottageBookerPackage extends Package
{

    protected $pkgHandle = 'cottage_booker';
    protected $appVersionRequired = '5.3.0';
    protected $pkgVersion = '1.0';

    public function getPackageDescription()
    {
        return t("Systeem om een huisje te reserveren.");
    }

    public function getPackageName()
    {
        return t("Cottage Booker");
    }

    /**
     * Install block, credits attribute + jobs, creditsLastUpdate,
     * fullName and the dashboard page.
     */
    public function install()
    {
        $pkg = parent::install();

        $this->_installBlock($pkg);

        $this->_installCredits($pkg);

        $this->_installCreditsLastUpdate($pkg);

        $this->_installFullName($pkg);

        $this->_installDashboardPage($pkg);

        $this->_installCreditsUpdateJob($pkg);
    }

    /**
     * Add extra attributes when a user is added
     */
    public function onStart()
    {
        Events::extend('on_user_add', 'SchelpenUser', 'setupUser', 'packages/'
            . $this->pkgHandle . '/models/SchelpenUser.php', array($ui));
    }

    /**
     * Install user credits attribute
     */
    private function _installCredits($pkg)
    {
        // Install 'credits' attribute
        Loader::model('user_attributes');
        if (!is_object(UserAttributeKey::getByHandle('cottage_booker_credits'))) {
            UserAttributeKey::add(
                'NUMBER', array(
                    'akHandle' => 'cottage_booker_credits',
                    'akName' => t('Cottage Booker credits'),
                    'akIsSearchable' => 0,
                    'akIsEditable' => 1,
                    'uakProfileEdit' => 0,
                    'uakProfileEditRequired' => 0,
                    'uakRegisterEdit' => 0,
                    'uakRegisterEditRequired' => 0,
                    'uakProfileDisplay' => 1), $pkg);
        }
    }

    /**
     * Install the booking system block
     */
    private function _installBlock($pkg)
    {
        // Install block
        if (!is_object(BlockType::getByHandle('cottage_booker'))) {
            BlockType::installBlockTypeFromPackage('cottage_booker', $pkg);
        }
    }

    /**
     * Log last update time of credits
     */
    private function _installCreditsLastUpdate($pkg)
    {
        // Install 'creditsLastUpdate' attribute
        Loader::model('user_attributes');
        if (!is_object(UserAttributeKey::getByHandle('cottage_booker_credits_last_update'))) {
            UserAttributeKey::add(
                'DATE', array(
                    'akHandle' => 'cottage_booker_credits_last_update',
                    'akName' => t('Cottage Booker credits (last update)'),
                    'akIsSearchable' => 0,
                    'akIsEditable' => 0,
                    'uakProfileEdit' => 0,
                    'uakProfileEditRequired' => 0,
                    'uakRegisterEdit' => 0,
                    'uakRegisterEditRequired' => 0,
                    'uakProfileDisplay' => 0), $pkg);
        }
    }

    /**
     * Install user fullname attribute
     */
    private function _installFullName($pkg)
    {
        // Install 'fullName' attribute
        if (!is_object(UserAttributeKey::getByHandle('full_name'))) {
            UserAttributeKey::add(
                'TEXT', array(
                    'akHandle' => 'full_name',
                    'akName' => t('Full name'),
                    'akIsSearchable' => 1,
                    'akIsEditable' => 1,
                    'uakProfileEdit' => 1,
                    'uakProfileEditRequired' => 1,
                    'uakRegisterEdit' => 1,
                    'uakRegisterEditRequired' => 1,
                    'uakProfileDisplay' => 1), $pkg);
        }
    }

    /**
     * Install admin page on dashboard
     */
    private function _installDashboardPage($pkg)
    {
        // Install the single page in the dashboard
        Loader::model('single_page');
        $oPage = SinglePage::add('/dashboard/cottage_booker', $pkg);
        if ($oPage) {
            $oPage->update(array('cName' => t('Cottage Booker'),
                'cDescriptions' => t('Beheer de instellingen van de Cottage Booker.')));
            $oPage->setAttribute('icon_dashboard', 'icon-wrench');
        }
    }

    /**
     * Keep credits up to date via a Job
     */
    private function _installCreditsUpdateJob($pkg)
    {
        Loader::model("job");
        Job::installByPackage("update_cottagebooker_credits", $pkg);
    }
}
