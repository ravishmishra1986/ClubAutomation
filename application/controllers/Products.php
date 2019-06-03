<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Products
 *
 * This class is used for Product Listing and its details
 */
class Products extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
    }

    /**
     * This Method is used to search products
     *
     */
    public function searchProducts()
    {
        $response = array();
        $search_params = $this->input->get();
        $search_key = isset($search_params['key']) ? $search_params['key'] : '';
        $response = $this->getUsdaProductList($search_key);
        $data['data']['key'] = $search_key;
        $data['data']['response'] = $response;
        $data['data']['cart_count'] = (isset($response['cart_count']) && !empty($response['cart_count'])) ? $response['cart_count'] : 0;
        unset($response['cart_count']);
        $this->load->view('product_listing', $data);
    }

    /**
     * This method is used to get products list for USDA API
     * @param string $search_key
     * @return mixed
     */
    public function getUsdaProductList($search_key = '')
    {
        $response = $this->products_model->getListing($search_key);
        return $response;
    }

    /**
     * This method is used to add products in cart
     * @return array
     */
    public function addToCart()
    {
        $params = $this->input->post();
        if (!isset($params['ndbno']) || empty($params['ndbno'])) {
            $this->errors = array(
                'errors' => array(
                    'error' => array(
                        array(
                            'status' => 501,
                            'parameter' => 'search_key',
                            'message' => 'Something wend wrong! Please try again.'
                        )
                    )
                )
            );
            return $this->errors;
        }
        try {
            $response = $this->products_model->addToCart($params);
            echo json_encode($response);
            exit;
        } catch (\Exception $e) {
          // Do log to check what happened - Either we can manage here that what exceptions should show to user and what to not
        }
    }

    /**
     * This method is used to list products added in cart
     * @return mixed
     */
    public function cart() {
        $get_all_cart_data = $this->products_model->getCartDetails();
        $this->load->view('cart', $get_all_cart_data);
    }
}