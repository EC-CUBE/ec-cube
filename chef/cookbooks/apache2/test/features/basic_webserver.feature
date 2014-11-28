@default
Feature: Serve web pages

In order to run my application
As a developer
I want to respond to website requests

  Scenario: Request homepage
    Given a new webserver
     When I request the root url
     Then the default page should be returned

  Scenario: Missing page
    Given a new webserver
     When I request a URL known not to exist
     Then page not found should be returned
