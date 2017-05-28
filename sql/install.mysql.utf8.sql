CREATE TABLE IF NOT EXISTS  `spc_paystack_transactions` (
  `tx_id` int(11) NOT NULL,
  `tx_user_id` int(11) NOT NULL,
  `tx_email` varchar(255) NOT NULL,
  `tx_reference` varchar(255) NOT NULL,
  `tx_balance_before` varchar(255) NOT NULL,
  `tx_unit` varchar(255) NOT NULL,
  `tx_balance_after` varchar(255) NOT NULL,
  `tx_status` varchar(255) NOT NULL,
  `tx_approved` datetime DEFAULT NULL,
  `tx_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `spc_paystack_transactions` (`tx_id`, `tx_user_id`, `tx_email`, `tx_reference`, `tx_balance_before`, `tx_unit`, `tx_balance_after`, `tx_status`, `tx_approved`, `tx_created`) VALUES
(1, 11, 'douglas@paystack.com', '2nj23e2n3', '32', '', '', '', NULL, '2017-05-27 23:53:12');

ALTER TABLE `spc_paystack_transactions` ADD PRIMARY KEY (`tx_id`);
ALTER TABLE `spc_paystack_transactions` MODIFY `tx_id` int(11) NOT NULL AUTO_INCREMENT;