<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class M_voxy_connect_api
 *
 * @author chuvantinh1991@gmail.com
 */
class M_voxy_connect_api_tinhcv extends data_base
{
    var $_voxy_server;
    var $_voxy_key;
    var $_voxy_api_secret;
    var $_voxy_difference_time = 7 * 3600; // chenh lech mui gio cua my voi vietnam
    var $_curl_time_out = 10; // second
    var $_curl_ssl_verifypeer = FALSE;

    /**
     * Du lieu mac dinh neu api voxy tra ve loi
     * {
     *  'error_message':'message error string!'
     * }
     */
    var $_data_return = Array(
        1 => array('error_message' => 'Có tham số đầu vào không phù hợp !'),
        2 => array('error_message' => 'Gặp lỗi xử lý gọi VOXY API !'),
        3 => array('error_message' => 'VOXY API mất kết nối !'),
        4 => array('error_message' => 'Kết quả VOXY API trả về gặp lỗi !'),
        5 => array('error_message' => 'Kết quả VOXY API trả về rỗng !'),
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function setting_table()
    {
        $this->_table_name = '';
        $this->_key_name = 'id';
        $this->_exist_created_field = false;
        $this->_exist_updated_field = false;
        $this->_exist_deleted_field = false;
        $this->_schema = Array();
        $this->_rule = Array();
        $this->_field_form = Array();
        $this->_field_table = Array();
    }

    public function setting_select()
    {
        $this->db->select('m.*');
        $this->db->from($this->_table_name . ' AS m');
    }

    /**
     * Ham chuan hoa mang du lieu tra ve cho nguoi dung
     *
     * @param string $result Ket qua tra ve tu voxy
     * @return array                Mang du lieu tra ve
     *
     * @author chuvantinh1991@gmail.com
     */
    private function _handling_the_result($result = '')
    {
        if ($result === FALSE || $result === NULL) {
            return $this->_data_return[3];
        }
        $result = json_decode($result);
        if (is_object($result)) {
            $result = (array)$result;
        } else if ($result === FALSE || !is_array($result)) {
            return $this->_data_return[4];
        }
        if (count($result) == 0) {
            $data_return = $this->_data_return[5];
        } else {
            $data_return = $result;
        }

        return $data_return;
    }

    //for product
    public function shopify_add_product($data)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/products.json';
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );

            $response = curl_exec($ch);
            $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * Ham add product to shopify use
     *
     * @param array $data Mang param can gui sang, cos the co nhieu id nen phai foreach
     * @return string               Chuoi params da convert
     */
    public function shopify_delete_product($data)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $data_return = null;
        if (is_array($data)) {
            foreach ($data as $key => $item) {
                $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/products/' . $item['id_shopify'] . '.json';
                try {
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                    //curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
                    //curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 6000);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
                    //$response       = curl_exec($ch);
                    //$data_return    = $this->_handling_the_result($response);

                    $result = curl_exec($ch);
                    $data_return = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                } catch (Exception $error) {
                    $data_return = $this->_data_return[2];
                } finally {
                    curl_close($ch);
                }
            }
        }

