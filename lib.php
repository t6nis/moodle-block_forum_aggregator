<?php
/*
 * Forum Block Library functions go here
 * 21.03.2011 by T6nis
 */
//get all forums used in course
function get_course_forums() {

    global $CFG, $COURSE, $USER;
  
    if ($forums = get_records_select("forum", "course = '$COURSE->id'", "id ASC")) {
        return $forums;
    }

}

//get selected forum data by id
function get_forum_by_id($forumid) {

    global $CFG, $COURSE, $USER;

    if ($forum = get_records_select("forum", "course = '$COURSE->id' AND id = '$forumid'", "id ASC")) {
        return $forum;
    }
}

?>
