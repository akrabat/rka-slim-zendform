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
     * Register service manager & set up Twig if
     * we have an environment
     *
     * @param \Pimple\Container $container
     */
    public function register(\Pimple\Container $container)
    {
        $smConfigurator = new ServiceManagerConfigurator();
        $serviceManager = $smConfigurator->createServiceManager($this->settings);

        $container['serviceManager'] = $serviceManager;

        if ($container['view'] instanceof \Slim\Views\Twig) {
            // Register Zend\Forms view helpers
            $viewHelperManager = $serviceManager->get('ViewHelperManager');
            $renderer = new \Zend\View\Renderer\PhpRenderer();
            $renderer->setHelperPluginManager($viewHelperManager);

            $environment = $container['view']->getEnvironment();
            $environment->registerUndefinedFunctionCallback(
                function ($name) use ($viewHelperManager, $renderer) {
                    if (!$viewHelperManager->has($name)) {
                        return false;
                    }

                    $callable = [$renderer->plugin($name), '__invoke'];
                    $options  = ['is_safe' => ['html']];
                    return new \Twig_SimpleFunction(null, $callable, $options);
                }
            );
        }
    }
}
