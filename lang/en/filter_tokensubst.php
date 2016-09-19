<?php

/**
 * Token substitution filter
 *
 * @package   filter_tokensubst
 * @author    Jonathon Fowler <fowlerj@usq.edu.au>
 * @copyright 2016 The University of Southern Queensland
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['erroremptytoken'] = 'No token name before the \'=\' on line {$a}.';
$string['errorinvalidtokenchar'] = 'Invalid token character found on line {$a}.';
$string['errornoseparator'] = 'No \'=\' found on line {$a}.';
$string['filtername'] = 'Token substitution';
$string['introtext'] = 'This filter will replace placeholders (tokens) in text with substitutes you define here.

Tokens are represented by `{{{` + `token-word` + `}}}` and are matched without regards to case. `token-word` may contain any sequence of letters, numbers, underscores, or hyphens. e.g. `{{{ASSIGNMENT_1_DUE_DATE}}}`, `{{{email-address}}}`

Define each token on a new line with the format: `token-word = expanded text`. Leading and traling spaces will be trimmed, and the expanded text ends at the newline.';
$string['parenttokens'] = 'Tokens at course level';
$string['tokens'] = 'Tokens';
