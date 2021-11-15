<?php
/**
 * @author Vitalii Piskovyi <vitalii.piskovyi@gmail.com>
 */
namespace VPX\WiremockExtension\Context\Initializer;

use Behat\Behat\Context\Context as ContextInterface;
use Behat\Behat\Context\Initializer\ContextInitializer;
use VPX\WiremockExtension\Context\WiremockAwareInterface;
use VPX\WiremockExtension\Context\WiremockContext;
use WireMock\Client\WireMock;

class WiremockAwareInitializer implements ContextInitializer
{
    /**
     * @var \WireMock\Client\WireMock
     */
    private $wiremock;

    /**
     * @var string
     */
    private $mappingPath;

    /**
     * @var array
     */
    private $preloadMappings;

    public function __construct(WireMock $wiremock, string $mappingPath, array $preloadMappings)
    {
        $this->wiremock = $wiremock;
        $this->mappingPath = $mappingPath;
        $this->preloadMappings = $preloadMappings;
    }

    /**
     * {@inheritdoc}
     */
    public function initializeContext(ContextInterface $context)
    {
        if ($context instanceof WiremockAwareInterface) {
            $context->setWiremock($this->wiremock);
        }
        if ($context instanceof WiremockContext) {
            $context->setMappingPath($this->mappingPath);
            $context->setPreloadMappings($this->preloadMappings);
        }
    }
}
