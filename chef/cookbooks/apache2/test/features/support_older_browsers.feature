@default @mod_setenvif
Feature: Support older browsers

  In order to be a good netizen
  As a developer
  I want to ensure that my server will respond to requests from older browsers

  Scenario: Support HTTP/1.0
    Given a new webserver
     When I request as a known browser that only supports HTTP/1.0
     Then the response should be HTTP/1.0 also
