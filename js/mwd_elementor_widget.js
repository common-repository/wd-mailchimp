jQuery("document").ready(function () {
    elementor.hooks.addAction( 'panel/open_editor/widget/mwd-elementor', function( panel, model, view ) {
        var mwd_el = jQuery('select[data-setting="mailchimp_form_id"]',window.parent.document);
        mwd_add_edit_link(mwd_el);
    });
    jQuery('body').on('change', 'select[data-setting="mailchimp_form_id"]',window.parent.document, function (){
        mwd_add_edit_link(jQuery(this));
    });
});

function mwd_add_edit_link(el) {
    var fm_el = el;
    var fm_id = fm_el.val();
    var a_link = fm_el.closest('.elementor-control-content').find('.elementor-control-field-description').find('a');
    var new_link = 'admin.php?page=manage_forms';
    if(fm_id !== '0'){
        /*new_link = 'admin.php?page=manage_forms&task=edit&current_id='+fm_el.val();*/
        new_link = 'admin.php?page=manage_forms';
    }
    a_link.attr( 'href', new_link);
}