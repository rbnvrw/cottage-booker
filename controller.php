<?php
namespace Concrete\Package\CottageBooker;

defined('C5_EXECUTE') or die(_("Access Denied."));

use \Concrete\Core\Package\Package;
use \Concrete\Core\Block\BlockType\BlockType;
use \Concrete\Core\Support\Facade\Events;
use \Concrete\Core\Page\Single as SinglePage;
use \Concrete\Core\Job\Job;

use \Concrete\Package\CottageBooker\Models\SchelpenUser;

/**
 * Main controller
 */
class Controller extends Package
{

    protected $pkgHandle = 'cottage_booker';
    protected $appVersionRequired = '8.5';
    protected $pkgVersion = '2.0';

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
        $this->setupAutoloader();
        $pkg = parent::install();

        $this->_installBlock($pkg);

        $this->_installDashboardPage($pkg);

        $this->_installCreditsUpdateJob($pkg);
    }

    /**
     * Add extra attributes when a user is added
     */
    public function on_start()
    {
        $this->setupAutoloader();
        Events::addListener('on_user_add', function($event) {
            $user = $event->getUserInfoObject();
            SchelpenUser::setupUser($user);
        });
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
     * Install admin page on dashboard
     */
    private function _installDashboardPage($pkg)
    {
        // Install the single page in the dashboard
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
        Job::installByPackage("update_cottagebooker_credits", $pkg);
    }

    /**
     * Configure the autoloader
     */
    private function setupAutoloader()
    {
        if (file_exists($this->getPackagePath() . '/vendor')) {
            require_once $this->getPackagePath() . '/vendor/autoload.php';
        }
    }
}
