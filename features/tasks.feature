# features/tasks.feature

Feature: Todo List
    In order to progress on project
    As a developer
    I need to track my tasks

Scenario: Adding task

    Given tab "Tasks" is open
    When I create new task memo
    Then I fill in "Smile" into textbox
    And hit "Save"
    When edit form opens
    Then I fill in "Often" into textarea
    And hit "Done"
