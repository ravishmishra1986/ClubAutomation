<script src="<?php echo $this->config->item('url_frontend') . '/assets/js/jquery-1.9.1.min.js'; ?>"></script>
<script type="text/javascript">
    var baseUrl = '<?php echo $this->config->item('url_frontend'); ?>';
    var addToCartUrl = '<?php echo $this->config->item('url_frontend') . 'products/addToCart'?>';
</script>
<div id="cover-spin" style="display: none;"></div>
<div style="max-width:1000px; margin: auto auto; position: relative;">
    <div style="float:left; width: 100%; background: #2e8ece">
        <div style="float: left; padding: 5px 0px 5px 5px;">
            <a href="<?php echo $this->config->item('url_frontend'); ?>"><img
                        src="<?php echo $this->config->item('url_frontend') . '/assets/images/logo.png'; ?>"></a>
        </div>
        <div style="float: right; padding: 32px 5px 28px 0px; width: 40%;">
            <div style="width: 70%; float: left">
                <span>Search Ingredients</span>
                <span>
                    <form method="get" name="search_ingredient" action="">
                        <span><input type="text" name="key" style="width: 150px;" placeholder="For example: bread"
                                     value="<?php echo (isset($data['key']) && !empty($data['key'])) ? $data['key'] : ''; ?>"
                                     style="" id="key"></span>
                        <span><input type="submit" id="find_ingredient"></span>
                    </form>
                </span>
            </div>
            <div style="float: right; padding: 20px 0px">
                <a href="<?php echo $this->config->item('url_frontend') . 'products/cart'; ?>">Cart(<span
                            id="cart_count"><?php echo (isset($data['cart_count']) && !empty($data['cart_count'])) ? $data['cart_count'] : 0; ?></span>)</a>
            </div>
        </div>
    </div>
    <div class="list-left">
        <?php if (!empty($data['response']['errors'])) { ?>
            <?php foreach ($data['response']['errors']['error'] as $error) { ?>
                <div style="float: left; width: 100%;"> <?php echo $error['message']; ?></div>
            <?php }
        } elseif (empty($data['response'])) { ?>
            <div style="float: left; width: 100%;">No Result found. Please search your product.</div>
        <?php }
        else { ?>
        <table border="1" cellspacing="0" width="100%">
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
                        <td><input type="button" id="<?php echo $value['ndbno']; ?>" class="add_to_cart_link"
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
                        if (data.status == 1) {
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
<style type="text/css">
    #cover-spin {
        position: absolute;
        top: 0px;
        right: 0px;
        width: 100%;
        height: 100%;
        background-color: #666;
        background-image: url("<?php echo $this->config->item('url_frontend') . '/assets/images/loader.gif'; ?>");
        background-repeat: no-repeat;
        background-position: center;
        z-index: 10000000;
        opacity: 0.4;
        filter: alpha(opacity=40); /* For IE8 and earlier */
    }
</style>