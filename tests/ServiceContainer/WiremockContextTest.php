<?php
/**
 * @author Vitalii Piskovyi <vitalii.piskovyi@gmail.com>
 */
namespace Tests\VPX\ServiceContainer;

use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use PHPUnit\Framework\TestCase;
use VPX\WiremockExtension\Context\WiremockContext;
use WireMock\Client\WireMock;
use WireMock\Stubbing\StubImportBuilder;
use WireMock\Stubbing\StubMapping;

class WiremockContextTest extends TestCase
{
    /**
     * @var \WireMock\Client\WireMock|\PHPUnit\Framework\MockObject\MockObject
     */
    private $wireMockMock;

    /**
     * @var \VPX\WiremockExtension\Context\WiremockContext
     */
    private $wiremockContext;

    /**
     * @dataProvider mappingDataProvider
     *
     * @param array $mappings
     */
    public function testLoadMappings(array $mappings)
    {
        $this->mockAddMappingWithFile($this->at(0), '/foo/bar.json');

        $this->wiremockContext->loadMappings($mappings);
    }

    public function testAddMappingWithWrongParameters()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('You must provide a `service` and `mapping` entry for each mapping.');

        $this->wiremockContext->loadMappings([ ['foo', 'bar'] ]);
    }

    public function testAddMappingWithWrongFile()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf('Mapping file `%s` does not exist.', $this->getMappingsPath() . '/bar/foo')
        );

        $this->wiremockContext->addMappingForService('foo', 'bar');
    }

    public function testAddMappingWithEmptyContent()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(sprintf('Mapping file `%s` is empty.', $this->getMappingsPath() . '/foo/empty.json'));

        $this->wiremockContext->addMappingForService('empty.json', 'foo');
    }

    public function testResetMappings()
    {
        $this->mockResetAction($this->at(0));

        $this->mockAddMappingWithFile($this->at(1), '/baz/qux.json');

        $this->wiremockContext->resetWiremock();
    }

    /**
     * @return array
     */
    public function mappingDataProvider(): array
    {
        return [
            [
                [
                    [
                        'service' => 'foo',
                        'mapping' => 'bar.json',
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->wireMockMock = $this->getMockBuilder(WireMock::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->wiremockContext = new WiremockContext();
        $this->wiremockContext->setWiremock($this->wireMockMock);
        $this->wiremockContext->setMappingPath($this->getMappingsPath());
        $this->wiremockContext->setPreloadMappings([
            [
                'service' => 'baz',
                'mapping' => 'qux.json',
            ],
        ]);
    }

    private function mockAddMappingWithFile(InvocationOrder $expectation, string $path)
    {
        $stub = (new StubImportBuilder())
            ->stub(
                StubMapping::fromArray(
                    json_decode(
                        file_get_contents($this->getMappingsPath() . $path),
                        true
                    )
                )
            );
        $this->wireMockMock
            ->expects($expectation)
            ->method('importStubs')
            ->with($stub)
            ->willReturn(NULL);
    }

    private function mockResetAction(InvocationOrder $expectation)
    {
        $this->wireMockMock
            ->expects($expectation)
            ->method('resetToDefault')
            ->willReturn(NULL);
    }

    /**
     * @return string
     */
    private function getMappingsPath(): string
    {
        return __DIR__ . '/../Resources/fixtures';
    }
}
