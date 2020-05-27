@bundled_product
Feature: Having a product in store which is a bundle of other products
    I want to be able to add bundled product to cart

    Background:
      Given the store operates on a single channel in "United States"
      Given the store has locale en_US

    @ui
    Scenario: Adding a product to cart
      Given I am a logged in customer
      And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
      And the store has a product "Johny Walker Black" priced at "$10.00"
      And the store has bundled product "Whiskey double pack" priced at "$18.00" which contains "Jack Daniels Gentleman" and "Johny Walker Black"
      And all store products appear under a main taxonomy
      Then I added product "Whiskey double pack" to the cart
      And I should be on my cart summary page
      And there should be one item in my cart
