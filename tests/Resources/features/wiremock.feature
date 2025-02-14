@wiremock-reset
Feature: Wiremock

  Scenario: Mappings
    Given the following services exist with mappings:
      | service | mapping  |
      | baz     | qux.json |
    When I am on "http://localhost:8080/path/to/awesome/method2"
    Then the response status code should be 200
    And the response should contain "{\"success\":true}"
    When I am on "http://localhost:8080/path/to/awesome/method"
    Then the response status code should be 201
    And the response should contain "{\"success\":true}"

  Scenario: Direct instrumentation
    Given a response is mocked
    When I am on "http://localhost:8080/path/to/awesome/method3"
    Then the response status code should be 200
    And the response should contain "{\"success\":true}"