        return $data_return;
    }

    /**
     * Ham edit product to shopify
     *
     * @param   array $data Mang param can gui sang
     *          $id_shopify     id of product on
     * @return string           tra ve ket qua
     */
    public function shopify_edit_product($id_shopify, $data)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/products/' . $id_shopify . '.json';
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            //curl_setopt($ch, CURLOPT_PUT, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );
            $response = curl_exec($ch);
            $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    public function shopify_edit_variant_product($variant_id, $data){
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/variants/' . $variant_id . '.json';
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            //curl_setopt($ch, CURLOPT_PUT, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );
            $response = curl_exec($ch);
            $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    public function shopify_add_variant_product($id_shopify,$data){
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/products/' . $id_shopify . '/variants.json';
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );

            $response = curl_exec($ch);
            $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    public function shopify_remove_variant_product($id_shopify,$variant_id){
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $data_return = null;
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/products/' . $id_shopify .'/variants/' .$variant_id. '.json';
                try {
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                    //curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
                    //curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 6000);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
                    //$response       = curl_exec($ch);
                    //$data_return    = $this->_handling_the_result($response);

                    $result = curl_exec($ch);
                    $data_return = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                } catch (Exception $error) {
                    $data_return = $this->_data_return[2];
                } finally {
                    curl_close($ch);
                }
        return $data_return;
    }

    public function shopify_get_products($id = false)
    {
        if ($id == false) {
            $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
            $password = "f8dd0fd578eec0b06af925fc0c777f98";
            $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/products.json?limit=50';
            try {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_TIMEOUT, 600);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
                $response = curl_exec($ch);
                $data_return = $this->_handling_the_result($response);
            } catch (Exception $error) {
                $data_return = $this->_data_return[2];
            } finally {
                curl_close($ch);
            }

            return $data_return;
        } else {
            $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
            $password = "f8dd0fd578eec0b06af925fc0c777f98";
            $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/products/' . $id . '.json';
            try {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_TIMEOUT, 600);
                curl_setopt($ch, CONNECTION_TIMEOUT, 600);

                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
                $response = curl_exec($ch);
                $data_return = $this->_handling_the_result($response);
            } catch (Exception $error) {
                $data_return = $this->_data_return[2];
            } finally {
                curl_close($ch);
            }

            return $data_return;
        }
    }

    //END for product


    //Begind for product
    /**
     * Ham add customer to shopify
     *
     * @param array $arr_params Mang param can gui sang voxy
     * @return string               Chuoi params da convert
     */
    public function shopify_add_kunden($data)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/customers.json';
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );

            $response = curl_exec($ch);
            $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * Ham add product to shopify use
     *
     * @param array $data Mang param can gui sang, cos the co nhieu id nen phai foreach
     * @return string               Chuoi params da convert
     */
    public function shopify_delete_customer($data)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $data_return = null;
        if (is_array($data)) {
            foreach ($data as $key => $item) {
                $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/customers/' . $item['id_customer'] . '.json';
                try {
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                    //curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
                    //curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
                    //$response       = curl_exec($ch);
                    //$data_return    = $this->_handling_the_result($response);

                    $result = curl_exec($ch);
                    $data_return = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                } catch (Exception $error) {
                    $data_return = $this->_data_return[2];
                } finally {
                    curl_close($ch);
                }
            }
        }

        return $data_return;
    }

    /**
     * Ham edit product to shopify
     *
     * @param   array $data Mang param can gui sang
     *          $id_shopify     id of product on
     * @return string           tra ve ket qua
     */
    public function shopify_edit_customer($id, $data)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/customers/' . $id . '.json';
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            //curl_setopt($ch, CURLOPT_PUT, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );
            $response = curl_exec($ch);
            $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    public function shopify_get_kunden($id = false)
    {
        if ($id == false) {
            $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
            $password = "f8dd0fd578eec0b06af925fc0c777f98";
            $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/customers.json?limit=100';
            try {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_TIMEOUT, 600);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
                $response = curl_exec($ch);
                $data_return = $this->_handling_the_result($response);
            } catch (Exception $error) {
                $data_return = $this->_data_return[2];
            } finally {
                curl_close($ch);
            }

            return $data_return;
        } else {
            $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
            $password = "f8dd0fd578eec0b06af925fc0c777f98";
            $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/customers/' . $id . '.json';
            try {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_TIMEOUT, 600);
                curl_setopt($ch, CONNECTION_TIMEOUT, 600);

                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
                $response = curl_exec($ch);
                $data_return = $this->_handling_the_result($response);
            } catch (Exception $error) {
                $data_return = $this->_data_return[2];
            } finally {
                curl_close($ch);
            }

            return $data_return;
        }
    }

    //End for custommer

    // begind orders
    public function shopify_get_orders($id = false)
    {
        if ($id == false) {
            $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
            $password = "f8dd0fd578eec0b06af925fc0c777f98";
            $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/orders.json?limit=100&status=any';
            try {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_TIMEOUT, 600);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
                $response = curl_exec($ch);
                $data_return = $this->_handling_the_result($response);
            } catch (Exception $error) {
                $data_return = $this->_data_return[2];
            } finally {
                curl_close($ch);
            }

            return $data_return;
        } else {
            if (is_array($id)) {

                $return = array();
                foreach ($id as $item) {
                    $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
                    $password = "f8dd0fd578eec0b06af925fc0c777f98";
                    $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/orders/' . $item . '.json';
                    try {
                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 600);
                        curl_setopt($ch, CONNECTION_TIMEOUT, 600);

                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
                        $response = curl_exec($ch);
                        $return[] = $this->_handling_the_result($response);
                    } catch (Exception $error) {
                        $data_return = $this->_data_return[2];
                    } finally {
                        curl_close($ch);
                    }
                }
                return $return;
            }
        }
    }

    public function shopify_edit_orders($id_order, $data)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/orders/' . $id_order . '.json';
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            //curl_setopt($ch, CURLOPT_PUT, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );
            $response = curl_exec($ch);
            $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    // end orders

    //metafields
    public function shopify_get_metafields($id)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/products/' . $id . '/metafields.json';
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 600);
            curl_setopt($ch, CONNECTION_TIMEOUT, 600);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            $response = curl_exec($ch);
            $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;

    }

    // begind category
    public function shopify_get_category($id = false)
    {
        if ($id == false) {
            $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
            $password = "f8dd0fd578eec0b06af925fc0c777f98";
            $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/custom_collections.json?limit=100';
            try {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_TIMEOUT, 600);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
                $response = curl_exec($ch);
                $data_return = $this->_handling_the_result($response);
            } catch (Exception $error) {
                $data_return = $this->_data_return[2];
            } finally {
                curl_close($ch);
            }

            return $data_return;
        } else {
            $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
            $password = "f8dd0fd578eec0b06af925fc0c777f98";
            $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/custom_collections/' . $id . '.json';
            try {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_TIMEOUT, 600);
                curl_setopt($ch, CONNECTION_TIMEOUT, 600);

                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
                $response = curl_exec($ch);
                $data_return = $this->_handling_the_result($response);
            } catch (Exception $error) {
                $data_return = $this->_data_return[2];
            } finally {
                curl_close($ch);
            }

            return $data_return;
        }
    }

    public function shopify_edit_category($cat_id, $data)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/custom_collections/' . $cat_id . '.json';
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            //curl_setopt($ch, CURLOPT_PUT, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );
            $response = curl_exec($ch);
            $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    public function shopify_add_category($data)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/custom_collections.json';
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );

            $response = curl_exec($ch);
            $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    public function shopify_delete_category($data)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $data_return = null;
        if (is_array($data)) {
            foreach ($data as $key => $item) {
                $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/custom_collections/' . $item['cat_id'] . '.json';
                try {
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                    //curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
                    //curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
                    //$response       = curl_exec($ch);
                    //$data_return    = $this->_handling_the_result($response);

                    $result = curl_exec($ch);
                    $data_return = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                } catch (Exception $error) {
                    $data_return = $this->_data_return[2];
                } finally {
                    curl_close($ch);
                }
            }
        }

        return $data_return;
    }
    //end category

    //update  and get metafields for category, production shopify_update_metafields
    public function shopify_update_metafields($id, $data)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/metafields/' . $id . '.json';
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            //curl_setopt($ch, CURLOPT_PUT, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );
            $response = curl_exec($ch);
            $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    // xu ly metafiedl get from shopify
    public function shopify_get_category_metafield($id)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/collections/' . $id . '/metafields.json';
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 600);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            $response = curl_exec($ch);
            $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    public function shopify_get_products_metafield($id)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/products/' . $id . '/metafields.json';
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 600);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            $response = curl_exec($ch);
            $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    public function shopify_get_variant_metafield($product_id, $variant_id)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/products/' . $product_id . '/variants/' . $variant_id . '/metafields.json';
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 600);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            $response = curl_exec($ch);
            $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    //add a product to custom collection
    public function shopify_add_product_to_collection($data)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/collects.json';
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );

            $response = curl_exec($ch);
            $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    public function shopify_get_collection_product($product_id)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/collects.json?product_id=' . $product_id;
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 600);
            curl_setopt($ch, CONNECTION_TIMEOUT, 600);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            $response = curl_exec($ch);
            $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    //remove a product to collection

    public function shopify_remove_product_to_collection($collection_id)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $data_return = null;
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/collects/' . $collection_id . '.json';
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            //curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            //curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            $result = curl_exec($ch);
            $data_return = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }
        return $data_return;
    }


    // get_all_event sync for 2 system , quan ly kho va shopify
    public function get_all_event()
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/events.json?limit=250';
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 600);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            $response = curl_exec($ch);
            $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }


    public function shopify_get_transactions($order_id)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/orders/' . $order_id . '/transactions.json';
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 600);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            $response = curl_exec($ch);
            $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    //get collection from product id shopify
    public function shopify_get_collection_id($product_id)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/collects.json?product_id=' . $product_id;
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 600);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            $response = curl_exec($ch);
            $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    //for variant table
    public function shopify_add_variant($product_id, $data)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/products/' . $product_id . '/variants.json';

        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );

            $response = curl_exec($ch);
            $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    /**
     * Ham add product to shopify use
     *
     * @param array $data Mang param can gui sang, cos the co nhieu id nen phai foreach
     * @return string               Chuoi params da convert
     */
    public function shopify_delete_variant($product_id, $variant_id)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $data_return = null;
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/products/' . $product_id . '/variants/' . $variant_id . '.json';

        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            //curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_gen_curl_header());
            //curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 6000);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            //$response       = curl_exec($ch);
            //$data_return    = $this->_handling_the_result($response);

            $result = curl_exec($ch);
            $data_return = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }


        return $data_return;
    }

    /**
     * Ham edit product to shopify
     *
     * @param   array $data Mang param can gui sang
     *          $id_shopify     id of product on
     * @return string           tra ve ket qua
     */
    public function shopify_edit_variant($variant_id, $data)
    {
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/variants/' . $variant_id . '.json';

        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            //curl_setopt($ch, CURLOPT_PUT, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_time_out);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );
            $response = curl_exec($ch);
            $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;
    }

    public function shopify_get_variants_metafield($product_id, $variant_id)
    { // cho metafield of variant
        $username = "0487dc3ef08ffa586e3a989ee0a2cae7";
        $password = "f8dd0fd578eec0b06af925fc0c777f98";
        $url = 'https://' . $username . ":" . $password . '@quan-ly-kho-hang.myshopify.com/admin/products/' . $product_id . '/variants/' . $variant_id . '/metafields.json';
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 600);
            curl_setopt($ch, CONNECTION_TIMEOUT, 600);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_curl_ssl_verifypeer);
            $response = curl_exec($ch);
            $data_return = $this->_handling_the_result($response);
        } catch (Exception $error) {
            $data_return = $this->_data_return[2];
        } finally {
            curl_close($ch);
        }

        return $data_return;

    }

    //END for variant

}
