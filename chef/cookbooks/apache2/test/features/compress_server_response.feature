@default @mod_deflate
Feature: Compress server response

In order to reduce the time taken to retrieve web pages
As a developer
I want to enable compression on server responses

  Scenario: Deflate compression
    Given a new webserver with deflate compression enabled
     When the browser requests a page specifying that it supports compression
     Then the response will be sent compressed

  Scenario: Deflate compression (no client support)
    Given a new webserver with deflate compression enabled
     When the browser requests a page specifying that it does not support compression
     Then the response will be sent uncompressed
