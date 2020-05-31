Feature: Lookup
  In order to have an awesome PHP UI Framework
  As a responsible open-source developer
  I need to write tests for our demo pages

  Scenario:
    Given I am on "autocomplete.php"
    Then I select value "Albania" in lookup "country1"
    Then I select value "Algeria" in lookup "country2"
    Then I select value "Algeria" in lookup "country3"
    And I press button "Save"
    And form submits
    Then I should see "Select:"
    And I should see "Albania Algeria Algeria"
