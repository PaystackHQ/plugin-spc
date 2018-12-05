<p align="center"><a href="https://paystack.com/"><img src="https://raw.githubusercontent.com/PaystackHQ/wordpress-payment-forms-for-paystack/master/icon.png" alt="Payment Forms for Paystack"></a></p>

# SPC(SMS Portal Creator) Plugin for Paystack

Welcome to the SMS Portal Creator Plugin for Paystack repository on GitHub. 

Here you can browse the source, look at open issues and keep track of development.

## Prepare

- Before you can start taking payments through Paystack, you will first need to sign up at: 
[https://dashboard.paystack.co/#/signup][link-signup]. To receive live payments, you should request a Go-live after
you are done with configuration and have successfully made a test payment.


## Installation

1. Download the [mod_spc_paystack.zip][link-releases] and install via your Joomla Mange extensions page. 
	<img src="screenshots/screenshot_step_1.png" alt="Step 1" width="650px"/>

2. Navigate to your modules page & Click the `New` button. 
	<img src="screenshots/screenshot_step_2.png" alt="Step 2" width="650px"/>

3. Click the "SPC Paystack Gateway Module" option. 
	<img src="screenshots/screenshot_step_3.png" alt="Step 3" width="650px"/>

4. Copy your API keys from your [Paystack Dashboard Settings][link-developer] and paste them on the module settings. 
	<img src="screenshots/screenshot_step_4.png" alt="Step 4" width="650px"/>

5. Create a new position by typing `spc_paystack` in the position field on the right side of the page. After creating it, save the module.
	<img src="screenshots/screenshot_step_5.png" alt="Step 5" width="650px"/>
	***
	<img src="screenshots/screenshot_step_5_1.png" alt="Step 5" width="650px"/>

6. Create the article(page) that will be the **buy SMS** page. 
	- Click on add new Article. 
	<img src="screenshots/screenshot_step_6_0.png" alt="Step 6" width="650px"/>

	- On the article content, paste the text `{loadposition spc_paystack}`
	<img src="screenshots/screenshot_step_6.png" alt="Step 6" width="650px"/>

7. Link the article(page) to your main menu: 
	- Click on add new Menu Item under the main menu. 
	<img src="screenshots/screenshot_step_7.png" alt="Step 7" width="650px"/>
	<img src="screenshots/screenshot_step_8.png" alt="Step 7" width="650px"/>

8. Click the select button beside the **Menu Item Type** option and select **Single article** under Articles on the popup overlay. 
	<img src="screenshots/screenshot_step_9.png" alt="Step 8" width="650px"/>
	<img src="screenshots/screenshot_step_10.png" alt="Step 8 " width="650px"/>

9. Click the select button beside the **Select Article** option and choose the buy sms page(article) you've created.
	<img src="screenshots/screenshot_step_11.png" alt="Step 9" width="650px"/>
	<img src="screenshots/screenshot_step_12.png" alt="Step 9" width="650px"/>

10. Save Menu and go to website to view the page created. 

## Notes 
I think step 6-10 can be optimized to be easier, my Joomla knowledge is weak. Please feel free to share your suggestions and send Pull Requests. 

## Support
For bug reports and feature requests directly related to this plugin, please use the [issue tracker](https://github.com/PaystackHQ/paystack-payment-forms-for-wordpress/issues). 

For questions related to using the plugin, please post an inquiry to the plugin [support forum](https://wordpress.org/support/plugin/payment-forms-for-paystack).

For general support or questions about your Paystack account, you can reach out by sending a message from [our website](https://paystack.com/contact).

## Community
If you are a developer, please join our Developer Community on [Slack](https://slack.paystack.com).

## Contributing to Payment Forms for Paystack

If you have a patch or have stumbled upon an issue with the Paystack Gateway for Paid Membership Pro plugin, you can contribute this back to the code. Please read our [contributor guidelines](https://github.com/PaystackHQ/wordpress-payment-forms-for-paystack/blob/master/CONTRIBUTING.md) for more information how you can do this.
