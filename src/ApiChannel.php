<?php

namespace CoolerProYT\RazermsPHP;

use http\Exception\InvalidArgumentException;

class ApiChannel{
    private string $secret_key;
    private string $verify_key;
    public string $url;

    public function __construct($secret_key,$verify_key,$sandbox = false)
    {
        $this->secret_key = $secret_key;
        $this->verify_key = $verify_key;
        $this->url = $sandbox ? 'https://sandbox.merchant.razer.com' : 'https://api.merchant.razer.com';
    }

    public function directStatusRequery(array $data){
        $mandatory = ['amount','txID','domain','skey'];

        foreach($mandatory as $key){
            if(!array_key_exists($key,$data)){
                throw new InvalidArgumentException("Mandatory key $key is missing");
            }
        }

        $data['type'] = 2;

        $curl = curl_init($this->url . '/RMS/API/gate-query/index.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        return json_encode($response);
    }

    public function indirectStatusRequery(array $data){
        $mandatory = ['amount','txID','domain','skey'];

        foreach($mandatory as $key){
            if(!array_key_exists($key,$data)){
                throw new InvalidArgumentException("Mandatory key $key is missing");
            }
        }

        $data['type'] = 2;

        $curl = curl_init($this->url . '/RMS/q_by_tid.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        return json_encode($response);
    }
}