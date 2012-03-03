// Create this function for browsers that don't have this method
if ( typeof Object.create !== 'function' ) {
    Object.create = function( obj ) {
        function F() {};
        F.prototype = obj;
        return new F();
    };
}

;(function ($, window, document, undefined) {
    var FileUploader = {
        init: function(options, ele) {
            var self            = this;
            self.element        = ele;
            self.$element       = $(ele);
            self.uploadUrl      = '/app_dev.php/upload_photo';
            self.files          = [];
            self.fileDisplay    = $('<div class="file row"><i class="icon-remove fl"></i><span class="span5"></span><span class="span1"></span></div>');
            self.options        = $.extend({}, $.fn.fileUploader.options, options);

            // Build what the user will see
            self.build();

            // Add event listeners for drag and drop events
            self.element.addEventListener('dragenter', self.doNothing, false);
            self.element.addEventListener('dragover', self.doNothing, false);
            self.element.addEventListener('drop', function(e) { self.dropFiles(e); }, false);
        },
        build: function() {
            var self   = this;
            var header = $(document.createElement('div'))
                .addClass('title-bar row')
                .append(document.createElement('div'))
                    .children('div')
                    .addClass('span5')
                    .html('Filename')
                .parent()
                .append(document.createElement('div'))
                    .children('div:last-child')
                    .addClass('span1')
                    .html('Size')
                .parent();

            var files = $(document.createElement('div'))
                .attr('id', 'files');

            var toolbar = $(document.createElement('div'))
                .attr('id', 'filebar')
                .append(document.createElement('button'))
                    .children('button')
                    .attr('id', 'upload')
                    .addClass('btn btn-primary disabled')
                    .html('Upload')
                    .append(document.createElement('i'))
                        .children('i')
                        .addClass('icon-upload')
                    .parent()
                .parent()
                .append(document.createElement('button'))
                    .children('button:last-child')
                    .attr('id', 'clear')
                    .addClass('btn disabled')
                    .html('Clear')
                    .append(document.createElement('i'))
                        .children('i')
                        .addClass('icon-trash')
                    .parent()
                .parent()


            self.$element.append(header.outerHTML() + files.outerHTML() + toolbar.outerHTML());
        },
        doNothing: function(e) {
            e.stopPropagation();
            e.preventDefault();
        },
        dropFiles: function(e) {
            var self        = this,
                files       = e.dataTransfer.files,
                i           = 0,
                fileCount   = files.length;

            self.files = files;

            // Add the files that were dragged in
            for (i = 0; i < fileCount; i++) {
                self.addFile(files[i]);
            }

            // Assign removeFile event handler
            self.$element.on('click', 'i.icon-remove', self.removeFile);

            self.doNothing(e);
        },
        addFile: function(file) {
            var self        = this,
                fileHtml    = self.fileDisplay.clone();

            // Assign the current file to the html
            fileHtml.file   = file;

            // Update html with filename and filesize,
            // then diplay it in the file list
            fileHtml
                .find('span:nth-child(2)')
                .text(file.fileName)
                .next()
                .text(Math.round(file.fileSize / 1024 * 100) / 100 + ' KB')
                .parent()
                .appendTo(self.$element.children('div#files'));
        },
        removeFile: function() {
            $(this).parent().slideUp(200, function() { $(this).remove(); });
        },
        clearList: function() {

        },
        uploadFiles: function() {

        }
    };

    $.fn.fileUploader = function(options) {
        return this.each(function() {
            var uploader = Object.create(FileUploader);
            uploader.init(options, this);
            $.data(this, 'fileUploader', uploader);
        });
    };

    // default options
    $.fn.fileUploader.options = {

    };

})(jQuery, window, document);

;(function ($, window, document, undefined) {
    $.fn.outerHTML = function() {
        var $this = $(this);
        if( "outerHTML" in $this[0] ) {
            return $this[0].outerHTML;
        } else {
            var content = $this.wrap('<div></div>').parent().html();
            $this.unwrap();
            return content;
        }
    }
})(jQuery, window, document);