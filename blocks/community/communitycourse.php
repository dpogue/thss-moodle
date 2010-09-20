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
 * @package    blocks
 * @subpackage community
 * @author     Jerome Mouneyrac <jerome@mouneyrac.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This page display the community course search form.
 * It also handles adding a course to the community block.
 * It also handles downloading a course template.
 */

require('../../config.php');
require_once($CFG->dirroot . '/blocks/community/locallib.php');
require_once($CFG->dirroot . '/blocks/community/forms.php');
require_once($CFG->dirroot . '/admin/registration/lib.php');

require_login();

$courseid = optional_param('courseid', $SITE->id, PARAM_INT); //if no courseid is given
$context = get_context_instance(CONTEXT_COURSE, $courseid);

$PAGE->set_context($context);
$PAGE->set_url('/blocks/community/communitycourse.php');
$PAGE->set_heading($SITE->fullname);
$PAGE->set_pagelayout('course');
$PAGE->set_title(get_string('searchcourse', 'block_community'));
$PAGE->navbar->ignore_active(true);
$PAGE->navbar->add(get_string('searchcourse', 'block_community'));

$search = optional_param('search', null, PARAM_TEXT);

//if no capability to search course, display an error message
$usercansearch = has_capability('moodle/community:add', $context);
$usercandownload = has_capability('moodle/community:download', $context);
if (empty($usercansearch)) {
    $notificationerror = get_string('cannotsearchcommunity', 'hub');
} else if (!extension_loaded('xmlrpc')) {
    $notificationerror = $OUTPUT->doc_link('admin/environment/php_extension/xmlrpc', '');
    $notificationerror .= get_string('xmlrpcdisabledcommunity', 'hub');
}
if (!empty($notificationerror)) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('searchcommunitycourse', 'block_community'), 3, 'main');
    echo $OUTPUT->notification($notificationerror);
    echo $OUTPUT->footer();
    die();
}

$communitymanager = new block_community_manager();
$renderer = $PAGE->get_renderer('block_community');

/// Check if the page has been called with trust argument
$add = optional_param('add', -1, PARAM_INTEGER);
$confirm = optional_param('confirmed', false, PARAM_INTEGER);
if ($add != -1 and $confirm and confirm_sesskey()) {
    $course = new stdClass();
    $course->name = optional_param('coursefullname', '', PARAM_TEXT);
    $course->description = optional_param('coursedescription', '', PARAM_TEXT);
    $course->url = optional_param('courseurl', '', PARAM_URL);
    $course->imageurl = optional_param('courseimageurl', '', PARAM_URL);
    $communitymanager->block_community_add_course($course, $USER->id);
    $notificationmessage = $OUTPUT->notification(get_string('addedtoblock', 'hub'),
                    'notifysuccess');
}

/// Delete temp file when cancel restore
$cancelrestore = optional_param('cancelrestore', false, PARAM_INT);
if ($usercandownload and $cancelrestore and confirm_sesskey()) {
    $filename = optional_param('filename', '', PARAM_ALPHANUMEXT);
    //delete temp file
    unlink($CFG->dataroot . '/temp/backup/' . $filename . ".mbz");
}

/// Download
$huburl = optional_param('huburl', false, PARAM_URL);
$download = optional_param('download', -1, PARAM_INTEGER);
$downloadcourseid = optional_param('downloadcourseid', '', PARAM_INTEGER);
$coursefullname = optional_param('coursefullname', '', PARAM_ALPHANUMEXT);
if ($usercandownload and $download != -1 and !empty($downloadcourseid) and confirm_sesskey()) {
    $course = new stdClass();
    $course->fullname = $coursefullname;
    $course->id = $downloadcourseid;
    $course->huburl = $huburl;
    $filenames = $communitymanager->block_community_download_course_backup($course);

    //OUTPUT: display restore choice page
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('restorecourse', 'block_community'), 3, 'main');
    echo $OUTPUT->notification(get_string('downloadconfirmed', 'block_community',
                    '/downloaded_backup/' . $filenames['privatefile']), 'notifysuccess');
    echo $renderer->restore_confirmation_box($filenames['tmpfile'], $context);
    echo $OUTPUT->footer();
    die();
}

/// Remove community
$remove = optional_param('remove', '', PARAM_INTEGER);
$communityid = optional_param('communityid', '', PARAM_INTEGER);
if ($remove != -1 and !empty($communityid) and confirm_sesskey()) {
    $communitymanager->block_community_remove_course($communityid, $USER->id);
    $notificationmessage = $OUTPUT->notification(get_string('communityremoved', 'hub'),
                    'notifysuccess');
}

//forms
$hubselectorform = new community_hub_search_form('',
                array('search' => $search, 'courseid' => $courseid));
$fromform = $hubselectorform->get_data();
$courses = null;
//Retrieve courses by web service
if (!empty($fromform)) {
    $downloadable = optional_param('downloadable', false, PARAM_INTEGER);

    $options = new stdClass();
    if (!empty($fromform->coverage)) {
        $options->coverage = $fromform->coverage;
    }
    if ($fromform->licence != 'all') {
        $options->licenceshortname = $fromform->licence;
    }
    if ($fromform->subject != 'all') {
        $options->subject = $fromform->subject;
    }
    if ($fromform->audience != 'all') {
        $options->audience = $fromform->audience;
    }
    if ($fromform->educationallevel != 'all') {
        $options->educationallevel = $fromform->educationallevel;
    }
    if ($fromform->language != 'all') {
        $options->language = $fromform->language;
    }

    //check if the selected hub is from the registered list (in this case we use the private token)
    $token = 'publichub';
    $registrationmanager = new registration_manager();
    $registeredhubs = $registrationmanager->get_registered_on_hubs();
    foreach ($registeredhubs as $registeredhub) {
        if ($huburl == $registeredhub->huburl) {
            $token = $registeredhub->token;
        }
    }

    $function = 'hub_get_courses';
    $params = array('search' => $search, 'downloadable' => $downloadable,
        'enrollable' => !$downloadable, 'options' => $options);
    $serverurl = $huburl . "/local/hub/webservice/webservices.php";
    require_once($CFG->dirroot . "/webservice/xmlrpc/lib.php");
    $xmlrpcclient = new webservice_xmlrpc_client($serverurl, $token);
    try {
        $courses = $xmlrpcclient->call($function, $params);
    } catch (Exception $e) {
        $errormessage = $OUTPUT->notification(
                        get_string('errorcourselisting', 'block_community', $e->getMessage()));
    }
}

// OUTPUT
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('searchcommunitycourse', 'block_community'), 3, 'main');
if (!empty($notificationmessage)) {
    echo $notificationmessage;
}
$hubselectorform->display();
if (!empty($errormessage)) {
    echo $errormessage;
}

//load javascript
$courseids = array();
if (!empty($courses)) {
    foreach ($courses as $course) {
        if (!empty($course['comments'])) {
            $courseids[] = $course['id'];
        }
    }
}
$PAGE->requires->yui_module('moodle-block_community-comments', 'M.blocks_community.init_comments',
        array(array('commentids' => $courseids)));

echo highlight($search, $renderer->course_list($courses, $huburl, $courseid));
echo $OUTPUT->footer();