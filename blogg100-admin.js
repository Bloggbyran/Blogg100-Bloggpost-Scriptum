(function($) {

    $(function() {
        var count_option = $('input[name="blogg100_options[count_option]"]');
        var ps_text = $('#ps_text');

        count_option.click(function(event) {
            var option_text_suggestion = $(this).attr('data-text_suggestion');
            if ($(this).attr('value') == 'custom') {
                ps_text.focus();
            }
            ps_text.text(option_text_suggestion);
        });

    });

})(jQuery);