$(document).ready(function() {
    /*$('#new-entry [type="submit"]').click(function(evt) {
        evt.preventDefault();
            
        $.ajax({
            url: '/app_dev.php/create',
            type: 'POST',
            enctype: 'multipart/form-data',
            data: {
                'entry[title]'  : $('#entry_title').val(),
                'entry[entry]'  : $('#entry_entry').val(),
                'entry[routes]' : $('#entry_routes').val(),
                'entry[_token]' : $('#entry__token').val()
            },
            success: function(data) {
                if (data.success == 1)
                {
                    $('#new-entry').before(flashMessage('New entry creating successfully!', 'success'));
                }
                else
                {
                    $('#new-entry').before(flashMessage(data.errors, 'error'));
                }
            }
        });
    });*/
    
    // Assign jQueryUI DatePickers
    $('.datepicker').datepicker();
    
    if (typeof initialize == 'function')
    {
        initialize();
    }

    $("#html5_uploader").pluploadQueue({
        // General settings
        runtimes : 'html5',
        url : '/upload.php',
        max_file_size : '20mb',
        chunk_size : '1mb',
        unique_names : false,
        // Resize images on clientside if we can
        filters : [
            {title : "Image files", extensions : "jpg,gif,png"},
            {title : "Zip files", extensions : "zip"}
        ]
    });
});

function flashMessage(msg, type)
{
   return $('<div class="alert-message ' + type + '" data-alert="alert"><a class="close" href="#">×</a><p>' + msg + '</p></div>');
}

