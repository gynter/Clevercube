<?php

/*
 +---------------------------------------------------------------------+
 | Clevercube External authentication plugin.                          |
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

class cc_external_auth extends rcube_plugin
{
    public $task = 'login|logout';

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

        # Initialize hooks.
        $this->add_hook('startup', array($this, 'startup'));
        $this->add_hook('authenticate', array($this, 'authenticate'));
        $this->add_hook('render_page', array($this, 'login_redirect'));
        $this->add_hook('login_failed', array($this,'login_failed'));
        $this->add_hook('logout_after', array($this,'logout_redirect'));

        # Get the redirection URLs from config.
        $this->login_redirect_url = $this->config->get('external_auth_login');
        $this->login_error_url = $this->config->get('external_auth_error');
        $this->login_logout_url = $this->config->get('external_auth_logout');

        if (empty($this->login_redirect_url))
            $this->login_redirect_url = false;
        if ($this->login_error_url === null)
            $this->login_error_url = $this->login_redirect_url;
        if ($this->login_logout_url === null)
            $this->login_logout_url = $this->login_redirect_url;
    }

    private function location($url, $message, $args)
    {
        if ($url === false)
            return false;
        $user = $args['user'];
        $host = $args['host'];
        list($name, $domain) = explode('@', $user);
        $url = str_replace('%m', urlencode($message), $url);
        $url = str_replace('%u', urlencode($user), $url);
        $url = str_replace('%h', urlencode($host), $url);
        $url = str_replace('%n', urlencode($name), $url);
        $url = str_replace('%d', urlencode($domain), $url);

        $this->rc->kill_session();
        header("Location: $url");
        exit;
    }

    public function startup($args)
    {
        # Let's set some test cookies.
        setcookie('rc_cc_ctest', 'Who Stole the Cookie from the Cookie Jar?', time() + 360);
        return $args;
    }

    public function authenticate($args)
    {
        if (!empty($_POST['_user']) && !empty($_POST['_pass']) && $this->rc->action == 'login' && $this->rc->task == 'login')
        {
            $args['valid'] = true;
            $args['cookiecheck'] = false;
        }
        return $args;
    }

    public function login_redirect($args)
    {
        if ($args['template'] == 'login')
        {
            $url = $this->login_redirect_url;
            $msg = '';

            if (!$this->rc->session->check_auth() && $this->rc->action != 'login' && $this->rc->task != 'login')
            {
                $url = $this->login_error_url;

                # Check if cookies are enabled.
                if (!array_key_exists('rc_cc_ctest', $_COOKIE) || empty($_COOKIE))
                {
                    $msg = rcube_label('cookiesdisabled');
                    $args = null;
                }
                else
                    $msg = rcube_label('sessionerror');
            }
            $this->location($url, $msg, $args);
        }
        return $args;
    }

    public function login_failed($args)
    {
        $error_labels = array(
            RCMAIL::ERROR_STORAGE          => 'storageerror',
            RCMAIL::ERROR_COOKIES_DISABLED => 'cookiesdisabled',
            RCMAIL::ERROR_INVALID_REQUEST  => 'invalidrequest',
            RCMAIL::ERROR_INVALID_HOST     => 'invalidhost',
        );
        $this->location($this->login_error_url, rcube_label(array_key_exists($args['code'], $error_labels) ? $error_labels[$args['code']] : 'loginfailed'), $args);
    }

    public function logout_redirect($args)
    {
        $this->location($this->login_logout_url, rcube_label('loggedout'), $args);
    }
}
?>
