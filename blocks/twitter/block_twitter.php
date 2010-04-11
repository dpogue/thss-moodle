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

/**
 * Block to display the latest Twitter status of a specified user.
 *
 * @package blocks
 * @copyright 2010 Darryl Pogue
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_twitter extends block_base {

    function init() {
        $this->blockname = get_class($this);
        $this->title = get_string('pluginname', $this->blockname);
        $this->version = 2010041100;
    }
    
    function applicable_formats() {
        return array('all' => true);
    }

    function specialization() {
    	global $CFG;
        
    	$filteropt = new stdClass;
        $filteropt->noclean = true;
        
        $title = '<a href="http://twitter.com/';
        if(isset($this->config->account)) {
        	$title .= $this->config->account;
        }
        $title .= '"><img src="'.$CFG->wwwroot.'/blocks/twitter/pix/twitter.png" alt="Twitter" /></a>';
        $this->title = format_text($title, FORMAT_HTML, $filteropt);
        
        unset($filteropt);
    }
    
    function instance_allow_multiple() {
        return true;
    }

    function has_config() {
        return false;
    }

    function instance_allow_config() {
        return true;
    }
    
    function get_content() {
    	global $OUTPUT, $PAGE;
    	
        if ($this->content !== NULL) {
            return $this->content;
        }
        if (empty($this->instance)) {
            return null;
        }
        
        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        $this->content->text = html_writer::tag('p', '', array('id' => 'twitter_'.$this->config->account));
        
        $options = new stdclass;
        $options->account = $this->config->account;
        $options->page   = 0;
        $options->autostart = true;
        $this->page->requires->js_init_call('M.block_twitter.init', array($options), true, array(
        	'name' => 'block_twitter',
        	'fullpath' => '/blocks/twitter/module.js',
        	'requires'=>array('base', 'io', 'node', 'json')));

        return $this->content;
    }
}
