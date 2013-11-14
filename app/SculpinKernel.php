<?php
use Sculpin\Bundle\SculpinBundle\HttpKernel\AbstractKernel;

class SculpinKernel extends AbstractKernel
{
    /**
     * {@inheritdoc}
     */
    public function getAdditionalSculpinBundles()
    {
        return array(
            'AlphaRPC\Bundle\SiteBundle\AlphaRPCSiteBundle'
        );
    }
}
