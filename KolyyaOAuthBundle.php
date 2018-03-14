<?php

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
