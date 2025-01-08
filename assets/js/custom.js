// Add items
var items = 1;
var item
$(document).on('click', '.add-more', function(){
    var day = $(this).data('day')
    if($('.'+day).length < 4) {
        $("select.select2-hidden-accessible").select2('destroy');
        item = $('.'+day+":first").html();
        $('<tr data-day="'+day+'" class="'+day+'">'+item+'</tr>').insertAfter('.'+day+":last");
        item = $('.'+day+":last");
        item.find('td:first').html('<span class="remove-item">X</span>');
        item.find('input[type="text"]').val("").end().find('select').val(null).change();
        // item.find('.item-counter').text(items);
        // item.find('.item-ref').val(ref+"-"+items);

        //  item.find('.datepicker').datepicker({ 
        //     autoclose: true, 
        //     todayHighlight: true,
        //     format: 'dd/mm/yyyy'
        // });

        // $('.select2').select2({
        //     width: '100%'
        // });
        if($('.'+day).length == 4)
            $(this).fadeOut();
    }
});
// Remove Items
$(document).on('click', '.remove-item', function(){
    var day = $(this).closest('tr').data('day')
    $(this).closest('tr').remove();
    if($('.'+day).length < 4){ 
        $('.'+day+":first").find('.add-more').fadeIn();
    }
    dayTotal(day)
    weeklyTotal()
});

var ref = "";
$(document).on('change','.received-qty, .ordered-qty',function() {
    var cart = $(this).closest('.item')
    var received = cart.find('.received-qty').val();
    var ordered = cart.find('.ordered-qty').val();
    if(received!=="" && ordered!==""){
      cart.find('.variance').val(received*1-ordered*1);
    }else{
      cart.find('.variance').val(0);
    }
});

// select change hide show
var hideCls, showCls;
$(document).on('change', 'select', function(){
    var hideCls = $(this).find('option:selected').data('hide');
    var showCls = $(this).find('option:selected').data('show');
    
    if(typeof hideCls !== undefined){
        $(hideCls).find('input').val("").end().find('select').val(null).change().end().addClass('d-none');
    }
    if(typeof showCls !== undefined){
        $(showCls).removeClass('d-none')
    }
});

// Resize background
if($('.bgSquare:visible').length>0){
    var element = document.getElementById('entry-form');
    new ResizeSensor(element, function() {
        updateHeight(element.clientHeight);
    });
}
function updateHeight(h)
{
    h=h-261
    $('.bgSquare').animate({height:h},5);
}

// Initialize the DatePicker
function addZero(val){
    return val < 10 ? ('0'+val) : val;
}
function addDays(date, days) {
    date = date.split("/")
    date = date[1]+"/"+date[0]+"/"+date[2];
    var d = new Date(date);
    d.setDate(d.getDate() + days);
    
    var new_date = addZero(d.getDate()) + '/' + addZero(d.getMonth()+1) + '/' + d.getFullYear();
    
    return new_date;
}
$(document).ready(function(){
    $('.select2').select2({
        width: '100%'
    });
    // $('.customer').select2({
    //     width: '100%',
    //     tags: true
    // });
    // $(".datepicker").datepicker({ 
    //     autoclose: true, 
    //     todayHighlight: true,
    //     format: 'dd/mm/yyyy'
    // });
    d = new Date();
    today = addZero(d.getDate()) + '/' + addZero(d.getMonth()+1) + '/' + d.getFullYear()
    const months = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December",
      ];
    $('.month').append(months[d.getMonth()])
    $('.year').append(d.getFullYear())
    ref = d.getFullYear().toString().substr(-2) +''+ addZero(d.getDate())+''+ addZero(d.getMonth()+1)  +''+ addZero(d.getHours()) +''+ addZero(d.getMinutes())
    $('[name="order_ref"]').val(ref);

    batch_code = addZero(d.getDate())+''+addZero(d.getMonth()+1)+''+d.getFullYear().toString().substr(-2)
    $('[name="batch_no"]').val(getJulianDate());
});

function getJulianDate() {
    let currentDate = new Date();
    let yearLastDigit = currentDate.getFullYear() % 10;
    let startOfYear = new Date(currentDate.getFullYear(), 0, 1);
    let daysSinceStartOfYear = Math.floor((currentDate - startOfYear) / (24 * 60 * 60 * 1000));
    let weekNo = Math.ceil((daysSinceStartOfYear + startOfYear.getDay() + 1) / 7);
    let dayOfWeek = currentDate.getDay();
    return yearLastDigit+''+weekNo+''+dayOfWeek
}

// Form Validate
function validate(){
    var valid = true;
    $('input, select, .select2-selection').removeClass('danger')
    $(".alert-danger").remove();
    $(".required:visible, .mandatory:visible").each(function () {
        if ($(this).val() == "" || $(this).val() == null || ($(this).attr('type')=="checkbox" && !$(this).is(":checked"))) {
            $(this).closest("div").append('<div class="alert-danger ps-1">This field is required</div>');
            $(this).addClass('danger')
            valid = false;
        }                
    });
    $(".email:visible").each(function(){
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/
        email_check =  emailReg.test($(this).val())
        if(email_check == false)
        {
            $(this).closest("div").append('<div class="alert-danger ps-1">Invalid email format</div>');
            $(this).addClass('danger')
            valid = false;
        }
    })

    var select 
    $(".select2:visible").each(function () {
        select = $(this).closest('div').find('select')
        if (select.hasClass('required') && (select.find('option:selected').val() == "" || select.find('option:selected').val() == null)) {
            $(this).closest("div").find('.alert-danger').remove()
            $(this).closest("div").append('<div class="alert-danger ps-1">This field is required</div>');
            $(this).closest('div').find('.select2-selection').addClass('danger')
            valid = false;
        }                
    });

    if($('.checkbox').length > 0 && $('.checkbox:visible').length > 0 && $('.checkbox:checked').length < 1)
    {
        $('.checkbox-error').append('<div class="alert-danger ps-1">Check at-least one checkbox!</div>');
        valid = false;
    }
    
    if (!valid) {
        $("html, body").animate(
            {
                scrollTop: $(".alert-danger:first").offset().top-80,
            },
            100
        );
    }
    return valid;
}

$(document).on('input','.number',function (e) {
    this.value = this.value.replace(/[^0.00-9.99]/g, '').replace(/(\..*)\./g, '$1').replace(new RegExp("(^[\\d]{50})[\\d]", "g"), '$1');
});