if(window.rcmail)
{
    rcmail.addEventListener('init', function(evt)
    {
        var tab = $('<span>').attr('id', 'settingstabplugincc_ar_manager').addClass('tablink');
        var button = $('<a>').attr('href', rcmail.env.comm_path+'&_action=plugin.cc_ar_manager').attr('title', rcmail.gettext('cc_ar_manager.tooltip')).html(rcmail.gettext('cc_ar_manager.button')).appendTo(tab);
        button.bind('click', function(e){ return rcmail.command('plugin.cc_ar_manager', this); });
        rcmail.add_element(tab, 'tabs');

        rcmail.register_command('plugin.cc_ar_manager', function(){ rcmail.goto_url('plugin.cc_ar_manager'); }, true);
        rcmail.register_command('plugin.cc_ar_manager-save', function(){
            rcmail.http_post('plugin.cc_ar_manager-save', {
                _ar_status: $('input:radio[name=_ar_status]:checked').val(),
                _ar_from_date: $('#rcmfd_ar_from_date_real').val(),
                _ar_until_date: $('#rcmfd_ar_until_date_real').val(),
                _ar_message: $('#rcmfd_ar_message').val()
                });
        }, true);

        if (rcmail.env.action == 'plugin.cc_ar_manager-ui' && rcmail.env.autoresponder_scheduled)
        {
            $('#rcmfd_ar_from_date').datepicker({ dateFormat: rcmail.env.autoresponder_date_format, altFormat: "yy-mm-dd", altField: "#rcmfd_ar_from_date_real" });
            $('#rcmfd_ar_until_date').datepicker({ dateFormat: rcmail.env.autoresponder_date_format, altFormat: "yy-mm-dd", altField: "#rcmfd_ar_until_date_real" });
            cc_ar_manager_setdate()
        }

        rcmail.addEventListener('plugin.cc_ar_manager-response', cc_ar_manager_response);
    });
}

function cc_ar_manager_response(response)
{
    if (response)
    {
        // Since I'm in the frame then push the message to the parent,
        // because parent has the messagebox.
        parent.rcmail.display_message(rcmail.gettext('cc_ar_manager.'+response[1]), response[0]);
        if (response[2])
            $('#rcmfd_ar_'+response[2]).focus();
    }
}

function cc_ar_manager_setdate()
{
    var from_date = new Date();
    var until_date = new Date();
    until_date.setDate(until_date.getDate()+rcmail.env.autoresponder_period);

    if (rcmail.env.autoresponder_from_date)
        from_date = new Date(rcmail.env.autoresponder_from_date)
    if (rcmail.env.autoresponder_until_date)
        until_date = new Date(rcmail.env.autoresponder_until_date)
    $('#rcmfd_ar_from_date').datepicker('setDate', from_date);
    $('#rcmfd_ar_until_date').datepicker('setDate', until_date);
}

function cc_ar_manager_status(elm)
{
    $('#rcmfd_ar_message').attr('disabled', false);
    if (rcmail.env.autoresponder_scheduled)
    {
        $('#rcmfd_ar_from_date').attr('disabled', true);
        $('#rcmfd_ar_until_date').attr('disabled', true);
    }
    switch(parseInt(elm.value))
    {
        case 1:
        break;
        case 2:
            if (rcmail.env.autoresponder_scheduled)
            {
                $('#rcmfd_ar_from_date').attr('disabled', false);
                $('#rcmfd_ar_until_date').attr('disabled', false);

                if ($('#rcmfd_ar_from_date').datepicker('getDate') == null)
                    cc_ar_manager_setdate()
            }
        break;
        default:
            $('#rcmfd_ar_message').attr('disabled', true);
    }
}
