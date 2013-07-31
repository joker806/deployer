$(document).ready(function() {

    var consoleOutput = $('#console-output');
    consoleOutput.hide();

    $('form').each(function() {
        var form    = $(this),
            action  = form.attr('action') || document.URL,
            method  = form.attr('method') || 'post',
            buttons = form.find('button[type="submit"], input[type="submit"]'),
            clickedButton;

        buttons.click(function(e) {
            clickedButton = $(this);
        });

        form.submit(function(e) {
            e.preventDefault();

            var button = clickedButton || buttons.first(),
                data   = form.serializeArray();

            if (button.attr('name')) {
                data.push({
                    name:  button.attr('name'),
                    value: button.attr('value')
                });
            }

            var req = createRequest();
            req.open(method, action, true);
            req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            req.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            req.send($.param(data));
        });

        return this;
    });

    var createRequest = function()
    {
        var req = new XMLHttpRequest();

        req.onreadystatechange = initialResponseHandler;

        return req;
    };

    var initialResponseHandler = function()
    {
        if (this.readyState != 2) {
            return;
        }

        if (this.getResponseHeader('Content-type') == 'application/json') {
            this.onreadystatechange = jsonResponseHandler

        } else {
            consoleOutput.html('').addClass('running').show();
            this.onreadystatechange = consoleResponseHandler
        }
    };

    var consoleResponseHandler = function()
    {
        var doScroll = $(document).scrollTop() != $(document).height() - $(window).height();

        consoleOutput.html(this.responseText);

        if (doScroll) {
            $(document).scrollTop($(document).height());
        }

        if (this.readyState == 4) {
            consoleOutput.removeClass('running');
        }
    };

    var jsonResponseHandler = function()
    {
        if (this.readyState < 4) {
            return;
        }

        console.log('json');
    };
});
