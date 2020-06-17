$(document).ready(function() {
    $(document).on('submit', '.steps__form', function (e) {
        e.preventDefault();

        submitForm($(this));

    });

// Диаграмма % для лизинга

    var Circle = function (sel) {
        var circles = document.querySelectorAll(sel);
        [].forEach.call(circles, function (el) {
            var degree = el.innerHTML;
            var valEl = parseFloat(el.innerHTML);
            valEl = valEl * 300 / 100;
            el.innerHTML = '<svg width="105" height="105"><circle transform="rotate(-90)" r="48" cx="-53" cy="53" /><circle transform="rotate(-90)" style="stroke-dasharray:' + valEl + 'px 300px;" r="48" cx="-53" cy="53" /><text x="25%" y="57%" class="progress-circle__number">' + degree + '</text></svg>';
        });
    };
    Circle('.js-progress');

    $(document).on('click', '.js-change-info', function () {
        var field = $('.steps__form-field');
        field.eq(0).removeClass('steps__form-field_disable').addClass('steps__form-field_active');
        field.eq(1).removeClass('steps__form-field_disable').addClass('steps__form-field_active');
        field.eq(2).removeClass('steps__form-field_disable').addClass('steps__form-field_active');
        field.eq(3).removeClass('steps__form-field_disable').addClass('steps__form-field_active');

        $('.js-companies-save-btn').addClass('active');
    });

    $('.js-datepicker').datepicker();

    $(document).on('click', '.companies__item', function () {
        if($(this).data('url')) window.location = $(this).data('url');
    });

    // Валидация форм
        function formValidation() {
            const forms = Array.from(document.querySelectorAll('form'));

            let checkEachInput;
            let check = [];
            let isValidate;

            function isValidEmail(emailAddress) {
                let pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
                return pattern.test(emailAddress);
            }

            function isValidPhone(phoneNumber) {
                let pattern = new RegExp(/^(\+7|7|8)?[\s-]?\(?[0-9]{3}\)?[\s-]?[0-9]{3}[\s-]?[0-9]{4}$/g);
                return pattern.test(phoneNumber);
            }

            function valid() {
                checkEachInput = true;
                check.push(checkEachInput);
            }

            function invalid() {
                checkEachInput = false;
                check.push(checkEachInput);
            }

            function validPhone(value) {
                if (isValidPhone(value)) {
                    valid();
                } else {
                    invalid();
                }
            }

            function validEmail(value, input) {
                if (!isValidEmail(value)) {
                    invalid();
                } else {
                    valid();
                }
            }

            function validByLength(value, length) {
                if (value.length > length) {
                    valid();
                } else {
                    invalid();
                }
            }

            function validByBetween(value, from, to) {
                if ((value.length <= to) && (value.length >= from) && value.trim().length) {
                    valid();
                } else {
                    invalid();
                }
            }

            function validChecked(input) {
                if (input.checked) {
                    valid();
                } else {
                    invalid();
                }
            }

            forms.forEach(form => {

                form.addEventListener('submit', function (ev) {
                    const inputs = Array.from(this.querySelectorAll('.js-validate'));

                    if (inputs.length) {
                        inputs.forEach((input) => {
                            const type = input.type || input.dataset.type;
                            const value = input.value;

                            switch (type) {
                                case 'text':
                                    validByBetween(value, 2, 70);
                                    break;
                                case 'tel':
                                    validPhone(value);
                                    break;
                                case 'email':
                                    validEmail(value, input);
                                    break;
                                case 'textarea':
                                    validByBetween(value, 1, 500);
                                    break;
                                case 'checkbox':
                                    validChecked(input);
                                    break;
                                case 'radio':
                                    validChecked(input);
                                    break;
                                default:
                                    console.log(type);
                                    break;
                            }

                            if (!checkEachInput) {
                                input.classList.add('is-error');

                                setTimeout(function () {
                                    input.classList.remove('is-error');
                                }, 5000);
                            }
                        });
                    }

                    if (inputs.length) {
                        if (check.length) {
                            isValidate = check.every(el => el === true);
                        }

                        if (isValidate === false) {
                            ev.preventDefault();
                            ev.stopPropagation();
                            check = [];
                        } else {
                            return true;
                        }
                    }
                });
            });
        }

        formValidation();

});


