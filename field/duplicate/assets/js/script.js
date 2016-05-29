(function($) {
  $.fn.duplicate = function() {
    return this.each(function() {
      var fieldname = 'duplicate';
      var field = $(this);


      if(field.data( fieldname )) {
      				return true;
      			} else {
      				field.data( fieldname, true );
      			}
      field.find('.btn-duplicate').click(function(e) {
        $('.input-duplicate').val('');
        field.find('.input-duplicate').toggleClass('active');
      });

      field.find('.input-duplicate').keypress(function(e) {
        if (e.which == 13) {
          if($(this).val() == "") {
            $('.message-duplicate').show().html('The field cannot be empty. Please enter a page title.')
            return false;
          }
          $.fn.ajax(fieldname);
          return false;
        }
      });

      field.find('.message-duplicate').on('click', '.fa-close', function(e){
        $('.message-duplicate').hide();
      });

      $.fn.ajax(fieldname);

    });


  };

  // Ajax function
  $.fn.ajax = function(fieldname) {
    var page = $('[data-field="' + fieldname + '"]').find('.btn-duplicate').data('page');
        parent = $('[data-field="' + fieldname + '"]').find('.btn-duplicate').data('parent');
        newID = $('[data-field="' + fieldname + '"]').find('.input-duplicate').val();
        newID = newID.replace(/[\/\\\)\($%^&*<>"'`´:;.\?=]/g, " ");
        blueprintKey = $('[data-field="' + fieldname + '"]').find('button').data('fieldname');
        base_url = window.location.href.replace(/(\/edit.*)/g, '/field') + '/' + blueprintKey + '/' + fieldname + '/ajax/';

    $.ajax({
      url: base_url + page + '/' + encodeURIComponent(newID) + '/' + parent,
      type: 'GET',
      success: function(result) {
        $('.message-duplicate').show().html(result);
        $('.input-duplicate').removeClass('active');
      }
    });
  };
})(jQuery);
