(function($){

    /* formular requests
    --------------------------------------------- */

    /* --- set defaults --- */
    // 1. input mail, 2. input radio
    var myElements = {
        "#casasync_remCat_email" : "casasync_request_per_remcat",
        "#casasync_request_per_mail_fallback_value" : "casasync_request_per_mail_fallback"
    };
    $.each(myElements, function(i, val) {
        if($('[name="' + val + '"]:checked').attr('value') == 0) {
            $(i).prop('readonly', true);
            $(i).prop('disabled', true);
        }
    });

    /* --- set or remove attributes --- */
    $.each(myElements, function(i, val) {
        $('[name="' + val + '"]').click(function() {
            if($(this).attr('value') != 0) {
                $(i).removeAttr('readonly');
                $(i).removeAttr('disabled');
            } else {
                 $(i).prop('readonly', true);
                 $(i).prop('disabled', true);
            }
        });
    });

}(jQuery));