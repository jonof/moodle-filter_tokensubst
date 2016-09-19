<?php

/**
 * Token substitution filter
 *
 * @package   filter_tokensubst
 * @author    Jonathon Fowler <fowlerj@usq.edu.au>
 * @copyright 2016 The University of Southern Queensland
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class tokensubst_filter_local_settings_form extends filter_local_settings_form {
    protected function definition_inner($mform) {
        $mform->addElement('static', '', '', markdown_to_html(get_string('introtext', 'filter_tokensubst')));

        if ($this->context instanceof context_module) {
            // Import the tokens from the parent (course) context.
            $parentctx = $this->context->get_parent_context();
            $parentconfig = filter_get_local_config('tokensubst', $parentctx->id);

            if (!empty($parentconfig['tokens'])) {
                $mform->addElement('static', '', get_string('parenttokens', 'filter_tokensubst'),
                    html_writer::tag('pre', s($parentconfig['tokens'])));
            }
        }

        $mform->addElement('textarea', 'tokens', get_string('tokens', 'filter_tokensubst'), array('rows' => 10, 'cols' => 50));

        $mform->addElement('checkbox', 'showtokens', get_string('showtokens', 'filter_tokensubst'));
    }

    public function validation($data, $files) {
        global $CFG;

        $errors = array();
        if (!empty($data['tokens'])) {
            require_once $CFG->dirroot . '/filter/tokensubst/filter.php';

            $parseerrors = array();
            filter_tokensubst::parse_config($data['tokens'], $parseerrors);
            $strs = array();
            foreach ($parseerrors as $errinfo) {
                $strs[] = get_string("error{$errinfo[1]}", 'filter_tokensubst', $errinfo[0]);
            }
            if ($strs) {
                $errors['tokens'] = implode(' ', $strs);
            }
        }
        return $errors;
    }

    public function save_changes($data) {
        $data = (array) $data;
        unset($data['submitbutton']);
        if (isset($data['tokens'])) {
            $data['tokens'] = rtrim($data['tokens']);
        }
        if (!isset($data['showtokens'])) {
            $data['showtokens'] = 0;
        }
        parent::save_changes($data);
    }
}
