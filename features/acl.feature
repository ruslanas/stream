Feature: Access Control List
    As admin
    I need to control access

    @completed
    Scenario: View list
        Given logged in as group "Admin" member
        Then call RESTfull "GET" endpoint "/acl/list"