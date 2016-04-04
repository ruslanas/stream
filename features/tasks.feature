# features/tasks.feature

Feature: Todo List
	In order to progress on project
	As a developer
	I need to track my tasks

	@completed
	Scenario: Adding task
		Given tab "Tasks" is open
		When I create new task memo
		Then I fill in "Smile" into textbox
		And hit "Save"
		When edit form opens
		Then I fill in "Often" into textarea
		And hit "Done"
		Then task appears in the list

	@completed
	Scenario: Closing
		Given there is a task with a title "Test task"
		Then I find a task with a title "Test task"
		And call RESTful "DELETE"
