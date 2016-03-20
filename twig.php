<?php
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
     * __controller
     *
     */
    protected function __controller()
    {
        Twig_Autoloader::register();

        $loader = new Twig_Loader_Filesystem('twig');
        $this->_twig = new Twig_Environment($loader);
    }

    /**
     * getInstance
     *
     */
    public static function getInstance()
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * renderTemplate
     *
     * @param mixed $sTemplate
     * @param mixed $aContext
     */
    public static function renderTemplate($sTemplate, $aContext)
    {
        self::$_instance = self::getInstance();
        return self::$_twig->render($sTemplate, $aContext);
    }
}
