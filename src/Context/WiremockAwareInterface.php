<?php
/**
 * @author Vitalii Piskovyi <vitalii.piskovyi@gmail.com>
 */
namespace VPX\WiremockExtension\Context;

use Behat\Behat\Context\Context;
use WireMock\Client\WireMock;

interface WiremockAwareInterface extends Context
{
    public function setWiremock(WireMock $wiremock);

    public function getWiremock(): WireMock;
}
