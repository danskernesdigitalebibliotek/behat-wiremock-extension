<?php
/**
 * @author Vitalii Piskovyi <vitalii.piskovyi@gmail.com>
 */
namespace VPX\WiremockExtension\Context;

use WireMock\Client\WireMock;

class WiremockAwareContext implements WiremockAwareContextInterface
{
    /**
     * @var \WireMock\Client\WireMock
     */
    private $wiremock;

    public function setWiremock(WireMock $wiremock)
    {
        $this->wiremock = $wiremock;
    }

    public function getWiremock(): WireMock
    {
        return $this->wiremock;
    }
}
