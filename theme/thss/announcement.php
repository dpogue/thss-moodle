<?php

/**
 * announcement_manager is a class to handle the displaying of rotating
 * announcements.
 *
 * @copyright 2010 Darryl Pogue
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class announcement_manager {
    
    protected $page;
    
    protected $nativeSVG = true;
    
    function __construct($page, $native) {
        $this->page = $page;
        $this->nativeSVG = $native;
        
        //$this->page->requires->js('announcement/javascript.php')->in_head();
    }
    
    public function print_announcements($containerID) {
        global $DB;
        $temp = array('twitter', 'weather'); //, 'craftfair'); //Should come from database...
        
        $jsarray = array();
        
        foreach ($temp as $block) {
		    if($instance = announcement_instance($block)) {
				
				if(!$this->nativeSVG) {
				    echo "\t\t\t".'<script type="image/svg+xml">'."\n";
				    echo "\t\t\t".'<svg id="'.$instance->get_id().'" version="1.1" font-family="geneva,segoe ui,arial,sans-serif" width="305" height="201"'.
				        ' viewBox="0 0 305 201">'."\n";
				} else {
                    echo "\t\t\t".'<svg id="'.$instance->get_id().'"'
                        .' font-family="geneva,segoe ui,arial,sans-serif"'
                        .' width="305" height="201" viewBox="0 0 305 201"'.">\n";
                        //.' display="none" opacity="0">'."\n";
				}
				
				$instance->output();
				
				if(!$this->nativeSVG) {
				    echo "\t\t\t".'</svg>'."\n";
				    echo "\t\t\t".'</script>'."\n";
				} else {
				    echo "\t\t\t".'</svg>'."\n";
				}
				
				$instance->javascript($this->page);
				$jsarray[] = $instance->get_id();
		    }
        }
        
        //$this->page->requires->js_function_call('YAHOO.moodle.announcements.init', array($containerID, $jsarray))->on_dom_ready();
        //$this->page->requires->js_function_call('YAHOO.moodle.announcements.fadein', array())->on_dom_ready();
    }
    
    public function print_one_announcement($block) {
	    if($instance = announcement_instance($block)) {
			
			if(!$this->nativeSVG) {
			    echo "\t\t\t".'<script type="image/svg+xml">'."\n";
			    echo "\t\t\t".'<svg id="'.$instance->get_id().'" version="1.1" font-family="sans-serif" width="305" height="201" viewBox="0 0 305 201">'."\n";
			} else {
			    echo "\t\t\t".'<svg id="'.$instance->get_id().'" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"'.
			        ' font-family="sans-serif" width="305" height="201" viewBox="0 0 305 201">'."\n";
			}
			
			$instance->output();
			
			if(!$this->nativeSVG) {
			    echo "\t\t\t".'</svg>'."\n";
			    echo "\t\t\t".'</script>'."\n";
			} else {
			    echo "\t\t\t".'</svg>'."\n";
			}
			
			$instance->javascript();
	    }
    }
}

/**
 * Creates a new object of the specified announcement class.
 *
 * @param string $announcename the name of the announcement.
 * @return announcement the requested announcement instance.
 */
function announcement_instance($announcename) {
    if(!announcement_load_class($announcename)) {
        return false;
    }
    $classname = 'announcement_'.$announcename;
    $retval = new $classname;

    return $retval;
}

/**
 * Load the announcement class for a particular type of announcement.
 *
 * @param string $announcename the name of the announcement.
 * @return boolean success or failure.
 */
function announcement_load_class($announcename) {
    global $CFG;

    if(empty($announcename)) {
        return false;
    }

    $classname = 'announcement_'.$announcename;

    if(class_exists($classname)) {
        return true;
    }

    $announcepath = $CFG->dirroot.'/announcement/'.$announcename.'/'.$announcename.'.php';

    if (file_exists($announcepath)) {
        include_once($announcepath);
    }else{
        debugging("$announcename code does not exist in $announcepath", DEBUG_DEVELOPER);
        return false;
    }

    return class_exists($classname);
}

function get_all_announcements() {
    global $CFG;
    
    $result = array();
    $dir = $CFG->dirroot.'/announcement';

    $items = new DirectoryIterator($dir);
    foreach ($items as $item) {
        if ($item->isDot() or !$item->isDir()) {
            continue;
        }
        $pluginname = $item->getFilename();
        if ($pluginname !== clean_param($pluginname, PARAM_SAFEDIR)) {
            // better ignore plugins with problematic names here
            continue;
        }
        $result[$pluginname] = $pluginname;
    }

    ksort($result);
    return $result;
}

/**
 * announcement is a base class for announcement objects.
 *
 * @copyright 2009 Darryl Pogue
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class announcement {

    var $content = NULL;
    
    var $name;
    
    var $id;
    
    /**
     * The class constructor
     *
     */
    function announcement() {
        $this->init();
    }

    /**
     * Fake constructor to keep PHP5 happy
     *
     */
    function __construct() {
        $this->announcement();
    }

    public function output() {
        echo $this->get_content();
    }
    
    public function javascript($page) {
        return;
    }
    
    public function get_name() {
        return $this->name;
    }
    
    function get_id() {
        return $this->id;
    }
}
