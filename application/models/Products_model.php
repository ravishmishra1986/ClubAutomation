<?php


class Products_model extends CI_Model
{
    var $parser;

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    public function getListing($search_key)
    {
        /**
         * Get Cart count and details
         */
        $cart_count = $this->getCartDetails('count');
        if (!empty($search_key)) {
            $api_base_url = $this->config->item('usda_api_base_url');
            $search_api = $this->config->item('auto_suggest_api');
            $usda_api_key = $this->config->item('usda_api_key');
            $max_suggestion_per_request = $this->config->item('max_suggestion_per_request');
            $api_to_hit = $api_base_url . $search_api . '?format=json&sort=n&offset=0&max=' . $max_suggestion_per_request . '&api_key=' . $usda_api_key . '&q=' . $search_key;
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $api_to_hit,
            ));
            $resp = curl_exec($curl);
            curl_close($curl);
            $suggestionsArr = json_decode($resp, 1);
            if (isset($suggestionsArr['errors']) && !empty($suggestionsArr['errors'])) {
                $suggestions = $suggestionsArr;
                $suggestions['cart_count'] = $cart_count;
            } else {
                $suggestions = $suggestionsArr['list']['item'];
                $suggestions['cart_count'] = $cart_count;
            }
        } else {
            $suggestions = array(
                'errors' => array(
                    'error' => array(
                        array(
                            'status' => 501,
                            'parameter' => 'search_key',
                            'message' => 'Please Enter one or more keywords to search'
                        )
                    )
                ),
                'cart_count' => $cart_count
            );
        }
        return $suggestions;
    }

    /**
     * This method is used to add products in cart
     * @param $params
     * @return array
     */
    public function addToCart($params)
    {
        $response = array(
            'status_code' => 502,
            'status' => true,
            'message' => 'Product successfully added in cart'
        );
        $product_id_to_add_in_cart = $params['ndbno'];
        $added_on = date('Y-m-d H:i:s');
        $cart_query = "INSERT INTO cart (session_id, cart_added, cart_updated_time, platform) VALUES (?,?,?,?) ";
        $this->db->query($cart_query, array($this->default_session_id, $added_on, $added_on, 'D'));
        $cart_id = $this->db->insert_id();
        if (!empty($cart_id)) {
            $cart_detail_query = "INSERT INTO cart_details (cart_id, product_id, quantity, price, added_on, last_updated) VALUES (?,?,?,?,?,?)";
            $this->db->query($cart_detail_query, array($cart_id, $product_id_to_add_in_cart, 1, 100, $added_on, $added_on));
            $cart_count = $this->getCartDetails('count');
            $response['cart_id'] = $cart_id;
            $response['cart_count'] = $cart_count;
        } else {
            $response = array(
                'status_code' => 501,
                'status' => false,
                'message' => 'Something went wrong! please try again'
            );
        }
        return $response;
    }

    /**
     * Get Cart count and details
     * @param string $type
     * @return array
     */
    public function getCartDetails($type = 'details')
    {
        $cart_details = array(
            'status' => true,
            'status_code' => 502,
            'cart_details' => array(),
            'message' => 'Empty cart items'
        );
        if ($type == 'count') {
            $count = $this->db->where(array('status' => 1, 'session_id' => $this->default_session_id))->from("cart")->count_all_results();
            return $count;
        } else {
            //Details
            $this->db->select('ct.cart_id, ctd.product_id');
            $this->db->from('cart ct');
            $this->db->join('cart_details ctd', 'ct.cart_id = ctd.cart_id', 'inner');
            $this->db->where('ct.session_id', $this->default_session_id);
            $this->db->where('ct.status', 1);
            $this->db->order_by('ct.cart_id', 'asc');
            $query = $this->db->get();
            if ($query->num_rows() != 0) {
                $nbdno_query = '';
                foreach ($query->result_array() as $item) {
                    $nbdno_query .= '&ndbno=' . $item['product_id'];
                }

                $api_base_url = $this->config->item('usda_api_base_url');
                $detail_api = $this->config->item('get_product_detail_api');
                $usda_api_key = $this->config->item('usda_api_key');
                $api_to_hit = $api_base_url . $detail_api . '?format=json&type=b&api_key=' . $usda_api_key . $nbdno_query;
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $api_to_hit,
                ));
                $resp = curl_exec($curl);
                curl_close($curl);
                $resp = json_decode($resp, true);
                $i = 0;
                $details = array();
                if (isset($resp['foods']) && !empty($resp['foods'])) {
                    foreach ($resp['foods'] as $v) {
                        if (!isset($v['error'])) {
                            $details[$i]['ndbno'] = $v['food']['desc']['ndbno'];
                            $details[$i]['name'] = $v['food']['desc']['name'];
                            $details[$i]['calories'] = $v['food']['nutrients'][0]['name'] . ' ' . $v['food']['nutrients'][0]['value'] . ' ' .  $v['food']['nutrients'][0]['unit'];
                            //@todo
                            //For getting all the nutrients, need to iterate $v['food']['nutrients'] node and do format of nutrients on basis of 'group' and return this to  view to show

                            $i++;
                        } else {
                            //error
                            $error[] = $v['error'];
                        }
                    }
                }
                if (!empty($error)) {
                    $cart_details = array(
                        'status' => false,
                        'status_code' => 501,
                        'cart_details' => array(),
                        'errors' => $error,
                        'message' => 'Something wend wrong! please try again'
                    );
                } else {
                    $cart_details = array(
                        'status' => true,
                        'status_code' => 502,
                        'total' => $i,
                        'cart_details' => $details
                    );
                }
                return $cart_details;
            } else {
                return $cart_details;
            }
        }
    }
}