# RazerMS-PHP

To install the package, run the following command:
```bash
composer require coolerproyt/razerms-php
```

In your PHP file, include the following code at the top of your code:
```php
use CoolerProYT\RazermsPHP\DirectPayment;

$rms = new RazerMS('YOUR_MERCHANT_ID','YOUR_VERIFY_KEY','SANDBOX_MODE'); // SANDBOX_MODE default value is false
```

## Direct Payment API:
Read the [RazerMS Official API Documentation](https://github.com/RazerMS/Documentation-RazerMS_API_Spec/blob/aadeac8c3e1773311f644b639137a07b7a895b22/%5BOfficial%5D%20Razer%20Direct%20Server%20API%20v1.6.23.pdf)

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

### Useful optional parameters:
```php
    'ReturnURL' => '',
    'NotificationURL' => '',
    'CallbackURL' => '',
    'FailedURL' => '',
```

## Redirect Payment to Payment Page:

If `AUTO_REDIRECT` is set to false, you can redirect the user to the payment page by using the following code:
```php
$rms->redirectPayment(json_decode($response)->TxnData);
```

## Check channel availability:

This function returns the availability of all channels enabled for a particular merchantID
```php
$response = $rms->checkChannelAvailability($merchantID);
```