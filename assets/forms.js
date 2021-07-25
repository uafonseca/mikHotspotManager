app.forms = {
    submit: function (form, onSuccess, event) {
        const scope = $(form);

        if (event) {
            event.preventDefault();
            event.stopImmediatePropagation();
        }
        let btn = form.find('button[type="submit"]');

        let defaultContent = btn.html();
    
        // Pace.ignore(function () {
            $.ajax({
                url: scope.attr('action'),
                type: scope.attr('method'),
                data: new FormData(form.get(0) ? form.get(0) : form),
                processData: false,
                contentType: false,
                beforeSend: function () {
                    if (typeof btn.data('loading-text') !== 'undefined') {
                        btn.prop('disabled', true);
                        btn.html(btn.data('loading-text'));
                    }
                },
                complete: function () {
                    if (typeof btn.data('loading-text') !== 'undefined') {
                        btn.prop('disabled', false);
                        btn.html(defaultContent)   
                    }
                },
                success: function (response) {
                    if (onSuccess !== undefined) onSuccess(response);
                }
            });
        // });
    }
};
// jQuery plugin to prevent double submission of forms
jQuery.fn.preventDoubleSubmission = function () {
    $(this).on('submit', function (e) {
        var $form = $(this);

        if ($form.data('submitted') === true) {
            // Previously submitted - don't submit again
            e.preventDefault();
            toastr.error('¡Para enviar un formulario básta con solo un click!');
        } else {
            // Mark it so that the next submit can be ignored
            // ADDED requirement that form be valid
            // if($form.valid()) {
                $form.data('submitted', true);
            // }
        }
    });

    return this;
};

$(function () {
    $('form').preventDoubleSubmission();
});
