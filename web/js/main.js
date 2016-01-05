$('.confirm').bind('click', function(e) {
    var answer = confirm($(this).data('message'));

    if (answer != true) {
        e.preventDefault();
    }
});

function ajaxLoad(url, target, showSpinner, spinnerTarget) {
    console.log('Loading ' + url + ' via AJAX...');

    if (showSpinner) {
        spinner(spinnerTarget, false);
    }

    $.ajax({
        url: url,
        success: function(data) {
            console.log('Loaded ' + url + '.');
            $(target).html(data);
            spinnerTarget.html('');
        },
        error: function () {
            console.log('Error loading ' + url + '.');
        }
    });
}

function spinner(target, append) {
    var spinner = '<div class="spinner pull-right"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>';

    if (append) {
        target.append(spinner);
    } else {
        target.html(spinner);
    }
}

$(document).ready(function() {
    $('.switch').click(function (e) {
        e.preventDefault();

        var target = $(this).data('target');
        var cookie = $(this).data('cookie');

        if ($(this).hasClass('switch-animate')) {
            $(target).slideToggle();
        } else {
            $(target).toggle();
        }

        if ($(this).find('span').hasClass('glyphicon-chevron-down')) {
            Cookies.set(cookie, 'extended');

            $(this).find('span').removeClass('glyphicon-chevron-down');
            $(this).find('span').addClass('glyphicon-chevron-up');
        } else {
            Cookies.set(cookie, 'collapsed');

            $(this).find('span').removeClass('glyphicon-chevron-up');
            $(this).find('span').addClass('glyphicon-chevron-down');
        }
    });

    $('.collapsed').toggle();

    $('.switch').each(function() {
        var target = $(this).data('target');
        var cookieName = $(this).data('cookie');

        if (!cookieName || cookieName == 'undefined') {
            return;
        }

        var cookieValue = Cookies.get(cookieName);

        if (!cookieValue || cookieValue == 'undefined') {
            return;
        }

        if (cookieValue == 'extended') {
            $(target).show();
            $(this).find('span').removeClass('glyphicon-chevron-down');
            $(this).find('span').addClass('glyphicon-chevron-up');
        } else if (cookieValue == 'collapsed') {
            $(target).hide();
            $(this).find('span').removeClass('glyphicon-chevron-up');
            $(this).find('span').addClass('glyphicon-chevron-down');
        }
    });

    $('.tt').tooltip();

    $('.checkbox-select-all').click(function (e) {
        if ($(this).prop('checked')) {
            $('.checkbox-select-all-item').prop('checked', true);
        } else {
            $('.checkbox-select-all-item').prop('checked', false);
        }
    });

    $('.ajax-reload').each(function(index) {
        var interval = $(this).data('interval');
        var url = $(this).data('url');
        var target = $(this);
        var showSpinner = $(this).data('show-spinner');
        var spinnerTarget = $(this).data('spinner-target');

        if (showSpinner == undefined) {
            showSpinner = false;
        }

        if (spinnerTarget == undefined) {
            spinnerTarget = target;
        } else {
            spinnerTarget = $(spinnerTarget);
        }

        console.log('ajax-reload-' + index + ' init [url: ' + url + ', interval: ' + interval + ', showSpinner: ' + showSpinner + ', spinnerTarget: ' + spinnerTarget + ']');

        setInterval(function() {
            ajaxLoad(url, target, showSpinner, spinnerTarget)
        }, (interval * 1000));
    });

    $('.batch-submit').click(function(e) {
        e.preventDefault();

        var action = $(this).data('action');
        var actionType = $(this).data('action-type');

        $('form#batch input[name="action"]').val(action);
        $('form#batch input[name="action_type"]').val(actionType);

        console.log('Batch action for selected items: ' + action + ', type: ' + actionType);

        $('form#batch').submit();
    });

    if (typeof parameters != 'undefined') {

        var textarea = $('#process_schedule_parameters');
        var typeList = $('#process_schedule_type');
        var helper1 = $('#parametersHelper1');
        var helper2 = $('#parametersHelper2');

        var editor = ace.edit("parameters_editor");
        textarea.css('display', 'none');
        editor.setTheme("ace/theme/crimson_editor");
        editor.getSession().setValue(textarea.val());
        editor.getSession().on('change', function(){
            textarea.val(editor.getSession().getValue());
        });

        var updateParameters = function() {
            var type = typeList.val();

            if (type == 'collector.sql.main' || type == 'collector.sql.Duplet') {
                editor.getSession().setMode("ace/mode/mysql");
                helper1.html('SQL query to be run.');
            } else {
                editor.getSession().setMode("ace/mode/json");
                helper1.html('Provided JSON structure will be converted to array and used as arguments for the process.');
            }

            if (typeof parameters[type] == "undefined" || parameters[type].length == 0) {
                helper2.html('');
            } else {
                var help = '<br/>Parameters for this type:<ul>';
                for (var key in parameters[type]) {
                    var param = parameters[type][key];
                    help += '<li><strong>' + key + '</strong> ('+ param.type + (param.required ? ', required' : '') + ') - ' + param.description + '</li>'
                }
                help += '</ul>';
                helper2.html(help);
            }
        };
        typeList.change(updateParameters);
        updateParameters();
    }
});