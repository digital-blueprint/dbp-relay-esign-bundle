<?php

declare(strict_types=1);

namespace DBP\API\ESignBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class DbpEsignExtension extends ConfigurableExtension
{
    public function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $this->extendArrayParameter(
            $container, 'api_platform.resource_class_directories', [__DIR__.'/../Entity']);

        $pathsToHide = [
            '/advancedly_signed_documents/{id}',
            '/advancedly_signed_documents',
            '/qualified_signing_requests/{id}',
            '/qualified_signing_requests',
        ];

        if ($_ENV['PDF_AS_VERIFICATION_ENABLE'] !== 'true') {
            $pathsToHide = array_merge($pathsToHide, [
                '/electronic_signature_verification_reports',
                '/electronic_signature_verification_reports/create',
                '/electronic_signature_verification_reports/{id}',
                '/electronic_signatures/{id}',
            ]);
        }

        $this->extendArrayParameter($container, 'dbp_api.paths_to_hide', $pathsToHide);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yaml');

        $container->setParameter('dbp_api.esign.config', $mergedConfig);
    }

    private function extendArrayParameter(ContainerBuilder $container, string $parameter, array $values)
    {
        if (!$container->hasParameter($parameter)) {
            $container->setParameter($parameter, []);
        }
        $oldValues = $container->getParameter($parameter);
        assert(is_array($oldValues));
        $container->setParameter($parameter, array_merge($oldValues, $values));
    }
}
