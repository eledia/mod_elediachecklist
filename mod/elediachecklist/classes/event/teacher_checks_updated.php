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
 * The mod_elediachecklist teacher checks updated event.
 *
 * @package    mod_elediachecklist
 * @copyright  2014 Davo Smith <moodle@davosmith.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_elediachecklist\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_elediachecklist teacher checks updated class.
 *
 * @package    mod_elediachecklist
 * @since      Moodle 2.7
 * @copyright  2014 Davo Smith <moodle@davosmith.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class teacher_checks_updated extends \core\event\base {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        $this->data['objecttable'] = 'elediachecklist';
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventteacherchecksupdated', 'mod_elediachecklist');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' has updated the teacher checks for user '$this->relateduserid' on the ".
        "checklist with the course module id '$this->contextinstanceid'";
    }

    /**
     * Get URL related to the action
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/elediachecklist/report.php', array(
            'id' => $this->contextinstanceid,
            'studentid' => $this->relateduserid
        ));
    }

    /**
     * Return the legacy event log data.
     *
     * @return array|null
     */
    protected function get_legacy_logdata() {
        return array(
            $this->courseid, 'eledia', 'update checks', 'report.php?id='.$this->contextinstanceid.
            '&studentid='.$this->relateduserid, $this->objectid, $this->contextinstanceid
        );
    }

    /**
     * Validate the event data
     */
    protected function validate_data() {
        if (!$this->relateduserid) {
            throw new \coding_exception('Must specify the user whose checks are being updated as the \'relateduserid\'');
        }
    }

    /**
     * Get the mapping to use when restoring logs from backup
     * @return string[]
     */
    public static function get_objectid_mapping() {
        return ['db' => 'elediachecklist', 'restore' => 'elediachecklist'];
    }
}

