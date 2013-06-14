<?php

/*
 +---------------------------------------------------------------------+
 | Clevercube External database plugin.                                |
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

function get_external_db($profile)
{
    $rcmail = rcmail::get_instance();

    if (empty($rcmail->external_databases))
        return false;
    else
        $databases = $rcmail->external_databases;

    if (empty($profile))
        raise_error(array('code' => 520,
                          'type' => 'php',
                          'file' => __FILE__,
                          'line' => __LINE__, 
                          'message' => "No external database profile defined"),
                    true, true);

    if (!array_key_exists($profile, $databases))
        raise_error(array('code' => 520,
                          'type' => 'php',
                          'file' => __FILE__,
                          'line' => __LINE__, 
                          'message' => "External database profile $profile not found"),
                    true, true);

    return $databases[$profile];
}

class cc_external_database extends rcube_plugin
{
    private $rc;
    private $config;

    public function init()
    {
        $this->rc = rcmail::get_instance();
        $this->config = $this->rc->config;

        # Load distribution configuration.
        $this->load_config('config/config.inc.php.dist');
        # Overwrite configuration values with user defined ones.
        $this->load_config('config/config.inc.php');

        if (empty($this->rc->external_databases))
            $databases = array();
        else
            $databases = $this->rc->external_databases;

        $profiles = $this->config->get('external_database');
        if (!empty($profiles))
            foreach ($profiles as $name => $profile)
                if (!array_key_exists($name, $databases))
                    $databases[$name] = $this->connect($profile);
        $this->rc->external_databases = $databases;
    }

    private function connect($profile)
    {
        $db_dsnw = array_key_exists('db_dsnw', $profile) ? $profile['db_dsnw'] : '';
        $db_dsnr = array_key_exists('db_dsnr', $profile) ? $profile['db_dsnr'] : '';
        $db_persistent = array_key_exists('db_persistent', $profile) ? $profile['db_persistent'] : false;
        $sql_debug = array_key_exists('sql_debug', $profile) ? $profile['sql_debug'] : false;
        $mode = array_key_exists('mode', $profile) ? $profile['mode'] : 'w';

        # 1486067
        if (is_array($db_dsnw) && empty($db_dsnw['new_link']))
            $db_dsnw['new_link'] = true;
        else if (!is_array($db_dsnw) && !preg_match('/\?new_link=true/', $db_dsnw))
            $db_dsnw .= '?new_link=true';

        # Backwards compatibility with releases before 0.8.6.
        if (class_exists('rcube_db'))
            $db = rcube_db::factory($db_dsnw, $db_dsnr, (bool) $db_persistent);
        else
            $db = new rcube_mdb2($db_dsnw, $db_dsnr, (bool) $db_persistent);
        $db->set_debug((bool) $sql_debug);
        $db->db_connect($mode);

        if ($db->is_error())
            raise_error(array('code' => 603,
                              'type' => 'db',
                              'message' => $db->is_error()),
                        true, true);
        # This must be unset, otherwise infinite recursion will happen
        # and RC will die. This will also disable the possibility
        # to debug the database which is bad, but currently the only
        # solution which I could find to fix this recursion error.
        if (!class_exists('rcube_db'))
            unset($db->db_handle->options['debug_handler']);
        return $db;
    }
}
?>
