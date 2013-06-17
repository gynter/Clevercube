<?php

/*
 +---------------------------------------------------------------------+
 | Clevercube Autoresponder manager plugin.                            |
 |                                                                     |
 | Copyright (c) 2013 Günter Kits                                      |
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

# This is the example API for Clevercube Autoresponder plugin.
# It shows how to define the actions for the API.
# If You use this API and change/update the Autoresponder settings
# then the changes will be written to logs/debug.
# Also remember, no settings will be saved after the update, the changes
# will only be dumped to the log.


# Import the api class definition file.
require_once('api.php');

# Your API must always extend to the api class.
class example extends ar_api
{
    # These are the default values for the fields shown in Autoresponder
    # page. They are also defined in api class, but You can overwrite
    # those with Your settings, if you like.

    # Default status:
    #   0 - disabled
    #   1 - enabled
    #   2 - scheduled
    public $status = 0;
    
    # Default starting date in YYYY-MM-DD format.
    public $from_date;

    # Default ending date in YYYY-MM-DD format.
    public $until_date;
    
    # Default message.
    public $message;

    # Roundcube instance.
    private $rc;

    protected function init()
    {
        # This is initialization function which will be called when
        # the API is initialized.
        # You can put stuff like database connections here.
        $this->rc = rcmail::get_instance();
    }

    # This functions is for loading the data for displaying in the form.
    # The return value must be an array of (status, from_date, until_date, message)
    # Both from_date and until_date must be in format of YYYY-MM-DD.
    protected function load()
    {
        # The config values are set in config/example.inc.php.dist and
        # can be overwritten in example.inc.php.
        # Config files are loaded by API constructor, so You don't need
        # to reload them.
        $status = $this->config->get('ar_api_example_status');
        $from_date = $this->config->get('ar_api_example_from');
        $until_date = $this->config->get('ar_api_example_until');
        $message = $this->config->get('ar_api_example_message');

        # Write the output to the logs/debug log.
        write_log('debug', sprintf("Autoresponder loaded (%s): %s, %s, %s, %s", $this->rc->get_user_name(), $status, $from_date, $until_date, $message));
        return array($status, $from_date, $until_date, $message);
    }

    protected function save($status, $from_date, $until_date, $message)
    {
        # Write the output to the logs/debug log.
        write_log('debug', sprintf("Autoresponder saved  (%s): %s, %s, %s, %s", $this->rc->get_user_name(), $status, $from_date, $until_date, $message));
        return true;
    }
}

?>
