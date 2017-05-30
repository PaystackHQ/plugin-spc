<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class ModSpcPaystackHelper{

	public static function getAjax(){
		$result = array();
		$input = JFactory::getApplication()->input;
		$type  = $input->get('type');
		if ($type == 'initialize') {
			$units  = $input->get('units');
			$result = ModSpcPaystackHelper::initializePayment($units);
		}elseif ($type == 'verify'){
			$reference  = $input->get('reference');

			$result = ModSpcPaystackHelper::verifyPayment($reference);
			// $result = 
		}else{
			$reference  = $input->get('reference');

			$result = ModSpcPaystackHelper::send_emails($reference);

		}
		return $result;
		
	}
	//Generate Transaction Reference
	public static function generate_new_code($length = 10){
	  $characters = '06EFGHI9KL'.time().'MNOPJRSUVW01YZ923234'.time().'ABCD5678QXT';
	  $charactersLength = strlen($characters);
	  $randomString = '';
	  for ($i = 0; $i < $length; $i++) {
	      $randomString .= $characters[rand(0, $charactersLength - 1)];
	  }
	  return  "SPC".time().$randomString;
	}
	public static function check_code($code){
		$db = JFactory::getDBO();

		$query = $db->getQuery(true)
		            ->select('*')
		            ->from($db->quoteName('#__spc_transactions'))
		            ->where('tx_rand_id = ' . $db->quote($code));
		$db->setQuery($query);
		$o_exist = $db->loadObjectList(); 
		  if (count($o_exist) > 0) {
		      $result = true;
		  } else {
		      $result = false;
		  }

	  return $result;
	}
	public static function generate_code(){
	  $code = 0;
	  $check = true;
	  while ($check) {
	      $code = ModSpcPaystackHelper::generate_new_code();
	      $check = ModSpcPaystackHelper::check_code($code);
	  }

	  return $code;
	}
	//Initialize payment
	
	public static function initializePayment($units){
		$user = JFactory::getUser();
		if (!$user->guest){
			$db = JFactory::getDBO();

			$params = ModSpcPaystackHelper::getParams();
			
			$key = $params['paystack_lpk'];

			if ($params['paystack_mode'] == 0) {
				$key = $params['paystack_tpk'];
			}

			// print_r($params);
			$balance_before = ModSpcPaystackHelper::getBalance($user);
			$multiplier = ModSpcPaystackHelper::getMultiplier($units);
			$amount = $multiplier*$units;
			$balance_after = $balance_before + $units;

			// die();
			$reference = ModSpcPaystackHelper::generate_code();
			//Create data object
			$row = new JObject();
			$row->tx_user_id = $user->id;
			$row->tx_rand_id = $reference;
			$row->tx_balance_before = $balance_before;
			$row->tx_unit = $units;
			$row->tx_amount = $amount;
			$row->tx_balance_after = $balance_after;
			$row->tx_status = 'Pending';
			$row->tx_gateway = 'Paystack';
			$row->tx_memo = $units.' units of SMS. Reference: '.$reference;
			
			//Insert new record into jos_book table.
			$ret = $db->insertObject('#__spc_transactions', $row);
			 
			//Get the new record id
			$new_id = (int)$db->insertid();
			$row->koboamount = $row->tx_amount*100;
			$row->name = $user->name." ";
			$row->tx_email = $user->email;
			$row->tx_multiplier = $multiplier;
			$row->key = $key;
			$row->status = 'success';
			$row->tx_memo = $units.' units of SMS.  @ '.$multiplier.' per unit';
			
			
		}else{
			$row  = array('status' => "failed", 'message' => 'You must be logged in to continue');
		}

		return $row;
	}

	//Verify Payment
	
	public static function verifyPayment($reference)
	{
		$result = array( );
		$db = JFactory::getDBO();
		

		$query = $db->getQuery(true)
		            ->select('*')
		            ->from($db->quoteName('#__spc_transactions'))
		            ->where('tx_rand_id = ' . $db->quote($reference));
		$db->setQuery($query);
		$result = $db->loadObjectList(); 
		if (count($result) > 0) {
			$tx = $result[0];
			if ($tx->tx_status == 'Pending') {
				
				$api_response = ModSpcPaystackHelper::verifyTransaction($reference);

				if ($api_response['result'] == 'success') {
					$amount = $api_response['amount'];
					if ($amount == $tx->tx_amount) {
						$value_given = ModSpcPaystackHelper::giveUnits($tx,$api_response['response']);
						$result = array('status' => "success", 'message' => 'Payment was successful','reference' => $reference);
					}else{
						$result = array('status' => "failed", 'message' => 'Invalid amount paid');
					}
				}else{
					$result = array('status' => "failed", 'message' => 'Transaction Not successful');
				}
			}else{
				$result = array('status' => "failed", 'message' => 'Possible hack, reference has already been used');
			}
		}else{
			$result = array('status' => "failed", 'message' => 'Possible hack, reference not found');
		}

		return $result;
		
	}
	//Get Module Params
	
	public static function getParams()
	{
		// $user = JFactory::getUser();
		jimport('joomla.application.module.helper');
        $module = JModuleHelper::getModule('mod_spc_paystack');
        $moduleParams = new JRegistry;
        $moduleParams->loadString($module->params);
		return $moduleParams;

		
	}
	//Give units to customer
	

	public static function giveUnits($tx,$response)
	{
		
		
		///Update credit; 
		$db = JFactory::getDbo();
		// Retrieve the shout
		$query = $db->getQuery(true)
		            ->select('*')
		            ->from($db->quoteName('#__users_xtra'))
		            ->where('user_id = ' . $tx->tx_user_id);
		// Prepare the query
		$db->setQuery($query);

		$dbxtra = $db->loadObjectList(); 
		//Update Wallet
		$xtra = $dbxtra[0];
		$extraobject = new stdClass();
		$extraobject->uxid = $xtra->uxid;
		$extraobject->wallet = $xtra->wallet+$tx->tx_unit;
		$result = JFactory::getDbo()->updateObject('#__users_xtra', $extraobject, 'uxid');
		///Update Payments table
		$txobject = new stdClass();
		$txobject->tx_id = $tx->tx_id;
		$txobject->tx_status = 'Approved';
		///
		jimport ('joomla.utilities.date');
		$date = new JDate('now');
		$curdate = date_format($date, 'd-m-Y H:i:s');;
		///
		$txobject->tx_approved = $curdate;
		$txobject->tx_gateway_response = "Reference:".$tx->tx_rand_id;
		$result = JFactory::getDbo()->updateObject('#__spc_transactions', $txobject, 'tx_id');
		
		$result  = array( 'status' => "success",'message' => "Payment Successful");
		return $result;

		
	}
	//Get customer balance
	
	public static function getBalance($user = null)
	{
		
		if ($user == null) {
			$user = JFactory::getUser();
		}

		$db = JFactory::getDbo();
		// Retrieve the shout
		$query = $db->getQuery(true)
		            ->select($db->quoteName('wallet'))
		            ->from($db->quoteName('#__users_xtra'))
		            ->where('user_id = ' . $db->Quote($user->id));
		// Prepare the query
		$db->setQuery($query);

		$result = $db->loadResult();
		return $result;

		
	}
	//Generate Multiplier for units
	
	public static function getMultiplier($amount)
	{
		$singlemultiplier = 1;
		$multipliers = ModSpcPaystackHelper::getMultipliers();
		if (count($multipliers) > 0) {
			foreach ($multipliers as $key => $multiplier) {
				if (($amount >= $multiplier['left']) && ($amount <= $multiplier['right']) ) {
					$singlemultiplier = $multiplier['amount'];
				}
			}
		}
		return $singlemultiplier;

		
	}
	//Get all multipliers
	
	public static function getMultipliers()
	{
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__spc_prices'); 
		$db->setQuery($query);
		$records = $db->loadObjectList();
		$multipliers = array();
		$first = $records[0];
		$settings = explode("\n", $first->price_setting);
		if (count($settings) > 0) {
			foreach ($settings as $key => $setting) {
				if ($setting != "" && $setting != NULL  ) {
					$arr = explode("-", $setting, 2);
					$left = $arr[0];
					$second = $arr[1];

					$arr2 = explode("=", $second, 2);
					$right = $arr2[0];
					$amount = $arr2[1];
					
					$multipier  = array(
						'left' => $left,
						'right' => $right, 
						'amount' =>  $amount
					);
					$multipliers[] = $multipier;
				}
				
			}
		}
		return $multipliers;

		
	}

	//Verify transaction was successful on Paystack 
	
	public static function verifyTransaction($reference)
	{
		$params = ModSpcPaystackHelper::getParams();
		$key = $params['paystack_lsk'];

		if ($params['paystack_mode'] == 0) {
			$key = $params['paystack_tsk'];
		}

		$url = 'https://api.paystack.co/transaction/verify/' . $reference;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $key]
        );
        $request = curl_exec($ch);
        curl_close($ch);

        if ($request) {
            $result = json_decode($request, false);
        }
        if ($result->data->status == "success") {
            $paid = $result->data->amount / 100;
            $result = ['result' => 'success', 'amount' => $paid,'response' => $result->data->gateway_response];

        } else {
            $result = ['result' => 'failed'];

        }
        return $result;
	}
	public static function send_emails($reference)
	{
		$result = array( );
		$db = JFactory::getDBO();
		$config = JFactory::getConfig();

		$query = $db->getQuery(true)
		            ->select('*')
		            ->from($db->quoteName('#__spc_transactions'))
		            ->where('tx_rand_id = ' . $db->quote($reference));
		$db->setQuery($query);
		$transaction = $db->loadObjectList(); 
		$tx = $transaction[0];

		$params = ModSpcPaystackHelper::getParams();
		
		if ($params['paystack_email_customer'] == 1) {
			$mailer = JFactory::getMailer();

			$sender = array( 
			    $config->get( 'mailfrom' ),
	    		$config->get( 'fromname' )
			);
			$user = JFactory::getUser($tx->tx_user_id);
			
			$mailer->setSender($sender);
			$mailer->addRecipient($user->email);
			$mailer->setSubject('Account notification from '.$config->get( 'fromname' ));

			$fcontent = "Hello ".ucfirst($user->username).", You've been credited ".$tx->tx_unit." units & your new balance is ".$tx->tx_balance_after.".";
			$fcontent.= "\n\n Thank you for paying with Paystack.";
			$mailer->setBody($fcontent);

			$send = $mailer->Send();
		}
		

       
	}
}
