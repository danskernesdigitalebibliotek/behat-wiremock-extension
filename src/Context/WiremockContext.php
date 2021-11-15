<?php
/**
 * @author Vitalii Piskovyi <vitalii.piskovyi@gmail.com>
 */
namespace VPX\WiremockExtension\Context;

use Behat\Behat\Context\Context as ContextInterface;
use Behat\Behat\Definition\Call\Given;
use Behat\Behat\Hook\Call\BeforeScenario;
use Behat\Gherkin\Node\TableNode;
use VPX\WiremockExtension\Exception\WiremockException;
use WireMock\Client\MappingBuilder;
use WireMock\Stubbing\StubImportBuilder;
use WireMock\Stubbing\StubMapping;

class WiremockContext implements WiremockAwareInterface, ContextInterface
{
    use WiremockAware;

    /**
     * @var string
     */
    private $mappingPath;

    /**
     * @var array
     */
    private $preloadMappings;

    public function setMappingPath(string $mappingPath)
    {
        $this->mappingPath = $mappingPath;
    }

    public function setPreloadMappings(array $preloadMappings)
    {
        $this->preloadMappings = $preloadMappings;
    }

     /**
     * @Given the following services exist with mappings:
     */
    public function theFollowingServicesExistWithMappings(TableNode $tableNode)
    {
        $this->loadMappings($tableNode->getHash());
    }

    /**
     * @Given :service exists with mapping :mapping
     */
    public function serviceExistsWithWithMapping(string $service, string $mapping)
    {
        $this->addMappingForService($mapping, $service);
    }

    /**
     * @BeforeScenario @wiremock-reset
     */
    public function resetWiremock()
    {
        $this->getWiremock()->resetToDefault();

        $this->loadMappings($this->preloadMappings);
    }

    public function loadMappings(array $mappings)
    {
        array_map(function (array $mapping) {
            if (!isset($mapping['service']) || !isset($mapping['mapping'])) {
                throw new \UnexpectedValueException('You must provide a `service` and `mapping` entry for each mapping.');
            }

            $this->addMappingForService($mapping['mapping'], $mapping['service']);
        }, $mappings);
    }

    public function addMappingForService(string $mapping, string $service)
    {
        $path = sprintf(
            '%s/%s/%s',
            rtrim($this->mappingPath, '/'),
            $service,
            ltrim($mapping, '/')
        );

        if (!is_file($path)) {
            throw new \RuntimeException(sprintf('Mapping file `%s` does not exist.', $path), 404);
        }

        $content = file_get_contents($path);


        if (empty($content)) {
            throw new \RuntimeException(sprintf('Mapping file `%s` is empty.', $path), 409);
        }

        $builder = (new StubImportBuilder())
            ->stub(StubMapping::fromArray(json_decode($content, true)));
        $this->getWiremock()->importStubs($builder);
    }
}
