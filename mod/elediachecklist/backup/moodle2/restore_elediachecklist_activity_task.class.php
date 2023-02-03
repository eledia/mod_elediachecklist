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
 * Restore from backup.
 * @copyright Davo Smith <moodle@davosmith.co.uk>
 * @package mod_checklist
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();



global $CFG;
require_once($CFG->dirroot.'/mod/elediachecklist/backup/moodle2/restore_elediachecklist_stepslib.php'); // Because it exists (must).

/**
 * elediachecklist restore task that provides all the settings and steps to perform one complete restore of the activity
 */
class restore_elediachecklist_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // Choice only has one structure step.
        // ALT //.
        //$this->add_step(new restore_checklist_activity_structure_step('checklist_structure', 'checklist.xml'));
        // NEU //.
        $this->add_step(new restore_elediachecklist_activity_structure_step('elediachecklist_structure', 'elediachecklist.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    public static function define_decode_contents() {

        // TESTEN
        //$this->

        $contents = array();

        // Felder, die (verschluesselte) Links enthalten koennten           //.
        // Felder die dekodiert/entschluesselt werden sollen.               //.
        // Hier: Das Feld Introduction/Beschreibung der Aktivitaets-Instanz //.
        // 'intro' = Tabellen-Feld: elediachecklist.intro                   //.
        $contents[] = new restore_decode_content('elediachecklist', array('intro'), 'elediachecklist');
        //                                       $tablename,        $fields,        $mapping (Default: $tablename)

        //$contents[] = new restore_decode_content('checklist_item', array('linkurl'),  'checklist_item');

        return $contents;
    }

    /**
     * Die Entschluesselungsregeln
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    public static function define_decode_rules() {

        // Die Verschlusselungsregeln findet man hier:                  //.
        // See, inverse function(?):                                    //.
        // backup_elediachecklist_activity_task::encode_content_links() //.

        /*
        // Link to the list of checklists.
        $search = "/(".$base."\/mod\/checklist\/index.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@CHECKLISTINDEX*$2@$', $content);

        // Link to checklist view by moduleid.
        $search = "/(".$base."\/mod\/checklist\/view.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@CHECKLISTVIEWBYID*$2@$', $content);

        // Link to checklist view by id.
        $search = "/(".$base."\/mod\/checklist\/view.php\?checklist\=)([0-9]+)/";
        $content = preg_replace($search, '$@CHECKLISTVIEWBYCHECKLIST*$2@$', $content);

        // Link to checklist report by moduleid.
        $search = "/(".$base."\/mod\/checklist\/report.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@CHECKLISTREPORTBYID*$2@$', $content);

        // Link to checklist report by id.
        $search = "/(".$base."\/mod\/checklist\/report.php\?checklist\=)([0-9]+)/";
        $content = preg_replace($search, '$@CHECKLISTREPORTBYCHECKLIST*$2@$', $content);

        // Link to checklist edit by moduleid.
        $search = "/(".$base."\/mod\/checklist\/edit.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@CHECKLISTEDITBYID*$2@$', $content);

        // Link to checklist edit by id.
        $search = "/(".$base."\/mod\/checklist\/edit.php\?checklist\=)([0-9]+)/";
        $content = preg_replace($search, '$@CHECKLISTEDITBYCHECKLIST*$2@$', $content);
        */

        /*
        global $CFG;
        $base = preg_quote($CFG->wwwroot, "/");
        $content = '';
        // Link to the list of checklists.
        // Kurs-ID // 2 = Napoleon //.
        // https://ngeiges.eledia.de/moodle311/public_html/mod/elediachecklist/index.php?id=2 //.
        $search = "/(".$base."\/mod\/elediachecklist\/index.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@ELEDIACHECKLISTINDEX*$2@$', $content);

        // Link to checklist view by moduleid.
        // moduleid = Aktivitaets-ID // 106
        // https://ngeiges.eledia.de/moodle311/public_html/mod/elediachecklist/view.php?id=106 //.
        $search = "/(".$base."\/mod\/elediachecklist\/view.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@ELEDIACHECKLISTVIEWBYID*$2@$', $content);

        // Link to checklist view by id.
        // Checklist-ID = 1
        // https://ngeiges.eledia.de/moodle311/public_html/mod/elediachecklist/view.php?eledia=1 //.
        $search = "/(".$base."\/mod\/elediachecklist\/view.php\?eledia\=)([0-9]+)/";
        $content = preg_replace($search, '$@ELEDIACHECKLISTVIEWBYELEDIA*$2@$', $content);
        */

        $rules = array();
        // List of checklists in course.
        //                                 $linkname,              $urltemplate,                           $mappings
        $rules[] = new restore_decode_rule('ELEDIACHECKLISTINDEX', '/mod/elediachecklist/index.php?id=$1', 'course');

        // Checklist by cm->id
        $rules[] = new restore_decode_rule('ELEDIACHECKLISTVIEWBYID', '/mod/elediachecklist/view.php?id=$1', 'course_module');
//        $rules[] = new restore_decode_rule('ELEDIACHECKLISTVIEWBYELEDIA', '/mod/elediachecklist/view.php?eledia=$1', 'checklist');
        // Checklist report by cm->id and forum->id.
//        $rules[] = new restore_decode_rule('CHECKLISTREPORTBYID', '/mod/elediachecklist/report.php?id=$1', 'course_module');
//        $rules[] = new restore_decode_rule('CHECKLISTREPORTBYCHECKLIST', '/mod/elediachecklist/report.php?checklist=$1', 'checklist');
        // Checklist edit by cm->id and forum->id.
//        $rules[] = new restore_decode_rule('CHECKLISTEDITBYID', '/mod/elediachecklist/edit.php?id=$1', 'course_module');
//        $rules[] = new restore_decode_rule('CHECKLISTEDITBYCHECKLIST', '/mod/elediachecklist/edit.php?checklist=$1', 'checklist');
        return $rules;

        // VORLAGE
        /*
        $rules = array();
        // List of checklists in course.
        $rules[] = new restore_decode_rule('CHECKLISTINDEX', '/mod/checklist/index.php?id=$1', 'course');
        // Checklist by cm->id and forum->id.
        $rules[] = new restore_decode_rule('CHECKLISTVIEWBYID', '/mod/checklist/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('CHECKLISTVIEWBYCHECKLIST', '/mod/checklist/view.php?checklist=$1', 'checklist');
        // Checklist report by cm->id and forum->id.
        $rules[] = new restore_decode_rule('CHECKLISTREPORTBYID', '/mod/checklist/report.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('CHECKLISTREPORTBYCHECKLIST', '/mod/checklist/report.php?checklist=$1', 'checklist');
        // Checklist edit by cm->id and forum->id.
        $rules[] = new restore_decode_rule('CHECKLISTEDITBYID', '/mod/checklist/edit.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('CHECKLISTEDITBYCHECKLIST', '/mod/checklist/edit.php?checklist=$1', 'checklist');
        return $rules;
        */
    }

    /**
     * Match up moduleids after restore is complete
     * @throws dml_exception
     */
    public function after_restore() {

        global $DB;

        // ENTFAELLT ALLES //.

        // Find all the items that have a 'moduleid' but are not headings and match them up to the newly-restored activities.
        //$items = $DB->get_records_select('checklist_item', 'checklist = ? AND moduleid > 0 AND itemoptional <> 2',
        //                                 array($this->get_activityid()));
        //
        //foreach ($items as $item) {
        //    $moduleid = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'course_module', $item->moduleid);
        //    if ($moduleid) {
        //        // Match up the moduleid to the restored activity module.
        //        $DB->set_field('checklist_item', 'moduleid', $moduleid->newitemid, array('id' => $item->id));
        //    } else {
        //        // Does not match up to a restored activity module => delete the item + associated user data.
        //        $DB->delete_records('checklist_check', array('item' => $item->id));
        //        $DB->delete_records('checklist_comment', array('itemid' => $item->id));
        //        $DB->delete_records('checklist_comment_student', array('itemid' => $item->id));
        //        $DB->delete_records('checklist_item', array('id' => $item->id));
        //    }
        //}
    }


    /**
     * Added fix from https://tracker.moodle.org/browse/MDL-34172
     */

    /**
     * Define the restore log rules that will be applied by the
     * restore_logs_processor when restoring elediachecklist logs. It must return one array
     * of restore_log_rule objects
     * @return restore_log_rule[]
     */
    public static function define_restore_log_rules() {
        $rules = array();
        // OLD //.
        //$rules[] = new restore_log_rule('checklist', 'add', 'view.php?id={course_module}', '{folder}');
        //$rules[] = new restore_log_rule('checklist', 'edit', 'edit.php?id={course_module}', '{folder}');
        //$rules[] = new restore_log_rule('checklist', 'view', 'view.php?id={course_module}', '{folder}');
        // NEW //.

        //public function __construct($module, $action, $urlread, $inforead,
        //        $modulewrite = null, $actionwrite = null, $urlwrite = null, $infowrite = null) {

        // Betrifft wohl nur? die Logdaten?

        //                              $module,           $action, $urlread,                      $inforead,
        $rules[] = new restore_log_rule('elediachecklist', 'add',   'view.php?id={course_module}', '{elediachecklist}');
        //$rules[] = new restore_log_rule('elediachecklist', 'edit',  'edit.php?id={course_module}', '{elediachecklist}');
        $rules[] = new restore_log_rule('elediachecklist', 'view',  'view.php?id={course_module}', '{elediachecklist}');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the restore_logs_processor when restoring
     * course logs. It must return one array of
     * restore_log_rule objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     * @return restore_log_rule[]
     */
    public static function define_restore_log_rules_for_course() {
        $rules = array();
        // OLD //.
        //$rules[] = new restore_log_rule('checklist', 'view all', 'index.php?id={course}', null);
        // NEW //.
        $rules[] = new restore_log_rule('elediachecklist', 'view all', 'index.php?id={course}', null);

        return $rules;
    }

}
