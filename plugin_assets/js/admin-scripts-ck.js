jQuery.noConflict();jQuery(document).ready(function(e){var t={"#casasync_remCat_email":"casasync_request_per_remcat","#casasync_request_per_mail_fallback_value":"casasync_request_per_mail_fallback"};e.each(t,function(t,n){if(e('[name="'+n+'"]:checked').attr("value")==0){e(t).prop("readonly",!0);e(t).prop("disabled",!0)}});e.each(t,function(t,n){e('[name="'+n+'"]').click(function(){if(e(this).attr("value")!=0){e(t).removeAttr("readonly");e(t).removeAttr("disabled")}else{e(t).prop("readonly",!0);e(t).prop("disabled",!0)}})})});