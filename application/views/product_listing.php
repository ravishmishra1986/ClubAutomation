<script src="<?php echo $this->config->item('url_frontend') . 'assets/js/jquery-1.9.1.min.js'; ?>"></script>
<link rel="stylesheet" href="<?php echo $this->config->item('url_frontend') . 'assets/css/cart.css'; ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script type="text/javascript">
    var baseUrl = '<?php echo $this->config->item('url_frontend'); ?>';
    var addToCartUrl = '<?php echo $this->config->item('url_frontend') . 'products/addToCart'?>';
</script>
<div id="cover-spin" style="display: none;"></div>
<div style="max-width:1000px; margin: auto auto; position: relative;">
    <div class="header-div">
        <div style="float: left; padding: 5px 0px 5px 5px;">
            <a href="<?php echo $this->config->item('url_frontend'); ?>"><img
                        src="<?php echo $this->config->item('url_frontend') . 'assets/images/logo.png'; ?>"></a>
        </div>
        <div class="search-div">
            <div style="width: 70%; float: left">
                <!--<span>Search Ingredients</span>-->
                <span>
                    <form method="get" name="search_ingredient" action="">
                        <span><input type="text" name="key" placeholder="Search Ingredients..." class="form-control"
                                     value="<?php echo (isset($data['key']) && !empty($data['key'])) ? $data['key'] : ''; ?>"
                                     style="" id="key"></span>
                        <span><input type="submit" value="Search" id="find_ingredient" class="button"></span>
                    </form>
                </span>
            </div>
            <div class="cart-link">
                <a href="<?php echo $this->config->item('url_frontend') . 'products/cart'; ?>" style="position: relative;"><i class="fa fa-shopping-cart" style="font-size:24px"></i><div id="cart_count" class="cart-item-cnt"><?php echo (isset($data['cart_count']) && !empty($data['cart_count'])) ? $data['cart_count'] : 0; ?></div><span> Cart</span></a>
            </div>
        </div>
    </div>
    <div class="list-data">
        <?php if (!empty($data['response']['errors'])) { ?>
            <?php foreach ($data['response']['errors']['error'] as $error) { ?>
                <div class="message-div"> <?php echo $error['message']; ?></div>
            <?php }
        } elseif (empty($data['response'])) { ?>
            <div class="message-div">No Result found. Please search your product.</div>
        <?php }
        else { ?>
        <table cellspacing="0" class="table">
            <thead>
            <tr>
                <th>Action</th>
                <th>Ingredient Name</th>
            </tr>
            </thead>
            <?php foreach ($data['response'] as $value) {
                if (!empty($value['name'])) {
                    ?>
                    <tr>
                        <td><input type="button" id="<?php echo $value['ndbno']; ?>" class="add_to_cart_link button"
                                   value="Add to Cart">
                        </td>
                        <td><?php echo $value['name']; ?></td>
                    </tr>
                <?php }
            }
            } ?>
            <table>
    </div>
</div>


<script>
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
</script>