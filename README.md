------------------------------------------------------------------------
Clevercube - Various plug-ins for the Roundcube webmail
========================================================================
------------------------------------------------------------------------

1.  Description
2.  Requirements
3.  Installation
4.  Configuration
5.  Plug-ins
  1.  External database
  2.  External authentication
  3.  Autoresponder manager
6.  License
7.  Support
8.  Authors

1. Description
--------------

Clevercube provides addons to extend the default behavior of Roundcube
webmail.

2. Requirements
---------------

- Roundcube 0.9.5 (<http://www.roundcube.net>).

3. Installation
---------------

- Download the latest release from
<https://github.com/gynter/Clevercube/releases/latest>
and extract the archive to the chosen destination directory.

    Development files can be browsed via web browser or can be optained
    from a git repository <https://github.com/gynter/Clevercube>.

    *Note: You can also get the sources using the
    [GIT](http://git-scm.com/book/en/Getting-Started-Installing-Git) and
    cloning the `https://github.com/gynter/Clevercube.git` repository.*

- Copy directory `clevercube` and all directories starting with `cc_`
to Roundcube `plugins` directory.

4. Configuration
----------------

To enable all Clevercube plug-ins add `clevercube` to the
`$rcmail_config['plugins']` array in `config/main.inc.php`. It's
recommended to define it as the first plugin.

    $rcmail_config['plugins'] = array('clevercube');

Configuration files for each plug-in are located in a directory
called `config/` relative to plug-in's directory.

Copy `config.inc.php.dist` to `config.inc.php` and set the options
as described within the file.

Each plug-in can be disabled separately from `clevercube` plug-in
config file.

5. Plug-ins
-----------

5.1. External database (`cc_external_database`)
-----------------------------------------------

This plug-in let's to use external databases in other plug-ins,
which support them.

To get the database handler, use the `get_external_db(profile)`
function in Your plug-in or configuration file. Different profiles
can be defined in the `config.inc.php`.

    $rcmail_config['my_plugin_db'] = get_external_db('users');

5.2. External authentication (`cc_external_auth`)
-------------------------------------------------

A plug-in that adds a possibility to log in via external login form
and redirects logout and login error pages to specified URL as GET
requests.

This is an example `PHP` external login page. Replace form action
URL `/roundcubemail/index.php` with URL of Your Roundcube
installation.

    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Webmail login</title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta name="description" content="Webmail login" />
        <meta name="keywords" content="Webmail login" />
    </head>
    <body>
    <?php
    if(!empty($_GET['message']))
    {
        $message = urldecode(trim($_GET['message']));
        echo '    <div style="color:#f00;display:none">'.$message.'</div>';
    }
    ?>
        <form name="webmail" action="/roundcubemail/index.php" method="post">
            <input name="_action" value="login" type="hidden" />
            User <input name="_user" type="text" /><br />
            Pass <input name="_pass" type="password" /><br />
            <input type="hidden" name="_task" value="login">
            <input type="hidden" name="_action" value="login" />
            <input type="submit" />
        </form>
    </body>
    <html>

*Note: If debug level is set to display errors and errors are being
displayed before the redirection process then redirection is not
performed. Make sure that PHP `display_errors` is `off` and
`$rcmail_config['debug_level']` is set to `1`.*

5.3. Autoresponder manager (`cc_ar_manager`)
--------------------------------------------

This plug-in provides a **framework** for an autoresponder managing
feature. Plug-in user could write the correct API for the
autoresponder solution which he or she uses in the servers. This
plug-in is only a framework, because when using the Postfix one
could define custom autoresponder solution with custom database
structure so it would be impossible to make an universal solution,
but this framework will give the common possibilities
(enabling/disabling, defining the response message and also setting
the response date period).

Please see the `api/example.php` for the example with comments.

6. License
----------

This software is licensed as described in the file `LICENSE.md`, which
You should have received as part of this distribution. The terms are
also available at
<https://github.com/gynter/Clevercube/blob/master/LICENSE.md>.

7. Support
----------

Issue tracker can be found at
<https://github.com/gynter/Clevercube/issues>.

8. Authors
----------

  - Günter Kits (gynter@kits.ee)
