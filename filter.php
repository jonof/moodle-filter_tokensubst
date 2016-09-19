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

class filter_tokensubst extends moodle_text_filter {
    private $tokens = null;
    private $texts = null;

    private static $parentconfigs = array();

    public function __construct($context, array $localconfig) {
        parent::__construct($context, $localconfig);

        $alltokens = array();
        $showtokens = false;

        if ($context instanceof context_module) {
            // Import the tokens from the parent (course) context.
            $parentctx = $context->get_parent_context();

            if (!isset(self::$parentconfigs[$parentctx->id])) {
                self::$parentconfigs[$parentctx->id] = filter_get_local_config('tokensubst', $parentctx->id);
            }
            if (!empty(self::$parentconfigs[$parentctx->id]['tokens'])) {
                $alltokens = array_merge($alltokens, self::parse_config(self::$parentconfigs[$parentctx->id]['tokens'], $errors));
            }
            if (!empty(self::$parentconfigs[$parentctx->id]['showtokens'])) {
                $showtokens = true;
            }
        }

        if (!empty($localconfig['tokens'])) {
            $alltokens = array_merge($alltokens, self::parse_config($localconfig['tokens'], $errors));
        }
        if (!empty($localconfig['showtokens'])) {
            $showtokens = true;
        }

        if ($showtokens && has_capability('moodle/filter:manage', $context)) {
            foreach ($alltokens as $token => $text) {
                // Invalidate the token's opening sigil with a zero-width space to prevent
                // repeat filterings expanding the label.
                $tokenlabel = str_replace('{{{', '{{{&#x200B;', $token);
                $alltokens[$token] = "<em>{$tokenlabel}</em>";
            }
            unset($text);
        }

        if ($alltokens) {
            $this->tokens = array_keys($alltokens);
            $this->texts = array_values($alltokens);
        }
    }

    public static function parse_config($configstr, &$errors) {
        $tokens = array();
        $linenum = 0;
        $errorat = array();

        foreach (explode("\n", $configstr) as $line) {
            $linenum += 1;
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $parts = explode('=', $line, 2);
            if (count($parts) != 2) {
                $errors[] = array($linenum, 'noseparator');
                continue;
            }

            $token = trim($parts[0]);
            $text = trim($parts[1]);

            if ($token === '') {
                $errors[] = array($linenum, 'emptytoken');
                continue;
            }
            if (preg_match('/[^-_a-z0-9]/iu', $token)) {
                $errors[] = array($linenum, 'invalidtokenchar');
                continue;
            }

            $tokens['{{{' . $token . '}}}'] = $text;
        }

        return $tokens;
    }

    public function filter($text, array $options = array()) {
        if (!$this->tokens) {
            return $text;
        }
        return str_ireplace($this->tokens, $this->texts, $text);
    }
}
