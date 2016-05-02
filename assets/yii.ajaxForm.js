/**
 * Yii form AJAX save widget.
 *
 * @author Joel Waldock
 */
(function($){
    $.fn.yiiAjaxForm = function (options) {
        var defaults = {
            // whether to reset the form when save is successful
            resetOnSave: false,
            // whether to disable the submit button when saving
            disableSubmit: false,
            // what to change submit button content to when saving - if false there is no change
            savingContent: false,
            // selector for submit button - not needed if submit button is inside form
            submitSelector: false,
            // form submission url - if false uses form action url
            actionUrl: false
        };
        
        var events = {
            /**
             * submitBegin event is when the submit button is pressed and the form is currently being submitted.
             * The signature of the event handler should be:
             *     function (event)
             * where
             *  - event: an Event object.
             */
            submitBegin: 'submitBegin',
            /**
             * submitEnd event is triggered when the AJAX submission of the form has ended.
             * The signature of the event handler should be:
             *     function (event, success, modeldata)
             * where
             *  - event: an Event object.
             *  - success: boolean whether the save was successful
             *  - modeldata: the model data as received by the server either an array of model fields 
             *  			 or an array of model field arrays
             */
            submitEnd: 'submitEnd',
            /**
             * submitFailed event is triggered when the AJAX submission of the form has failed.
             * The signature of the event handler should be:
             *     function (event, xhr)
             * where
             *  - event: an Event object.
             *  - xhr: jqXHR object
             */
            submitFailed: 'submitFailed',
        };

        /**
         * Sets the form submit button into saving mode
         * @param data the form data object
         * @param setSaving whether to set the submit button into saving mode
         */
        var setSavingButton = function(data, setSaving) {
            var settings = data.settings;
            if (settings.savingContent) {
                data.submitButton.html(setSaving ? settings.savingContent : data.submitContent);
                data.submitButton.prop('disabled', settings.disableSubmit && setSaving);
            }
        }

        /**
         * Attaches the submit handler
         * @param $form the form jQuery object
         */
        var attachSubmit = function ($form) {
            var data = $form.data('yiiAjaxForm');
            var settings = data.settings;
            var $submitButton = data.submitButton;
            
            $form.on('reset', function(event) {
                data.submitted = false;
            });
            
            $form.on('beforeSubmit', function(event) {
                var formData = $form.data('yiiActiveForm');
                event.preventDefault();
                if (data.submitted) { return false; }
                
                $form.trigger(events.submitBegin);
                data.submitted = true;
                setSavingButton(data, true);

                $.post(settings.actionUrl, $form.serialize(), function (response) {
                    if (response.success) {
                        if (settings.resetOnSave) { $form.trigger('reset'); }
                    } else {
                        data.submitted = false;
                    }

                    $form.trigger(events.submitEnd, [response.success, response.modeldata]);
                    setSavingButton(data, false);
                }).fail( function(xhr, textStatus, errorThrown) {
                    data.submitted = false;
                    setSavingButton(data, false);
                    $form.trigger(events.submitFailed, xhr);
                    $.error('Ajax form save failed: ' + xhr.responseText);
                });
                return false;
            });
        };
        
        // Attach ajaxSubmitForm to forms
        return this.each(function () {
            var $form = $(this);
            var settings = $.extend(defaults, options);
            settings.actionUrl = settings.actionUrl || $form.attr('action');
            var $submitButton = options.submitSelector ? $(options.submitSelector) : $(':submit', $form);                
            // populate form data
            $form.data('yiiAjaxForm', {
                settings: settings,
                submitted: false,
                submitButton: $submitButton,
                submitContent: $submitButton.html()
            });
            
            attachSubmit($form);
        });
    }
})(jQuery);