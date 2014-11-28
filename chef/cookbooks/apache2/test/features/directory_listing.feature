@default @mod_autoindex
Feature: Directory listing

In order to allow browsing of the webserver filesystem
As a developer
I want to enable directory listing

  Scenario: View directory listing
    Given a new webserver with directory listing enabled
      And a path configured to allow directory listing
     When I request the directory listing path
     Then the directory listing should be returned successfully

  Scenario: Re-order files listed
    Given a new webserver with directory listing enabled
      And a path configured to allow directory listing with fancy indexing
     When I request the directory listing path
     Then the directory listing should be returned successfully
      And I will be able to sort the files by size
