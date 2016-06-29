/**
 * Yii ActiveForm form AJAX submission widget.
 *
 * @author Joel Waldock
 */
(function($){
    $.fn.ajaxForm = function (options) {
        var defaults = {
            // form submission url - if undefined uses form action url
            action: undefined,
            // whether to trigger reset on successful submission
            resetOnSuccess: true,
            // whether form reset is required to resubmit form
            requireReset: true,
            // AJAX failure callback - signature: function(xhr, textStatus, errorThrown)
            failCallback: undefined,
        };
        
        var events = {
            /**
             * submitBegin event is when the yii ActiveForm is currently being submitted.
             * The signature of the event handler should be:
             *     function (event)
             * where
             *  - event: an Event object.
             */
            submitBegin: 'submitBegin',
            /**
             * submitEnd event is triggered when the AJAX submission of the form has ended.
             * The signature of the event handler should be:
             *     function (event, success, data)
             * where
             *  - event: an Event object.
             *  - success: boolean whether the submission was successful
             *  - data: the model data as received by the server either an array of model fields 
             *  			 or an array of model field arrays
             *  - fail: boolean whether AJAX request failed
             */
            submitEnd: 'submitEnd',
        };

        /**
         * Attaches the submit handler
         * @param $form the form jQuery object
         */
        var attachSubmit = function ($form) {
            var data = $form.data('ajaxSubmitForm');
            var settings = data.settings;
            
            // alow the form to be resubmitted after being reset

            $form.on('reset', function(e) {
                data.submitted = false;
            });

            $form.on('beforeSubmit', function(e) {
                e.preventDefault();
            	var formData = $form.data('yiiActiveForm');
                
                if (data.submitted) {
                	return false;
                }
                
                $form.trigger(events.submitBegin);
                data.submitted = true;

                $.ajax({
                	type: 'post',
                	url: settings.action,
                	data: $form.serialize(),
                	success: function (response) {
                        data.submitted = response.success && settings.requireReset;
                        
                        if (response.success && settings.resetOnSuccess) {
    						$form.trigger('reset');
    					}
                        $form.trigger(events.submitEnd, [response.success, response.data]);
                    },
                    error: [function(xhr, textStatus, errorThrown) {
                        data.submitted = false;
                        $form.trigger(events.submitEnd, [false, null]);
                    }, settings.failCallback]
                });
                return false;
            });
        };
        
        // Attach ajaxSubmitForm to forms
        return this.each(function () {
            var $form = $(this);
            var settings = $.extend(defaults, options);
            settings.action = settings.action || $form.attr('action');
            // populate form data
            $form.data('ajaxSubmitForm', {
                settings: settings,
                submitted: false,
            });
            
            attachSubmit($form);
        });
    }
})(jQuery);
