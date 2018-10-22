(function($) {
  /* Bind init function all current and future nodes */
  Drupal.behaviors.qcombobox = {
    attach: function(context, settings) {
      $("div.qcombobox input:hidden").each(function() {
        var hinput = $(this)
        var conf_key = hinput.attr("alt");
        if (!conf_key) {
          return;
        }
        if (!Drupal.settings.qcombobox) {
          return
        }
        var settings = Drupal.settings.qcombobox[conf_key];
        if (!settings) {
          return;
        }
        var input_id = hinput.attr('id');
        var params = {
          additionalFields: [hinput[0]],
          match: settings.match,
          autoSelectFirst: settings.autoSelectFirst,
          autoFill: settings.autoFill,
          mustMatch: settings.mustMatch,
          width: settings.width,
          onItemSelect: function(item, input) {
            $(input).change();
            $(hinput).change();
          }
        };
        if (settings.ajax !== undefined) {
          params.ajax = settings.ajax;
          params.ajaxParams = settings.ajaxParams;
        }
        if (settings.data != undefined) {
          params.data = settings.data;
        }
        $('#quickselect-' + input_id).quickselect(params);
      });
    }
  };
})(jQuery);