function submitForm(form, apply = true, status = '') {

    var arform = form;
    var form = form[0];
    var action = $(form).attr('action');
    var formData = new FormData;
    $(form).find('input').each(function () {
        if($(this).prop('type') == 'radio' || $(this).prop('type') == 'file') return;
        if (!apply && $(this).attr('name') == 'apply') return;
        if($(this).prop('type') == 'checkbox'){
            if($(this).prop('checked')) formData.append($(this).attr('name'), $(this).val());
        }else{
            formData.append($(this).attr('name'), $(this).val());
        }
    });
    $(form).find('select').each(function () {
        formData.append($(this).attr('name'), $(this).val());
    });
    $(form).find('input[type="radio"]:checked').each(function () {
        formData.append($(this).attr('name'), $(this).val());
    });
    $(form).find('input[type="file"]').each(function () {
        if ($(this).attr('multiple')) {
            for (var i = 0; i < $(this)[0].files.length; ++i) {
                formData.append($(this).attr('name') + '-' + i, $(this)[0].files[i]);
            }
        } else if ($(this)[0].files[0]) formData.append($(this).attr('name'), $(this)[0].files[0]);
    });
    formData.append('AJAX', 'Y');
    if(status) formData.append('status', status);
    if(window.google_public_key) {
        grecaptcha.execute(window.google_public_key, {action: 'authorize'})
            .then(function (token) {
                formData.append('g-recaptcha-response', token);
                $.ajax({
                    type: "POST",
                    url: action,
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'html',
                    beforeSend: function loading(){
                        if(('#steps-load').length) $.fancybox.open($('#steps-load'));
                        $(form).find('.button[type="submit"]').prop('disabled','disabled');
                    },
                    success: function success(data) {
                        $.fancybox.close();
                        $('.steps__form-row_errors').remove();
                        if ($(data).find('.steps__form-row_errors').html()) {
                            $(form).find('.button[type="submit"]').removeAttr('disabled');
                            $('.steps__form-head:eq(0)').after($(data).find('.steps__form-row_errors'));
                        } else if ($(data).hasClass('modal')) {
                            $('#steps-load').remove();
                            var res = document.createElement("DIV");
                            res.innerHTML = data.trim();
                            document.body.appendChild(res.firstChild);
                            $.fancybox.close();
                            $.fancybox.open($('#steps-load'));
                            if ($(data).hasClass('steps_waiting')) {
                                if ($(data).find('[name="LEAD"]').val()) {
                                    formData.append('LEAD', $(data).find('[name="LEAD"]').val());
                                }
                                let timer = setInterval(function () {
                                    $.ajax({
                                        type: "POST",
                                        url: '/local/ajax/checkStatus.php',
                                        data: formData,
                                        contentType: false,
                                        processData: false,
                                        dataType: 'json',
                                        success: function succ_ajax(data_aj) {
                                            if (data_aj.success && data_aj.status != 'waiting') {
                                                clearInterval(timer);
                                                submitForm(arform, true, data_aj.status);
                                                $(form).find('.button[type="submit"]').removeAttr('disabled');
                                                form.reset();
                                            } else if (data_aj.success == false) {
                                                clearInterval(timer);
                                                submitForm(arform, true, 'negative');
                                                $(form).find('.button[type="submit"]').removeAttr('disabled');
                                                form.reset();
                                            }
                                        }
                                    });
                                }, 5000);
                            } else {
                                $(form).find('.button[type="submit"]').removeAttr('disabled');
                                form.reset();
                            }
                        } else {
                            if ($(data).find('.steps__form-wrap').html()) {
                                $('.steps__form-wrap').replaceWith($(data).find('.steps__form-wrap'));
                            }
                        }
                    },
                    error: function error(jqXHR, textStatus, errorThrown) {
                        $('.steps__text').text('Ошибка! Пожалуйста, перезагрузите страницу');
                        console.log(textStatus);
                    }
                });
            });
    }else{
        $.ajax({
            type: "POST",
            url: action,
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'html',
            beforeSend: function loading(){
                $.fancybox.open($('#steps-load'));
                $(form).find('.button[type="submit"]').prop('disabled','disabled');
            },
            success: function success(data) {
                $.fancybox.close();
                $('.steps__form-row_errors').remove();
                if ($(data).find('.steps__form-row_errors').html()) {
                    $(form).find('.button[type="submit"]').removeAttr('disabled');
                    $('.steps__form-head:eq(0)').after($(data).find('.steps__form-row_errors'));
                } else if ($(data).hasClass('modal')) {
                    $('#steps-load').remove();
                    var res = document.createElement("DIV");
                    res.innerHTML = data.trim();
                    document.body.appendChild(res.firstChild);
                    $.fancybox.close();
                    $.fancybox.open($('#steps-load'));
                    if ($(data).hasClass('steps_waiting')) {
                        if ($(data).find('[name="LEAD"]').val()) {
                            formData.append('LEAD', $(data).find('[name="LEAD"]').val());
                        }
                        let timer = setInterval(function () {
                            $.ajax({
                                type: "POST",
                                url: '/local/ajax/checkStatus.php',
                                data: formData,
                                contentType: false,
                                processData: false,
                                dataType: 'json',
                                success: function succ_ajax(data_aj) {
                                    if (data_aj.success && data_aj.status != 'waiting') {
                                        clearInterval(timer);
                                        submitForm(arform, true, data_aj.status);
                                        $(form).find('.button[type="submit"]').removeAttr('disabled');
                                        form.reset();
                                    } else if (data_aj.success == false) {
                                        clearInterval(timer);
                                        submitForm(arform, true, 'negative');
                                        $(form).find('.button[type="submit"]').removeAttr('disabled');
                                        form.reset();
                                    }
                                }
                            });
                        }, 5000);
                    } else {
                        $(form).find('.button[type="submit"]').removeAttr('disabled');
                        form.reset();
                    }
                } else {
                    if ($(data).find('.steps__form-wrap').html()) {
                        $('.steps__form-wrap').replaceWith($(data).find('.steps__form-wrap'));
                    }
                }
            },
            error: function error(jqXHR, textStatus, errorThrown) {
                $('.steps__text').text('Ошибка! Пожалуйста, перезагрузите страницу');
                console.log(textStatus);
            }
        });
    }
}
function validateDate(e) {
    let elm = e.path[0];
    if( e.keyCode < 47 || e.keyCode > 57) {
        e.preventDefault();
    }

    var len = elm.value.length;

    if(len !== 1 || len !== 3) {
        if(e.keyCode == 47) {
            e.preventDefault();
        }
    }
    if(len === 2) {
        if (e.keyCode !== 8 && e.keyCode !== 46) {
            elm.value = elm.value+'.';
        }
    }

    if(len === 5) {
        if (e.keyCode !== 8 && e.keyCode !== 46) {
            elm.value = elm.value+'.';
        }
    }
}