# behat-wiremock-extension

[![Build Status](https://github.com/kasperg/behat-wiremock-extension/actions/workflows/tests.yml/badge.svg)](https://github.com/kasperg/behat-wiremock-extension/actions/workflows/tests.yml)

This is an extension which integrates [Wiremock](http://wiremock.org/), a 
simulator for HTTP-based APIs, with [Behat](https://docs.behat.org/en/latest/), 
a Behavior-Driven Development framework for PHP.

The extension lets developers instrument Wiremock and control which responses 
should be returned for what requests using the [Wiremock admin API](http://wiremock.org/docs/api/).

The extension was originally developed by [Vitalii Piskovyi](https://github.com/vpx)
and has since been modernized by [Det Digitale Folkebibliotek](https://github.com/danskernesdigitalebibliotek/), 
resort for applications in the national infrastructure for libraries in Denmark.

## Usage

### Configuration

```yaml
default:
  extensions:
    VPX\WiremockExtension\ServiceContainer\WiremockExtension:
        # Url to Wiremock
        base_url: http://localhost:8080
        # Base path for mapping files
        mapping_path: "%paths.base%/tests/Resources/fixtures"
        # Mappings which must be loaded automatically for every test
        preload_mappings:
          - { service: foo, mapping: bar.json }
  suites:
    default:
      contexts:
        - VPX\WiremockExtension\Context\WiremockContext
```

### Mappings

Developers can provide a JSON file containing [mappings of requests and 
responses](http://wiremock.org/docs/stubbing/#basic-stubbing) to instrument 
Wiremock.

```gherkin
# app.feature
@wiremock-reset
Feature: Wiremock

  Scenario: Trying to load /path/to/awesome/method2
    Given the following services exist with mappings:
      | service | mapping  |
      | baz     | qux.json |
    When I am on "http://localhost:8080/path/to/awesome/method2"
    Then the response status code should be 200
    And the response should contain "{\"success\":true}"
    When I am on "http://localhost:8080/path/to/awesome/method"
    Then the response status code should be 201
    And the response should contain "{\"success\":true}"
```

### Direct instrumentation

Developers can instrument Wiremock in a context by implementing the
`WiremockAwareInterface` interface and using the `WiremockAware` trait.

Instrumentation features are provided through an instance of the Wiremock client
provided by the [`wiremock-php`](https://github.com/rowanhill/wiremock-php) 
library.

```php
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
```
