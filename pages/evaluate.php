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
 * @package    block_edupublisher
 * @copyright  2019 Zentrum für Lernmanagement (www.lernmanagement.at)
 * @author     Julia Laßnig & Robert Schrenk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');

require_once($CFG->dirroot . '/blocks/edupublisher/block_edupublisher.php');
require_once($CFG->dirroot . '/blocks/edupublisher/classes/etapas_evaluation_form.php');

$packageid = required_param('packageid', PARAM_INT);

$id = optional_param('id', 0, PARAM_INT);
$perma = optional_param('perma', '', PARAM_TEXT);

$package = \block_edupublisher::get_package($packageid, false);

$PAGE->set_url(new moodle_url('/blocks/edupublisher/pages/evaluate.php', array('id' => $id, 'packageid' => $packageid, 'perma' => $perma)));

$context = \context_course::instance($package->course);
$PAGE->set_context($context);
$PAGE->set_title(get_string('etapas_evaluation', 'block_edupublisher'));
$PAGE->set_heading(get_string('etapas_evaluation', 'block_edupublisher'));
$PAGE->set_pagelayout('incourse');

block_edupublisher::print_app_header();

if (!has_capability('block/edupublisher:canevaluate', \context_system::instance())) {
    echo $OUTPUT->render_from_template('block_edupublisher/alert', array(
        'type' => 'danger',
        'content' => get_string('no_permission', 'block_edupublisher'),
        'url' => $CFG->wwwroot . '/my',
    ));
} else {
    //Instantiate etapas_evaluation_form
    echo "<h3>$package->title</h3>\n";
    $package->packageid = $package->id;
    $mform = new block_edupublisher\etapas_evaluation_form();
    $mform->set_data($package);

    //Form processing and displaying is done here
    if ($mform->is_cancelled()) {
        redirect($PAGE->url);
    } else if ($dataobject = $mform->get_data()) {
        $DB->insert_record('block_edupublisher_evaluatio', $dataobject);
        echo $OUTPUT->render_from_template('block_edupublisher/alert', array(
            'type' => 'success',
            'content' => get_string('saved_successfully', 'block_edupublisher'),
            'url' => new moodle_url('/blocks/edupublisher/pages/evaluate.php', array('id' => $id, 'packageid' => $packageid, 'perma' => $perma)),
        ));
    } else {
        $mform->display();
    }
}

block_edupublisher::print_app_footer();
