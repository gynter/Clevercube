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
$labels['button'] = "Automaatvastaja";
$labels['tooltip'] = "Seadista automaatvastaja";
$labels['title'] = "Automaatvastaja seaded";
$labels['message'] = "Sõnum";
$labels['turnoff'] = "Välja";
$labels['turnon'] = "Sisse";
$labels['scheduled'] = "Ajastatud";
$labels['from'] = "Alates";
$labels['until'] = "kuni";

$messages = array();
$messages['fill_message'] = "Palun sisestage sõnum.";
$messages['illegal_from_date'] = "Vigane alguskuupäev.";
$messages['illegal_until_date'] = "Vigane lõpukuupäev.";
$messages['from_in_past'] = "Alguskuupäev ei tohi olla minevikus.";
$messages['until_after_from'] = "Lõpukuupäev peab olema pärast alguskuupäeva.";
$messages['activated'] = "Automaatvastaja seadistatud ja aktiveeritud.";
$messages['deactivated'] = "Automaatvastaja deaktiveeritud.";
$messages['failed'] = "Automaatvastaja seadistamine ebaõnnestus.";
$messages['internal_error'] = "Süsteemiviga!";

?>
