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
 * Restore from backup steps.
 * @copyright Davo Smith <moodle@davosmith.co.uk>
 * @package mod_checklist
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Class restore_elediachecklist_activity_structure_step
 */
class restore_elediachecklist_activity_structure_step extends restore_activity_structure_step {

    /**
     * List of elements that can be restored
     * @return array
     * @throws base_step_exception
     */
    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        // OLD //.
        //$paths[] = new restore_path_element('checklist', '/activity/checklist');
        //$paths[] = new restore_path_element('checklist_item', '/activity/checklist/items/item');
        //if ($userinfo) {
        //    $paths[] = new restore_path_element('checklist_check', '/activity/checklist/items/item/checks/check');
        //    $paths[] = new restore_path_element('checklist_comment', '/activity/checklist/items/item/comments/comment');
        //    $paths[] = new restore_path_element(
        //        'checklist_comment_student',
        //        '/activity/checklist/items/item/studentcomments/studentcomment'
        //    );
        //}
        // NEW //.
        $paths[] = new restore_path_element('elediachecklist', '/activity/elediachecklist');

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Restore a elediachecklist record.
     * @param array|object $data
     * @throws base_step_exception
     * @throws dml_exception
     * @throws restore_step_exception
     */
    protected function process_elediachecklist($data) {

        global $DB;

        // Backup & Restore Forum
        // https://moodle.org/mod/forum/view.php?f=128

        //
        // Instance ID check.
        //

        // Allow only one instance with ID 1 //.
        // Is there already one?             //.
        $elediachecklist = $DB->get_record('elediachecklist', ['id' => 1]);
        if($elediachecklist) {
            $msg  = "Die Aktivität 'eLeDia Checklist' kann nur einmal angelegt werden. "."\n";
            $msg .= "Es ist nur eine Instanz von 'eLeDia Checklist' mit der ID = 1 im System zulässig. "."\n";
            $msg .= "Löschen sie zuerst bitte die bereits im System vorhandene 'eLeDia Checklist'.";

            // Moodle-Excetion
            $errorcode = 'elediachecklist_instance_id_1_already_exists';
            $module    = 'mod_elediachecklist';          // default: $module    = ''                                           //.
            $link = new moodle_url('/course/index.php'); // = nach dem Klick auf den Weiter-Button, zur Uebersicht aller Kurse //.
            $a         = $msg;                           // default: $a         = null                                         //.
            $debuginfo = null;                           // default: $debuginfo = null                                         //.
            throw new moodle_exception($errorcode, $module, $link, $a, $debuginfo);
            return;
        }

        //
        // Insert.
        //

        // Vorlage: elediachecklist_add_instance() //.

        $data = (object) $data;
        $data->course = $this->get_courseid();

        $oldid = $data->id;
        $newid = 1;

        $data->timecreated = $this->apply_date_offset($data->timecreated);   // ???
        $data->timemodified = $this->apply_date_offset($data->timemodified); // ???
        if(!isset($data->theme)) {
            $data->theme = 'default';
        }

        // All Parameters
        $params = array(
            'id'                    => $newid,
            'course'                => $data->course,
            'name'                  => $data->name,
            'intro'                 => $data->intro,
            'introformat'           => $data->introformat,
            'timecreated'           => $data->timecreated,
            'timemodified'          => $data->timemodified,
            'useritemsallowed'      => $data->useritemsallowed,
            'teacheredit'           => $data->teacheredit,
            'theme'                 => $data->theme,
            'duedatesoncalendar'    => $data->duedatesoncalendar,
            'teachercomments'       => $data->teachercomments,
            'maxgrade'              => $data->maxgrade,
            'autopopulate'          => $data->autopopulate,
            'autoupdate'            => $data->autoupdate,
            'completionpercent'     => $data->completionpercent,
            'completionpercenttype' => $data->completionpercenttype,
            'emailoncomplete'       => $data->emailoncomplete,
            'lockteachermarks'      => $data->lockteachermarks
        );
        //echo '<pre>'.print_r($params, true).'</pre>'; die();

        // Create the only instance.
        $sql = "INSERT INTO {elediachecklist} ";
        $sql .= "(";
        $sql .= "id, course, name, intro, introformat, ";
        $sql .= "timecreated, timemodified, useritemsallowed, teacheredit, ";
        $sql .= "theme, ";
        $sql .= "duedatesoncalendar, teachercomments, maxgrade, ";
        $sql .= "autopopulate, autoupdate, completionpercent, ";
        $sql .= "completionpercenttype, emailoncomplete, lockteachermarks ";
        $sql .= ") ";
        $sql .= "VALUES ";
        $sql .= "(";
        $sql .= ":id, :course, :name, :intro, :introformat, ";
        $sql .= ":timecreated, :timemodified, :useritemsallowed, :teacheredit, ";
        $sql .= ":theme, ";
        $sql .= ":duedatesoncalendar, :teachercomments, :maxgrade, ";
        $sql .= ":autopopulate, :autoupdate, :completionpercent, ";
        $sql .= ":completionpercenttype, :emailoncomplete, :lockteachermarks ";
        $sql .= ") ";

        $DB->execute($sql, $params);

        //$newid = $DB->insert_record('elediachecklist', $data);
        $this->set_mapping('elediachecklist', $oldid, $newid);
        $this->apply_activity_instance($newid);

        //----------------------------------------------
        // ORIGINAL -> process_elediachecklist()
        //
        //$data = (object) $data;
        //$oldid = $data->id;
        //$data->course = $this->get_courseid();
        //
        //$data->timecreated = $this->apply_date_offset($data->timecreated);
        //$data->timemodified = $this->apply_date_offset($data->timemodified);
        //
        //$newid = $DB->insert_record('elediachecklist', $data);
        //$this->set_mapping('elediachecklist', $oldid, $newid);
        //$this->apply_activity_instance($newid);
    }

