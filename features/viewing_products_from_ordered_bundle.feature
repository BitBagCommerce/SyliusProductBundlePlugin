@bundled_product
Feature: Reviewing products from ordered bundle
  As a Customer
  I want to be able to see products in the bundle I ordered

  Background:
    Given the store operates on a single channel in "United States"
    And the store ships everywhere for Free
    And the store allows paying Offline
    And the store has a product "Jim Beam" priced at "$10.00"
    And the store has a product "Coca-Cola" priced at "$5.00"
    And the store has bundled product "Jim Beam&Coke" priced at "$12.00" which contains "Jim Beam" and "Coca-Cola"
    And all store products appear under a main taxonomy
    And I am a logged in customer with name "Bundle Customer"

  @ui @shop
  Scenario: Viewing bundled products in cart
    Given I have product "Jim Beam&Coke" in the cart
    When I see the summary of my cart
    Then there should be one item in my cart
    And this item should have name "Jim Beam&Coke"
    And there should be bundled products listed
    And the list should contain "Jim Beam"
    And the list should contain "Coca-Cola"

  @ui @shop
  Scenario: Viewing bundled products in order history
    Given there is a customer "sylius@example.com" that placed an order "#1" later
    And the customer bought a single bundle "Jim Beam&Coke"
    And I addressed it to "Ankh Morpork", "Frost Alley", "90210" "Los Angeles" in the "United States"
    And for the billing address of "Ankh Morpork" in the "Frost Alley", "90210" "Los Angeles", "United States"
    And I chose "Free" shipping method with "Offline" payment
    When I view the summary of my order "#1"
    Then it should have the number "#1"
    And there should be bundled products listed in order details
    And the list should contain "Jim Beam" in order details
    And the list should contain "Coca-Cola" in order details
