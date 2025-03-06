# customfield_courselist/tests/behat/field.feature
@core @core_customfield
Feature: Courselist custom field functionality
  In order to use courselist custom fields
  As an admin
  I need to create and manage courselist fields with course images

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | admin    | Admin     | User     |
    And I log in as "admin"

  Scenario: Create a courselist custom field with a course image
    Given I navigate to "Courses > Custom fields" in site administration
    And I press "Add a new custom field"
    And I set the following fields to these values:
      | Field type | Courselist     |
      | Name       | My Courselist  |
      | Short name | mycourselist   |
    And I upload "lib/tests/fixtures/empty.txt" file to "Course image" filemanager
    And I press "Save changes"
    Then I should see "My Courselist" in the "Custom fields" "table"

  Scenario: View courselist field in a course
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "custom fields" exist:
      | name         | shortname    | type      | category     | configdata                      |
      | My Courselist| mycourselist | courselist| Default      | {"course_image":"draft_123"}    |
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I follow "Course 1"
    And I follow "Edit settings"
    Then I should see "My Courselist"
    And I should see "Course image" in the "My Courselist" "fieldset"