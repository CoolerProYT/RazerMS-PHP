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

    //Unable to use
    /*public function dailyTransaction(array $data){
        $mandatory = ['merchantID','skey','rdate'];

        foreach($mandatory as $key){
            if(!array_key_exists($key,$data)){
                throw new InvalidArgumentException("Mandatory key $key is missing");
            }
        }

        $data['version'] = '4.0';

        $curl = curl_init($this->url . '/RMS/API/PSQ/psq-daily.php');

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        return json_encode($response);
    }*/

    //Unable to use
    /*public function settlementReport(){

    }*/

    public function staticQr(array $data){
        if($this->url == 'https://sandbox.merchant.razer.com') return json_encode(['status' => 'error','message' => 'This function is not available in sandbox mode']);

        $curl = curl_init($this->url . '/RMS/API/staticqr/index.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        $response->qrcode_link = 'https://qrcode.tec-it.com/API/QRCode?errorcorrection=H&color=%23ff1486&backcolor=%23ffffff&data=' . $response->qrcode_data;

        return json_encode($response);
    }

    public function cardBIN(array $data){
        if($this->url == 'https://sandbox.merchant.razer.com') return json_encode(['status' => 'error','message' => 'This function is not available in sandbox mode']);

        $mandatory = ['domain','skey','BIN'];

        foreach($mandatory as $key){
            if(!array_key_exists($key,$data)){
                throw new InvalidArgumentException("Mandatory key $key is missing");
            }
        }

        $query = http_build_query($data);
        $curl = curl_init($this->url . '/RMS/query/q_BINinfo.php?' . $query);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);

        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        echo json_encode($response);
    }

    public function voidPending(array $data){
        $mandatory = ['tranID','amount','merchantID','checksum'];

        foreach($mandatory as $key){
            if(!array_key_exists($key,$data)){
                throw new InvalidArgumentException("Mandatory key $key is missing");
            }
        }

        $curl = curl_init($this->url . '/RMS/API/VoidPendingCash/index.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        return json_encode($response);
    }

    //Void pending non cash, but void pending cash function same as void pending non cash
    /*public function voidPending(array $data){
        if($this->url == 'https://sandbox.merchant.razer.com') return json_encode(['status' => 'error','message' => 'This function is not available in sandbox mode']);

        $mandatory = ['ReferenceNo','TxnChannel','TxnAmount','MerchantID','Signature'];

        foreach($mandatory as $key){
            if(!array_key_exists($key,$data)){
                throw new InvalidArgumentException("Mandatory key $key is missing");
            }
        }

        $curl = curl_init($this->url . '/RMS/API/VoidPendingNonCash/index.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        return json_encode($response);
    }*/

    public function refund(array $data){
        $mandatory = ['RefundType','MerchantID','RefID','TxnID','Amount','Signature'];

        foreach($mandatory as $key){
            if(!array_key_exists($key,$data)){
                throw new InvalidArgumentException("Mandatory key $key is missing");
            }
        }

        $curl = curl_init($this->url . '/RMS/API/refundAPI/index.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        return json_encode($response);
    }

    public function refundStatus(array $data){
        $mandatory = ['TxnID','MerchantID','Signature'];

        foreach($mandatory as $key){
            if(!array_key_exists($key,$data)){
                throw new InvalidArgumentException("Mandatory key $key is missing");
            }
        }

        $curl = curl_init($this->url . '/RMS/API/refundAPI/q_by_txn.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        return json_encode($response);
    }
}