<?php
namespace RKA\Form;

use Pimple\ServiceProviderInterface;

class FormProvider implements \Pimple\ServiceProviderInterface
{
    private $settings;

    /**
     * Create new ZF2 Form integration service provider
     *
     * @param array|ArrayAccess $settings
     */
    public function __construct($settings = [])
    {
        $this->settings = $settings;
    }

    /**
     * Register service provider
     *
     * @param \Pimple\Container $container
     */
    public function register(\Pimple\Container $container)
    {
        $smConfigurator = new ServiceManagerConfigurator();
        $serviceManager = $smConfigurator->createServiceManager($this->settings);

        $container['serviceManager'] = $this;
    }
}
