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

$doc = JFactory::getDocument();

$doc->addScript('https://js.paystack.co/v1/inline.js');
$loadJquery = $params->get('loadJquery', 1);
if ($loadJquery == '1') {
	JHtml::_('jquery.framework');
	$doc->addScript(JURI::root().'modules/mod_spc_paystack/tmpl/jquery.blockUI.min.js');
}

$js = <<<JS
(function ($) {

	$(document).on('click', '#ps_buysmsunits', function () {
		var units   = $('#ps_units').val(); 
		if(units == null || units < 1){
			alert('Amount greater than 1 required');
			return false;
		}
		$.blockUI({ message: 'Initializing Payment' });

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
				$.unblockUI();
				if (response.success && response.data.status == 'success'){
					var data = response.data;
					var names = data.name.split(' ');
					var firstName = names[0] || "";
					var lastName = names[1] || "";
					var handler = PaystackPop.setup({
	 					key: data.key,
	 					email: data.tx_email,
	 					amount: data.koboamount,
						firstname: firstName,
						lastname: lastName,
	 					ref: data.tx_rand_id,
	 					callback: function(response){
	 						$.blockUI({ message: 'Verifying Payment' });
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
									$.unblockUI();
									if (response.success && response.data.status == 'success'){
										alert('Payment Successful');
										window.location.reload();

									}else{
										alert(response.data.message);
									}

								}
							});
	 						
	 					},
	 					onClose: function(){

	 					 }
	 				});
	 				handler.openIframe();

				}else{
					alert(response.data.message);
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