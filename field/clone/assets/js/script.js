(function($) {
  $.fn.clone = function() {
    return this.each(function() {
      var field = $(this);
      var fieldname = 'clone';

      field.find('.btn-clone').click(function(e) {
        $('.input-clone').val('');
        field.find('.input-clone').toggleClass('active');
      });

      field.find('.input-clone').keypress(function(e) {
        if (e.which == 13) {
          if($(this).val() == "") {
            $('.clone-message').show().html('The field cannot be empty. Please enter a page title.')
            return false;
          }
          $.fn.ajax( field, fieldname);
          return false;
        }
      });

      field.find('.clone-message').on('click', '.fa-close', function(e){
        $('.clone-message').hide();
      });

    });

    $.fn.ajax(field, fieldname);

  };

  // Ajax function
  $.fn.ajax = function(field, fieldname) {

    var page = field.find('.btn-clone').data('page');
        parent = field.find('.btn-clone').data('parent');
        newID = field.find('.input-clone').val();
        newID = newID.replace(/[\/\\\)\($%^&*<>"'`´:;.\?=]/g, " ");
        blueprintKey = field.find('button').data('fieldname');
        base_url = window.location.href.replace(/(\/edit.*)/g, '/field') + '/' + blueprintKey + '/' + fieldname + '/ajax/';

    $.ajax({
      url: base_url + page + '/' + parent + '/' + encodeURIComponent(newID),
      type: 'GET',
      success: function(result) {
        $('.clone-message').show().html(result);
        $('.input-clone').removeClass('active');
      }
    });
  };
})(jQuery);
