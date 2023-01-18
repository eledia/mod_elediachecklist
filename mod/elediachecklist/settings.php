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

defined('MOODLE_INTERNAL') || die();
if ($ADMIN->fulltree) {

    /*
    // EINSTELLUNGEN DES PLUGINS
    */

    //$sql = 'SELECT * FROM {elediachecklist_item} ORDER BY position ASC ';
    $sql = 'SELECT * FROM {elediachecklist_item} ORDER BY duetime ASC, displaytext ASC';
    $result = $DB->get_records_sql($sql);
    //echo '<pre>'.print_r($result, true).'</pre>';

    //$vorbereitung = array();
    //$nachbearbeitung = array();
    $all = array();
    foreach ($result as $item) {
        $id = $item->id;
        $displaytext = $item->displaytext;
        $all[$id] = $displaytext;
    }
    //echo '<pre>'.print_r($all, true).'</pre>';

    $name = 'elediachecklist/erinnerung_kvb_name';
    $visiblename = get_string('erinnerung_kvb', 'elediachecklist');
    $description = get_string('erinnerung_kvb_beschreibung', 'elediachecklist');
    $defaultsetting = array();
    $choices = $all;
    $admin_setting_configmultiselect =
            new admin_setting_configmultiselect($name, $visiblename, $description, $defaultsetting, $choices);
    $settings->add($admin_setting_configmultiselect);

    $name = 'elediachecklist/erinnerung_knb_name';
    $visiblename = get_string('erinnerung_knb', 'elediachecklist');
    $description = get_string('erinnerung_knb_beschreibung', 'elediachecklist');
    $defaultsetting = array();
    $choices = $all;
    $settings->add(new admin_setting_configmultiselect($name, $visiblename, $description, $defaultsetting, $choices));

}
