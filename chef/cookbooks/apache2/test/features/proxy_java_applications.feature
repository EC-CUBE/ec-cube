@java
Feature: Proxy Java applications

In order to host dynamic websites
As a developer
I want be able to proxy requests to a Java application

  @mod_proxy_ajp
  Scenario: Proxy Java application server
    Given a new webserver with support for proxying to Java application servers enabled
    When a request is made to a Java application that generates a list of request parameters
    Then the expected request parameters will be present
