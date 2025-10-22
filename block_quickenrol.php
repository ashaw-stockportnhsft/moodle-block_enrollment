<?php

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/blocks/quickenrol/lib.php');

class block_quickenrol extends block_base {

    function init() {
        //$this->title = get_string('pluginname', 'block_quickenrol');
		$this->title = 'Quick Links' ;
    }

    function instance_allow_multiple() {
        return false;
    }

    public function applicable_formats() {
        return array('all' => true);
    }

    function get_content() {
        global $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        // shortcut -  only for admins
		$systemcontext = context_system::instance();
        if (!isloggedin() || isguestuser() || !has_capability('moodle/user:editprofile', $systemcontext) ){
            return false;
        }
		
		//Patched block entries below to add img and padding so it displays correctly in v3.4.2+
        $this->content = new stdClass();
		$this->content->text = '';
        $this->content->footer = '';
        //$this->content->text .= '<br><a href="'.$CFG->wwwroot.'/blocks/quickenrol/quickenrol.php">'.get_string('enrolusers', 'block_quickenrol').'</a>'; 
		// Modded to include code to only show if editing teacher access (or more)
		if (has_capability('moodle/user:create', context_system::instance())) { //Checks user role, section not visible to Non-Editing teachers
			//Original modded Code
			$this->content->text .= '<br><p class="tree_item branch navigation_node" tabindex="-1"><i class="icon fa fa fa-user-plus fa-fw navicon" aria-hidden="true" tabindex="0" aria-selected="true"></i><a href="'.$CFG->wwwroot.'/blocks/quickenrol/quickenrol.php">Quick Enrol user on eLearning'.'</a></p>'; 
			//Some extra functions that go alonge with enrolling a user, like adding a user and viewing existing users
			$this->content->text .= '<p class="tree_item branch navigation_node" tabindex="-1"><i class="icon fa fa fa-gear fa-fw navicon" aria-hidden="true" tabindex="0" aria-selected="true"></i><a href="'.$CFG->wwwroot.'/user/editadvanced.php?id=-1">'.get_string('addnewuser', 'block_quickenrol').'</a></p>';
		}
		if (has_capability('moodle/user:update', context_system::instance())) { //Checks user permissions - only show if can update a user (this is what is needed for access to browse anyway)
			$this->content->text .= '<p class="tree_item branch navigation_node" tabindex="-1"><i class="icon fa fa fa-user-group fa-fw navicon" aria-hidden="true" tabindex="0" aria-selected="true"></i><a href="'.$CFG->wwwroot.'/admin/user.php">'.get_string('browseusers', 'block_quickenrol').'</a></p>';
			//$this->content->text .= '<br><a href="'.$CFG->wwwroot.'/admin/user/user_bulk.php">'.get_string('bulkuseractions', 'block_quickenrol').'</a>';
			//Original modded code end
		}
		
		//Patch to include all courses link (taken from 'block_course_list.php)
		if (has_capability('moodle/course:update', context_system::instance()) || empty($CFG->block_course_list_hideallcourseslink)) {
			$this->content->text .= '<p class="tree_item branch navigation_node" tabindex="-1"><i class="icon fa fa fa-network-wired fa-fw navicon" aria-hidden="true" tabindex="0" aria-selected="true"></i><a href="'.$CFG->wwwroot.'/course/index.php">Browse list of courses'.'</a></p>'; 	
		}		
		//Manage Cohorts 
		if (has_capability('moodle/cohort:manage', context_system::instance())) {
			$this->content->text .= '<p class="tree_item branch navigation_node" tabindex="-1"><i class="icon fa fa fa-gear fa-fw navicon" aria-hidden="true" tabindex="0" aria-selected="true"></i><a href="'.$CFG->wwwroot.'/cohort/index.php">Manage cohorts'.'</a></p>'; 	
		}	
		//View Custom SQL Reports
		$this->content->text .= '<p class="tree_item branch navigation_node" tabindex="-1"><i class="icon fa fa fa-chart-column fa-fw navicon" aria-hidden="true" tabindex="0" aria-selected="true"></i><a href="'.$CFG->wwwroot.'/report/customsql/index.php">Custom SQL Reports'.'</a></p>';

        return $this->content;
    }

}
