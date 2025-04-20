<?php

namespace Dashify\DashifyBundle;

use Dashify\DashifyBundle\DependencyInjection\DashifyExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DashifyBundle extends Bundle
{
    public function getContainerExtension(): DashifyExtension
    {
        if (null === $this->extension) {
            $this->extension = new DashifyExtension();
        }

        return $this->extension;
    }
} 
