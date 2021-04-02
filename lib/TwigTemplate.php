<?php
namespace Concrete\Package\CottageBooker\Lib;

require_once(__DIR__ . '/../vendor/autoload.php');

/**
 * Class TwigTemplate
 * @author Ruben Verweij
 */
class TwigTemplate
{
    /**
     * _instance
     *
     * @var mixed
     */
    private static $_instance;

    /**
     * _twig
     *
     * @var mixed
     */
    private static $_twig;

    /**
    * @param Concrete\Core\Page\View\PageView $oPageView
    *
    */
    public function __construct($oPageView = null)
    {
        \Twig_Autoloader::register();

        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../twig');
        $this->_twig = new \Twig_Environment($loader);

        # Legacy code
        $t = new \Twig\TwigFunction('t', function ($val) {
            return t($val);
        });
        $this->_twig->addFunction($t);
        $emp = new \Twig\TwigFunction('empty', function ($val) {
            return empty($val);
        });
        $this->_twig->addFunction($emp);
        $action = new \Twig\TwigFunction('action', function ($action, $bid) use ($oPageView) {
            if($oPageView !== null) {
                return $oPageView->action($action, $bid);
            }else{
                return '';
            }
        });
        $this->_twig->addFunction($action);
    }

    /**
     * getInstance
     *
     * @param Concrete\Core\Page\View\PageView $oPageView
     */
    public static function getInstance($oPageView = null)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($oPageView);
        }
        return self::$_instance;
    }

    /**
     * renderTemplate
     *
     * @param mixed $sTemplate
     * @param mixed $aContext
     * @param Concrete\Core\Page\View\PageView $oPageView
     */
    public static function renderTemplate($sTemplate, $aContext, $oPageView = null)
    {
        self::$_instance = self::getInstance($oPageView);
        return self::$_instance->_twig->render($sTemplate, $aContext);
    }
}
