<?php

/**
* https://support.paykickstart.com/api/
*/
class PaykickstartAPI {

    // auth_token is the api key (not campaign secret)
    // see (https://app.paykickstart.com/admin/platform-settings#)
     private $auth_token;
     private $api_url = 'https://app.paykickstart.com/api/'; // app
    //  private $api_url = "https://dev.paykickstart.com/api/"; // dev

    public function __construct($auth_token, $options=array())
    {
        $this->auth_token = $auth_token;
    }

    private function curl($route, $data, $post = false)
    {
        //Set up API path and method
        $url = $this->api_url . $route;
        $data["auth_token"] = $this->auth_token;

        //Create request data string
        $data = http_build_query($data);

        //Execute cURL request
        $ch = curl_init();

        if ($post) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            $url = $url . "?" . $data;
        }
        curl_setopt($ch, CURLOPT_URL, $url);

        // make sure curl returns a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output);
    }

    /**
     * This GET request returns license status information for a specific license key
     *
     * Example Response:
     * {
     *   "success": 1,
     *   "message": "",
     *   "data": {
     *      "valid": 1,
     *      "active": 1
     *    }
     * }
     */
    public function get_status($license_key)
    {
        $route = "licenses/status";
        $data = array("license_key" => $license_key);
        return $this->curl($route, $data);
    }

    /**
     * This GET request returns license information for a specific license key
     *
     * Example Response:
     * {
     *  "success": 0,
     *  "message": "",
     *  "data": {
     *      "license_key": "D3WS-UCTG-IDFZ-ASHU",
     *      "purchase_id": "PK-P0DHYTR0WZ",
     *      "product_id": 1234,
     *      "status": 1,
     *      "guid": null
     *  }
     * }
     */
    public function get_data($license_key)
    {
        $route = "licenses/data";
        $data = array("license_key" => $license_key);
        return $this->curl($route, $data);
    }


    /**
     * This POST request activates the license for a specific license key / GUID combination
     *
     * Example Response:
     * {
     *  "success": 1,
     *  "message": "",
     *  "data": {
     *      "license_key": "D3WS-UCTG-IDFZ-ASHU",
     *      "status": 1,
     *      "guid": "46B46540CC-128A-63DA-439F-80623S7A"
     *  }
     * }
     */
    public function activate_license($license_key)
    {
        $route = "licenses/activate";
        $data = array(
            "license_key" => $license_key,
            "guid" => @gethostbyaddr($_SERVER["SERVER_ADDR"]),
        );
        return $this->curl($route, $data, $post = true);
    }

    public function clear_license($license_key)
    {
        $route = "licenses/clear";
        $data = array("license_key" => $license_key);
        return $this->curl($route, $data, $post = true);
    }

    public function license_is_valid($license_key)
    {
        $licenseStatus = $this->get_status($license_key);
        $data = $licenseStatus->data;
        if ((isset($data->valid) && $data->valid == 1) &&
            (isset($data->active) && $data->active == 1)) {
            return true;
        }
        return false;
    }

    public function license_key_is_invalid($license_key)
    {
        return !$this->license_is_valid($license_key);
    }
}