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
 * @copyright  2019 Digital Education Society (http://www.dibig.at)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/blocks/edupublisher/block_edupublisher.php');

$id = optional_param('id', 0, PARAM_INT);
$publisher = $DB->get_record('block_edupublisher_pub', array('id' => $id), '*', IGNORE_MISSING);
$context = context_system::instance();
// Must pass login
$PAGE->set_url('/blocks/edupublisher/pages/publishers.php?id=' . $id);
require_login();
$PAGE->set_context($context);
$PAGE->set_title((!empty($publisher->id) ? $publisher->name : get_string('publisher', 'block_edupublisher')));
$PAGE->set_heading((!empty($publisher->id) ? $publisher->name : get_string('publisher', 'block_edupublisher')));
$PAGE->set_pagelayout('mydashboard');
$PAGE->requires->css('/blocks/edupublisher/style/main.css');
$PAGE->requires->css('/blocks/edupublisher/style/ui.css');

block_edupublisher::print_app_header();

if (block_edupublisher::is_maintainer(array('commercial'))) {
    // if id > 0 show publisher
    // else show list of publishers
    if ($id > 0) {
        // Publisher details and users
        ?>
            <a href="/blocks/edupublisher/pages/publishers.php" class="btn btn-primary"><?php echo get_string('back'); ?></a>
        <?php
        echo $OUTPUT->render_from_template(
            'block_edupublisher/publisher_user',
            array('publisherid' => $id, 'users' => array())
        );
    } else {
        $publishers = $DB->get_records_sql('SELECT * FROM {block_edupublisher_pub} ORDER BY name ASC');
        echo $OUTPUT->render_from_template(
            'block_edupublisher/publisher',
            array('header' => '1')
        );
        foreach ($publishers AS $publisher) {
            echo $OUTPUT->render_from_template(
                'block_edupublisher/publisher',
                $publisher
            );
        }
        echo $OUTPUT->render_from_template(
            'block_edupublisher/publisher',
            array('id' => 0, 'name' => '')
        );
    }


} else {
    echo $OUTPUT->render_from_template(
        'block_edupublisher/alert',
        (object) array(
            'content' => get_string('permission_denied', 'block_edupublisher'),
            'url' => $CFG->wwwroot . '/blocks/edupublisher/pages/package.php?id=' . $package->id,
            'type' => 'danger',
        )
    );
}

block_edupublisher::print_app_footer();
