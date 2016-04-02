Feature: Todo List
    In order to progress on project
    As a developer
    I need to track my tasks

Scenario: Adding tasks

    Given "Tasks" tab is open
    Then I start typing new task into textbox
    And save
    Then I enter description
    And save