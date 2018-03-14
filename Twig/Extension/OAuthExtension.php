<?php

namespace Kolyya\OAuthBundle\Twig\Extension;

use Kolyya\OAuthBundle\Templating\Helper\OAuthHelper;

class OAuthExtension extends \Twig_Extension
{
    protected $helper;

    public function __construct(OAuthHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('kolyya_oauth_buttons', array($this, 'getButtons'), array('is_safe' => array('html'))),
        );
    }

    public function getButtons()
    {
        return $this->helper->getButtons();
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'kolyya_oauth';
    }
}
