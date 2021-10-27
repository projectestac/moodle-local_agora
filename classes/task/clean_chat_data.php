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
 * Delete corrupt records (mod_chat)
 *
 * @package    local_agora
 * @copyright  2021 TICxCAT <info@ticxcat.cat>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_agora\task;

/**
 * Delete corrupt records (mod_chat)
 */
class clean_chat_data extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('clean_chat_data', 'local_agora');
    }

    /**
     * Performs the task
     */
    public function execute() {
        global $DB, $CFG;

        $sql = "SELECT * FROM {external_services_functions} WHERE functionname LIKE 'mod_chat%'";
        $records = $DB->get_records_sql($sql, null);
        $num = count($records);
        if ($num >= 1) {
            $DB->delete_records_select('external_services_functions', "functionname LIKE 'mod_chat%'", null);

            // Mailing
            $mailtext = '';
            $mailhtml = '';
            $mailtext .= 'Hem eliminat $num registres de chat. Estem analitzant el servidor : '.$CFG->wwwroot."\n"."\n";
            $mailhtml .= 'Hem eliminat $num registres de chat. Estem analitzant el servidor : '.$CFG->wwwroot."<br/>\n"."<br/>\n";
            $userfrom = $DB->get_record('user', array('username' => 'xtecadmin'));
            $userto = $DB->get_record('user', array('username' => 'xtecadmin'));
            if ($userfrom && $userto) {
                $userto->email = 'aginard@xtec.cat';
                $result_email = email_to_user($userto, $userfrom, 'Errors mod_chat', $mailtext, $mailhtml);
                $userto->email = 'mgrau226@xtec.cat';
                $result_email = email_to_user($userto, $userfrom, 'Errors mod_chat', $mailtext, $mailhtml);
                $userto->email = 'iban.cardona@ticxcat.cat';
                $result_email = email_to_user($userto, $userfrom, 'Errors mod_chat', $mailtext, $mailhtml);
                $userto->email = 'ignacio.abejaro@ticxcat.cat';
                $result_email = email_to_user($userto, $userfrom, 'Errors mod_chat', $mailtext, $mailhtml);
            }
            // End mailing
        }
    }
}