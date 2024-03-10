<?php

/**
 * CSAPay Payment Gateway
 * @author CSA Smart Pay Technologies
 * @Website https://csasmartpay.co.in/
 * @license 2.4 
 * @Disclaimer Please do not temper the code 
 * settings.
 *
 * @return array
 */


 if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}
////////////////////////////////
require_once(dirname(__FILE__) . '/csapay/checck.php');


 //////////////////


 ///////////////////////////////////////////
function csapay_config(){

    $configarray = array(
        
		"FriendlyName" => array("Type" => "System", "Value"=>"CSAPay Gateway"),
		"apikey" => array("FriendlyName" => "API Key", "Type" => "text", "Size" => "150", ),
		"secrekey" => array("FriendlyName" => "API Token", "Type" => "text", "Size" => "150", ),
		"upiid" => array("FriendlyName" => "Upi ID", "Type" => "text", "Size" => "150", ),
        'testMode' => array(
            'FriendlyName' => 'Test Mode',
            'Type' => 'yesno',
            'Description' => 'Tick to enable test mode',
        )
      

	 
	);		
	return $configarray;
}

////////////////

 
/**
 * Payment link.
 *
 * Required by third party payment gateway modules only.
 *
 * Defines the HTML output displayed on an invoice. Typically consists of an
 * HTML form that will take the user to the payment gateway endpoint.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/third-party-gateway/
 *
 * @return string
 */



function csapay_link($params) {	




    // Invoice Parameters
    $invoiceId = $params['invoiceid'];
    $description = $params["description"];
    $amount = $params['amount'];
    $currencyCode = $params['currency'];

    // Client Parameters
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $email = $params['clientdetails']['email'];
    $address1 = $params['clientdetails']['address1'];
    $address2 = $params['clientdetails']['address2'];
    $city = $params['clientdetails']['city'];
    $state = $params['clientdetails']['state'];
    $postcode = $params['clientdetails']['postcode'];
    $country = $params['clientdetails']['country'];
    $phone = $params['clientdetails']['phonenumber'];
    // System Parameters

    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $returnUrl = $params['returnurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];

    $icid = $params['invoiceid'] . '_' . time();
    $amount = $params['amount'];
    setcookie("my_cookie", $icid, time() + 30, "/");
    setcookie("my_amount", $amount, time() + 30, "/");
    $token = $params['token'];
    $secret = $params['secret'];
    $RECHPAY_ENVIRONMENT = 'PROD';

    $checkSum = "";
    $upiuid = "";
    $paramList = array();

    $cust_Mobile = $phone;
    $cust_Email = $email;

    $txnAmount = $amount;
    $txnNote = $firstname;


    $callback = $systemUrl . 'modules/gateways/callback/' . $moduleName . '.php';

    $TXN_URL = 'https://csapay.co.in/order/process';

    $upiuid = $params['upiid'];

    $paramList["cust_Mobile"] = $cust_Mobile;
    $paramList["cust_Email"] = $cust_Email;

    $paramList["upiuid"] = $upiuid;
    $paramList["token"] = $token;
    $paramList["orderId"] = $icid;
    $paramList["txnAmount"] = $txnAmount;
    $paramList["txnNote"] = $txnNote;
    $paramList["callback_url"] = $callback;

    $checkSum = RechPayChecksum::generateSignature($paramList, $secret);

    $code = '<form method="post" action="' . $TXN_URL . '" name="f1">
        <table border="1">
            <tbody>';
    foreach ($paramList as $name => $value) {
        $code .= '<input type="hidden" name="' . $name . '" value="' . $value . '">';
    }
    $code .= '<input type="hidden" name="checksum" value="' . $checkSum . '">
            </tbody>
        </table>
        <input type="submit" value="' . $langPayNow . '">
    </form>';

    return $code;
}

function csapay_cancelSubscription($params)
{
    // Gateway Configuration Parameters
    $accountId = $params['accountID'];
    $secret = $params['secret'];
    $testMode = $params['testMode'];
    $dropdownField = $params['dropdownField'];
    $radioField = $params['radioField'];
    $textareaField = $params['textareaField'];

    // Subscription Parameters
    $subscriptionIdToCancel = $params['subscriptionID'];

    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];

    return array(
        // 'success' if successful, any other value for failure
        'status' => 'success',
        // Data to be recorded in the gateway log - can be a string or array
        'rawdata' => $responseData,
    );
}