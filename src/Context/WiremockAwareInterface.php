<?php
/**
 * @author Vitalii Piskovyi <vitalii.piskovyi@gmail.com>
 */
namespace VPX\WiremockExtension\Context;

use WireMock\Client\WireMock;

interface WiremockAwareInterface
{
    public function setWiremock(WireMock $wiremock);

    public function getWiremock(): WireMock;
}
