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

class cc_ar_manager extends rcube_plugin
{
    public $task = 'settings';

    public function init()
    {
        $output = $this->api->output;
        $config = $this->api->config;
        # Load distribution configuration.
        $this->load_config('config/config.inc.php.dist');
        # Overwrite configuration values with user defined ones.
        $this->load_config('config/config.inc.php');

        $this->load_ar_api($config->get('autoresponder_api'));

        $this->add_texts('localization/');
        # Plug-in frame.
        $this->register_action('plugin.cc_ar_manager', array($this, 'settings_display'));
        # The page inside of the frame (config page).
        $this->register_action('plugin.cc_ar_manager-ui', array($this, 'settings_ui'));
        # Save command, called using AJAX HTTP POST.
        $this->register_action('plugin.cc_ar_manager-save', array($this, 'settings_save'));
        $this->include_script('skins/classic/cc_ar_manager.js');

        $output->add_label(
            'cc_ar_manager.button',
            'cc_ar_manager.tooltip'
        );
    }

    private function load_ar_api($ar_api)
    {
        $config = $this->api->config;

        if(empty($ar_api))
            raise_error(array('code' => 520,
                  'type' => 'php',
                  'file' => __FILE__,
                  'line' => __LINE__, 
                  'message' => "Autoresponder API not defined"),
            true, true);

        if(!file_exists($this->home."/api/$ar_api.php"))
            raise_error(array('code' => 520,
                  'type' => 'php',
                  'file' => __FILE__,
                  'line' => __LINE__, 
                  'message' => "Autoresponder API not found: $ar_api"),
            true, true);

        # Initialize the api.
        require($this->home."/api/$ar_api.php");
        $this->ar_api = new $ar_api($ar_api);
        $this->ar_api->scheduled = $config->get('autoresponder_scheduled');
    }

    public function settings_display()
    {
        $output = $this->api->output;

        $this->register_handler('plugin.body', array($this, 'settings_ui_frame'));
        $output->set_pagetitle($this->gettext('title'));
        $output->send('plugin');
    }

    public function settings_ui_frame()
    {
        $attrs = array('name' => 'cc_ar_manager_frame',
                       'id' => 'cc_ar_manager_frame',
                       'width' => '100%',
                       'height' => '100%',
                       'frameborder' => '0',
                       'src' => './?_task=settings&_action=plugin.cc_ar_manager-ui'
                        );
        $html = html::tag('iframe', $attrs);
        $html = html::div(array('class' => 'iframebox', 'id' => 'prefs-box', 'style' => 'left: 0'), $html);
        return $html;
    }

    public function settings_ui()
    {

        $output = $this->api->output;
        $config = $this->api->config;

        $this->ar_api->api_load();
        $this->register_handler('plugin._ar_manager_form', array($this, 'settings_ui_form'));

        $output->add_label(
            'cc_ar_manager.fill_message',
            'cc_ar_manager.illegal_from_date',
            'cc_ar_manager.illegal_until_date',
            'cc_ar_manager.from_in_past',
            'cc_ar_manager.until_after_from',
            'cc_ar_manager.activated',
            'cc_ar_manager.deactivated',
            'cc_ar_manager.failed',
            'cc_ar_manager.internal_error'
        );

        $output->set_env('autoresponder_from_date', $this->ar_api->from_date);
        $output->set_env('autoresponder_until_date', $this->ar_api->until_date);
        $output->set_env('autoresponder_scheduled', $config->get('autoresponder_scheduled'));
        $output->set_env('autoresponder_period', $config->get('autoresponder_period'));
        $output->set_env('autoresponder_date_format', $config->get('autoresponder_date_format'));
        $output->set_pagetitle($this->gettext('title'));
        $output->send('cc_ar_manager.edit');
    }

    public function settings_ui_form()
    {
        $output = $this->api->output;
        $config = $this->api->config;

        $table = new html_table(array('cols' => 2));

        $input = new html_radiobutton(array('name' => '_ar_status', 'label' => 'cc_ar_manager.turnoff', 'id' => 'rcmfd_ar_turnoff', 'value' => 0, 'onclick' => "cc_ar_manager_status(this);"));
        $table->add('title', rcube_label('cc_ar_manager.turnoff'));
        $table->add(null, $input->show($this->ar_api->status));

        $input = new html_radiobutton(array('name' => '_ar_status', 'label' => 'cc_ar_manager.turnon', 'id' => 'rcmfd_ar_turnon', 'value' => 1, 'onclick' => "cc_ar_manager_status(this);"));
        $table->add('title', rcube_label('cc_ar_manager.turnon'));
        $table->add(null, $input->show($this->ar_api->status));

        if ($config->get('autoresponder_scheduled'))
        {
            $input = new html_radiobutton(array('name' => '_ar_status', 'label' => 'cc_ar_manager.scheduled', 'id' => 'rcmfd_ar_scheduled', 'value' => 2, 'onclick' => "cc_ar_manager_status(this);"));
            $table->add('title', rcube_label('cc_ar_manager.scheduled'));
            $table->add(null, $input->show($this->ar_api->status));

            $input_a = new html_inputfield(array('label' => 'cc_ar_manager.from', 'id' => 'rcmfd_ar_from_date', 'size' => 10, 'maxlength' => 10, 'disabled' => ($this->ar_api->status == 2 ? 0 : 1)));
            $input_b = new html_inputfield(array('label' => 'cc_ar_manager.until', 'id' => 'rcmfd_ar_until_date', 'size' => 10, 'maxlength' => 10, 'disabled' => ($this->ar_api->status == 2 ? 0 : 1)));

            $input_c = new html_hiddenfield(array('name' => '_ar_from_date', 'id' => 'rcmfd_ar_from_date_real'));
            $input_d = new html_hiddenfield(array('name' => '_ar_until_date', 'id' => 'rcmfd_ar_until_date_real'));

            $table->add(null, $input_c->show().$input_d->show());
            $table->add(null, rcube_label('cc_ar_manager.from').' '.$input_a->show().' '.rcube_label('cc_ar_manager.until').' '.$input_b->show());
        }

        $table->add('title', '&nbsp;');
        $table->add('title', '&nbsp;');

        $input = new html_textarea(array('name' => '_ar_message', 'label' => 'cc_ar_manager.message', 'id' => 'rcmfd_ar_message', 'cols' => 70, 'rows' => 20, 'disabled' => ($this->ar_api->status > 0 ? 0 : 1)));
        $table->add('title', rcube_label('cc_ar_manager.message'));
        $table->add(null, $input->show($this->ar_api->message));

        $content = $content.html::div(array('class' => 'boxcontent'), $table->show());

        return $output->form_tag(array(
            'id' => 'rcmfd_ar_form',
            'name' => '_ar_form',
            'class' => 'propform',
            'method' => 'post'
        ), $content);
    }

    public function settings_save()
    {
        $output = $this->api->output;

        # Send the response to the callback event listener.
        $output->command('plugin.cc_ar_manager-response', 
                        $this->ar_api->api_save(
                            get_input_value('_ar_status', RCUBE_INPUT_POST),
                            get_input_value('_ar_from_date', RCUBE_INPUT_POST),
                            get_input_value('_ar_until_date', RCUBE_INPUT_POST),
                            get_input_value('_ar_message', RCUBE_INPUT_POST))
        );
    }
}
?>
