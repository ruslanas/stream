Feature: Access Control List

    As administrator
    In order to keep system secure
    I need fine grained control of user permission

    Scenario Outline: Allow action

        Given user <user_id> registered at "basic" privilege level
        When <user_id> can <action>
        Then allow
        When <user_id> can <action> not
        Then throw exception

        Examples:

            # visitors (not logged in) can only view certain pages
            # user cannot update user_id [fixed]
            # user can only delete his tasks
            # 


            | name | permission | controller | action   | user_id |
            | tom  | 1          | tasks      | delete   | 1       |
            | tom  | 0          | tasks      | create   | 1       |
            | tom  | 1          | tasks      | delegate | 1       |
            | tom  | 0          | users      | register | 1       |
