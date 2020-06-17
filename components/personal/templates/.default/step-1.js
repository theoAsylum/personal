$(document).ready(function(){

    $('.js-datepicker').datepicker();

    $("input[name='org_name']").suggestions({
        token: window.suggestions_api_key,
        type: "PARTY",
        minChars: 3,
        onSelect: function(suggestion) {
            $("input[name='inn']").val(suggestion.data.inn);
        }
    });

    $("input[name='inn']").suggestions({
        token: window.suggestions_api_key,
        type: "PARTY",
        minChars: 3,
        onSelect: function(suggestion) {
            $("input[name='org_name']").val(suggestion.value);
            $("input[name='inn']").val(suggestion.data.inn);
        }
    });

});