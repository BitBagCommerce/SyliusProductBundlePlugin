@bundled_product
Feature: Creating a product in store which is a bundle of other products
  As an Administrator
  I want to be able to add bundled product to cart

  Background:
    Given the store operates on a single channel in "United States"
    And I am logged in as an administrator
    And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    And the store has a product "Johny Walker Black" priced at "$10.00"
    And the store has a product "Jim Beam Double Oak" priced at "$10.00"

  @ui @javascript
  Scenario: Creating a bundled product
    When I want to create a new bundled product
    And I specify its code as "WHISKEY_PACK"
    And I name it "Whiskey double pack" in "English (United States)"
    And I set its slug to "whiskey-double-pack" in "English (United States)"
    And I set its price to "$10.00" for "United States" channel
    And I set its original price to "$20.00" for "United States" channel
    And I add product "Johny Walker Black" and "Jack Daniels Gentleman" to the bundle
    And I add it
    Then I should be notified that it has been successfully created

  @ui @javascript
  Scenario: Creating a bundled product with more products
    When I want to create a new bundled product
    And I specify its code as "WHISKEY_BIG_PACK"
    And I name it "Whiskey triple pack" in "English (United States)"
    And I set its slug to "whiskey-triple-pack" in "English (United States)"
    And I set its price to "$10.00" for "United States" channel
    And I set its original price to "$20.00" for "United States" channel
    And I add product "Johny Walker Black" and "Jack Daniels Gentleman" and "Jim Beam Double Oak" to the bundle
    And I add it
    Then I should be notified that it has been successfully created
    And there should be a "WHISKEY_BIG_PACK" bundle containing "Johny Walker Black" with quantity 1
    And there should be a "WHISKEY_BIG_PACK" bundle containing "Jack Daniels Gentleman" with quantity 1
    And there should be a "WHISKEY_BIG_PACK" bundle containing "Jim Beam Double Oak" with quantity 1

  @ui @javascript
  Scenario: Creating a bundled product with higher quantity
    When I want to create a new bundled product
    And I specify its code as "WHISKEY_BIG_PACK"
    And I name it "Whiskey triple pack" in "English (United States)"
    And I set its slug to "whiskey-triple-pack" in "English (United States)"
    And I set its price to "$10.00" for "United States" channel
    And I set its original price to "$20.00" for "United States" channel
    And I add product "Jim Beam Double Oak" with quantity 5 and "Jack Daniels Gentleman" with quantity 2 to the bundle
    And I add it
    Then I should be notified that it has been successfully created
    And there should be a "WHISKEY_BIG_PACK" bundle containing "Jim Beam Double Oak" with quantity 5
    And there should be a "WHISKEY_BIG_PACK" bundle containing "Jack Daniels Gentleman" with quantity 2
