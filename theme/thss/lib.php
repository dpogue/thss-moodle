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
 * General purpose functions for the thss theme.
 *
 * @copyright 2010 Darryl Pogue
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */

function thss_process_css($css, $theme) {
    if (!empty($theme->settings->welcomecolour)) {
        $welcomecol = $theme->settings->welcomecolour;
    } else {
        $welcomecol = null;
    }
    $css = thss_set_welcomecolour($css, $welcomecol);

    return $css;
}

/**
 * Sets the background colour of the welcome message and home tab in CSS.
 *
 * @param string $css
 * @param mixed $colour
 * @return string
 */
function thss_set_welcomecolour($css, $colour) {
    $tag = '[[setting:welcomecolour]]';
    $tag_grad = '[[setting:welcomecolourfade]]';

    if (is_null($colour)) {
        $rgb = Hex2RGB('#800000');
    } else {
        $rgb = Hex2RGB($colour);
    }
    $replacement = 'rgb('.$rgb[0].','.$rgb[1].','.$rgb[2].')';
    $css = str_replace($tag, $replacement, $css);
    $replacement = 'rgba('.$rgb[0].','.$rgb[1].','.$rgb[2].',0.4)';
    $css = str_replace($tag_grad, $replacement, $css);
    return $css;
}


/**
 * Converts a hex colour to an array of RGB values.
 *
 * @params string $colour
 * @returns array
 *
 * @copyright 2006 Jonas John
 * http://www.jonasjohn.de/snippets/php/hex2rgb.htm
 */
function Hex2RGB($colour) {
    $colour = str_replace('#', '', $colour);
    if (strlen($colour) != 6){ return array(0,0,0); }
    $rgb = array();
    for ($x=0;$x<3;$x++){
        $rgb[$x] = hexdec(substr($colour,(2*$x),2));
    }
    return $rgb;
}

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
