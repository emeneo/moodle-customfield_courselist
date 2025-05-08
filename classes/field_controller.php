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
 * Customfields courselist plugin
 *
 * @package   customfield_courselist
 * @copyright 2018 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customfield_courselist;

use core_files\external\delete\draft;

defined('MOODLE_INTERNAL') || die;

/**
 * Class field
 *
 * @package customfield_courselist
 * @copyright 2018 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class field_controller  extends \core_customfield\field_controller
{
    /**
     * Plugin type
     */
    const TYPE = 'courselist';

    /**
     * Add fields for editing a courselist field.
     *
     * @param \MoodleQuickForm $mform
     */
    public function config_form_definition(\MoodleQuickForm $mform)
    {
        $mform->addElement('header', 'header_specificsettings', get_string('specificsettings', 'customfield_courselist'));
        $mform->setExpanded('header_specificsettings', true);

        $mform->addElement(
            'filemanager',
            'configdata[course_image]',
            get_string('course_image', 'customfield_courselist'),
            null,
            $this->get_file_options()
        );
    }
    
    public function prepare_for_config_form(\stdClass $formdata) {
        $fieldname = 'configdata[course_image]';
        if($formdata->configdata['course_image']){
            $context = \context_system::instance();
            $draftitemid = file_get_submitted_draft_itemid('attachments');
            $entryId = $formdata->configdata['course_image'];
            file_prepare_draft_area(
                $draftitemid, 
                $context->id, 
                'coursefield_courselist', 
                'attachment', 
                $entryId, 
                $this->get_file_options()
            );
            $formdata->$fieldname = $draftitemid;
        }
        return $formdata;
    }

    /**
     * Validate the data on the field configuration form
     *
     * @param array $data from the add/edit profile field form
     * @param array $files
     * @return array associative array of error messages
     */
    public function config_form_validation(array $data, $files = array()): array
    {
        $errors = parent::config_form_validation($data, $files);
        
        if ($data['configdata']['uniquevalues']) {
            $errors['configdata[uniquevalues]'] = get_string('errorconfigunique', 'customfield_courselist');
        }

        if($data['configdata']['course_image']){
            $context = \context_system::instance();
            file_save_draft_area_files(
                $data['configdata']['course_image'],
                $context->id,
                'coursefield_courselist',
                'attachment',
                $data['configdata']['course_image'],
                $this->get_file_options()
            );
        }

        return $errors;
    }

    /**
     * Does this custom field type support being used as part of the block_myoverview
     * custom field grouping?
     * @return bool
     */
    public function supports_course_grouping(): bool
    {
        return true;
    }

    /**
     * If this field supports course grouping, then this function needs overriding to
     * return the formatted values for this.
     * @param array $values the used values that need formatting
     * @return array
     */
    public function course_grouping_format_values($values): array
    {
        $name = $this->get_formatted_name();
        return [
            1 => $name . ': ' . get_string('yes'),
            BLOCK_MYOVERVIEW_CUSTOMFIELD_EMPTY => $name . ': ' . get_string('no'),
        ];
    }

    private function get_file_options()
    {
        return [
            'subdirs' => 0,
            'maxbytes' => 10485760,
            'areamaxbytes' => 10485760,
            'maxfiles' => 1,
            'accepted_types' => ['image'],
            'return_types' => FILE_INTERNAL | FILE_EXTERNAL,
        ];
    }
}
