@basic_web_app
Feature: Deploy basic webapp

In order to run my application
As a developer
I want to deploy a basic web application

  Scenario: Deploy basic webapp
    Given a new webserver
     When I request the root path of the webapp
     Then the webapp default page will be returned
