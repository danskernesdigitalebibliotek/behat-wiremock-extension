<?php
/**
 * @author Vitalii Piskovyi <vitalii.piskovyi@gmail.com>
 */
namespace VPX\WiremockExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use GuzzleHttp\Client;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use VPX\WiremockExtension\Context\Initializer\WiremockAwareInitializer;
use WireMock\Client\WireMock;

class WiremockExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'wiremock';
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('base_url')
                    ->defaultValue('http://localhost:8080')
                ->end()
                ->scalarNode('mapping_path')
                    ->isRequired()
                ->end()
                ->arrayNode('preload_mappings')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('service')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('mapping')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $url_info = parse_url($config['base_url'] ?: 'http://localhost:8080');
        $container->setParameter('wiremock.host', $url_info['host']);
        $container->setParameter('wiremock.port', $url_info['port']);
        $container->setParameter('wiremock.mapping_path', $config['mapping_path']);
        $container->setParameter('wiremock.preload_mappings', $config['preload_mappings']);

        $definition = (new Definition(WireMock::class))
            ->setFactory([WireMock::class, 'create'])
            ->setArguments([
                $container->getParameter('wiremock.host'),
                $container->getParameter('wiremock.port'),
            ]);
        $container->setDefinition('wiremock.client', $definition);

        $definition = new Definition(
            WiremockAwareInitializer::class,
            [
                new Reference('wiremock.client'),
                $container->getParameter('wiremock.mapping_path'),
                $container->getParameter('wiremock.preload_mappings'),
            ]
        );
        $definition->addTag(ContextExtension::INITIALIZER_TAG, ['priority' => 0]);
        $container->setDefinition('wiremock.context_initializer', $definition);
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
    }
}
