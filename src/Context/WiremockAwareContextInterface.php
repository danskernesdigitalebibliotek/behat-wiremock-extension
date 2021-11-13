<?php
/**
 * @author Vitalii Piskovyi <vitalii.piskovyi@gmail.com>
 */
namespace VPX\WiremockExtension\Context;

use WireMock\Client\WireMock;

interface WiremockAwareContextInterface
{
    public function setWiremock(WireMock $wiremock);

    public function getWiremock(): WireMock;
}
