function addZero(val){
    return val < 10 ? ('0'+val) : val;
}

function validate(submit_type){
    var valid = true;
    $('input, select, .select2-selection').removeClass('danger')
    $(".text-danger").remove();
    $(".required:visible").each(function () {
        if ($(this).val() == "" || $(this).val() == null) {
            if(submit_type !== 'user-submits'){
                $(this).closest("div").append('<div class="text-danger ps-1">This field is required</div>');
            }
            else if(submit_type == 'user-submits'){
                $(this).closest("div").append('<div class="text-danger ps-1">Required</div>');
            }
            $(this).addClass('danger')
            valid = false;
        }                
    });

    var select 
    $(".select2:visible").each(function () {
        select = $(this).closest('div').find('select')
        if (select.hasClass('required') && (select.find('option:selected').val() == "" || select.find('option:selected').val() == null)) {
            $(this).closest("div").append('<div class="text-danger ps-1">This field is required</div>');
            $(this).closest('div').find('.select2-selection').addClass('danger')
            valid = false;
        }                
    });

    
    if (!valid) {
        $("html, body").animate(
            {
                scrollTop: $(".text-danger:first").offset().top-80,
            },
            100
        );
    }
    return valid;
}

$(document).on('input','.number',function (e) {
    this.value = this.value.replace(/[^0.00-9.99]/g, '').replace(/(\..*)\./g, '$1').replace(new RegExp("(^[\\d]{50})[\\d]", "g"), '$1');
});