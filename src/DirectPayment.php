<?php
namespace CoolerProYT\RazermsPHP;

use http\Exception\InvalidArgumentException;

class DirectPayment
{
    private string $secret_key;
    private string $verify_key;
    public string $url;

    public function __construct($secret_key,$verify_key,$sandbox = false)
    {
        $this->secret_key = $secret_key;
        $this->verify_key = $verify_key;
        $this->url = $sandbox ? 'https://sandbox.merchant.razer.com' : 'https://pay.merchant.razer.com';
    }

    public function createPayment(array $data, $autoRedirect = false){
        $mandatory = ['MerchantID','ReferenceNo','TxnType','TxnChannel','TxnCurrency','TxnAmount','Signature'];

        foreach($mandatory as $key){
            if(!array_key_exists($key,$data)){
                throw new InvalidArgumentException("Mandatory key $key is missing");
            }
        }

        $curl = curl_init($this->url . '/RMS/API/Direct/latest/index.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        if(!$autoRedirect) return json_encode($response);
        if(isset($response->status)) return json_encode($response);

        $this->redirectToPaymentPage($response->TxnData);
    }

    public function redirectToPaymentPage(\stdClass $data){
        $form = '<form id="paymentForm" action="' . $data->RequestURL . '" method="' . $data->RequestMethod .'">';
        foreach ($data->RequestData as $key => $value) {
            $form .= '<input type="hidden" name="' . $key . '" value="' . $value . '">';
        }
        $form .= '</form>';

        echo $form;
        echo '<script>document.getElementById("paymentForm").submit();</script>';
    }

    public function channelStatus($merchantID){
        $datetime = date('YmdHis');
        $curl = curl_init($this->url . '/RMS/API/chkstat/channel_status.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, [
            'merchantID' => $merchantID,
            'datetime' => $datetime,
            'skey' => HASH_HMAC('sha256',$datetime.$merchantID,$this->verify_key)
        ]);

        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        return json_encode($response);
    }
}
?>