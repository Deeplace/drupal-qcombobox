/* Bind init function all current and future nodes */
Drupal.behaviors.qcombobox = {
    attach: function (context, drupal_settings) {
        jQuery("div.qcombobox input:hidden", context).each(function () {
            var hinput = jQuery(this);
            var conf_key = hinput.attr("alt");
            if (!conf_key) {
                return;
            }
            if (!drupal_settings.qcombobox) {
                return;
            }
            var settings = drupal_settings.qcombobox[conf_key];
            if (!settings) {
                return;
            }
            var input_id = hinput.attr("id");
            var params = {
                additionalFields: [hinput[0]],
                match: settings.match,
                autoSelectFirst: settings.autoSelectFirst,
                autoFill: settings.autoFill,
                mustMatch: settings.mustMatch,
                width: settings.width,
                onItemSelect: function (item, input) {
                    jQuery(input).change();
                    jQuery(hinput).change();
                }
            };
            if (settings.ajax !== undefined) {
                params.ajax = settings.ajax;
                params.ajaxParams = settings.ajaxParams;
            }
            if (settings.data !== undefined) {
                params.data = settings.data;
            }
            jQuery("#quickselect-" + input_id).quickselect(params);
        });
    }
};
