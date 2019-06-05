$(document).ready(function () {
    $('.add_to_cart_link').click(function () {
        var id = $(this).attr('id');
        if (id) {
            $("#cover-spin").show();
            $.ajax({
                url: addToCartUrl,
                dataType: "json",
                type: "POST",
                data: {"ndbno": id},
                success: function (data) {
                    $("#cover-spin").hide();
                    console.log(data);
                    if (data.status) {
                        $('#cart_count').html(data.cart_count);
                        //alert(data.message)
                    } else {
                        alert(data.message)
                        return false;
                    }
                }
            });
        }
    });
})