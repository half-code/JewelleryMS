$(function(){
    // $('#form').submit(function(event){
    //     event.preventDefault();
    //
    //     const $form = $(this),
    //         url = $form.attr('action');
    //     const button = $form.find("button[type=submit]");
    //     const formData = $form.serialize()
    //         + '&'
    //         + encodeURI(button.attr('name'))
    //         + '='
    //         + encodeURI(button.attr('value'));
    //     const $posting = $.post(url, formData);
    //     $posting.done(function(result){
    //         if(result.status === "success"){
    //             iziToast.success({
    //                 message: result.msg,
    //                 position: "bottomRight",
    //             });
    //             $form.find("input[type=text], textarea, select, input[type=number], input[type=email]").val("");
    //         }else if(result.status === "edited"){
    //             // var loc = window.location.href;
    //             // loc = loc.substring(0, loc.indexOf("?", 0));
    //             // window.location.replace(loc);
    //             iziToast.success({
    //                 message: result.msg,
    //                 position: "bottomRight",
    //             });
    //         }else if(result.status === "error"){
    //             iziToast.error({
    //                 message: result.msg,
    //                 position: "bottomRight",
    //             });
    //         }else if(result.status === "deleted"){
    //             var url = window.location.href;
    //             $('#deleteModal').modal('hide');
    //             $('#deleteModal').on('hidden.bs.modal', () => {
    //                 $('#include').load(url + " #include>*", "");
    //                 iziToast.success({
    //                     message: result.msg,
    //                     position: "bottomRight",
    //                 });
    //             });
    //         }
    //     });
    // });

    /**
     * Selectize is a library which will initialize all the input where selectize is needed
     */
    $('.selectize').selectize();

    /**
     * Bootstrap tooltip.
     */
    $('[data-toggle="tooltip"]').tooltip();

    /**
     * Copy id from view all page to the delete modal
     */
    $(".delete").click(function () {
        let $delete_path = $(this).data('delete');
        $('#form').attr('action', $delete_path);
    });

    /**
     * Data tables
     */
    $('.tables').DataTable();


    /*
     *  Invoice page
     */
    $('#invoice_date').change(function (event) {
        $('#due_date').attr({
            min: event.target.value,
        });
    });


    /**
     * This is used to login into hidden modlue when the user double clicks the profile icon
     */
    $('#h-profile').dblclick(function () {
        window.location.href = "/login.php";
    });
});