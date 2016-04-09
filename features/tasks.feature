# features/tasks.feature

Feature: Tasks

	In order to progress on project
    As project owner
	I need to keep track of all tasks

    @completed
    Scenario: Delegation

        Given log in with email "behat@stream.wri.lt" and password "foo"
        And there is user with email "protractor@stream.wri.lt"
        And there is task with title "Delegate this task" and id "10"
        When open task with id "10"
        And can delegate
        Then delegate to "protractor@stream.wri.lt"
