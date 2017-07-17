define([
    'jquery',
    'Magento_Ui/js/form/form'
], function ($, form) {
    // TODO need refactoring
    $(document).on('change', '[name="applies_to"]', function () {
        var target = $('[data-index="types"]');
        (this.value === '0') ? target.hide() : target.show();
    });

    $(document).on('change', '[name="customer_segments"]', function () {
        var target = $('[data-index="customer_segment_ids"]');
        (this.value === '0') ? target.hide() : target.show();
    });

    return form.extend({
        save: function (redirect, data) {
            this.validate();

            // TODO need refactoring
            var content = document.querySelector('[data-index="content"]');
            if (content) {
                var inputs = content.querySelectorAll('[name]');
                for (var i=0; i<inputs.length; i++) {
                    var input = inputs[i];
                    if (input.checked === false) {
                        continue;
                    }
                    data[input.name] = input.value
                }
            }

            if (!this.additionalInvalid && !this.source.get('params.invalid')) {
                this.setAdditionalData(data)
                    .submit(redirect);
            }
        }
    });
});