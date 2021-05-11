$(document).ready(function() {
    // $('#product_information').DataTable();

    $(".product_record").each(function(index){
        $(this).on("click", function(){
            // alert("OK");
            window.location.href = "/admin/updateProduct.php?id=" + $(this).attr('id');

        });
    });
});
