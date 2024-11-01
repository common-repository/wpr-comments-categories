jQuery(document).ready(function ($) {
    $('a.wpr-comment-cat').on('click', function (e) {
        e.preventDefault();
        var data_cat = $(this).data("cat-name");
        if ('' === data_cat) {
            $(".comment").show();
        } else {
            var target = "." + data_cat;
            $(".comment").not(target).hide();
            $(target).show();
        }

    });
});