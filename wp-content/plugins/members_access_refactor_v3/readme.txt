=== members access plugin ===

Contributors: GetShopped.org
Tags: e-commerce, shop, cart, goldcart, members, subscription, member management, capability 
Version: 2.0
Tested up to: 3.0
Requires at least: WP-e-commerce 3.7 and Gold Cart


== Description ==

This plugin allows you to create subscriptions, which can be sold as products, Subscriptions are created to manage the content and restrict users to your pages.
This plugin also allows you to manage and update your users, adjust your subscription lengths and rebill users for there subscription automatically. 

http://getshopped.org/extend/premium-upgrades/premium-upgrades/members-only-module/

== Installation ==

Note: The WP e-Commerce plugin and the Gold Cart plugin must be installed and activated for this to work
Download WP e-commerce: http://getshopped.org

==== First Time Install ====

1. Upload the 'members_access_plugin' to the '/wp-content/plugins/' directory

2. Activate Ômembers_access_pluginÕ through the ÔPluginsÕ menu in WordPress


==== Upgrading from Members only module to the new Members and Capability plugin ====

1. Remove ALL 'members only files' or 'members' from 'gold_cart_files' from the Ô/wp-content/uploads/wpsc/upgrades/Õ directory (if using WP-e-commerce 3.6) or /wp-content/plugins/gold_cart_files_plugin (for WP-e-Commerce 3.7), This must be done first as it causes conflicts with the new files.

2. Note if your upgrading you will need to recreate all your subscriptions again and their products - however for your users that have already bought a subscription please see the section on importing and manually applying their subscriptions - this technique can be used to reapply the capabilities to your users without them even knowing.

3. You can now follow the Installation instructions

==== Downloading a new version ====

This plugin currently does not have automatic plugin notification, however any premium upgrades for GetShopped.org can be downloaded from:

http://getshopped.org/extend/premium-upgrades-files/

You will be required to enter your Session ID (this can be found with your API key on your purchase receipt and is also the invoice ID on your payola receipt - note the invoice ID is different from the transaction ID)

== Support ==

If you have any problems with Members Access or require more information here are your options
	
General help: http://getshopped.org/resources/docs/

Gold Cart Installation: http://getshopped.org/resources/docs/installation/members-only-module/

Support Forum: http://www.getshopped.org/forums/

Premium Support Forum: http://getshopped.org/resources/premium-support/

==Getting started with this plugin==

==== Important things to note / required settings ====

For this module to work, you must configure some WordPress settings. Users must be logged into your site to purchase a membership or subscription, otherwise we cannot associate their subscription with a WordPress user account.

To do this, click on the Settings > General link in the left sidebar, and check the Òanyone can registerÓ checkbox next to the ÒMembershipÓ option
On the Store > Settings > Checkout page, turn on the Ó Users must register before checking outÓ option.

Note: If you plan on using authorize.net and the members only module, make sure that you sign up for authorize.net Abr. The authorize.net Payment Gateway utilizes the Abr Òauthorize.netÓ recurring billing option. It is not a WP e-Commerce option, WP e-Commerce uses Abr by default. However we recommend using PayPal for your recurring billing.

Users with the WP Administrator role do not require any capabilities they can view all pages by default.
If using "remove all capabilities" the user who you are removing them from will have their WP capability set back to Subscriber by default, if this user was an editor or had any other WP role then this will need to be manually changed back in the WP users Menu. Remove all does not Delete the users WordPress account it simply removes all the additional capabilities. 

Included with this plugin is also a new merchant.class.php please copy this over the top of the old one located in this directory: wp-content/plugins/wp-e-commerce/wpsc-includes/merchant.class.php
You will need to use this modified class to support the recurring billing - if your using wp-e-commerce 3.8 then no need to worry the changes have already been included with your version.


==== Setting up the subscriptions / capabilities ====
 
1. This plugin comes with three default capabilities (memberships) Basic Membership Access, Premium Membership Access and Premium Forums Access, However you can add as many different capabilities as you like. (recommended)

To do this go to store >> members >> Manage Capabilities from there you can add edit or delete as many capabilities as you like, it is however not recommended to delete capabilities that users currently have assigned to them as these will be removed from their profile

2. If you want to sell your subscriptions you can set them up as products, this is the same as adding a new product to your shop only you will notice that from now on when you add new products there are more options at the bottom, Select the relevant options that you want for this subscription,

3. You can now set up pages / posts and select which subscription / subscriptions and able to view this post, like creating the subscription product all the options are down at the bottom of the page.

==== Setting up the members ====

All the user management is stored under Shop >> members

When members buy your subscription product their subscription is automatically created based on the settings you created for the product.
You can manually set up users (this is useful if you don't want to charge them but still want to grant them access to pages) to do this they must already be a WP user of your site.

==== Import ====

This plugin has a nice little import feature for removing capabilities and adding multiple capabilities to your WP users. The import will get ALL WordPress users of your site, if a capability is applied then these users will now appear in your members list.

==== Testing ====

The best way to test if this works is to log out and try and view the subscription only page / blog post you have just created, you should be denied access and promoted to sign in.

NOTE: When testing if the purchasable subscription gets applied to your profile you must ensure that the payment in the sales log has been "accepted" The user will not automatically be populated with the purchased capability until payment has been accepted. 