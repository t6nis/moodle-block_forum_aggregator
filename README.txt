Forum Aggregator by Tõnis Tartes since 2011

This works for Moodle 2.x version.

Customized forum block, which allows teacher to select from which forums are latest posts shown on course page.
This block is an alternative for Recent News block which is tied to News Forum.

    Includes all forums used in course
    Teacher can select one or more forums to be shown
    Can select number of latest posts to be shown(max 25)

This block makes no DB changes!
//13.05.2014 - If forum tracking is FORCED then unread posts are shown with different background color. 
Background color can be set in global block config. Thank you Thomas Bantle!

//12.02.2014 - Moodle 2.6 release(2014021200). Please note this version will not work below 2.6!!!
If you want to use this block below Moodle 2.6 then please download from this branch:
https://github.com/t6nis/moodle-block_forum_aggregator/tree/2013091300

//09.13.2013 - Refactored code and now showing the latest posts foreach selected forum. 
Before it showed the latest discussions(first posts) only. 

//06.05.2013 - Changed the view of posts in block

//19.10.2012 - Fixed a bug, related to showing different count of posts. Thank you Thomas Bantle!

//04.07.2012 - Minor lang fixes

//10.06.2012 - There were some messages popping up in Moodle 2.3, when debugging was enabled. Fixed it. 

//11.06.2012 - Updating Forum Block to Forum Aggregator. Removing old entries from git.