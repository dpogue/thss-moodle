<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * Email message processor - send a given message by email
 *
 * @author Luis Rodrigues
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package
 */
require_once($CFG->dirroot.'/message/output/lib.php');

class message_output_email extends message_output {
    /**
     * Processes the message (sends by email).
     * @param object $message the message to be sent
     */
    function send_message($message) {
        global $DB, $SITE;

        $userto = $DB->get_record('user', array('id' => $message->useridto));
        $userfrom = $DB->get_record('user', array('id' => $message->useridfrom));

        //check user preference for where user wants email sent
        $usertoemailaddress = get_user_preferences('message_processor_email_email', '', $message->useridto);

        if ( !empty($usertoemailaddress)) {
            $userto->email = $usertoemailaddress;
        }

        //concatenating the footer on here so that it appears on emails but not within the saved message
        $messagetosend = null;
        if (!empty($message->fullmessage)) {
            $messagetosend = $message->fullmessage.$message->footer;
        }

        $messagetosendhtml = null;
        if (!empty($message->fullmessagehtml)) {
            $messagetosendhtml = $message->fullmessagehtml.$message->footerhtml;
        }

        $result = email_to_user($userto, $userfrom,
            $message->subject, $messagetosend, $messagetosendhtml);

        return $result===true; //email_to_user() can return true, false or "emailstop"
        //return true;//do we want to report an error if email sending fails?
    }

    /**
     * Creates necessary fields in the messaging config form.
     * @param object $mform preferences form class
     */
    function config_form($preferences){
        global $USER;
        $string = get_string('email').': <input size="30" name="email_email" value="'.$preferences->email_email.'" />';
        if (empty($preferences->email_email)) {
            $string .= ' ('.get_string('default').': '.$USER->email.')';
        }
        return $string;
    }

    /**
     * Parses the form submitted data and saves it into preferences array.
     * @param object $mform preferences form class
     * @param array $preferences preferences array
     */
    function process_form($form, &$preferences){
        $preferences['message_processor_email_email'] = $form->email_email;
    }

    /**
     * Loads the config data from database to put on the form (initial load)
     * @param array $preferences preferences array
     * @param int $userid the user id
     */
    function load_data(&$preferences, $userid){
        $preferences->email_email = get_user_preferences( 'message_processor_email_email', '', $userid);
    }
}
