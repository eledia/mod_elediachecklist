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
 * Version information
 *
 * @copyright Norbert Geiges <ng@eledia.de>
 * @package mod_elediachecklist
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


if(!function_exists('display_add_field_default_value_by_eledia')) {

    function display_add_field_default_value_by_eledia($fieldid) {

        $config = get_config('elediachecklist');

        $moddatadefaultkeys = array();
        if (isset($config) && isset($config->data_field_id_default) && (int)$config->data_field_id_default > 0) {
            $fieldiddefault = (int)$config->data_field_id_default;
            $key = 'field_'.$fieldiddefault.'_default';
            $moddatadefaultkeys[] = $key;
        }

        $content = '';

        $key = 'field_'.$fieldid.'_default';
        if (isset($_REQUEST[$key])) {
            if (in_array($key, $moddatadefaultkeys)) {
                $content = urldecode($_REQUEST[$key]);
            }
        }

        return $content;
    }
}
