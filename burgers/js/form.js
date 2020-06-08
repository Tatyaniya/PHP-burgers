$( function() {

    // $('#form-submit').click(function(e) {
    //     e.preventDefault();
    //     console.log(2);
    // });

    $('#form-submit').submit(function (e) {
        e.preventDefault();
        $.ajax({
            data: $(this).serialize(),
            dataType: "html",
            success: function (result) {
               console.log('Форма отправлена');
            }
        });
    });

});