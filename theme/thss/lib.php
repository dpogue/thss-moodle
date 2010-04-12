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

require_once('announcement.php');

function tabmenu_nav($index=false) {
    global $USER, $CFG, $SITE, $DB;
    
    $out = array();
    $out[] = html_writer::tag('li', html_writer::tag('a', '<em>'.get_string('home').'</em>', array('href' => $CFG->wwwroot.'/index.php')), ($index ? array('class' => 'selected') : null));
    
    if($tabs = $DB->get_records('theme_thss_menu', array('root' => 0), 'pos ASC')) {
    
        foreach ($tabs as $tab) {
            $output  = html_writer::start_tag('li', null);
            $output .= html_writer::tag('a', '<em>'.$tab->name.'</em>', array('href' => $tab->link));
            
            if($children = $DB->get_records('theme_thss_menu', array('root' => $tab->id), 'pos ASC')) {
                $output .= html_writer::start_tag('ul', null);
                
                foreach ($children as $child) {
                    $output .= html_writer::tag('li', html_writer::tag('a', '<em>'.$child->name.'</em>', array('href' => $child->link)), null);
                }
                
                $output .= html_writer::end_tag('ul');
            }
            
            $output .= html_writer::end_tag('li');
            
            $out[] = $output;
        }
    }
    
    return html_writer::tag('ul', implode('', $out), array('id' => 'tabbar'));
}

function announcements($page, $all=false) {
    $mgr = new announcement_manager($page, true);

    echo $mgr->print_announcements('rotate');
}
