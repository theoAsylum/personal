$(document).ready(function() {
/*
    $(document).on('click', '.js-upload-file-delete', function () {
        $(this).parent().remove();
    });
*/
    $(document).on('change', 'input:file', function () {
        var file = $(this)[0].files[0];
        if(!file.type.match(/(.docx)|(.doc)|(.msword)|(.rtf)|(.png)|(.jpeg)|(.jpg)|(.png)|(.pdf)$/i) || (file.size / 1024).toFixed(0) > 5000)  return false;
        else submitForm($(this).parents('form'),false);
    });

    $(document).on('click', '.js-upload-file-delete', function () {
        var form = $(this).parents('form');
        var file_id = $(this).data('file-id');
        var name = $(this).data('name');
        if(file_id){
            let con = confirm("Файл будет удалён");
            if(con) {
                var res = document.createElement("input");
                res.setAttribute('value', $(this).data('file-id'))
                res.setAttribute('name', 'DELETE_' + name);
                res.setAttribute('type', 'hidden');
                $(form).append(res);
                submitForm($(this).parents('form'), false);
            }
        }
    });

});