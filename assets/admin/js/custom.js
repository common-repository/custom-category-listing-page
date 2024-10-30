jQuery(document).ready(function() {
    jQuery("input[name='troo_post_type']").change(function() {
        var p_type = jQuery(this).attr('checked', 'checked').val();
        if (p_type == "post") {
            jQuery("#taxonomy-product_cat").hide();
            jQuery("#taxonomy-category").show();
        } else {
            jQuery("#taxonomy-category").hide();
            jQuery("#taxonomy-product_cat").show();
        }
    });
});