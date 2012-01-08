$(document).ready(function() {
    $('#new-entry [type="submit"]').click(function(evt) {
        evt.preventDefault();
            
        $.ajax({
            url: '/app_dev.php/create',
            type: 'POST',
            data: {
                'entry[title]'  : $('#entry_title').val(),
                'entry[entry]'  : $('#entry_entry').val(),
                'entry[_token]' : $('#entry__token').val()
            },
            success: function(data) {
                if (data.success == 1)
                {
                    $('#new-entry').before(flashMessage('New entry creating successfully!', 'success'));
                }
                else
                {
                    $('#new-entry').before(flashMessage(data.errrors, 'error'));
                }
            }
        });
    });
    
    // Assign jQueryUI DatePickers
    $('.datepicker').datepicker();
    

    
    if (typeof initialize == 'function')
    {
        initialize();
    }
});

function flashMessage(msg, type)
{
   return $('<div class="alert-message ' + type + '" data-alert="alert"><a class="close" href="#">×</a><p>' + msg + '</p></div>');
}

