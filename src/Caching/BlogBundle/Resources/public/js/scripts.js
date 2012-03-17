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

    var images = $('.images');

    images.imagesLoaded(function() {
        images.masonry({
            itemSelector    : '.image',
            isAnimated      : true
        });
    });

    $('.images a').fancybox();

    $('#fancybox-wrap').on('click', 'img', function() {
        $.fancybox.next();
    });
});

function flashMessage(msg, type)
{
   return $('<div class="alert-message ' + type + '" data-alert="alert"><a class="close" href="#">×</a><p>' + msg + '</p></div>');
}

$('div#uploader').fileUploader({
    upload_url: '/app_dev.php/upload_photo'
});

$('div.container').on('click', 'button.show-uploader', function() {
    $this = $(this);
    $('div#uploader').fileUploader('addExtraData', {
        entry_id: $this.data('entry-id'),
        test: 'test'
    }).modal();
});