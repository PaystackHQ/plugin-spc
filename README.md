# Paystack SPC(sms portal creator) Module
Tested on SPC4 

## :warning: **Deprecation Notice**

We regret to inform you that the Paystack SPC(sms portal creator) Module is now deprecated and will no longer be actively maintained or supported.

**Reasons for deprecation**:
- Compatibility issues with the latest software versions
- Security vulnerabilities that cannot be addressed sufficiently.
- Obsolete functionality that is no longer relevant

To ensure a seamless experience, we recommend exploring the Paystack Integrations Directory for [alternative plugins](https://paystack.com/integrations?category=automation) that are actively maintained and supported.


## Prepare

- Before you can start taking payments through Paystack, you will first need to sign up at: 
[https://dashboard.paystack.co/#/signup][link-signup]. To receive live payments, you should request a Go-live after
you are done with configuration and have successfully made a test payment.


## Installation Steps

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

[link-developer]: https://dashboard.paystack.co/#/settings/developer
[link-signup]: https://dashboard.paystack.co/#/signup
[link-releases]: https://github.com/Kendysond/Paystack-SPC-module/releases