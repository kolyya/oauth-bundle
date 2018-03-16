<?php
/**
 * Created by PhpStorm.
 * User: Niko
 * Date: 15.03.2018
 * Time: 18:42
 */

namespace Kolyya\OAuthBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class KolyyaOAuthBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = $this->createContainerExtension();
        }

        return $this->extension;
    }
}