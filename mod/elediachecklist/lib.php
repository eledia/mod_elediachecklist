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
 * Functions to link into the main Moodle API
 * @copyright Davo Smith <moodle@davosmith.co.uk>
 * @package mod_elediachecklist
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** Do not sent emails on completion */
define("CHECKLIST_EMAIL_NO", 0);
/** Send emails to students on completion */
define("CHECKLIST_EMAIL_STUDENT", 1);
/** Send emails to teachers on completion */
define("CHECKLIST_EMAIL_TEACHER", 2);
/** Send emails to students and teachers on completion */
define("CHECKLIST_EMAIL_BOTH", 3);

/** Teacher marked item as "No" */
define("CHECKLIST_TEACHERMARK_NO", 2);
/** Teacher marked item as "Yes" */
define("CHECKLIST_TEACHERMARK_YES", 1);
/** No teacher mark */
define("CHECKLIST_TEACHERMARK_UNDECIDED", 0);

/** Checklist updated by students */
define("CHECKLIST_MARKING_STUDENT", 0);
/** Checklist updated by teachers */
define("CHECKLIST_MARKING_TEACHER", 1);
/** Checklist updated by students and teachers */
define("CHECKLIST_MARKING_BOTH", 2);

/** Linked activities should not update item status */
define("CHECKLIST_AUTOUPDATE_NO", 0);
/** Linked activites should update item status */
define("CHECKLIST_AUTOUPDATE_YES", 2);
/** Linked activites should update items status, but can be overridden by students */
define("CHECKLIST_AUTOUPDATE_YES_OVERRIDE", 1);

/** Do not import activities into the checklist */
define("CHECKLIST_AUTOPOPULATE_NO", 0);
/** Import activities from the current section into the checklist */
define("CHECKLIST_AUTOPOPULATE_SECTION", 2);
/** Import all activities in the course into the checklist */
define("CHECKLIST_AUTOPOPULATE_COURSE", 1);

/** Maximum indend allowed */
define("CHECKLIST_MAX_INDENT", 10);

global $CFG;
require_once(__DIR__.'/locallib.php');
require_once($CFG->libdir.'/completionlib.php');

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $checklist An object from the form in mod_form.php
 * @return int The id of the newly inserted checklist record
 */
