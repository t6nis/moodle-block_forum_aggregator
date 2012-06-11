<?php
/*
 * Forum Block 
 * 21.03.2011 by T6nis
 * Allows teacher to choose between different forums to show in block.
 * Can add custom title and select how many latest posts to be shown.
 */
class block_forum_block extends block_base {

    function init() {

        $this->title = get_string('blocktitle', 'block_forum_block');
        $this->version = 2007101509;
        
    }
    
    function specialization() {

        global $CFG, $USER, $COURSE;

        if (!empty($this->config->title)) {
            $this->title = $this->config->title;
        }
        
    }

    function instance_allow_config() {
        return true;
    }

    function get_content() {

        global $CFG, $USER, $COURSE;

        //Include needed libraries
        require_once($CFG->dirroot.'/mod/forum/lib.php'); 
        require_once($CFG->dirroot.'/blocks/forum_block/lib.php');

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        $text = '';

        $modinfo = get_fast_modinfo($COURSE); //course mod data
        
        if (!empty($this->config->forum_id)) {

            foreach ($this->config->forum_id as $key => $value) {
                
                    if (empty($this->config->forum_id)) {
                        return '';
                    } else if (empty($modinfo->instances['forum'][$value])) {
                        //maybe someone deleted a forum? then skip that value..
                        continue;
                    }
                    //if post in array get the maxpost value
                    if (array_key_exists($value, $this->config->max_posts)) {
                        $max_posts = $this->config->max_posts[$value];
                    }
                    
                    $cm = $modinfo->instances['forum'][$value];

                    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

                    $strftimerecent = get_string('strftimerecent');
                    $strmore = get_string('more', 'forum');
                    
                    //if visible
                    if ($cm->visible == 1) {
                        
                        //show list
                        $text .= "\n<ul class='unlist'>\n";
                        $text .= '<li class="forum_title">'.$cm->name.'</li>';
                        if ( $discussions = forum_get_discussions($cm, 'p.modified DESC', false, -1, $max_posts ) ) {
                            foreach ($discussions as $discussion) {

                                $discussion->subject = $discussion->name;

                                $discussion->subject = format_string($discussion->subject, true, $COURSE->id);

                                $text .= '<li class="post">'.
                                         '<div class="head">'.
                                         '<div class="date">'.userdate($discussion->modified, $strftimerecent).'</div>'.
                                         '<div class="name">'.fullname($discussion).'</div></div>'.
                                         '<div>'.$discussion->subject.' '.
                                         '<a href="'.$CFG->wwwroot.'/mod/forum/discuss.php?d='.$discussion->discussion.'">'.
                                         $strmore.'...</a></div>'.
                                         "</li>\n";

                            }
                        } else {
                            $text .= '<li class="no_posts">('.get_string('noposts', 'block_forum_block').')</li>';
                        }
                        $text .= "</ul>\n";
                    }
            }
            
        }
        
        $this->content->text = $text;

        return $this->content;

    }

    function instance_config_save($data) {

        global $CFG;
        // Default behavior: save all variables as $CFG properties
        if(!empty($CFG->block_simplehtml_strict)) {
            $data->title = strip_tags($data->title);
        }

        return parent::instance_config_save($data);
    }

}
?>