<?php
/**
* @package    SpcPaystack
* @author     DouglasKendyson
* @copyright  Copyright2017-Paystack
* @license    GNUGeneralPublicLicenseversion2orlaterseeLICENSE.txt
**/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Include the login functions only once
require_once __DIR__ . '/helper.php';

// $params->def('greeting', 1);

// $type	          = ModLoginHelper::getType();
// $return	          = ModLoginHelper::getReturnUrl($params, $type);
// $twofactormethods = ModLoginHelper::getTwoFactorMethods();
// $user	          = JFactory::getUser();
// $layout           = $params->get('layout', 'default');

// // Logged users must load the logout sublayout
// if (!$user->guest)
// {

// 	$layout .= '_logout';
// }


// print_r($settings);

if ($params['paystack_mode'] == 0) {
	$key = $params['paystack_tpk'];
}
echo $key;

// require JModuleHelper::getLayoutPath('mod_spc_paystack', $layout);
// Instantiate global document object
$doc = JFactory::getDocument();
$doc->addScript('https://js.paystack.co/v1/inline.js');

$js = <<<JS
(function ($) {

	$(document).on('click', '#ps_buysmsunits', function () {
		var units   = $('#ps_units').val(); 
		if(units == null || units < 1){
			alert('Amount greater than 1 required');
			return false;
		}
		$.ajax({
			type   : 'POST',
			data   : {
				'option' : 'com_ajax',
				'module' : 'spc_paystack',
				'units'   : units,
				'type': 'initialize',
				'format' : 'json'
			},
			success: function (response) {
				if (response.success){
					var data = response.data;
					var names = data.name.split(' ');
					var firstName = names[0] || "";
					var lastName = names[1] || "";
					var handler = PaystackPop.setup({
	 					key: '$key',
	 					email: data.tx_email,
	 					amount: data.koboamount,
						firstname: firstName,
						lastname: lastName,
	 					ref: data.tx_reference,
	 					callback: function(response){
	 						$.ajax({
								type: 'POST',
								data: {
									'option' : 'com_ajax',
									'module' : 'spc_paystack',
									'reference'   : response.trxref,
									'type': 'verify',
									'format' : 'json'
								},
								success: function (response) {
									if (response.success){
										console.log(response);

									}

								}
							});
	 						
	 					},
	 					onClose: function(){

	 					 }
	 				});
	 				handler.openIframe();

				}

			}
		});
		return false;
	});
	
})(jQuery)
JS;
$doc->addScriptDeclaration($js);
require JModuleHelper::getLayoutPath('mod_spc_paystack');

?>