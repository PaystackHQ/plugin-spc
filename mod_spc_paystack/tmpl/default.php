<?php
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once JPATH_SITE . '/components/com_users/helpers/route.php';

JHtml::_('behavior.keepalive');
JHtml::_('bootstrap.tooltip');

// print_r($params);
$balance = ModSpcPaystackHelper::getBalance();
if ($balance != null) {
	echo "<p style='color:red;'>Your SMS balance is: ".$balance." units.</p>";
}

$multipliers = ModSpcPaystackHelper::getMultipliers();
// print_r($multipliers);
?>

<form method="post">
	Get Credited Instantly<br>
	<div class="label">No Of Units</div>
	<input name="ps_units" id="ps_units"  class="form_element" placeholder="No of SMS Units" type="number" required>
	<br/>
	<div id="priceNGN"></div>
	<br/>
	<button class="button art-button" value="Buy SMS Units" id="ps_buysmsunits" type="submit">Buy SMS Units</button>
</form>
<br>

<img src="<?php echo JURI::root().'modules/mod_spc_paystack/tmpl/paystack.png';?>" width="250px">

<script type="text/javascript">
(function ($) {
	function number_format(number, decimals, dec_point, thousands_sep) {

	  number = (number + '').replace(/[^0-9+\-Ee.]/g, '');

	  var n = !isFinite(+number) ? 0 : +number,

	    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),

	    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,

	    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,

	    s = '',

	    toFixedFix = function (n, prec) {

	      var k = Math.pow(10, prec);

	      return '' + Math.round(n * k) / k;

	    };

	  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');

	  if (s[0].length > 3) {

	    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);

	  }

	  if ((s[1] || '').length < prec) {

	    s[1] = s[1] || '';

	    s[1] += new Array(prec - s[1].length + 1).join('0');

	  }

	  return s.join(dec);

	}
	$("#ps_units").on('input', function() {
		var n = jQuery('#ps_units').val().split(',').join('').split(' ').join('');
		n = parseFloat(n);
		unitcost = 1;
		<?php if (count($multipliers) > 0) { 

			foreach ($multipliers as $key => $multiplier) {
		?>
			if( n >= parseFloat(<?php echo $multiplier["left"]; ?>) && n <= parseFloat(<?php echo $multiplier["right"]; ?>)) unitcost = parseFloat(<?php echo $multiplier["amount"]; ?>); 	
		<?php } } ?>
		

		jQuery('#priceNGN').html('<h4>Amount: NGN '+number_format(n*unitcost,2)+'</h4>');
		return true;
	});
	})(jQuery)
</script>