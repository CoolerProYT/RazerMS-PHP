# RazerMS-PHP

To install the package, run the following command:
```bash
composer require coolerproyt/razerms-php
```

# PaymentChannel

In your PHP file, include the following code at the top of your code:

```php
use CoolerProYT\RazermsPHP\PaymentChannel;

$rms = new PaymentChannel('YOUR_MERCHANT_ID','YOUR_VERIFY_KEY','SANDBOX_MODE'); // SANDBOX_MODE default value is false
```

## Direct Payment API:
Read the [RazerMS Direct Server API Documentation](https://github.com/RazerMS/Documentation-RazerMS_API_Spec/blob/aadeac8c3e1773311f644b639137a07b7a895b22/%5BOfficial%5D%20Razer%20Direct%20Server%20API%20v1.6.23.pdf)

Card Payment:
```php
$response = $rms->createPayment([
    'MerchantID' => 'YOUR_MERCHANT_ID',
    'ReferenceNo' => 'YOUR_REFERENCE_NO',
    'TxnType' => 'SALS', //Refer to the API documentation for the full list of TxnType
    'TxnChannel' => 'CREDIT7', //Refer to the API documentation for the full list of TxnChannel
    'TxnCurrency' => 'MYR',
    'TxnAmount' => '1.00', //Amount your customer needs to pay
    'Signature' => md5('TnxAmount'.'YOUR_MERCHANT_ID'.'YOUR_REFERENCE_NO'.'YOUR_VERIFY_KEY'),
    'CC_PAN' => '5555555555554444', //Credit Card Number for sandbox mode
    'CC_CVV2' => '444', //Credit Card CVV for sandbox mode
    'CC_MONTH' => '12', //Credit Card Expiry Month for sandbox mode, can be any future date
    'CC_YEAR' => '26', //Credit Card Expiry Year for sandbox mode, can be any future date
    //Other optional and conditional parameters, refer to the API documentation for the full list
],'AUTO_REDIRECT'); //AUTO_REDIRECT will redirect the user to the payment page, default value will be false
```

Other Payment Methods:
```php
$response = $rms->createPayment([
    'MerchantID' => 'YOUR_MERCHANT_ID',
    'ReferenceNo' => 'YOUR_REFERENCE_NO',
    'TxnType' => 'SALS', //Refer to the API documentation for the full list of TxnType
    'TxnChannel' => 'TNG-EWALLET', //Refer to the API documentation for the full list of TxnChannel
    'TxnCurrency' => 'MYR',
    'TxnAmount' => '1.00', //Amount your customer needs to pay
    'Signature' => md5('TnxAmount'.'YOUR_MERCHANT_ID'.'YOUR_REFERENCE_NO'.'YOUR_VERIFY_KEY'),
],'AUTO_REDIRECT'); //AUTO_REDIRECT will redirect the user to the payment page, default value will be false
```

Useful optional parameters:
```php
    'ReturnURL' => '',
    'NotificationURL' => '',
    'CallbackURL' => '',
    'FailedURL' => '',
```

## Redirect Payment to Payment Page:

If `AUTO_REDIRECT` is set to false, you can redirect the user to the payment page by using the following code:
```php
$rms->redirectToPaymentPage(json_decode($response)->TxnData);
```

## Livewire Auto Redirect
For livewire, auto redirect will not be working, you need to pass the `TxnData` to laravel controller and run `redirectPayment` function in the controller.

### Example:

#### Livewire Controller
```php
namespace App\Livewire;

use Livewire\Component;
use CoolerProYT\RazermsPHP\PaymentChannel;

class Payment extends Component
{
    public function pay(){
        $rms = new PaymentChannel('YOUR_MERCHANT_ID','YOUR_VERIFY_KEY','SANDBOX_MODE');

        $response = $rms->createPayment([
            'MerchantID' => 'YOUR_MERCHANT_ID',
            'ReferenceNo' => '1',
            'TxnType' => 'SALS', //Refer to the API documentation for the full list of TxnType
            'TxnChannel' => 'CREDITZ', //Refer to the API documentation for the full list of TxnChannel
            'TxnCurrency' => 'MYR',
            'TxnAmount' => '1.00', //Amount your customer needs to pay
            'Signature' => md5('TnxAmount'.'YOUR_MERCHANT_ID'.'YOUR_REFERENCE_NO'.'YOUR_VERIFY_KEY'),
            'CC_PAN' => '5555555555554444', //Credit Card Number for sandbox mode
            'CC_CVV2' => '444', //Credit Card CVV for sandbox mode
            'CC_MONTH' => '12', //Credit Card Expiry Month for sandbox mode, can be any future date
            'CC_YEAR' => '26', //Credit Card Expiry Year for sandbox mode, can be any future date
            //Other optional and conditional parameters, refer to the API documentation for the full list
        ]); //AUTO_REDIRECT will redirect the user to the payment page, default value will be false

        $response = json_decode($response);

        return redirect()->route('pay')->with(['postData' => $response->TxnData]);
    }

    public function render()
    {
        return view('livewire.payment');
    }
}
```

#### Laravel Controller
```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CoolerProYT\RazermsPHP\DirectPayment;

class PaymentController extends Controller
{
    public function pay(Request $request){
        $rms = new PaymentChannel('YOUR_MERCHANT_ID','YOUR_VERIFY_KEY','SANDBOX_MODE');
        
        $rms->redirectToPaymentPage($request->session()->get('postData'));
    }
}
```

## Channel Status API:

This function returns the availability of all channels enabled for a particular merchantID
```php
$response = $rms->checkChannelAvailability($merchantID);
```

# ApiChannel
Read the [RazerMS Official API Documentation](https://github.com/RazerMS/Documentation-RazerMS_API_Spec/blob/main/%5Bofficial%20API%5D%20Razer%20API%20Spec%20for%20Merchant%20(v13.59).pdf)

In your PHP file, include the following code at the top of your code:

```php
use CoolerProYT\RazermsPHP\ApiChannel;

$rms = new ApiChannel('YOUR_MERCHANT_ID','YOUR_VERIFY_KEY','SANDBOX_MODE'); // SANDBOX_MODE default value is false
```

## Direct Status Requery
This will trigger a query to the payment channel or bank status server and there are cases that bank
status server is not in-sync with its payment server that might give different results, that leads to a defer
update and will trigger a callback from PG server, once the status is synced and changed.

```php
$response = $rms->directStatusRequery([
    'amount' => 'TRANSACTION_AMOUNT_OF_THE_txID',
    'txID' => 'YOUR_txID',
    'domain' => 'YOUR_MERCHANT_ID',
    'skey' => md5('YOUR_txID'.'YOUR_MERCHANT_ID'.'YOUR_VERIFY_KEY'.'TRANSACTION_AMOUNT_OF_THE_txID')
]);
```

## Indirect Status Requery
Request & Response parameters are the same as Direct Status Requery but the format and parameters order of the responses are slightly different.

```php
$response = $rms->directStatusRequery([
    'amount' => 'TRANSACTION_AMOUNT_OF_THE_txID',
    'txID' => 'YOUR_txID',
    'domain' => 'YOUR_MERCHANT_ID',
    'skey' => md5('YOUR_txID'.'YOUR_MERCHANT_ID'.'YOUR_VERIFY_KEY'.'TRANSACTION_AMOUNT_OF_THE_txID')
]);
```

## Static QR-Code Generator
For merchant to generate static QR code of e-wallet.

I have modified the response because the default response url is not working.
```php
$response = $rms->staticQr([
    'merchantID' => 'YOUR_MERCHANT_ID',
    'channel' => 'DuitNowSQR',
    'orderid' => 'YOUR_ORDER_ID',
    'currency' => 'MYR',
    'bill_name' => 'Item name',
    'bill_desc' => 'Item description',
    'checksum' => md5('YOUR_MERCHANT_ID'.'DuitNowSQR'.'YOUR_ORDER_ID'.'MYR'.'YOUR_VERIFY_KEY')
]);
```
## Card BIN Checker
To retrieve the card BIN information such as card type and the issuer information.

```php
$response = $rms->cardBin([
    'domain' => 'YOUR_MERCHANT_ID',
    'skey' => md5('YOUR_MERCHANT_ID'.'YOUR_SECRET_KEY','BIN'),
    'BIN' => '555566' //First 6-digit number of the PAN
]);
```

## Void Pending
For merchants to cancel and void the cash payment request order, before getting paid or the
expiry time, and force-to-expired.

```php
$response = $rms->voidPending([
    'tranID' => 'TRANSACTION_ID',
    'amount' => 'AMOUNT_OF_THE_TRANSACTION',
    'merchantID' => 'YOUR_MERCHANT_ID',
    'checksum' => md5('TRANSACTION_ID'.'AMOUNT_OF_THE_TRANSACTION'.'YOUR_MERCHANT_ID'.'YOUR_VERIFY_KEY')
]);
```

## Refund
Merchants can request a full/partial refund for a “captured” or “settled” transaction regardless
of the payment method. The request can be sent within 180 days from the transaction creation
date and the refund process will take about 7-14 days after the request is sent.

Merchants can enable the “Refund Portal” so that buyers who need a refund could easily
provide the bank account details to shorten the refund lead time.

```php
$response = $rms->refund([
    'RefundType' => 'P',
    'MerchantID' => 'YOUR_MERCHANT_ID',
    'RefID' => 'ORDER_ID',
    'TxnID' => 'TRANSACTION_ID',
    'Amount' => 'AMOUNT_TO_REFUND',
    'Signature' => md5('P'.'YOUR_MERCHANT_ID'.'REF_ID'.'TXN_ID'.'AMOUNT_TO_REFUND'.'YOUR_SECRET_KEY')
]);
```

## Refund Status
Merchant is able to do a status inquiry for a refund transaction.

```php
$response = $rms->refundStatus([
    'TxnID' => 'TRANSACTION_ID',
    'MerchantID' => 'YOUR_MERCHANT_ID',
    'Signature' => md5('TRANSACTION_ID'.'YOUR_MERCHANT'.'YOUR_VERIFY_KEY')
]);
```

| Status Code | Description       |
|-------------|-------------------|
| 00          | Refund successful |
| 11          | Refund rejected   |
| 22          | Refund pending    |