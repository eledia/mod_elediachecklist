<?php
// This file is part of the Checklist plugin for Moodle - http://moodle.org/
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
 * Activity instance editing form.
 * @copyright Davo Smith <moodle@davosmith.co.uk>
 * @package mod_elediachecklist
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Class mod_elediachecklist_mod_form
 */
class mod_elediachecklist_mod_form extends moodleform_mod {

    /**
     * Define form elements
     * @throws coding_exception
     * @throws dml_exception
     */
    public function definition() {

        global $CFG;
        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('modulename', 'elediachecklist'), array('size' => '64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        if ($CFG->branch < 29) {
            $this->add_intro_editor(true, get_string('checklistintro', 'elediachecklist'));
        } else {
            $this->standard_intro_elements(get_string('checklistintro', 'elediachecklist'));
        }

        $mform->addElement('header', 'checklistsettings', get_string('checklistsettings', 'elediachecklist'));
        $mform->setExpanded('checklistsettings', true);

        $ynoptions = array(0 => get_string('no'), 1 => get_string('yes'));
        $mform->addElement('select', 'useritemsallowed', get_string('useritemsallowed', 'elediachecklist'), $ynoptions);

        $teditoptions = array(
            CHECKLIST_MARKING_STUDENT => get_string('teachernoteditcheck', 'elediachecklist'),
            CHECKLIST_MARKING_TEACHER => get_string('teacheroverwritecheck', 'elediachecklist'),
            CHECKLIST_MARKING_BOTH => get_string('teacheralongsidecheck', 'elediachecklist')
        );
        $mform->addElement('select', 'teacheredit', get_string('teacheredit', 'elediachecklist'), $teditoptions);

        $mform->addElement('select', 'duedatesoncalendar', get_string('duedatesoncalendar', 'elediachecklist'), $ynoptions);
        $mform->setDefault('duedatesoncalendar', 0);

        $mform->addElement('select', 'teachercomments', get_string('teachercomments', 'elediachecklist'), $ynoptions);
        $mform->setDefault('teachercomments', 1);

        $mform->addElement('text', 'maxgrade', get_string('maximumgrade'), array('size' => '10'));
        $mform->setDefault('maxgrade', 100);
        $mform->setType('maxgrade', PARAM_INT);

        $emailrecipients = array(
            CHECKLIST_EMAIL_NO => get_string('no'),
            CHECKLIST_EMAIL_STUDENT => get_string('teachernoteditcheck', 'elediachecklist'),
            CHECKLIST_EMAIL_TEACHER => get_string('teacheroverwritecheck', 'elediachecklist'),
            CHECKLIST_EMAIL_BOTH => get_string('teacheralongsidecheck', 'elediachecklist')
        );
        $mform->addElement('select', 'emailoncomplete', get_string('emailoncomplete', 'elediachecklist'), $emailrecipients);
        $mform->setDefault('emailoncomplete', 0);
        $mform->addHelpButton('emailoncomplete', 'emailoncomplete', 'elediachecklist');

        $autopopulateoptions = array(
            CHECKLIST_AUTOPOPULATE_NO => get_string('no'),
            CHECKLIST_AUTOPOPULATE_SECTION => get_string('importfromsection', 'elediachecklist'),
            CHECKLIST_AUTOPOPULATE_COURSE => get_string('importfromcourse', 'elediachecklist')
        );
        $mform->addElement('select', 'autopopulate', get_string('autopopulate', 'elediachecklist'), $autopopulateoptions);
        $mform->setDefault('autopopulate', 0);
        $mform->addHelpButton('autopopulate', 'autopopulate', 'elediachecklist');

        $checkdisable = true;
        $str = 'autoupdate';
        if (get_config('mod_elediachecklist', 'linkcourses')) {
            $str = 'autoupdate2';
            $checkdisable = false;
        }

        $autoupdateoptions = array(
            CHECKLIST_AUTOUPDATE_NO => get_string('no'),
            CHECKLIST_AUTOUPDATE_YES => get_string('yesnooverride', 'elediachecklist'),
            CHECKLIST_AUTOUPDATE_YES_OVERRIDE => get_string('yesoverride', 'elediachecklist')
        );
        $mform->addElement('select', 'autoupdate', get_string($str, 'elediachecklist'), $autoupdateoptions);
        $mform->setDefault('autoupdate', 1);
        $mform->addHelpButton('autoupdate', $str, 'elediachecklist');
        $mform->addElement('static', 'autoupdatenote', '', get_string('autoupdatenote', 'elediachecklist'));
        if ($checkdisable) {
            $mform->disabledIf('autoupdate', 'autopopulate', 'eq', 0);
        }

        $mform->addElement('selectyesno', 'lockteachermarks', get_string('lockteachermarks', 'elediachecklist'));
        $mform->setDefault('lockteachermarks', 0);
        $mform->addHelpButton('lockteachermarks', 'lockteachermarks', 'elediachecklist');

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }

    /**
     * Pre-process form data
     * @param array $defaultvalues
     */
    public function data_preprocessing(&$defaultvalues) {
        parent::data_preprocessing($defaultvalues);

        // Set up the completion checkboxes which aren't part of standard data.
        // We also make the default value (if you turn on the checkbox) for those
        // numbers to be 1, this will not apply unless checkbox is ticked.
        $defaultvalues['completionpercentenabled'] = !empty($defaultvalues['completionpercent']) ? 1 : 0;
        if (empty($defaultvalues['completionpercent'])) {
            $defaultvalues['completionpercent'] = 100;
        }
        if (empty($defaultvalues['completionpercenttype'])) {
            $defaultvalues['completionpercenttype'] = 'percent';
        }
    }

    /**
     * Add completion rules
     * @return string[]
     * @throws coding_exception
     */
    public function add_completion_rules() {
        $mform = $this->_form;

        $group = array();
        $group[] = $mform->createElement('checkbox', 'completionpercentenabled', '',
                                         get_string('completionpercent', 'elediachecklist'), array('class' => 'checkbox-inline'));
        $group[] = $mform->createElement('text', 'completionpercent', '', array('size' => 3));
        $mform->setType('completionpercent', PARAM_INT);
        $opts = [
            'percent' => get_string('percent', 'mod_elediachecklist'),
            'items' => get_string('itemstype', 'mod_elediachecklist'),
        ];
        $group[] = $mform->createElement('select', 'completionpercenttype', '', $opts);

        $mform->addGroup($group, 'completionpercentgroup', get_string('completionpercentgroup', 'elediachecklist'), array(' '), false);
        $mform->disabledIf('completionpercent', 'completionpercentenabled', 'notchecked');
        $mform->disabledIf('completionpercenttype', 'completionpercentenabled', 'notchecked');
        $mform->addHelpButton('completionpercentgroup', 'completionpercentgroup', 'mod_elediachecklist');

        return array('completionpercentgroup');
    }

    /**
     * Are completion rules enabled?
     * @param array $data
     * @return bool
     */
    public function completion_rule_enabled($data) {
        return (!empty($data['completionpercentenabled']) && $data['completionpercent'] != 0);
    }

    /**
     * Get the form data
     * @return false|object
     */
    public function get_data() {
        $data = parent::get_data();
        if (!$data) {
            return false;
        }
        // Turn off completion settings if the checkboxes aren't ticked.
        if (isset($data->completionpercent)) {
            $autocompletion = !empty($data->completion) && $data->completion == COMPLETION_TRACKING_AUTOMATIC;
            if (empty($data->completionpercentenabled) || !$autocompletion) {
                $data->completionpercent = 0;
            }
        }
        return $data;
    }

}
