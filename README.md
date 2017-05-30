# Paystack SPC(sms portal creator) Module
Tested on SPC4 

## Installation

1. Download the `[mod_spc_paystack.zip](https://github.com/Kendysond/Paystack-SPC-module/releases)`  and install via your Joomla Mange extensions page. 
<img src="screenshots/screenshot_step_1.png" alt="Step 1" width="1250px"/>

2. Navigate to your modules page. 
<img src="screenshots/screenshot_step_2.png" alt="Step 2" width="1250px"/>
3. Click the New button and select the "SPC Paystack Gateway Module " link. 
<img src="screenshots/screenshot_step_3.png" alt="Step 3" width="1250px"/>
4. Copy your API keys from your [Paystack Dashboard Settings](https://dashboard.paystack.co/#/settings/developer) and paste them on the module settings
<img src="screenshots/screenshot_step_4.png" alt="Step 4" width="1250px"/>
5. You'll need to create a position by typing `spc_paystack` in the position field on the right side of the page, after creating it, save the module
<img src="screenshots/screenshot_step_5.png" alt="Step 5"/>
6. You'll create the page(article) for the buy sms page. Navigate to your articles page and create a new article. On the article paste the text `{loadposition spc_paystack}`
<img src="screenshots/screenshot_step_6.png" alt="Step 6" width="1250px"/>
7. Time to link the page(article) to your menu bar: 
<img src="screenshots/screenshot_step_7.png" alt="Step 7"/>
***
<img src="screenshots/screenshot_step_8.png" alt="Step 7"/>
8. Click the select button beside the `Menu Item Type` option and select Single article under Articles on the popup 
<img src="screenshots/screenshot_step_9.png" alt="Step 8"/>
<img src="screenshots/screenshot_step_10.png" alt="Step 8"/>
9. Click the select button beside the `Select Article` option and choose the buy sms page(article) created 
<img src="screenshots/screenshot_step_11.png" alt="Step 9"/>
<img src="screenshots/screenshot_step_12.png" alt="Step 9"/>
10. Save Menu and go to website to view the page created. 

## Notes 
I think step 6-10 can be optimized to be easier, my Joomla is limited. Please feel free to share your suggestions. 