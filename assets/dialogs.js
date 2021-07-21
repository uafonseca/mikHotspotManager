app.dialogs = {
    confirm: function (options) {
        let message = 'Debe confirmar la acción antes de continuar';

        if (options) {
            if (options['message'] !== undefined) message = options['message'];

            const event = options['event'] ? options['event'] : false;

            if (event) {
                event.preventDefault();
                event.stopImmediatePropagation();
            }
        }

        $.confirm({
            title: 'Aviso',
            content: message,
            buttons: {
                confirm: {
                    text: 'Confirmar',
                    btnClass: 'btn-primary',
                    action: function () {
                        if (options['onAccept'] !== undefined) options['onAccept']();
                    }
                },
                cancel: {
                    text: 'Cancelar',
                    btnClass: 'btn-default',
                    action: function () {
                        if (options['onDecline'] !== undefined) options['onDecline']();
                    }
                }
            }
        });
    },
    create: function (options) {
        const event = options['event'] ? options['event'] : false;

        if (event) {
            event.preventDefault();
            event.stopImmediatePropagation();
        }
        $.dialog({
            columnClass: options['columnClass'] ? options['columnClass'] : 'medium',
            containerFluid: options['containerFluid'] ? options['containerFluid'] : false,
            backgroundDismiss: false,
            type: options['type']? options['type']: 'default',
            content: function () {
                let self = this;
                return $.ajax({
                    url: options['url'] ? options['url'] : false,
                    method: 'get'
                }).done(function (response) {
                    self.setTitle(options['title'] ? options['title'] : '');
                    self.setContent(response);
                }).fail(function () {
                    self.setContent('Algo fue mal');
                });
            }
        });
    },
    prompt: function (options) {
        if (options) {
            const event = options['event'] ? options['event'] : false;

            if (event) {
                event.preventDefault();
                event.stopImmediatePropagation();
            }
        }

        $.confirm({
            title: 'Detalles de transición',
            content: '' +
                '<form action="" class="formName">' +
                '<div class="form-group">' +
                '<label>Descripción</label>' +
                '<textarea class="name form-control" required />' +
                '</div>' +
                '</form>',
            buttons: {
                formSubmit: {
                    text: 'Aceptar',
                    btnClass: 'btn-blue',
                    action: function () {
                        const name = this.$content.find('.name').val();

                        if(!name){
                            $.alert('Debe completar el campo descripción');
                            return false;
                        }

                        if (options['onAccept'] !== undefined) options['onAccept'](name);
                    }
                },

                cancel: {
                    text: 'Cancelar',
                    btnClass: 'btn-default',
                    action: function () {
                    }
                }
            },
            onContentReady: function () {
                const jc = this;
                this.$content.find('form').on('submit', function (e) {
                    e.preventDefault();
                    jc.$$formSubmit.trigger('click');
                });
            }
        });
    },
    close: function (scope) {
        $(scope).parents('.jconfirm-box').find('.jconfirm-closeIcon').trigger('click');
    }
};