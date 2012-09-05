<?php

/*
 +---------------------------------------------------------------------+
 | Clevercube Autoresponder manager plugin.                            |
 |                                                                     |
 | Copyright (c) 2012 Günter Kits                                      |
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

class ar_api
{
    public $api;
    public $status = 0;
    public $from_date;
    public $until_date;
    public $message;

    public $scheduled = false;
    public $config;

    public function __construct($api)
    {
        $this->config = rcmail::get_instance()->config;
        $this->api = $api;

        # Load distribution configuration.
        $this->config->load_from_file("plugins/cc_ar_manager/config/$api.inc.php.dist");
        # Overwrite configuration values with user defined ones.
        $this->config->load_from_file("plugins/cc_ar_manager/config/$api.inc.php");
        $this->init();
    }

    public function api_load()
    {
        $reply = $this->load();
        if (empty($reply))
            return false;

        list($status, $from_date, $until_date, $message) = $reply;
        if (!$this->scheduled)
        {
            $from_date = null;
            $until_date = null;
            if ($status == 2)
                $status = 1;
        }

        $this->status = $status;
        $this->from_date = $from_date;
        $this->until_date = $until_date;
        $this->message = $message;
        return true;
    }

    public function api_save($status, $from_date, $until_date, $message)
    {
        if ($status > 0 && empty($message))
            return array('error', 'fill_message', 'message');

        if(!$this->api_load())
            return array('error', 'internal_error', 'status');

        switch ($status)
        {
            case 0:
                $from_date = $this->from_date;
                $until_date = $this->until_date;
                $message = $this->message;
                break;
            case 1:
                $from_date = $this->from_date;
                $until_date = $this->until_date;
                break;
            case 2:
                # This should normally never happen, unless someone is
                # messing with the posted form data!
                if (!$this->scheduled)
                    return array('error', 'internal_error', 'status');

                if (empty($from_date))
                    return array('error', 'illegal_from_date', 'from_date');
                if (empty($until_date))
                    return array('error', 'illegal_until_date', 'until_date');

                if (!preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/', $from_date))
                    return array('error', 'illegal_from_date', 'from_date');
                if (!preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/', $until_date))
                    return array('error', 'illegal_until_date', 'until_date');

                $today = date('Y-m-d', strtotime("now"));
                $from_date = date('Y-m-d', strtotime($from_date));
                $until_date = date('Y-m-d', strtotime($until_date));

                if ($from_date < $today)
                    return array('error', 'from_in_past', 'from_date');
                else if ($from_date > $until_date)
                    return array('error', 'until_after_from', 'until_date');
                break;
        }

        if ($saved = $this->save($status, $from_date, $until_date, $message))
        {
            $this->status = $status;
            $this->from_date = $from_date;
            $this->until_date = $until_date;
            $this->message = $message;
        }
        else
            return array('error', 'failed', 'status');

        if ($saved && $status == 0)
            return array('confirmation', 'deactivated', null);
        else if ($saved && $status > 0)
            return array('confirmation', 'activated', null);
        else
            return array('error', 'failed', 'status');
        # This should normally never happen, unless someone is messing
        # with the posted form data!
        return array('error', 'internal_error', 'status');
    }
}

?>
