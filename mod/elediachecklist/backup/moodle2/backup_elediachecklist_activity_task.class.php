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
 * Backup definition.
 *
 * @copyright Davo Smith <moodle@davosmith.co.uk>
 * @package mod_checklist
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


// SQL-Tabellen im Vergleich
// -------------------------
// elediachecklist           - checklist
// elediachecklist_item      - checklist_item
// elediachecklist_check     - checklist_check
// elediachecklist_comment   - checklist_comment
//                           - checklist_comment_student
//                           - checklist_comp_notification
// elediachecklist_item_date -
// elediachecklist_my_check  -
// elediachecklist_my_item   -

// API for activity modules
// https://docs.moodle.org/dev/Backup_API#API_for_activity_modules

// Backup 2.0 for developers
// https://docs.moodle.org/dev/Backup_2.0_for_developers            !!!
// https://docs.moodle.org/dev/Backup_2.0_general_architecture

// Restore 2.0 for developers
// https://docs.moodle.org/dev/Restore_2.0_for_developers


global $CFG;

require_once($CFG->dirroot.'/mod/elediachecklist/backup/moodle2/backup_elediachecklist_stepslib.php'); // Because it exists (must).
require_once($CFG->dirroot.'/mod/elediachecklist/backup/moodle2/backup_elediachecklist_settingslib.php'); // Because it exists (optional).

/**
 * Checklist backup task that provides all the settings and steps to perform one complete backup of the activity
 */
class backup_elediachecklist_activity_task extends backup_activity_task {

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
        // Forum only has one structure step.
        // ALT
        //$this->add_step(new backup_checklist_activity_structure_step('checklist structure', 'checklist.xml'));
        // NEU
        $this->add_step(new backup_elediachecklist_activity_structure_step('elediachecklist_structure', 'elediachecklist.xml'));
    }

    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links
     * @param string $content
     * @return string
     */
    public static function encode_content_links($content) {

        // See, inverse function(?):                                    //.
        // restore_elediachecklist_activity_task::define_decode_rules() //.

        global $CFG;

        $base = preg_quote($CFG->wwwroot, "/");

        // ALT
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

        // NEU

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
//        $search = "/(".$base."\/mod\/elediachecklist\/view.php\?eledia\=)([0-9]+)/";
//        $content = preg_replace($search, '$@ELEDIACHECKLISTVIEWBYELEDIA*$2@$', $content);

        // Link to checklist report by moduleid.
        // moduleid = Aktivitaets-ID // 106
        // https://ngeiges.eledia.de/moodle311/public_html/mod/elediachecklist/report.php?id=106 //.
        //$search = "/(".$base."\/mod\/elediachecklist\/report.php\?id\=)([0-9]+)/";
        //$content = preg_replace($search, '$@ELEDIACHECKLISTREPORTBYID*$2@$', $content);

        // DIESE OPTION GIBT ES NICHT IN DER DATEI report.php !!!
        // Link to checklist report by id.
        // Checklist-ID = 1
        // https://ngeiges.eledia.de/moodle311/public_html/mod/elediachecklist/report.php?eledia=1 //.
        //$search = "/(".$base."\/mod\/elediachecklist\/report.php\?eledia\=)([0-9]+)/";
        //$content = preg_replace($search, '$@ELEDIACHECKLISTREPORTBYELEDIA*$2@$', $content);

        // edit.php - GESPERRT - Leerausgabe !!!
        // Link to checklist edit by moduleid.
        // moduleid = Aktivitaets-ID // 58
        // https://ngeiges.eledia.de/moodle311/public_html/mod/elediachecklist/edit.php?id=58 //.
        //$search = "/(".$base."\/mod\/elediachecklist\/edit.php\?id\=)([0-9]+)/";
        //$content = preg_replace($search, '$@ELEDIACHECKLISTEDITBYID*$2@$', $content);

        // DIESE OPTION GIBT ES NICHT IN DER DATEI report.php !!!
        // Link to checklist edit by id.
        // Checklist-ID = 1
        // https://ngeiges.eledia.de/moodle311/public_html/mod/elediachecklist/edit.php?eledia=1 //.
        //$search = "/(".$base."\/mod\/checklist\/edit.php\?checklist\=)([0-9]+)/";
        //$content = preg_replace($search, '$@CHECKLISTEDITBYCHECKLIST*$2@$', $content);

        return $content;
    }
}