    /**
     * Restore an item record.
     * @param array|object $data
     * @throws dml_exception
     * @throws restore_step_exception
     */
    // ENTFAELLT //.
    /*
    protected function process_checklist_item($data) {
        global $DB;

        $data = (object) $data;
        $oldid = $data->id;

        $data->checklist = $this->get_new_parentid('checklist');
        if ($data->userid > 0) {
            $data->userid = $this->get_mappingid('user', $data->userid);
        }
        // Update to new data structure, where 'hidden' status is stored in separate field.
        if ($data->itemoptional == 3) {
            $data->itemoptional = 0;
            $data->hidden = 1;
        } else if ($data->itemoptional == 4) {
            $data->itemoptional = 2;
            $data->hidden = 1;
        }

        // Apply offset to the deadline.
        $data->duetime = $this->apply_date_offset($data->duetime);

        if (!$this->task->is_samesite()) {
            $data->linkcourseid = null; // Course links do not work when restoring to a different site.
        }

        // Sort out the rest of moduleids in the 'after_restore' function - after all the other activities have been restored.

        $newid = $DB->insert_record('checklist_item', $data);
        $this->set_mapping('checklist_item', $oldid, $newid);
    }
    */

    /**
     * Restore a checkmark record.
     * @param array|object $data
     * @throws dml_exception
     * @throws restore_step_exception
     */
    // ENTFAELLT //.
    /*
    protected function process_checklist_check($data) {
        global $DB;

        $data = (object) $data;
        $oldid = $data->id;

        $data->item = $this->get_new_parentid('checklist_item');
        if ($data->usertimestamp > 0) {
            $data->usertimestamp = $this->apply_date_offset($data->usertimestamp);
        }
        if ($data->teachertimestamp > 0) {
            $data->teachertimestamp = $this->apply_date_offset($data->teachertimestamp);
        }
        $data->userid = $this->get_mappingid('user', $data->userid);
        if ($data->teacherid) {
            $data->teacherid = $this->get_mappingid('user', $data->teacherid);
        }

        $newid = $DB->insert_record('checklist_check', $data);
        $this->set_mapping('checklist_check', $oldid, $newid);
    }
    */

    /**
     * Restore a comment record.
     * @param array|object $data
     * @throws dml_exception
     * @throws restore_step_exception
     */
    // ENTFAELLT //.
    /*
    protected function process_checklist_comment($data) {
        global $DB;

        $data = (object) $data;
        $oldid = $data->id;

        $data->itemid = $this->get_new_parentid('checklist_item');
        $data->userid = $this->get_mappingid('user', $data->userid);
        if ($data->commentby > 0) {
            $data->commentby = $this->get_mappingid('user', $data->commentby);
        }

        $newid = $DB->insert_record('checklist_comment', $data);
        $this->set_mapping('checklist_comment', $oldid, $newid);
    }
    */

    /**
     * Restore a student comment record.
     * @param array|object $data
     * @throws dml_exception
     * @throws restore_step_exception
     */
    // ENTFAELLT //.
    /*
    protected function process_checklist_comment_student($data) {
        global $DB;

        $data = (object) $data;
        $oldid = $data->id;

        $data->itemid = $this->get_new_parentid('checklist_item');
        $data->usermodified = $this->get_mappingid('user', $data->usermodified);

        $newid = $DB->insert_record('checklist_comment_student', $data);
        $this->set_mapping('checklist_comment_student', $oldid, $newid);
    }
    */

    /**
     * Extra actions to take once restore is complete.
     */
    protected function after_execute() {
        // Add checklist related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_elediachecklist', 'intro', null);
    }
}
