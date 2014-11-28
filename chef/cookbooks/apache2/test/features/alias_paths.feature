Feature: Alias Paths

In order to host a website with the URL structure different to the filesystem structure
As a developer
I want to be able to alias paths

  @default @mod_alias
  Scenario: Aliased directory
    Given a new webserver with aliasing enabled
      And an alias defined
     When I request the alias path
     Then the aliased resource should be returned successfully
