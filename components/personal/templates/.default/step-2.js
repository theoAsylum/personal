$(document).ready(function() {

// Плагин Select2 для селектов с поиском

    $('.js-step-select').each(function (e) {
        var selectPlaceholder = $(this).data('select-placeholder');
        var searchPlaceholder = $(this).data('search-placeholder');

        $(this).select2({
            placeholder: selectPlaceholder,
        }).on('select2:open', function (e) {
            $('.select2-search__field').attr('placeholder', searchPlaceholder);
        }).on('select2:select', function(e){
            var data = $(e.params.data.element).data('model');
            if(data){
                filterModel(data);
            }else{
                data = $(e.params.data.element).data('brand');
                if(data){
                    filterBrand(data);
                }
                data = $(e.params.data.element).data('payment');
                if(data){
                    paymentUpdate(data,$('input[name="TIME"]:checked').data('num'));
                }
            }
        });
    });

    $('.js-reset-btn').on('click', function (e) {
        e.preventDefault();
        var form = $('.js-step-select');
        form.find('option').removeAttr('disabled');
        form.val(null).trigger('change');
    });

// step range slider

    $('.js-step-range').ionRangeSlider({
        min: 14,
        max: 49,
        from: 25,
        to: 25,
        postfix: '%',
        type: "single",
        onChange: function (data) {
            var $input = data.input;
            stepRangeCalc();
        },
        onUpdate: function (data) {
            var $input = data.input;
            stepRangeCalc();
        }
    });

    function stepRangeCalc() {
        var range = $('.js-step-range').data("ionRangeSlider").result.from;
        var price = $('.js-step-range-price');
        var priceVal = $('input[name="price"]').val();

        var count = range/100 * priceVal;

        price.html(count.toLocaleString('ru') + ' ₽');
        $('input[name="FIRST_PAYMENT"]').val(count);
    }

    $(document).on('change','input[name="TIME"]',function(){
        var payment = $('select[name="MODEL"] option:selected').data('payment');
        if(payment) paymentUpdate(payment,$(this).data('num'));
    })

});

function filterModel(list) {
    var form = $('select[name="MODEL"]');
    if (list.indexOf(',') + 1) {
        var arList = list.split(',');
    }
    form.find('option').hide().attr('disabled', true).removeAttr('selected');
    if (arList) {
        for (let i = 0; i < arList.length; i++) {
            form.find('option[value="' + arList[i] + '"]').show().removeAttr('disabled');
        }
    } else {
        form.find('option[value="' + list + '"]').show().removeAttr('disabled').attr('selected', 'selected');
    }
    setTimeout(function() {
        form.trigger('change');
    }, 1);
}
function filterBrand(id) {
    var form = $('select[name="BRAND"]');

    //form.find('option').hide().attr('disabled', true).removeAttr('selected');
    form.find('option[value="' + id + '"]').attr('selected', 'selected');

    setTimeout(function() {
        form.trigger('change');
    }, 1);
}
function paymentUpdate(payment,time) {
    if (payment.indexOf(',') + 1) {
        var arList = payment.split(',');
    }else return false;
    let my_range = $(".js-step-range").data("ionRangeSlider");

    if(time != undefined && my_range){
        my_range.update({
            min: arList[time],
        });
    }
}