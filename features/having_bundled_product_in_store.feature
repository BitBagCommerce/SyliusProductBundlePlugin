@bundled_product
Feature: Having a product in store which is a bundle of other products
    I want to be able to add bundled product to cart

    Background:
      Given the store operates on a single channel in "United States"
      And I am a logged in customer
      And the store ships everywhere for Free
      And the store allows paying Offline

    @ui
    Scenario: Adding a product bundle to the cart
      Given the store has a product "Jack Daniels Gentleman" priced at "$10.00"
      And the store has a product "Johny Walker Black" priced at "$10.00"
      And the store has bundled product "Whiskey double pack" priced at "$18.00" which contains "Jack Daniels Gentleman" and "Johny Walker Black"
      And all store products appear under a main taxonomy
      Then I added product "Whiskey double pack" to the cart
      And I should be on my cart summary page
      And there should be one item in my cart

    @ui
    Scenario: Adding a few product bundles to the cart
      Given the store has a product "Jim Beam" priced at "$10.00"
      And the store has a product "Jim Beam Double Oak" priced at "$10.00"
      And the store has bundled product "Jim Beam double pack" priced at "$18.00" which contains "Jim Beam" and "Jim Beam Double Oak"
      And all store products appear under a main taxonomy
      Then I added product "Jim Beam double pack" to the cart
      And I change product "Jim Beam double pack" quantity to 5 in my cart
      And I should see "Jim Beam double pack" with quantity 5 in my cart

    @ui
    Scenario: Placing an order for a bundled product
      Given the store has a product "Jim Beam" priced at "$10.00"
      And the store has a product "Jim Beam Double Oak" priced at "$10.00"
      And the store has bundled product "Jim Beam double pack" priced at "$18.00" which contains "Jim Beam" and "Jim Beam Double Oak"
      Given I have product "Jim Beam double pack" in the cart
      And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
      And I proceed with "Free" shipping method and "Offline" payment
      And I confirm my order
      Then I should see the thank you page
