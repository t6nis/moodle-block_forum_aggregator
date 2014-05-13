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
/*
 * @package    block
 * @subpackage forum_aggregator
 * @author     Tõnis Tartes <t6nis20@gmail.com>
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_forum_aggregator extends block_base {
    
    public function init() {
        $this->title = get_string('blocktitle', 'block_forum_aggregator');
    }
    
    public function specialization() {

        global $CFG, $USER, $COURSE;

        if (!empty($this->config->title)) {
            $this->title = $this->config->title;
        }

    }

    function has_config() {
        return true;
    }
    
    public function instance_allow_multiple() {
        return true;
    }
    
    public function instance_allow_config() {
        return true;
    }
    
    public function get_content() {
        
        global $DB, $CFG, $USER, $COURSE, $OUTPUT;

        //Include needed libraries
        require_once($CFG->dirroot.'/mod/forum/lib.php'); 
        
        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';
        
        $text = '';

        $modinfo = get_fast_modinfo($COURSE); //course mod data

        if (!empty($this->config->forum_id)) {

            foreach ($this->config->forum_id as $key => $value) {

                    //if forum not been made available
                    if ($value == 0) {
                        continue;
                    }
                    
                    if (empty($modinfo->instances['forum'][$key])) {
                        //maybe someone deleted a forum? then skip that value..
                        continue;
                    }
                    
                    $max_posts = '';
                    //if post in array get the maxpost value
                    if (array_key_exists($key, $this->config->max_posts)) {
                        if ($this->config->max_posts[$key] > 0) {
                            $max_posts = $this->config->max_posts[$key];
                        } else {
                            continue;
                        }
                    }
                    
                    $cm = $modinfo->instances['forum'][$key];

                    $context = context_module::instance($cm->id);

                    $strftimerecent = get_string('strftimerecent');
                    $strmore = get_string('more', 'forum');
                    
                    //if visible
                    if ($cm->visible == 1) {
                        
                        if (! $forum = $DB->get_record("forum", array("id" => $key))) {
                            print_error('invalidforumid', 'forum');
                        }

                        //show list                        
                        $text .= html_writer::start_tag('ul', array('class'=> 'unlist'));
                        $text .= html_writer::tag('li', html_writer::link(new moodle_url('/mod/forum/view.php?id='.$cm->id), $cm->name), array('class' => 'forum_title'));
                        
                        $allnames = get_all_user_name_fields(true, 'u');
                        $posts = $DB->get_records_sql('SELECT d.id, p.*, '.$allnames.', u.email, u.picture, u.imagealt
                                            FROM {forum_discussions} d
                                            LEFT JOIN {forum_posts} p ON p.discussion = d.id
                                            LEFT JOIN {user} u ON p.userid = u.id
                                            WHERE d.forum = "'.$key.'"
                                            ORDER BY p.modified DESC LIMIT 0, '.$max_posts.'');
                        
                        if (!empty($posts)) {
                            
                            foreach ($posts as $post) {
                                
                                $post_style = array('class' => 'post');
                                if ($forum->trackingtype == FORUM_TRACKING_ON) {
                                    if (!forum_tp_is_post_read($USER->id, $post)) {
                                        $post_style = array('class' => 'post', 'style' => 'background-color: '.get_config('block_forum_aggregator', 'unread_post_color'));                                    
                                    }
                                }
                                
                                $post->message = format_string($post->message, true, $COURSE->id);                        
                                $post->message = shorten_text($post->message, 80, true, '');

                                $user = $DB->get_record('user', array('id'=>$post->userid), '*', MUST_EXIST);
                                
                                $text .= html_writer::start_tag('li', $post_style).
                                         html_writer::start_tag('div', array('class' => 'head')).
                                         html_writer::tag('div', $post->subject, array('class' => 'subject')).
                                         html_writer::tag('div', $OUTPUT->user_picture($user, array('size'=>21, 'class'=>'userpostpic')), array('class' => 'userpic')).
                                         html_writer::tag('div', fullname($post), array('class' => 'name')).
                                         html_writer::tag('div', get_string('posted', 'block_forum_aggregator').userdate($post->modified, $strftimerecent), array('class' => 'date')).
                                         html_writer::end_tag('div').
                                         html_writer::start_tag('div').
                                         $post->message.' '.
                                         html_writer::link(new moodle_url('/mod/forum/discuss.php?d='.$post->discussion.'#p'.$post->id), $strmore.'...' , array('class' => 'postreadmore')).
                                         html_writer::end_tag('div').
                                         html_writer::end_tag('li');
                            }
                        } else {
                            $text .= html_writer::tag('li', '('.get_string('noposts', 'block_forum_aggregator').')', array('class' => 'no_posts'));
                        }
                        $text .= html_writer::end_tag('ul');
                    }
            }
            
        }
        
        $this->content->text = $text;
        
        return $this->content;
    }
    
    public function instance_config_save($data, $nolongerused = false) {

        global $CFG;

        // Default behavior: save all variables as $CFG properties
        if(get_config('forum_aggregator', 'Allow_HTML') == '1') {
            $data->title = strip_tags($data->title);
        }

        return parent::instance_config_save($data, $nolongerused);
    }
}

?>
