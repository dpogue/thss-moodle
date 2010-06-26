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
 * The customisable settings for the thss theme.
 *
 * @copyright 2010 Darryl Pogue
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */

$admsettings = new admin_settingpage('theme_thss',
   get_string('admsettings_title', 'theme_thss'));

// Colour of the highlighted tab on the main page
$name = 'theme_thss/welcomecolour';
$title = get_string('welcomecolour', 'theme_thss');
$description = get_string('welcomecolour_desc', 'theme_thss');
$default = '#800000';
$setting = new admin_setting_configtext($name, $title, $description, 
    $default, PARAM_CLEAN, 12);
$admsettings->add($setting);

// Text displayed in the welcome box
$name = 'theme_thss/welcometext';
$title = get_string('welcometext', 'theme_thss');
$description = get_string('welcometext_desc', 'theme_thss');
$default = '<b>Welcome to Moodle!</b>';
$setting = new admin_setting_configtextarea($name, $title,
    $description, $default, PARAM_CLEANHTML, '50', '10');
$admsettings->add($setting);

$ADMIN->add('themes', $admsettings);
