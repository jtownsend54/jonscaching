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

//    $("#html5_uploader").pluploadQueue({
//        // General settings
//        runtimes : 'html5',
//        url : '/upload.php',
//        max_file_size : '20mb',
//        chunk_size : '1mb',
//        unique_names : false,
//        // Resize images on clientside if we can
//        filters : [
//            {title : "Image files", extensions : "jpg,gif,png"},
//            {title : "Zip files", extensions : "zip"}
//        ]
//    });

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

    //FileUploader.init();
});

function flashMessage(msg, type)
{
   return $('<div class="alert-message ' + type + '" data-alert="alert"><a class="close" href="#">×</a><p>' + msg + '</p></div>');
}

var FileUploader2 = {
    container : document.getElementById("files"),
    fileDiv : $('<div class="file row"><i class="icon-remove fl"></i><span class="span5"></span><span class="span1"></span></div>'),
    files : Array(),
    init : function() {
        FileUploader.container.addEventListener("dragenter", this.dragEnter, false);
        FileUploader.container.addEventListener("dragover", this.dragOver, false);
        FileUploader.container.addEventListener("drop", this.drop, false);
        $('#upload').on('click', this.uploadFiles);
    },
    dragEnter : function(e) {
        e.stopPropagation();
        e.preventDefault();
    },
    dragOver : function(e) {
        e.stopPropagation();
        e.preventDefault();
    },
    drop : function(e) {
        e.stopPropagation();
        e.preventDefault();

        FileUploader.displayFiles(e.dataTransfer.files);
    },
    displayFiles : function(files) {
        var numFiles = files.length;
        FileUploader.files = files;

        for (var i = 0; i < numFiles; i++) {
            var file        = files[i];
            var fileHtml    = $(FileUploader.fileDiv).clone();
            fileHtml.file   = file;

            fileHtml.find('i')
                .on('click', FileUploader.removeFile)
                .next()
                .text(file.fileName)
                .next()
                .text(Math.round(file.fileSize / 1024 * 100) / 100 + ' KB')
                .parent()
                .appendTo(FileUploader.container);
        }

        FileUploader.showToolbar();
    },
    removeFile : function() {
        var parent = $(this).parent();
        parent.slideUp(200, function() {
            parent.remove();
        });
    },
    clearList : function() {
        $('#filebar').slideUp();
    },
    fileExists : function(file) {

    },
    showToolbar: function() {
        $('#filebar')
            .show()
            .animate({
                bottom: '0'
            }, 300);
    },
    hideToolbar: function() {
        $('#filebar')
            .animate({
                bottom: '-30'
            }, 300);
    },
    uploadFiles: function() {
        var files = FileUploader.files,
            length = files.length;

        for (var i = 0; i < length; i++) {
            var xhr         = new XMLHttpRequest(),
                formData    = new FormData();

            formData.append('test', 'test');
            formData.append('file', files[i]);

            xhr.open("POST", "/app_dev.php/upload_photo");
            xhr.send(formData);
        }
    }
}

$('div#uploader').fileUploader();