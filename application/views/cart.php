<script src="<?php echo $this->config->item('url_frontend') . '/assets/js/jquery-1.9.1.min.js'; ?>"></script>
<link rel="stylesheet" href="<?php echo $this->config->item('url_frontend') . 'assets/css/cart.css'; ?>">
<div style="max-width:1000px; margin: auto auto; position: relative;">
    <div class="header-div">
        <div style="float: left; padding: 5px 0px 5px 5px;">
            <a href="<?php echo $this->config->item('url_frontend'); ?>"><img src="<?php echo $this->config->item('url_frontend') . '/assets/images/logo.png'; ?>"></a>
        </div>
        <div style="float: right; padding: 32px 5px 28px 0px; width: 40%;">
            <div style="float: right;>
                <button type="button" class="button"  id= "back">Back</button>
            </div>
        </div>
    </div>

    <div class="list-data">
        <?php if (!empty($errors)) { ?>
            <?php foreach ($errors as $error) { ?>
                <div style="float: left; width: 100%;"> <?php echo $error['error']; ?></div>
            <?php }
        } elseif (empty($cart_details)) { ?>
            <div style="float: left; width: 100%;">Your Shopping Cart is empty. <a
                        href="<?php echo $this->config->item('url_frontend') . 'products/searchProducts'; ?>">Search
                    Products</a></div>
        <?php }
        else { ?>
        <table cellspacing="0" class="table">
            <thead>
            <tr>
                <th>S. No.</th>
                <th>Ingredient Name</th>
                <th>Total Calories</th>
            </tr>
            </thead>
            <?php $i = 1;
                foreach ($cart_details as $value) {
                    if (!empty($value['name'])) {
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $value['name']; ?></td>
                            <td><?php echo $value['calories']; ?></td>
                        </tr>
                    <?php $i ++; }
                }
            } ?>
            <table>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#back').on('click', function(){
            <?php $send = $_SERVER['HTTP_REFERER'];?>
            var redirect_to="<?php echo $send;?>";
            window.location.href = redirect_to;
        });
    });
</script>