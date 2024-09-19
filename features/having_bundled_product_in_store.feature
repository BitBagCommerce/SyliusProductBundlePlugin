@bundled_product
Feature: Having a product in store which is a bundle of other products
  As a Customer
  I want to be able to order bundled products

  Background:
    Given the store operates on a single channel in "United States"
    And the store has "VAT" tax rate of 50% for "Coke" within the "US" zone
    And I am a logged in customer
    And the store ships everywhere for Free
    And the store allows paying Offline
    And the store has a product "Jim Beam" priced at "$10.00"
    And this product has "Jim Beam 1L" variant priced at "$15.00" identified by "JIM_BEAM_1L"
    And the store has a product "Jim Beam Double Oak" priced at "$10.00"
    And the store has a product "Coca-Cola" priced at "$5.00"
    And the store has bundled product "Jim Beam double pack" priced at "$18.00" which contains "Jim Beam" and "Jim Beam Double Oak"
    And the store has bundled product "Jim Beam&Coke" priced at "$12.00" which contains "Jim Beam" and "Coca-Cola"
    And it belongs to "Coke" tax category
    And all store products appear under a main taxonomy

  @ui
  Scenario: Adding product bundles to cart
    When I added product "Jim Beam double pack" to the cart
    And I change product "Jim Beam double pack" quantity to 5 in my cart
    Then I should see "Jim Beam double pack" with quantity 5 in my cart
    And my cart total should be "$90.00"

  @ui
  Scenario: Placing an order for bundled products
    Given I have product "Jim Beam double pack" in the cart
    And I have product "Jim Beam&Coke" in the cart
    And my cart total should be "$30.00"
    And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
    And I proceed with "Free" shipping method and "Offline" payment
    When I confirm my order
    Then I should see the thank you page

  @ui
  Scenario: Placing an order for bundled products with promotion applied
    Given there is a promotion "Holiday promotion"
    And this promotion gives "$1.00" off on every product with minimum price at "$15.00"
    And I have product "Jim Beam double pack" in the cart
    And I have product "Jim Beam&Coke" in the cart
    And my cart total should be "$29.00"
    And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
    And I proceed with "Free" shipping method and "Offline" payment
    When I confirm my order
    Then I should see the thank you page

  @ui
  Scenario: Placing an order for bundled products with tax applied
    Given I have product "Jim Beam double pack" in the cart
    And I have product "Jim Beam&Coke" in the cart
    And I have product "Coca-Cola" in the cart
    When I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
    And I proceed with "Free" shipping method and "Offline" payment
    Then my cart total should be "$41.00"
    And my cart taxes should be "$6.00"

  @api
  Scenario: Adding product bundles to cart with API
    When I pick up my cart
    And I add bundle "Jim Beam&Coke" with quantity 5 to my cart
    And I add bundle "Jim Beam&Coke" with quantity 5 to my cart
    Then I should have bundle "Jim Beam&Coke" with quantity 10 in my cart
    And I should have product "Jim Beam" in bundled items
    And I should have product "Coca-Cola" in bundled items

  @api
  Scenario: Adding unpacked product bundles to cart and overwriting variants with API
    When I pick up my cart
    And I add bundle "Jim Beam&Coke" with quantity 5 to my cart and overwrite "JIM_BEAM" with "JIM_BEAM_1L"
    Then I should have bundle "Jim Beam&Coke" with quantity 5 in my cart
    And I should have product variant "JIM_BEAM_1L" in bundled items
    And I should not have product variant "JIM_BEAM" in bundled items
    And I should have product "Coca-Cola" in bundled items

  @api
  Scenario: Adding unpacked product bundles to cart and overwriting variants with invalid variant with API
    When I pick up my cart
    And I add bundle "Jim Beam&Coke" with quantity 5 to my cart and overwrite "COCA_COLA" with "JIM_BEAM_1L"
    Then I should have bundle "Jim Beam&Coke" with quantity 5 in my cart
    And I should not have product variant "JIM_BEAM_1L" in bundled items
    And I should have product variant "JIM_BEAM" in bundled items
    And I should have product "Coca-Cola" in bundled items

  @api
  Scenario: Adding packed product bundles to cart and overwriting varians with API
    Given product bundle "JIM_BEAM&COKE" is packed
    When I pick up my cart
    And I add bundle "Jim Beam&Coke" with quantity 5 to my cart and overwrite "JIM_BEAM" with "JIM_BEAM_1L"
    Then I should have bundle "Jim Beam&Coke" with quantity 5 in my cart
    And I should not have product variant "JIM_BEAM_1L" in bundled items
    And I should have product variant "JIM_BEAM" in bundled items
    And I should have product "Coca-Cola" in bundled items
