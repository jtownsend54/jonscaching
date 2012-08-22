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

    masonify($('.images'));

    $('.images a').fancybox();
    $('a.google-map').fancybox({
        width: 615,
        height: 500
    });

    $('#fancybox-wrap').on('click', 'img', function() {
        $.fancybox.next();
    });

    var count;
    var ready = true;

    $(window).scroll(function(){
        if ($(window).scrollTop() >= parseInt($(document).height() - $(window).height() - 300) && ready) {
            ready = false;
            count = $('input#post-count').val();
            if (count != 'done') {
                $.when(loadArticle(count)).done(function() {
                    ready = true;
                });
            }
        }
    });
});

function masonify(ele) {
    ele.imagesLoaded(function() {
        ele.masonry({
            itemSelector    : '.image',
            isAnimated      : true
        });
    });
}

function flashMessage(msg, type)
{
   return $('<div class="alert-message ' + type + '" data-alert="alert"><a class="close" href="#">×</a><p>' + msg + '</p></div>');
}

$('div#uploader').fileUploader({
    upload_url: '/upload-photo'
});

$('div.container').on('click', 'button.show-uploader', function() {
    $this = $(this);
    $('div#uploader').fileUploader('addExtraData', {
        entry_id: $this.data('entry-id'),
        test: 'test'
    }).modal();
});

function loadArticle(pageNumber){
    return $.ajax({
            url: "/load-article",
            type:'POST',
            dataType:'json',
            data: {
                offset: pageNumber
            },
            success: function(data) {
                $('input#post-count').val(data.new_offset);

                if (data.success) {
                    $('div.container').find('.entry:last').after(data.html);
                    var images = $('.entry:last').find('.images');
                    masonify(images);
                    images.find('a').fancybox();
                    $('.entry:last').find('a.google-map').fancybox({
                        width: 615,
                        height: 500
                    });
                }
            }
        });
}