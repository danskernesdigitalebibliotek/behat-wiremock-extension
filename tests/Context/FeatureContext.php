<?php

namespace Tests\VPX\WiremockExtension\Context;

use VPX\WiremockExtension\Context\WiremockAware;
use VPX\WiremockExtension\Context\WiremockAwareInterface;
use WireMock\Client\WireMock;

class FeatureContext implements WiremockAwareInterface {
    use WiremockAware;

    /**
     * @Given a response is mocked
     */
    public function assertMockedResponse() {
        $this->getWiremock()->stubFor(
            WireMock::get(
                WireMock::urlPathEqualTo("/path/to/awesome/method3")
            )
                ->willReturn(WireMock::aResponse()
                    ->withBody(json_encode([
                    "success" => true
                ]))
            )
        );
    }
}
