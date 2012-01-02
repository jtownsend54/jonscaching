$(document).ready(function() {
    $('#new-entry [type="submit"]').click(function(evt) {
        evt.preventDefault();
            
        $.ajax({
            url: '/app_dev.php/',
            data: {
                'title': $('#entry_title').val(),
                'entry': $('#entry_entry').val()
            },
            success: function(data) {
                console.log(data.success);
            }
        });
    });
    
    // Assign jQueryUI DatePickers
    $('.datepicker').datepicker();
    
    console.log('Function' + initialize);
    
    if (typeof initialize == 'function')
    {
        initialize();
    }
});

