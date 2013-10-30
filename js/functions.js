jQuery(document).ready(function($) {
    var displayIndex = $("#easy-pie-mm-theme").attr("displayIndex");
               
    $('#easy-pie-mm-bxslider').bxSlider(
    {
        controls: false,
        pagerCustom: "#easy-pie-mm-bxpager",
        slideWidth: 550,
        startSlide: displayIndex
    });

    // New Media uploader logic
    var custom_uploader;
 
    $('#easy-pie-mm-upload-logo-button').click(function(e) {
 
        e.preventDefault();
 
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
 
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
 
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#easy-pie-mm-field-logo').val(attachment.url);
            $('#easy-pie-mm-field-logo-preview').css("display", "block");
            $('#easy-pie-mm-field-logo-preview').attr("src", attachment.url);
        });
 
        //Open the uploader dialog
        custom_uploader.open();
 
    });
});

