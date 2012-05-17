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
            self.files          = [];
            self.fileDisplay    = $('<div class="file row"><i class="icon-remove fl"></i><span class="span4"></span><span class="span2"></span></div>');
            self.options        = $.extend({}, $.fn.fileUploader.options, options);
            self.extraData      = {};

            // Hide the uploader
            self.$element
                .hide()
                .addClass('modal')
                .addClass('fade');

            // Build what the user will see
            self.build();

            // Add event listeners for drag and drop events, and buttons
            self.element.addEventListener('dragenter', self.doNothing, false);
            self.element.addEventListener('dragover', self.doNothing, false);
            self.element.addEventListener('drop', function(e) { self.dropFiles(e); }, false);
            self.$element.on('click', 'button#clear', function() { self.clearList(self) });
            self.$element.on('click', 'button#upload', function() { self.uploadFiles(self); });
        },
        build: function() {
            var self   = this;
            var header = $(document.createElement('div'))
                .addClass('title-bar row')
                .append(document.createElement('div'))
                    .children('div')
                    .addClass('span4')
                    .html('Filename')
                .parent()
                .append(document.createElement('div'))
                    .children('div:last-child')
                    .addClass('span2')
                    .html('Size')
                .parent();

            var files = $(document.createElement('div'))
                .addClass('files');

            var toolbar = $(document.createElement('div'))
                .addClass('filebar')
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
                .parent();

            self.$element.append(header.outerHTML() + files.outerHTML() + toolbar.outerHTML());
        },
        doNothing: function(e) {
            e.stopPropagation();
            e.preventDefault();
        },
        dropFiles: function(e) {
            var self        = this,
                fileList    = e.dataTransfer.files,
                i           = 0,
                fileCount   = fileList.length;

            // Add the files that were dragged in
            for (i = 0; i < fileCount; i++) {
                self.addFile(fileList[i]);
                self.files.push(fileList[i]);
            }

            // Assign removeFile event handler
            self.$element.on('click', 'i.icon-remove', function() { self.removeFile($(this), self); });

            // Remove disabled state from clear button if we have files
            if (self.files.length > 0) {
                self.$element.find('button').removeClass('disabled');
            }

            self.doNothing(e);
        },
        addFile: function(file) {
            var self        = this,
                fileHtml    = self.fileDisplay.clone();

            // Update html with filename and filesize,
            // then diplay it in the file list
            fileHtml
                .find('span:nth-child(2)')
                .text(file.fileName)
                .next()
                .text(Math.round(file.fileSize / 1024 * 100) / 100 + ' KB')
                .parent()
                .appendTo(self.$element.children('div.files'));
        },
        removeFile: function($this, self) {
            var $this       = $this,
                fileName    = $this.next().text(),
                i           = 0,
                filecount   = self.files.length;

            // Remove the div that holds the file info
            $this.parent().slideUp(200, function() { $(this).remove(); });

            // Find the file that the user wants to remove pre-upload
            for (i = 0; i < filecount; i++) {
                if (self.files[i].fileName == fileName) {
                    self.files.splice(i, 1);
                    break;
                }
            }

            // Remove disabled state from clear button if we have files
            if (self.files.length > 0) {
                self.$element.find('button#clear').removeClass('disabled');
            } else {
                self.$element.find('button').addClass('disabled');
            }
        },
        clearList: function(self) {
            self.$element.find('div.files').html('');
            self.files = [];
            self.$element.find('button').addClass('disabled');
        },
        uploadFiles: function() {
            var self = this,
                files = self.files,
                fileCount = files.length;

            for (var i = 0; i < fileCount; i++) {
                var xhr         = new XMLHttpRequest(),
                    formData    = new FormData();

                // Add any extra data the user has passed in for the xhr request
                for (var key in self.extraData) {
                    if (typeof self.extraData[key] !== 'object') {
                        formData.append(key, self.extraData[key]);
                    }
                }
                console.log(files[i]);

                formData.append('file', files[i]);

                xhr.open("POST", self.options.upload_url);
                xhr.send(formData);
            }

            self.clearList(self);
        },
        addExtraData: function() {
            var self = $.data(this[0], 'uploaderInstance');
            // arguments[0] is the object passed in
            self.extraData = $.extend({}, self.extraData, arguments[0]);

            // Return this for use with method chaining
            return this;
        }
    };

    $.fn.fileUploader = function(method) {
        // Method calling logic
        if ( FileUploader[method] ) {
            return FileUploader[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return this.each(function() {
                var uploader = Object.create(FileUploader);
                uploader.init(method, this);
                $.data(this, 'uploaderInstance', uploader);
            });
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.fileUploader' );
        }
    };

    // default options
    $.fn.fileUploader.options = {
        upload_url: '/uploads'
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