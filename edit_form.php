<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

/**
 * Edit form.
 * 
 * @package    block_forum_aggregator
 * @author     Tonis Tartes <t6nis20@gmail.com>
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_forum_aggregator_edit_form extends block_edit_form {
    
    protected function specific_definition($mform) {
        
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_title', get_string('configtitle', 'block_forum_aggregator'));
        $mform->setType('config_title', PARAM_TEXT);
        
        $forums = $this->get_course_forums();
        
        if (!empty($forums)) {
            
            foreach ($forums as $key => $value) {

                $mform->addElement('header', 'forum_settings', $value->name);
                
                $mform->addElement('advcheckbox', 'config_forum_id['.$value->id.']',  get_string('forum_selection', 'block_forum_aggregator'), '', array('group' => 1), array(0,1));
                $mform->addHelpButton('config_forum_id['.$value->id.']', 'forum_selection', 'block_forum_aggregator');
                
                $forum_description_html = html_writer::start_tag('div', array('class' => 'fitem')).
                                          html_writer::tag('div', get_string('forum_description', 'block_forum_aggregator'), array('class' => 'fitemtitle')).
                                          html_writer::tag('div', $value->intro, array('class' => 'felement fitemdescription')).
                                          html_writer::end_tag('div');
                
                $mform->addElement('html', $forum_description_html);
                
                $post_array = array();
                
                for ($i = 0; $i <= 25; $i++) {
                    $post_array[] = $i; 
                }
                
                $mform->addElement('select', 'config_max_posts['.$value->id.']',  get_string('posts', 'block_forum_aggregator'), $post_array);
                
                $mform->addHelpButton('config_max_posts['.$value->id.']', 'max_num_of_posts', 'block_forum_aggregator');
                
            }
            
        }   
        
    }
    
    // Get course forums.
    private function get_course_forums() {
        
        global $DB, $CFG, $COURSE, $USER;

        if ($forums = $DB->get_records_select("forum", "course = '$COURSE->id'")) {
            return $forums;
        }

    }
    
    // Get selected forum data by id.
    private function get_forum_by_id($forumid) {

        global $DB, $CFG, $COURSE, $USER;

        if ($forum = $DB->get_records_select("forum", "course = '$COURSE->id' AND id = '$forumid'")) {
            return $forum;
        }
    }
    
}