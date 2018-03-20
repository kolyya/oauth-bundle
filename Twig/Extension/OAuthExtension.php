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
            new \Twig_SimpleFunction('kolyya_oauth_buttons', array($this, 'getOauthButtons'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('kolyya_connect_buttons', array($this, 'getConnectButtons'), array('is_safe' => array('html'))),
        );
    }

    public function getOauthButtons()
    {
        return $this->helper->getOauthButtons();
    }

    public function getConnectButtons()
    {
        return $this->helper->getConnectButtons();
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
