# features/tasks.feature

Feature: Todo List
	In order to progress on project
	As a developer
	I need to track my tasks

	@wip @javascript
	Scenario: Adding task
		Given I am on "/tasks"
		When I fill in "title" with "Smile"
		And I press "Save"
		Then I should see "Smile"

	@completed
	Scenario: Closing
		Given there is a task with a title "Test task"
		Then I find a task with a title "Test task"
		And call RESTful "DELETE"
