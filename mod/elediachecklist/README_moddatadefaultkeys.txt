/**
 * @package     mod_elediachecklist
 * @author      N. Geiges <ng@eledia.de>
 * @copyright   2023 eLeDia GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//------------------------------------------------------------------------------


A) INTRODUCTION
---------------

The module 'mod_elediachecklist' contains a link to a 'problem database'.
This 'problem database' is an instance of the Moodle activity database.
Normally, when creating a new record, all fields of the database displayed blank.
With the following construction, database fields of the type 'text' and 'number'
can be preassigned with values in the form.


B) ADD MANUAL CODE TO A CORE MOODLE FILE
----------------------------------------

1. Open the core moodle file: mod/data/lib.php

2. Go to the method: display_add_field() (near line 290)

3. You see at the beginning of the code something like this:

        ...
        if ($formdata) {
            $fieldname = 'field_' . $this->field->id;
            $content = $formdata->$fieldname;
        } else if ($recordid) {
            $content = $DB->get_field('data_content', 'content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid));
        } else {
            $content = '';
        }
        ...

4. Now add the following code as shown below:

        ...
        if ($formdata) {
            $fieldname = 'field_' . $this->field->id;
            $content = $formdata->$fieldname;
        } else if ($recordid) {
            $content = $DB->get_field('data_content', 'content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid));
        } else {
            $content = '';
            ///////////////////////////////
            // Add eledia code manual // Start
            $file = dirname(__DIR__).'/elediachecklist/moddatadefaultkeys.php';
            if(is_file($file)) {
                include($file);
                $content = display_add_field_default_value_by_eledia($this->field->id);
            }
            // Add eledia code manual // End
            ///////////////////////////////
        }
        ...

5. Save the file.


//------------------------------------------------------------------------------