function elediachecklist_add_instance($checklist) {

    /* @var pgsql_native_moodle_database $DB */

    global $DB;

    //$str = '<pre>'.print_r($checklist, true).'</pre>';
    //echo $str;
    //die();

    // Allow only one instance with ID 1. //.
    // Is there already one?              //.
    $elediachecklist = $DB->get_record('elediachecklist', ['id' => 1]);
    if($elediachecklist) {
        $msg  = "Die Aktivität 'eLeDia Checklist' kann nur einmal angelegt werden. "."\n";
        $msg .= "Es ist nur eine Instanz von 'eLeDia Checklist' mit der ID = 1 im System zulässig.";
        // Attention: Maybe this entry is marked as 'deleted' //.
        // and not yet physically really deleted.             //.
        $conditions = array(
            'course'             => $checklist->course,
            'module'             => $checklist->module,
            'instance'           => 1,
            'deletioninprogress' => 1
        );
        $deleted = $DB->get_record('course_modules', $conditions);
        if($deleted) {
            $msg  = "Die Aktivität 'eLeDia Checklist' kann nur einmal angelegt werden. "."\n";
            $msg .= "Es ist nur eine Instanz von 'eLeDia Checklist' mit der ID = 1 im System zulässig. "."\n";
            $msg .= "Diese befindet sich gerade im Löschprozess. "."\n";
            $msg .= "Warten Sie den Cron-Job ab und legen Sie hier die Aktivität erneut an.";
        }
        mtrace($msg);
        // ??? //.
        return;
    }

    //------------------------------------//.
    // TABLE: elediachecklist             //.
    //------------------------------------//.
    // id                    - int        //.
    // course                - int        //.
    // name                  - text       //.
    // intro                 - text, null //.
    // introformat           - int        //.
    // timecreated           - int        //.
    // timemodified          - int        //.
    // useritemsallowed      - int, null  //.
    // teacheredit           - int, null  //.
    // theme                 - text, null //.
    // duedatesoncalendar    - int, null  //.
    // teachercomments       - int, null  //.
    // maxgrade              - int        //.
    // autopopulate          - int, null  //.
    // autoupdate            - int, null  //.
    // completionpercent     - int, null  //.
    // completionpercenttype - text       //.
    // emailoncomplete       - int, null  //.
    // lockteachermarks      - int, null  //.
    //------------------------------------//.

    // Add/modify values.
    $checklist->id = 1;
    $checklist->timecreated = time();
    $checklist->timemodified = 0;
    if(!isset($checklist->theme)) {
        $checklist->theme = 'default';
    }

    // All Parameters
    $params = array(
        'id'                    => $checklist->id,
        'course'                => $checklist->course,
        'name'                  => $checklist->name,
        'intro'                 => $checklist->intro,
        'introformat'           => $checklist->introformat,
        'timecreated'           => $checklist->timecreated,
        'timemodified'          => $checklist->timemodified,
        'useritemsallowed'      => $checklist->useritemsallowed,
        'teacheredit'           => $checklist->teacheredit,
        'theme'                 => $checklist->theme,
        'duedatesoncalendar'    => $checklist->duedatesoncalendar,
        'teachercomments'       => $checklist->teachercomments,
        'maxgrade'              => $checklist->maxgrade,
        'autopopulate'          => $checklist->autopopulate,
        'autoupdate'            => $checklist->autoupdate,
        'completionpercent'     => $checklist->completionpercent,
        'completionpercenttype' => $checklist->completionpercenttype,
        'emailoncomplete'       => $checklist->emailoncomplete,
        'lockteachermarks'      => $checklist->lockteachermarks
    );

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

    elediachecklist_grade_item_update($checklist);

    if (!empty($checklist->completionexpected)) {
        \core_completion\api::update_completion_date_event($checklist->coursemodule, 'elediachecklist', $checklist->id,
                                                           $checklist->completionexpected);
    }

    return $checklist->id;

    // Note:                                                               //.
    // Language pack for plugin 'block_eledia_adminexamdates':             //.
    // 'checklistlink' -> mod/elediachecklist/tabtermin.php?id=106&examid= //.
    //                    106 = course_modules.id of this instance         //.
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $checklist An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function elediachecklist_update_instance($checklist) {

    global $DB;

    $checklist->timemodified = time();
    $checklist->id = $checklist->instance;

    $oldrecord = $DB->get_record('elediachecklist', ['id' => $checklist->id]);

    $newmax = $checklist->maxgrade;
    $oldmax = $oldrecord->maxgrade;

    $oldcompletion = $oldrecord->completionpercent;
    $newcompletion = $checklist->completionpercent ?? $oldcompletion;
    $oldcompletiontype = $oldrecord->completionpercenttype;
    $newcompletiontype = $checklist->completionpercenttype ?? $oldcompletiontype;

    $newautoupdate = $checklist->autoupdate;
    $oldautoupdate = $oldrecord->autoupdate;

    $newteacheredit = $checklist->teacheredit;
    $oldteacheredit = $oldrecord->teacheredit;

    $DB->update_record('elediachecklist', $checklist);

    // Add or remove all calendar events, as needed.
    $course = $DB->get_record('course', array('id' => $checklist->course));
    $cm = get_coursemodule_from_instance('elediachecklist', $checklist->id, $course->id);
    $chk = new checklist_class($cm->id, 0, $checklist, $cm, $course);
    $chk->setallevents();

    elediachecklist_grade_item_update($checklist);
    if ($newmax != $oldmax) {
        elediachecklist_update_grades($checklist);
    } else if ($newcompletion && ($newcompletion != $oldcompletion || $newcompletiontype != $oldcompletiontype)) {
        // This will already be updated if checklist_update_grades() is called.
        $ci = new completion_info($course);
        $context = context_module::instance($cm->id);
        if (get_config('mod_elediachecklist', 'onlyenrolled')) {
            $users = get_enrolled_users($context, 'mod/elediachecklist:updateown', 0, 'u.id', null, 0, 0, true);
        } else {
            $users = get_users_by_capability($context, 'mod/elediachecklist:updateown', 'u.id');
        }
        foreach ($users as $user) {
            $ci->update_state($cm, COMPLETION_UNKNOWN, $user->id);
        }
    }
    if ($newautoupdate) {
        if (!$oldautoupdate) {
            $chk->update_all_autoupdate_checks();
        } else {
            $oldautoteacher = ($oldteacheredit == CHECKLIST_MARKING_TEACHER);
            $newautoteacher = ($newteacheredit == CHECKLIST_MARKING_TEACHER);
            if ($oldautoteacher != $newautoteacher) {
                // Just switched to/from teacher-only marking => automatic checkmarks need updating
                // (as they are updating a different value from before).
                $chk->update_all_autoupdate_checks();
            }
        }
    }

    if (!empty($checklist->completionexpected)) {
        \core_completion\api::update_completion_date_event($checklist->coursemodule, 'elediachecklist', $checklist->id,
                                                           $checklist->completionexpected);
    }

    return true;
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function elediachecklist_delete_instance($id) {

    global $DB;

    if (!$checklist = $DB->get_record('elediachecklist', array('id' => $id))) {
        return false;
    }

    // Remove all calendar events.
    if ($checklist->duedatesoncalendar) {
        $checklist->duedatesoncalendar = false;
        $course = $DB->get_record('course', array('id' => $checklist->course));
        $cm = get_coursemodule_from_instance('elediachecklist', $checklist->id, $course->id);
        if ($cm) { // Should not be false, but check, just in case...
            $chk = new checklist_class($cm->id, 0, $checklist, $cm, $course);
            $chk->setallevents();
        }
    }

    // Do not execute! Dangerous for $checklist->id = 1 !
    // $checklist->id = 1 should be the only one!
    //$items = $DB->get_records('elediachecklist#item', array('checklist' => $checklist->id), '', 'id');
    //if (!empty($items)) {
    //    $items = array_keys($items);
    //    $DB->delete_records_list('elediachecklist#check', 'item', $items);
    //    $DB->delete_records_list('elediachecklist#comment', 'itemid', $items);
    //    $DB->delete_records('elediachecklist#item', array('checklist' => $checklist->id));
    //}
    $DB->delete_records('elediachecklist', array('id' => $checklist->id));

    elediachecklist_grade_item_delete($checklist);

    return true;
}

/**
 * Update all checklist grades on the site
 */
function elediachecklist_update_all_grades() {
    global $DB;

    $checklists = $DB->get_records('elediachecklist');
    foreach ($checklists as $checklist) {
        elediachecklist_update_grades($checklist);
    }
}

/**
 * Update the checklist grades
 * @param object $checklist
 * @param int $userid
 */
function elediachecklist_update_grades($checklist, $userid = 0) {
    global $DB;

    $params = array(
        'checklist' => $checklist->id,
        'userid' => 0,
        'itemoptional' => CHECKLIST_OPTIONAL_NO,
        'hidden' => CHECKLIST_HIDDEN_NO
    );
    $items = \mod_elediachecklist\local\checklist_item::fetch_all($params);

    if (!$items) {
        return;
    }
    if (!$course = $DB->get_record('course', array('id' => $checklist->course))) {
        return;
    }
    if (!$cm = get_coursemodule_from_instance('elediachecklist', $checklist->id, $course->id)) {
        return;
    }
    $context = context_module::instance($cm->id);

    $checkgroupings = false; // Don't check items against groupings unless we really have to.
    $groupings = checklist_class::get_course_groupings($course->id);
    foreach ($items as $item) {
        if ($item->groupingid && isset($groupings[$item->groupingid])) {
            $checkgroupings = true;
            break;
        }
    }

    if ($checklist->teacheredit == CHECKLIST_MARKING_STUDENT) {
        $date = ', MAX(c.usertimestamp) AS datesubmitted';
        $where = 'c.usertimestamp > 0';
    } else {
        $date = ', MAX(c.teachertimestamp) AS dategraded';
        $where = 'c.teachermark = '.CHECKLIST_TEACHERMARK_YES;
    }

    if ($checkgroupings) {
        if ($userid) {
            $users = $DB->get_records('user', array('id' => $userid), null, 'id, firstname, lastname');
        } else {
            if (get_config('mod_elediachecklist', 'onlyenrolled')) {
                $users = get_enrolled_users($context, 'mod/elediachecklist:updateown', 0, 'u.id, u.firstname, u.lastname',
                                            null, 0, 0, true);
            } else {
                $users = get_users_by_capability($context, 'mod/elediachecklist:updateown', 'u.id, u.firstname, u.lastname');
            }
            if (!$users) {
                return;
            }
        }

        $grades = array();

        // With groupings, need to update each user individually (as each has different groupings).
        foreach ($users as $uid => $user) {
            $groupings = checklist_class::get_user_groupings($uid, $course->id);

            $total = 0;
            $itemlist = [];
            foreach ($items as $item) {
                if ($item->groupingid) {
                    if (!in_array($item->groupingid, $groupings)) {
                        continue;
                    }
                }
                $itemlist[] = $item->id;
                $total++;
            }
            $itemlist = implode(',', $itemlist);

            if (!$total) { // No items - set score to 0.
                $ugrade = new stdClass;
                $ugrade->userid = $uid;
                $ugrade->rawgrade = 0;
                $ugrade->date = time();

            } else {
                $tab = elediachecklist_tab('eledia_adminexamdates_chk'); // elediachecklist__check
                $sql = 'SELECT (SUM(CASE WHEN '.$where.' THEN 1 ELSE 0 END) * ? / ? ) AS rawgrade'.$date;
                $sql .= " FROM {".$tab."} c ";
                $sql .= " WHERE c.item IN ($itemlist)";
                $sql .= ' AND c.userid = ? ';

                $ugrade = $DB->get_record_sql($sql, array($checklist->maxgrade, $total, $uid));
                if (!$ugrade) {
                    $ugrade = new stdClass;
                    $ugrade->rawgrade = 0;
                    $ugrade->date = time();
                }
                $ugrade->userid = $uid;
            }

            $ugrade->firstname = $user->firstname;
            $ugrade->lastname = $user->lastname;

            $grades[$uid] = $ugrade;
        }

    } else {
        // No need to check groupings, so update all student grades at once.

        if ($userid) {
            $users = $userid;
        } else {
            if (get_config('mod_elediachecklist', 'onlyenrolled')) {
                $users = get_enrolled_users($context, 'mod/elediachecklist:updateown', 0, 'u.id', null, 0, 0, true);
            } else {
                $users = get_users_by_capability($context, 'mod/elediachecklist:updateown', 'u.id', '', '', '', '', '', false);
            }
            if (!$users) {
                return;
            }
            $users = array_keys($users);
        }

        $total = count($items);

        [$usql, $uparams] = $DB->get_in_or_equal($users, SQL_PARAMS_NAMED);
        [$isql, $iparams] = $DB->get_in_or_equal(array_keys($items), SQL_PARAMS_NAMED);

        if (class_exists('\core_user\fields')) {
            $namesql = \core_user\fields::for_name()->get_sql('u', true);
        } else {
            $namesql = (object)[
                'selects' => ','.get_all_user_name_fields(true, 'u'),
                'joins' => '',
                'params' => [],
                'mappings' => [],
            ];
        }

        $tab = elediachecklist_tab('eledia_adminexamdates_chk'); // elediachecklist__check
        $sql = "
             SELECT u.id AS userid, (SUM(CASE WHEN $where THEN 1 ELSE 0 END) * :maxgrade / :total ) AS rawgrade $date
                    {$namesql->selects}
               FROM {user} u
          LEFT JOIN {".$tab."} c ON u.id = c.userid
                    {$namesql->joins}
              WHERE u.id $usql
                AND c.item $isql
           GROUP BY u.id {$namesql->selects}
        ";

        $params = array_merge(['maxgrade' => $checklist->maxgrade, 'total' => $total], $uparams, $iparams, $namesql->params);
        $grades = $DB->get_records_sql($sql, $params);
    }

    foreach ($grades as $grade) {
        // Log completion of checklist.
        if ($grade->rawgrade == $checklist->maxgrade) {
            if ($checklist->emailoncomplete) {
                // Do not send another email if this checklist was already 'completed' in the last hour.
                if (!checklist_sent_email_recently($cm->id)) {
                    if (!isset($context)) {
                        $context = context_module::instance($cm->id);
                    }

                    // Prepare email content.
                    $details = new stdClass();
                    $details->user = fullname($grade);
                    $details->checklist = s($checklist->name);
                    $details->coursename = $course->fullname;

                    if ($checklist->emailoncomplete == CHECKLIST_EMAIL_TEACHER
                        || $checklist->emailoncomplete == CHECKLIST_EMAIL_BOTH
                    ) {
                        // Email will be sent to the all teachers who have capability.
                        $subj = get_string('emailoncompletesubject', 'elediachecklist', $details);
                        $content = get_string('emailoncompletebody', 'elediachecklist', $details);
                        $content .= new moodle_url('/mod/elediachecklist/view.php', array('id' => $cm->id));

                        $groups = groups_get_all_groups($course->id, $grade->userid, $cm->groupingid);

                        $groupmode = groups_get_activity_groupmode($cm, $course);

                        if (is_array($groups) && count($groups) > 0 && $groupmode != NOGROUPS) {
                            $groups = array_keys($groups);
                        } else if ($groupmode != NOGROUPS) {
                            // If the user is not in a group, and the checklist is set to group mode,
                            // then set $groups to a non-existant id so that only users with
                            // 'moodle/site:accessallgroups' get notified.
                            $groups = -1;
                        } else {
                            $groups = '';
                        }

                        if ($recipients = get_users_by_capability($context, 'mod/elediachecklist:emailoncomplete',
                                                                  'u.*', '', '', '', $groups)) {
                            foreach ($recipients as $recipient) {
                                email_to_user($recipient, $grade, $subj, $content, '', '', '', false);
                            }
                        }
                    }
                    if ($checklist->emailoncomplete == CHECKLIST_EMAIL_STUDENT
                        || $checklist->emailoncomplete == CHECKLIST_EMAIL_BOTH
                    ) {
                        // Email will be sent to the student who completes this checklist.
                        $subj = get_string('emailoncompletesubjectown', 'elediachecklist', $details);
                        $content = get_string('emailoncompletebodyown', 'elediachecklist', $details);
                        $content .= new moodle_url('/mod/elediachecklist/view.php', array('id' => $cm->id));

                        $recipientstudent = $DB->get_record('user', array('id' => $grade->userid));
                        email_to_user($recipientstudent, $grade, $subj, $content, '', '', '', false);
                    }
                }
            }
            $params = array(
                'contextid' => $context->id,
                'objectid' => $checklist->id,
                'userid' => $grade->userid,
            );
            $event = \mod_elediachecklist\event\checklist_completed::create($params);
            $event->trigger();
        }
        $ci = new completion_info($course);
        if ($cm->completion == COMPLETION_TRACKING_AUTOMATIC) {
            $ci->update_state($cm, COMPLETION_UNKNOWN, $grade->userid);
        }
    }

    elediachecklist_grade_item_update($checklist, $grades);
}

/**
 * Make sure multiple completion emails are not sent from the same user within the last hour.
 * (Assuming they don't log out and log back in again).
 *
 * @param int $cmid
 * @return bool - true if an email has already been sent recently
 */
function elediachecklist_sent_email_recently($cmid) {
    global $SESSION;
    if (!isset($SESSION->checklist_recent_email)) {
        $SESSION->checklist_recent_email = array();
    }
    if (!empty($SESSION->checklist_recent_email[$cmid])) {
        $nexttime = $SESSION->checklist_recent_email[$cmid] + HOURSECS;
        if (time() < $nexttime) {
            return true;
        }
    }
    $SESSION->checklist_recent_email[$cmid] = time();
    return false;
}

/**
 * Delete the checklist grade item.
 * @param object $checklist
 * @return int
 */
function elediachecklist_grade_item_delete($checklist) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');
    if (!isset($checklist->courseid)) {
        $checklist->courseid = $checklist->course;
    }

    return grade_update('mod/elediachecklist', $checklist->courseid, 'mod', 'elediachecklist', $checklist->id, 0, null, array('deleted' => 1));
}

/**
 * Update the checklist grade items
 * @param object $checklist
 * @param null $grades
 * @return int
 */
function elediachecklist_grade_item_update($checklist, $grades = null) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    if (!isset($checklist->courseid)) {
        $checklist->courseid = $checklist->course;
    }

    $params = array('itemname' => $checklist->name);
    if ($checklist->maxgrade > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax'] = $checklist->maxgrade;
        $params['grademin'] = 0;
    } else {
        $params['gradetype'] = GRADE_TYPE_NONE;
    }

    if ($grades === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }

    return grade_update('mod/elediachecklist', $checklist->courseid, 'mod', 'elediachecklist', $checklist->id, 0, $grades, $params);
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $checklist
 * @return null
 */
function elediachecklist_user_outline($course, $user, $mod, $checklist) {

    global $DB;

    // Handle groupings.
    $groupingsql = checklist_class::get_grouping_sql($user->id, $checklist->course);

    $tab = elediachecklist_tab('eledia_adminexamdates_itm'); // elediachecklist__item
    $sel  = 'checklist = ? AND userid = 0 AND itemoptional = '.CHECKLIST_OPTIONAL_NO;
    $sel .= ' AND hidden = '.CHECKLIST_HIDDEN_NO." AND $groupingsql";
    $items = $DB->get_records_select($tab, $sel, array($checklist->id), '', 'id');
    if (!$items) {
        return null;
    }

    $total = count($items);
    [$isql, $iparams] = $DB->get_in_or_equal(array_keys($items));

    $sql = "userid = ? AND item $isql AND ";
    if ($checklist->teacheredit == CHECKLIST_MARKING_STUDENT) {
        $sql .= 'usertimestamp > 0';
        $order = 'usertimestamp DESC';
    } else {
        $sql .= 'teachermark = '.CHECKLIST_TEACHERMARK_YES;
        $order = 'teachertimestamp DESC';
    }
    $params = array_merge(array($user->id), $iparams);

    $tab = elediachecklist_tab('eledia_adminexamdates_chk'); // elediachecklist__check
    $checks = $DB->get_records_select($tab, $sql, $params, $order);

    $return = null;
    if ($checks) {
        $return = new stdClass;

        $ticked = count($checks);
        $check = reset($checks);
        if ($checklist->teacheredit == CHECKLIST_MARKING_STUDENT) {
            $return->time = $check->usertimestamp;
        } else {
            $return->time = $check->teachertimestamp;
        }
        $percent = sprintf('%0d', ($ticked * 100) / $total);
        $return->info = get_string('progress', 'elediachecklist').': '.$ticked.'/'.$total.' ('.$percent.'%)';
    }

    return $return;
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $checklist
 * @return boolean
 */
function elediachecklist_user_complete($course, $user, $mod, $checklist) {
    $chk = new checklist_class($mod->id, $user->id, $checklist, $mod, $course);

    $chk->user_complete();

    return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in checklist activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @param object $course
 * @param bool $isteacher
 * @param int $timestart
 * @return boolean
 */
function elediachecklist_print_recent_activity($course, $isteacher, $timestart) {
    return false;  // True if anything was printed, otherwise false.
}

/**
 * Print an overview of the checklist
 * @param array $courses
 * @param array $htmlarray
 */
function elediachecklist_print_overview($courses, &$htmlarray) {
    global $USER, $CFG, $DB;

    $config = get_config('elediachecklist');
    if (isset($config->showmymoodle) && !$config->showmymoodle) {
        return; // Disabled via global config.
    }
    if (!isset($config->showcompletemymoodle)) {
        $config->showcompletemymoodle = 1;
    }
    if (!isset($config->showupdateablemymoodle)) {
        $config->showupdateablemymoodle = 1;
    }
    if (empty($courses) || !is_array($courses) || count($courses) == 0) {
        return;
    }

    if (!$checklists = get_all_instances_in_courses('elediachecklist', $courses)) {
        return;
    }

    $strchecklist = get_string('modulename', 'elediachecklist');

    foreach ($checklists as $checklist) {
        $showall = true;
        $context = context_module::instance($checklist->coursemodule);

        // If only the student is responsible for updating the checklist.
        if ($checklist->teacheredit == CHECKLIST_MARKING_STUDENT) {
            if ($showall = !has_capability('mod/elediachecklist:updateown', $context, null, false)) {
                if ($config->showupdateablemymoodle) {
                    continue;
                }
            }
        } else { // If the teacher is involved with updating the checklist.
            if ($config->showupdateablemymoodle) {
                continue;
            }
        }

        $progressbar = checklist_class::print_user_progressbar($checklist->id, $USER->id,
                                                               '270px', true, true,
                                                               !$config->showcompletemymoodle);
        if (empty($progressbar)) {
            continue;
        }

        if ($showall) { // Show all items whether or not they are checked off (as this user is unable to check them off).
            $groupingsql = checklist_class::get_grouping_sql($USER->id, $checklist->course);
            $tab = elediachecklist_tab('eledia_adminexamdates_itm'); // elediachecklist__item
            $dateitems = $DB->get_records_select($tab,
                                                 "checklist = ? AND duetime > 0 AND $groupingsql",
                                                 array($checklist->id),
                                                 'duetime');
        } else { // Show only items that have not been checked off.
            $groupingsql = checklist_class::get_grouping_sql($USER->id, $checklist->course, 'i.');
            $tab1 = elediachecklist_tab('eledia_adminexamdates_itm'); // elediachecklist__item
            $tab2 = elediachecklist_tab('eledia_adminexamdates_chk'); // elediachecklist__check
            $dateitems = $DB->get_records_sql("SELECT i.* FROM {".$tab1."} i
                                                 JOIN {".$tab2."} c ON c.item = i.id
                                                WHERE i.checklist = ? AND i.duetime > 0 AND c.userid = ? AND usertimestamp = 0
                                                  AND $groupingsql
                                                ORDER BY i.duetime", array($checklist->id, $USER->id));
        }

        $str = '<div class="checklist overview"><div class="name">'.$strchecklist.': '.
            '<a title="'.$strchecklist.'" href="'.$CFG->wwwroot.'/mod/elediachecklist/view.php?id='.$checklist->coursemodule.'">'.
            $checklist->name.'</a></div>';
        $str .= '<div class="info">'.$progressbar.'</div>';
        foreach ($dateitems as $item) {
            $str .= '<div class="info">'.format_string($item->displaytext).': ';
            if ($item->duetime > time()) {
                $str .= '<span class="itemdue">';
            } else {
                $str .= '<span class="itemoverdue">';
            }
            $str .= date('j M Y', $item->duetime).'</span></div>';
        }
        $str .= '</div>';
        if (empty($htmlarray[$checklist->course]['checklist'])) {
            $htmlarray[$checklist->course]['checklist'] = $str;
        } else {
            $htmlarray[$checklist->course]['checklist'] .= $str;
        }
    }
}

/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of newmodule. Must include every user involved
 * in the instance, independient of his role (student, teacher, admin...)
 * See other modules as example.
 *
 * @param int $checklistid ID of an instance of this module
 * @return mixed boolean/array of students
 */
function elediachecklist_get_participants($checklistid) {

    global $DB;

    $params = array($checklistid);

    $tab = elediachecklist_tab('eledia_adminexamdates_itm'); // elediachecklist__item
    $sql = 'SELECT DISTINCT u.id
              FROM {user} u
              JOIN {".$tab."} i ON i.userid = u.id
             WHERE i.checklist = ?';
    $userids1 = $DB->get_records_sql($sql, $params);

    $tab1 = elediachecklist_tab('eledia_adminexamdates_chk'); // elediachecklist__check
    $tab2 = elediachecklist_tab('eledia_adminexamdates_itm'); // elediachecklist__item
    $sql = 'SELECT DISTINCT u.id
              FROM {user} u
              JOIN {".$tab1."} c ON c.userid = u.id
              JOIN {".$tab2."} i ON i.id = c.item
             WHERE i.checklist = ?';
    $userids2 = $DB->get_records_sql($sql, $params);

    return $userids1 + $userids2;
}

/**
 * This function returns if a scale is being used by one checklist
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $checklistid ID of an instance of this module
 * @param int $scaleid
 * @return bool
 */
function elediachecklist_scale_used($checklistid, $scaleid) {
    return false;
}

/**
 * Checks if scale is being used by any instance of checklist.
 * This function was added in 1.9
 *
 * This is used to find out if scale used anywhere
 * @param int $scaleid
 * @return boolean True if the scale is used by any checklist
 */
function elediachecklist_scale_used_anywhere($scaleid) {
    return false;
}

/**
 * Execute post-install custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function elediachecklist_install() {
    return true;
}

/**
 * Execute post-uninstall custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function elediachecklist_uninstall() {
    return true;
}

/**
 * Get the form elements for the reset form
 * @param HTML_QuickForm $mform
 */
function elediachecklist_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'checklistheader', get_string('modulenameplural', 'elediachecklist'));
    $mform->addElement('checkbox', 'reset_checklist_progress', get_string('resetchecklistprogress', 'elediachecklist'));
}

/**
 * Get the options for the reset form
 * @param object $course
 * @return array
 */
function elediachecklist_reset_course_form_defaults($course) {
    return array('reset_checklist_progress' => 1);
}

/**
 * Reset the checklist userdata
 * @param object $data
 * @return array
 */
function elediachecklist_reset_userdata($data) {
    global $DB;

    $status = array();
    $component = get_string('modulenameplural', 'elediachecklist');
    $typestr = get_string('resetchecklistprogress', 'elediachecklist');
    $status[] = array('component' => $component, 'item' => $typestr, 'error' => false);

    if (!empty($data->reset_checklist_progress)) {
        $checklists = $DB->get_records('elediachecklist', array('course' => $data->courseid));
        if (!$checklists) {
            return $status;
        }

        [$csql, $cparams] = $DB->get_in_or_equal(array_keys($checklists));
        $tab = elediachecklist_tab('eledia_adminexamdates_itm'); // elediachecklist__item
        $items = $DB->get_records_select($tab, 'checklist '.$csql, $cparams);
        if (!$items) {
            return $status;
        }

        $itemids = array_keys($items);

        $tab = elediachecklist_tab('eledia_adminexamdates_chk'); // elediachecklist__check
        $DB->delete_records_list($tab, 'item', $itemids);

        $tab = elediachecklist_tab('eledia_adminexamdates_cmt'); // elediachecklist__comment
        $DB->delete_records_list($tab, 'itemid', $itemids);

        $tab = elediachecklist_tab('eledia_adminexamdates_itm'); // elediachecklist__item
        $sql = "checklist $csql AND userid <> 0";
        $DB->delete_records_select($tab, $sql, $cparams);

        // Reset the grades.
        foreach ($checklists as $checklist) {
            elediachecklist_grade_item_update($checklist, 'reset');
        }
    }

    return $status;
}

/**
 * Update calendar events
 * @param int $courseid
 * @param object|int|null $instance
 * @param object|null $cm
 * @return bool
 */
function elediachecklist_refresh_events($courseid = 0, $instance = null, $cm = null) {
    global $DB;

    if ($instance) {
        if (!is_object($instance)) {
            $instance = $DB->get_record('elediachecklist', ['id' => $instance], '*', MUST_EXIST);
        }
        $checklists = [$instance];
    } else if ($courseid) {
        $checklists = $DB->get_records('elediachecklist', array('course' => $courseid));
        $course = $DB->get_record('course', array('id' => $courseid));
    } else {
        $checklists = $DB->get_records('elediachecklist');
        $course = null;
    }

    foreach ($checklists as $checklist) {
        if ($checklist->duedatesoncalendar) {
            $cm = get_coursemodule_from_instance('elediachecklist', $checklist->id, $checklist->course);
            $chk = new checklist_class($cm->id, 0, $checklist, $cm, $course);
            $chk->setallevents();
        }
    }

    return true;
}

/**
 * What features does the checklist support?
 * @param string $feature
 * @return bool|null
 */
function elediachecklist_supports($feature) {
    global $CFG;
    if (!defined('FEATURE_SHOW_DESCRIPTION')) {
        // For backwards compatibility.
        define('FEATURE_SHOW_DESCRIPTION', 'showdescription');
    }

    if ((int)$CFG->branch < 28) {
        if ($feature === FEATURE_GROUPMEMBERSONLY) {
            return true;
        }
    }

    switch ($feature) {
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_COMPLETION_HAS_RULES:
            return true;
        // !!! //.
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;

        default:
            return null;
    }
}

/**
 * Calculate the completion state of the checklist for the given user.
 * Retained for compatibility with Moodle 3.10 and below.
 * @param object $course
 * @param object $cm
 * @param int $userid
 * @param int $type
 * @return bool
 */
function elediachecklist_get_completion_state($course, $cm, $userid, $type) {
    global $DB;

    if (!($checklist = $DB->get_record('elediachecklist', array('id' => $cm->instance)))) {
        throw new Exception("Can't find checklist {$cm->instance}");
    }

    $result = $type; // Default return value.

    if ($checklist->completionpercent) {
        list($ticked, $total) = checklist_class::get_user_progress($cm->instance, $userid);
        if ($checklist->completionpercenttype === 'items') {
            // Completionpercent is the actual number of items that need checking-off.
            $value = $checklist->completionpercent <= $ticked;
        } else {
            // Completionpercent is the percentage of items that need checking-off.
            $value = $checklist->completionpercent <= ($ticked * 100 / $total);
        }
        if ($type == COMPLETION_AND) {
            $result = $result && $value;
        } else {
            $result = $result || $value;
        }
    }

    return $result;
}

/**
 * Add extra info needed to output activity onto course page
 * @param object $coursemodule
 * @return cached_cm_info|false
 */
function elediachecklist_get_coursemodule_info($coursemodule) {
    global $DB;

    $fields = 'id, name, intro, introformat, completionpercent, completionpercenttype';
    if (!$checklist = $DB->get_record('elediachecklist', ['id' => $coursemodule->instance], $fields)) {
        return false;
    }

    $result = new cached_cm_info();
    $result->name = $checklist->name;
    if ($coursemodule->showdescription) {
        $result->content = format_module_intro('elediachecklist', $checklist, $coursemodule->id, false);
    }

    if ($coursemodule->completion == COMPLETION_TRACKING_AUTOMATIC) {
        // Needed by Moodle 3.11 and above - ignored by earlier versions.
        $result->customdata['customcompletionrules']['completionpercent'] = [
            $checklist->completionpercent, $checklist->completionpercenttype,
        ];
    }

    return $result;
}

/**
 *
 */

// SQL-Tabellen im Vergleich
// -------------------------
// elediachecklist           - checklist
// elediachecklist__item      - checklist_item
// elediachecklist__check     - checklist_check
// elediachecklist__comment   - checklist_comment
//                           - checklist_comment_student
//                           - checklist_comp_notification
// elediachecklist__item_date -
// elediachecklist__my_check  -
// elediachecklist__my_item   -


function elediachecklist_tab($name) {

    //$gettype = 'old';
    $gettype = 'new';
    // 'old' -> Funktioniert auch noch mit den alten Tabellen - ACHTUNG: Dann keine Installation vornehmen!
    // 'new' -> Kann erst installiert werden, wenn, Rene sein Plugin fertig hat.

    $names = array(
        // old name                 =>  new name
            'elediachecklist_check'     => 'eledia_adminexamdates_chk',    //
            'elediachecklist_comment'   => 'eledia_adminexamdates_cmt',    //
            'elediachecklist_item'      => 'eledia_adminexamdates_itm',    //
            'elediachecklist_item_date' => 'eledia_adminexamdates_itm_d',  //
            'elediachecklist_my_check'  => 'eledia_adminexamdates_my_chk', //
            'elediachecklist_my_item'   => 'eledia_adminexamdates_my_itm', //
    );
    $oldnames = array_keys($names);
    $newnames = array_values($names);

    $ret = $name;
    // Alter Namen gewuenscht
    if($gettype == 'old') {
        // Alten Namen uebergeben - bleibt
        if(in_array($name, $oldnames)) {
            $ret = $name;
        }
        // Neuen Namen uebergeben - konvertieren
        else {
            $flip = array_flip($names);
            //echo '<pre>'.print_r($flip, true).'</pre>';
            if(isset($flip[$name])) {
                $ret = $flip[$name];
            }
        }
    }
    // Neuer Namen gewuenscht
    else if($gettype == 'new') {
        // Neuen Namen uebergeben - bleibt
        if(in_array($name, $newnames)) {
            $ret = $name;
        }
        // Alten Namen uebergeben - konvertieren
        else {
            if(isset($names[$name])) {
                $ret = $names[$name];
            }
        }
    }

    //echo 'ret = '.$ret.'<br />'."\n"; //die();

    return $ret;
}
