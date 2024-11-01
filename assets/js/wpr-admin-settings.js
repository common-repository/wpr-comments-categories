jQuery(document).ready(function ($) {
    $('#wpr-add-new-category').on('submit', function (e) {
        e.preventDefault();
        var cat_name = $(this).find('input[name="wpr-new-category"]').val();
        $.ajax({
            type: 'POST',
            url: ajax_backend.ajax_url,
            data: {action: 'wpr_add_category', cat_name: cat_name, nonce: ajax_backend.nonce},
            async: false,
            dataType: 'json',
            success: function (response) {
                $('#wpr-current-categories').append('<li>' +
                    cat_name + ' <a href="#" class="wpr-delete-cat" data-cat-name="' + cat_name + '">[X]</a>' +
                    '</li>'
                );
                $('input[name="wpr-new-category"]').val('');
            }
        });
    });

    $('#wpr-current-categories').on('click', '.wpr-delete-cat', function (e) {
        e.preventDefault();
        var cat_name = $(this).attr('data-cat-name');
        $.ajax({
            type: 'POST',
            url: ajax_backend.ajax_url,
            data: {action: 'wpr_delete_category', cat_name: cat_name, nonce: ajax_backend.nonce},
            async: false,
            context: this,
            dataType: 'json',
            success: function (response) {
                $(this).closest('li').remove();
            }
        });
    });

    $(function () {
        $('.wpr-color-field').wpColorPicker();
    });
});