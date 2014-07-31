<?php

/*
 +---------------------------------------------------------------------+
 | Clevercube Autoresponder manager plugin.                            |
 |                                                                     |
 | Copyright (c) 2014 Günter Kits                                      |
 |                                                                     |
 | Permission is hereby granted, free of charge, to any person         |
 | obtaining a copy of this software and associated documentation      |
 | files (the "Software"), to deal in the Software without             |
 | restriction, including without limitation the rights to use, copy,  |
 | modify, merge, publish, distribute, sublicense, and/or sell copies  |
 | of the Software, and to permit persons to whom the Software is      |
 | furnished to do so, subject to the following conditions:            |
 |                                                                     |
 | The above copyright notice and this permission notice shall be      |
 | included in all copies or substantial portions of the Software.     |
 |                                                                     |
 | THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,     |
 | EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF  |
 | MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND               |
 | NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS |
 | BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN  |
 | ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN   |
 | CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE    |
 | SOFTWARE.                                                           |
 +---------------------------------------------------------------------+
*/

$labels = array();
$labels['button'] = "Autoresponder";
$labels['tooltip'] = "Change autoresponder settings";
$labels['title'] = "Autoresponder settings";
$labels['message'] = "Message";
$labels['turnoff'] = "Off";
$labels['turnon'] = "On";
$labels['scheduled'] = "Scheduled";
$labels['from'] = "From";
$labels['until'] = "to";

$messages = array();
$messages['fill_message'] = "Please enter the message.";
$messages['illegal_from_date'] = "Illegal starting date.";
$messages['illegal_until_date'] = "Illegal ending date.";
$messages['from_in_past'] = "Starting date cannot be in the past.";
$messages['until_after_from'] = "Ending date must be after starting date.";
$messages['activated'] = "Autoresponder set and activated.";
$messages['deactivated'] = "Autoresponder deactivated.";
$messages['failed'] = "Failed to set up autoresponder.";
$messages['internal_error'] = "Internal error!";

?>
