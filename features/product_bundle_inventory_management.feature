@bundled_product
Feature: Stock of bundled products gets updated
  In order to be sure that bundled products stock gets updated
  As an Administrator
  I want to be able to see correct stock of bundled products after purchasing a bundle

  Background:
    Given update bundled products stock environmental variable is set to "1"
    And the store operates on a single channel in "United States"

    And the store has a product "Jack Daniels"
    And "Jack Daniels" product is tracked by the inventory
    And there are 10 units of product "Jack Daniels" available in the inventory

    And the store has a product "Johny Walker"
    And "Johny Walker" product is tracked by the inventory
    And there are 10 units of product "Johny Walker" available in the inventory

    And the store has bundled product "Whiskey double pack" priced at "$18.00" which contains "Jack Daniels" and "Johny Walker"
    And "Whiskey double pack" product is tracked by the inventory
    And there are 10 units of product "Whiskey double pack" available in the inventory

    And the store ships everywhere for Free
    And the store allows paying Offline
    And there is a customer "jane.doe@example.com" that placed an order "#00009516"
    And I am logged in as an administrator

  @ui
  Scenario: Holding bundled products inventory units
    Given the customer bought 3 "Whiskey double pack" products
    And bundled products are bound to the order
    And the customer chose "Free" shipping method with "Offline" payment

    When I view variants of the product "Whiskey double pack"
    Then 1 units of the product "Whiskey double pack" should be on hold
    And 10 units of the product "Whiskey double pack" should be on hand

    When I view variants of the product "Jack Daniels"
    Then 1 units of the product "Jack Daniels" should be on hold
    And 10 units of the product "Jack Daniels" should be on hand

    When I view variants of the product "Johny Walker"
    Then 1 units of the product "Johny Walker" should be on hold
    And 10 units of the product "Johny Walker" should be on hand
