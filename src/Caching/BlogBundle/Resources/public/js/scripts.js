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
                console.log(data.success);
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

