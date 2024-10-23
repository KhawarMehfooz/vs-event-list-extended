/*Toggle recurring options*/

jQuery(document).ready(function($) {
    var $isRecurring = $('#vsel_is_recurring');
    var $recurringOptions = $('#vsel_recurring_options');

    function toggleRecurringOptions() {
        if ($isRecurring.is(':checked')) {
            $recurringOptions.show();
        } else {
            $recurringOptions.hide();
        }
    }

    $isRecurring.on('change', toggleRecurringOptions);

    toggleRecurringOptions();
});