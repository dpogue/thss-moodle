<?php

require_once($CFG->libdir.'/formslib.php');

class user_message_form extends moodleform {

    function definition() {
        $mform =& $this->_form;
        $mform->addElement('header', 'general', get_string('message', 'message'));


        $mform->addElement('editor', 'messagebody', get_string('messagebody'), null, null);
        $mform->addRule('messagebody', '', 'required', null, 'server');
        $mform->setHelpButton('messagebody', array('writing', 'reading', 'questions', 'richtext2'), false, 'editorhelpbutton');        

        $this->add_action_buttons();
    }
}
