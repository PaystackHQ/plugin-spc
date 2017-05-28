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
class ModSpcPaystackHelper
{
	public static function getAjax()
	{
		$result = array();
		$input = JFactory::getApplication()->input;
		$units  = $input->get('units');
		$type  = $input->get('type');
		if ($type == 'initialize') {
			$result = ModSpcPaystackHelper::initializePayment($units);
		}else{
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
	public static function initializePayment($units)
	{
		$user = JFactory::getUser();
		$db = JFactory::getDBO();

		// print_r($params);
		$balance_before = ModSpcPaystackHelper::getBalance($user);
		$multiplier = ModSpcPaystackHelper::getMultiplier($user);
		$amount = $multiplier*$units;
		$balance_after = $balance_before + $units;

		// die();
		$reference = "SPC".time();
		//Create data object
		$row = new JObject();
		$row->tx_user_id = $user->id;
		$row->tx_email = $user->email;
		$row->tx_reference = $reference;
		$row->tx_balance_before = $balance_before;
		$row->tx_units = $units;
		$row->tx_multiplier = $multiplier;
		$row->tx_amount = $amount;
		$row->tx_balance_after = $balance_after;
		$row->tx_status = 'pending';
		
		//Insert new record into jos_book table.
		$ret = $db->insertObject('spc_paystack_transactions', $row);
		 
		//Get the new record id
		$new_id = (int)$db->insertid();
		$row->koboamount = $row->tx_amount*100;
		$row->name = $user->name;
		$row->name = $user->name;

		return $row;
	}

	/**
	 * Returns the current users type
	 *
	 * @return string
	 */
	public static function verifyTransaction($reference)
	{
		
		
		
	}

	public static function getBalance($user)
	{
		// $user = JFactory::getUser();
		$db = JFactory::getDbo();
		// Retrieve the shout
		$query = $db->getQuery(true)
		            ->select($db->quoteName('wallet'))
		            ->from($db->quoteName('#__users_xtra'))
		            ->where('user_id = ' . $db->Quote($user->id));
		// Prepare the query
		$db->setQuery($query);
		// Load the row.
		$result = $db->loadResult();
		// print_r($result);
		return $result;

		
	}
	public static function getMultiplier($amount)
	{
		
		// print_r($result);
		// $db =& JFactory::getDBO();
		// $query = $db->getQuery(true);
		// $query->select('*');
		// $query->from('#__spc_prices'); 
		// // $query->where('id = 1');   //put your condition here    
		// $db->setQuery($query);
		// //echo $db->getQuery();exit;//SQL query string  
		// //check if error
		// if ($db->getErrorNum()) {
		//   echo $db->getErrorMsg();
		//   exit;
		// }
		// $records = $db->loadObjectList();

		// $first = $records[0];
		// $settings = $first->price_setting;
		return 19;

		
	}

	/**
	 * Get list of available two factor methods
	 *
	 * @return array
	 */
	public static function getTwoFactorMethods()
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_users/helpers/users.php';

		return UsersHelper::getTwoFactorMethods();
	}
}
