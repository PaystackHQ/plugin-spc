<?php
/**
* @package		JJ Module Generator
* @author		JoomJunk
* @copyright	Copyright (C) 2011 - 2012 JoomJunk. All Rights Reserved
* @license		http://www.gnu.org/licenses/gpl-3.0.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * Helper for mod_login
 *
 * @package     Joomla.Site
 * @subpackage  mod_login
 *
 * @since       1.5
 */
class ModSpcPaystackHelper{

	public static function getAjax(){
		$result = array();
		$input = JFactory::getApplication()->input;
		$type  = $input->get('type');
		if ($type == 'initialize') {
			$units  = $input->get('units');
			$result = ModSpcPaystackHelper::initializePayment($units);
		}else{
			$reference  = $input->get('reference');

			$result = ModSpcPaystackHelper::verifyPayment($reference);
			// $result = 
		}
		return $result;
		
	}
	/**
	 * Retrieve the url where the user should be returned after logging in
	 *
	 * @param   \Joomla\Registry\Registry  $params  module parameters
	 * @param   string                     $type    return type
	 *
	 * @return string
	 */
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
			$reference = "SPC".time();
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
			$row->tx_memo = $units.' units of SMS on SMS portal via Paystack';
			
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
			
		}else{
			$row  = array('status' => "failed", 'message' => 'You must be logged in to continue');
		}

		return $row;
	}

	/**
	 * Returns the current users type
	 *
	 * @return string
	 */
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
						$result = array('status' => "success", 'message' => 'Payment was successful');
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
	public static function getParams()
	{
		// $user = JFactory::getUser();
		jimport('joomla.application.module.helper');
        $module = JModuleHelper::getModule('mod_spc_paystack');
        $moduleParams = new JRegistry;
        $moduleParams->loadString($module->params);
		return $moduleParams;

		
	}

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
		$txobject->tx_gateway_response = $response;
		$result = JFactory::getDbo()->updateObject('#__spc_transactions', $txobject, 'tx_id');
		
		$result  = array( 'status' => "success",'message' => "Payment Successful");
		return $result;

		
	}
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

	/**
	 * Get list of available two factor methods
	 *
	 * @return array
	 */
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
}
