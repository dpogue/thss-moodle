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
 * This file contains the generic moodleform bridge for the backup user interface
 * as well as the individual forms that relate to the different stages the user
 * interface can exist within.
 * 
 * @package   moodlecore
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Backup moodleform bridge
 *
 * Ahhh the mighty moodleform bridge! Strong enough to take the weight of 682 full
 * grown african swallows all of whom have been carring coconuts for several days.
 * EWWWWW!!!!!!!!!!!!!!!!!!!!!!!!
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class backup_moodleform extends moodleform {
    /**
     * The stage this form belongs to
     * @var backup_ui_stage
     */
    protected $uistage = null;
    /**
     * True if we have a course div open, false otherwise
     * @var bool
     */
    protected $coursediv = false;
    /**
     * True if we have a section div open, false otherwise
     * @var bool
     */
    protected $sectiondiv = false;
    /**
     * True if we have an activity div open, false otherwise
     * @var bool
     */
    protected $activitydiv = false;
    /**
     * Creates the form
     *
     * @param backup_ui_stage $uistage
     * @param moodle_url|string $action
     * @param mixed $customdata
     * @param string $method get|post
     * @param string $target
     * @param array $attributes
     * @param bool $editable
     */
    function __construct(backup_ui_stage $uistage, $action=null, $customdata=null, $method='post', $target='', $attributes=null, $editable=true) {
        $this->uistage = $uistage;
        parent::__construct($action, $customdata, $method, $target, $attributes, $editable);
    }
    /**
     * The standard form definition... obviously not much here
     */
    function definition() {
        $mform = $this->_form;
        $stage = $mform->addElement('hidden', 'stage', $this->uistage->get_next_stage());
        $stage = $mform->addElement('hidden', 'backup', $this->uistage->get_backupid());
    }
    /**
     * Definition applied after the data is organised.. why's it here? because I want
     * to add elements on the fly.
     */
    function definition_after_data() {
        $mform = $this->_form;
        $this->add_action_buttons(get_string('cancel'), get_string('onstage'.$this->uistage->get_stage().'action', 'backup'));
    }
    /**
     * Closes any open divs
     */
    function close_task_divs() {
        if ($this->activitydiv) {
            $this->_form->addElement('html', html_writer::end_tag('div'));
            $this->activitydiv = false;
        }
        if ($this->sectiondiv) {
            $this->_form->addElement('html', html_writer::end_tag('div'));
            $this->sectiondiv = false;
        }
        if ($this->coursediv) {
            $this->_form->addElement('html', html_writer::end_tag('div'));
            $this->coursediv = false;
        }
    }
    /**
     * Adds the backup_setting as a element to the form
     * @param backup_setting $setting
     * @return bool
     */
    function add_setting(backup_setting $setting, backup_task $task=null) {
        $mform = $this->_form;
        if ($setting->get_visibility() != backup_setting::VISIBLE) {
            return false;
        }
        if ($setting->get_status() == backup_setting::NOT_LOCKED) {
            // First add the formatting for this setting
            $this->add_html_formatting($setting);
            // The call the add method with the get_element_properties array
            call_user_method_array('addElement', $mform, $setting->get_ui()->get_element_properties($task));
            $mform->setDefault($setting->get_ui_name(), $setting->get_value());
            $this->_form->addElement('html', html_writer::end_tag('div'));
        } else {
            // Add as a fixed unchangeable setting
            $this->add_fixed_setting($setting);
        }
        return true;
    }
    /**
     * Adds a heading to the form
     * @param string $name
     * @param string $text
     */
    function add_heading($name , $text) {
        $this->_form->addElement('header', $name, $text);
    }
    /**
     * Adds HTML formatting for the given backup setting, needed to group/segment
     * correctly.
     * @param backup_setting $setting
     */
    protected function add_html_formatting(backup_setting $setting) {
        $mform = $this->_form;
        $isincludesetting = (strpos($setting->get_name(), '_include')!==false);
        if ($isincludesetting && $setting->get_level() != backup_setting::ROOT_LEVEL)  {
            switch ($setting->get_level()) {
                case backup_setting::COURSE_LEVEL:
                    if ($this->activitydiv) {
                        $this->_form->addElement('html', html_writer::end_tag('div'));
                        $this->activitydiv = false;
                    }
                    if ($this->sectiondiv) {
                        $this->_form->addElement('html', html_writer::end_tag('div'));
                        $this->sectiondiv = false;
                    }
                    if ($this->coursediv) {
                        $this->_form->addElement('html', html_writer::end_tag('div'));
                    }
                    $mform->addElement('html', html_writer::start_tag('div', array('class'=>'grouped_settings course_level')));
                    $mform->addElement('html', html_writer::start_tag('div', array('class'=>'include_setting course_level')));
                    $this->coursediv = true;
                    break;
                case backup_setting::SECTION_LEVEL:
                    if ($this->activitydiv) {
                        $this->_form->addElement('html', html_writer::end_tag('div'));
                        $this->activitydiv = false;
                    }
                    if ($this->sectiondiv) {
                        $this->_form->addElement('html', html_writer::end_tag('div'));
                    }
                    $mform->addElement('html', html_writer::start_tag('div', array('class'=>'grouped_settings section_level')));
                    $mform->addElement('html', html_writer::start_tag('div', array('class'=>'include_setting section_level')));
                    $this->sectiondiv = true;
                    break;
                case backup_setting::ACTIVITY_LEVEL:
                    if ($this->activitydiv) {
                        $this->_form->addElement('html', html_writer::end_tag('div'));
                    }
                    $mform->addElement('html', html_writer::start_tag('div', array('class'=>'grouped_settings activity_level')));
                    $mform->addElement('html', html_writer::start_tag('div', array('class'=>'include_setting activity_level')));
                    $this->activitydiv = true;
                    break;
                default:
                    $mform->addElement('html', html_writer::start_tag('div', array('class'=>'normal_setting')));
                    break;
            }
        } else if ($setting->get_level() == backup_setting::ROOT_LEVEL) {
            $mform->addElement('html', html_writer::start_tag('div', array('class'=>'root_setting')));
        } else {
            $mform->addElement('html', html_writer::start_tag('div', array('class'=>'normal_setting')));
        }
    }
    /**
     * Adds a fixed or static setting to the form
     * @param backup_setting $setting
     */
    function add_fixed_setting(backup_setting $setting) {
        $this->add_html_formatting($setting);

        $mform = $this->_form;
        $settingui = $setting->get_ui();
        if ($setting->get_status() != backup_setting::NOT_LOCKED) {
            $mform->addElement('static', 'static_'.$settingui->get_name(), $settingui->get_label(), get_string('settingislocked','backup',$settingui->get_static_value()));
        } else {
            $mform->addElement('static','static_'. $settingui->get_name(), $settingui->get_label(), $settingui->get_static_value());
        }
        $mform->addElement('hidden', $settingui->get_name(), $settingui->get_value());

        $this->_form->addElement('html', html_writer::end_tag('div'));
    }
    /**
     * Adds dependencies to the form recursively
     * 
     * @param backup_setting $setting
     * @param backup_setting $basesetting
     */
    function add_dependencies(backup_setting $setting, $basesetting=null) {
        $mform = $this->_form;
        if ($basesetting == null) {
            $basesetting = $setting;
        }
        foreach ($setting->get_dependencies() as $dependency) {
            $dependency = $dependency->get_dependant_setting();
            switch ($basesetting->get_ui_type()) {
                case backup_setting::UI_HTML_CHECKBOX :
                    $mform->disabledIf($dependency->get_ui_name(), $basesetting->get_ui_name(), 'notchecked');
                    $this->add_dependencies($dependency, $basesetting);
                    break;
                case backup_setting::UI_HTML_DROPDOWN :
                    $mform->disabledIf($dependency->get_ui_name(), $basesetting->get_ui_name(), 'eq', 0);
                    $this->add_dependencies($dependency, $basesetting);
                    break;
                default:
                    debugging('Unknown backup setting type', DEBUG_DEVELOPER);
                    break;
            }
        }
    }
    /**
     * Returns true if the form was cancelled, false otherwise
     * @return bool
     */
    public function is_cancelled() {
        return (optional_param('cancel', false, PARAM_BOOL) || parent::is_cancelled());
    }
}
/**
 * Initial backup user interface stage moodleform.
 *
 * Nothing to override we only need it defined so that moodleform doesn't get confused
 * between stages.
 */
class backup_initial_form extends backup_moodleform {}
/**
 * Schema backup user interface stage moodleform.
 *
 * Nothing to override we only need it defined so that moodleform doesn't get confused
 * between stages.
 */
class backup_schema_form extends backup_moodleform {}
/**
 * Confirmation backup user interface stage moodleform.
 *
 * Nothing to override we only need it defined so that moodleform doesn't get confused
 * between stages.
 */
class backup_confirmation_form extends backup_moodleform {}