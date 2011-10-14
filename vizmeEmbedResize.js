jQuery(function() {
    var vizmeEmbedResize = function() {
        jQuery(".vizme-embed-wrapper").each(function() {
            var vif = jQuery("iframe", jQuery(this));
            var w   = jQuery(this).width() - 10;
            vif.width(w);

            if (vif.hasClass('vizmefull') || w < 580)
                vif.height(Math.round(0.75*w));
            else
                vif.height(Math.round(0.563*w));
        });
    }

    jQuery(window).resize(vizmeEmbedResize);
    vizmeEmbedResize();
});