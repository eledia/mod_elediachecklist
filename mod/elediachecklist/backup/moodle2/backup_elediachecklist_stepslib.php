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
 * Backup steps.
 * @copyright Davo Smith <moodle@davosmith.co.uk>
 * @package mod_checklist
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that will be used by the backup_forum_activity_task
 */


/**
 * Define the complete elediachecklist structure for backup, with file and id annotations
 */
class backup_elediachecklist_activity_structure_step extends backup_activity_structure_step {

    /**
     * Define the backup structure
     * @return backup_nested_element
     * @throws base_element_struct_exception
     * @throws base_step_exception
     */
    protected function define_structure() {

        //--------------------------------------------------------------------------------------------------------------------------
        // NEU
        //

        // 1 ---------- To know if we are including userinfo.
        // ??? Was ist das?

        $userinfo = $this->get_setting_value('userinfo');
        //$str = '<pre>'.print_r($userinfo, true).'</pre>';
        //norbertLog($str);
        //die();


        // 2 ---------- Define each element separated.

        $elediachecklist = new backup_nested_element('elediachecklist', array('id'), array(
            // ------------------------------------- //.
            // 'id'     -> NEIN -> in $attributes genannt
            // 'course' -> NEIN -> nicht noetig, not needed (die Kurs-ID ist immer 'automatatisch' beim Export/Import bekannt)
            // ------------------------------------- //.
            //'id',//.
            //'course',//.
            'name', 'intro', 'introformat', 'timecreated', 'timemodified', 'useritemsallowed',
            'teacheredit', 'theme', 'duedatesoncalendar', 'teachercomments', 'maxgrade', 'autopopulate', 'autoupdate',
            'completionpercent', 'completionpercenttype', 'emailoncomplete', 'lockteachermarks'
        ));

        //$elediachecklistitems = new backup_nested_element('elediachecklistitems');

        //$elediachecklistitem = new backup_nested_element('elediachecklistitem', array('id'), array(
        //    // ------------------------------------- //.
        //    // 'id'                  -> NEIN -> in $attributes genannt
        //    // 'checklist'           -> NEIN -> in set_source_table() genannt
        //    // 'userid'              -> JA   -> wegen Verknuepfung -> annotate_ids()
        //    // 'moduleid'            -> JA   -> wegen Verknuepfung -> annotate_ids()
        //    // 'eventid'             -> NEIN -> {event}     ???
        //    // 'groupingid'          -> NEIN -> {groupings} ???
        //    // 'openlinkinnewwindow' -> NEIN -> immer 0     ???
        //    // ------------------------------------- //.
        //    //'id',//.
        //    //'checklist',//.
        //    'userid', 'displaytext', 'position', 'indent', 'itemoptional', 'duetime',
        //   //'eventid',//.
        //    'colour', 'moduleid', 'hidden',
        //    //'groupingid',//.
        //    'linkcourseid', 'linkurl',
        //    //'openlinkinnewwindow',//.
        //    'emailtext'
        //));

        //$elediachecklistchecks = new backup_nested_element('elediachecklistchecks');

        //$elediachecklistcheck = new backup_nested_element('elediachecklistcheck', array('id'), array(
        //    // ------------------------------------- //.
        //    // 'id'        -> NEIN -> in $attributes genannt
        //    // 'item'      -> NEIN -> in set_source_table() genannt
        //    // 'userid'    -> JA   -> wegen Verknuepfung -> annotate_ids()
        //    // 'teacherid' -> JA   -> wegen Verknuepfung -> annotate_ids()
        //    // ------------------------------------- //.
        //    //'id',//.
        //    //'item',//.
        //    'userid', 'usertimestamp', 'teachermark', 'teachertimestamp', 'teacherid'
        //));

        //$elediachecklistcomments = new backup_nested_element('elediachecklistcomments');

        //$elediachecklistcomment = new backup_nested_element('elediachecklistcomment', array('id'), array(
        //    // ------------------------------------- //.
        //    // 'id'        -> NEIN -> in $attributes genannt
        //    // 'itemid'    -> NEIN -> in set_source_table() genannt
        //    // 'userid'    -> JA   -> wegen Verknuepfung -> annotate_ids()
        //    // 'commentby' -> JA   -> wegen Verknuepfung -> annotate_ids()
        //    // ------------------------------------- //.
        //    //'id',//.
        //    //'itemid',//.
        //   'userid', 'commentby', 'text'
        //));

        //$elediachecklistitemdates = new backup_nested_element('$elediachecklistitemdates');

        //$elediachecklistitemdate = new backup_nested_element('elediachecklistitemdate', array('id'), array(
        //    // ------------------------------------- //.
        //    // 'id'      -> NEIN -> in $attributes genannt
        //    // 'examid'  -> ???  -> eledia_admininexamdates.id
        //    // 'checkid' -> ???  -> elediachecklist__item.id -> immer 7 // -> keine Annotations, da feste IDs in Tabelle
        //    // ------------------------------------- //.
        //    //'id',//.
        //    'examid', 'checkid', 'checkdate'
        //));

        //$elediachecklistmycheck = new backup_nested_element('elediachecklistmycheck', array('id'), array(
        //    // ------------------------------------- //.
        //    // 'id'           -> NEIN -> in $attributes genannt
        //    // 'id_item'      -> ???  -> elediachecklist__my_item.id
        //    // 'id_checklist' -> ???  -> immer 0
        //    // 'id_exam'      -> ???  -> eledia_admininexamdates.id
        //    // ------------------------------------- //.
        //    //'id',//
        //    'id_item', 'id_checklist', 'id_exam'
        //));

        //$elediachecklistmyitem = new backup_nested_element('elediachecklistmyitem', array('id'), array(
        //    // ------------------------------------- //.
        //    // 'id' -> NEIN -> in $attributes genannt
        //    // ------------------------------------- //.
        //    //'id',//.
        //    'displaytext', 'is_checkbox', 'type'
        //));


        // 3 ---------- Build the tree.

        //$elediachecklist->add_child($elediachecklistitems);
        //$elediachecklistitems->add_child($elediachecklistitem);

        //$elediachecklistitem->add_child($elediachecklistchecks);
        //$elediachecklistchecks->add_child($elediachecklistcheck);

        //$elediachecklistitem->add_child($elediachecklistcomments);
        //$elediachecklistcomments->add_child($elediachecklistcomment);

        //$elediachecklistitem->add_child($elediachecklistitemdates);
        //$elediachecklistitemdates->add_child($elediachecklistitemdate);


        // 4 ---------- Define sources.

        $elediachecklist->set_source_table('elediachecklist', array('id' => backup::VAR_ACTIVITYID));

        //if ($userinfo) {
        //    $elediachecklistitem->set_source_table('elediachecklist__item', array('checklist' => backup::VAR_PARENTID));
        //    $elediachecklistcheck->set_source_table('elediachecklist__check', array('item' => backup::VAR_PARENTID));
        //    $elediachecklistcomment->set_source_table('elediachecklist__comment', array('itemid' => backup::VAR_PARENTID));
        //    $elediachecklistitemdate->set_source_table('elediachecklist__item_date', array('checkid' => backup::VAR_PARENTID));
        //} else {
        //    $elediachecklistitem->set_source_sql('SELECT * FROM {elediachecklist__item} WHERE userid = 0 AND checklist = ?', array(backup::VAR_PARENTID));
        //    $elediachecklistitemdate->set_source_table('elediachecklist__item_date', array('checkid' => backup::VAR_PARENTID));
        //}


        // 5 ---------- Define id annotations.
        // FK-Beziehungen

        // In der Tabelle 'elediachecklist__item' verweist das Feld 'userid' auf die Tabelle 'user'.
        // In der Tabelle 'elediachecklist__item' verweist das Feld 'moduleid' auf die Tabelle 'course_modules'.
        //$elediachecklistitem->annotate_ids('user', 'userid');
        //$elediachecklistitem->annotate_ids('course_modules', 'moduleid');
        // ??? Was ist mit all den anderen IDs in 'elediachecklist__item'?
        // ??? 'eventid' nicht genannt
        // ??? 'groupingid' nicht genannt
        // ??? 'linkcourseid' nicht genannt

        // In der Tabelle 'elediachecklist__check' verweist das Feld 'userid' auf die Tabelle 'user'.
        // In der Tabelle 'elediachecklist__check' verweist das Feld 'teacherid' auf die Tabelle 'eledia_admininexamdates'.
        //$elediachecklistcheck->annotate_ids('user', 'userid');
        //$elediachecklistcheck->annotate_ids('eledia_adminexamdates', 'teacherid');

        // In der Tabelle 'elediachecklist__comment' verweist das Feld 'userid' auf die Tabelle 'user'.
        // In der Tabelle 'elediachecklist__comment' verweist das Feld 'commentby' auf die Tabelle 'user'.
        //$elediachecklistcomment->annotate_ids('user', 'userid');
        //$elediachecklistcomment->annotate_ids('user', 'commentby');

        // In der Tabelle 'elediachecklist__item_date' verweist das Feld 'examid' auf die Tabelle 'eledia_admininexamdates'.
        //$elediachecklistitemdate->annotate_ids('examid', 'eledia_admininexamdates');


        // 6 ---------- Define file annotations.
        // ???
        $elediachecklist->annotate_files('mod_elediachecklist', 'intro', null); // This file area hasn't itemid.


        // ??? Wo/wie werden die Eintraege beruecksichtigt von: elediachecklist__my_item ???
        // 7 ---------- Return the root element (forum), wrapped into standard activity structure.
        return $this->prepare_activity_structure($elediachecklist);
    }

}
