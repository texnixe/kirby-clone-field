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

      $('.btn-duplicate').click(function(e) {
        $('.message-duplicate').hide().removeClass("success error");
        $('.input-duplicate').val('').toggleClass('active');
        //field.find('.input-duplicate').;
      });

      $('.input-duplicate').keypress(function(e) {
        if (e.which == 13) {
          if($(this).val() == "") {
            $('.message-duplicate').show().html('The field cannot be empty. Please enter a page title.').addClass('error').append('<i class="icon fa fa-close"></i>')
            return false;
          }
          $.fn.ajax(fieldname);
          return false;
        }
      });

      $('.message-duplicate').on('click', '.fa-close', function(e){
        $(this).parent().hide().removeClass("success error");
      });

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
      success: function(response) {
        var r = JSON.parse(response);
        $('.message-duplicate').show().html(r.message).addClass(r.class).append('<i class="icon fa fa-close"></i>');
        $('.input-duplicate').removeClass('active');
      }
    });
  };
})(jQuery);
