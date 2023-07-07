@bundled_product
Feature: Creating a product in store which is a bundle of other products
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
