=== WP Custom Fields Search ===
Contributors: don@don-benjamin.co.uk
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=don@don-benjamin.co.uk&item_name=Custom Search Donation&currency_code=GBP
Tags: search,custom fields,widget,sidebar
Requires at least: 2.5
Tested up to: 2.8.4
Stable tag: 0.3.16

This plugin allows multiple form inputs to be configured to search different aspects of a post including custom fields.


== Description ==

This is a search plugin for wordpress, designed to filter posts in a more structured way than the default wordpress search. Specifically it allows multiple form inputs to be configured to search different aspects of a post, i.e. one term could search the post content, one the post title and one could search a custom field associated with the post.

The blog admin is able to build a customised search form. It allows you to search by the standard post information and by custom fields and to choose what HTML form elements should be presented for each search field.

I originally developed this plugin for a client project. Basically the problem they had was that they were storing real estate data as wordpress posts with associated custom fields and they wanted to search for entries based on price, location and features which was not possible with the default wordpress search.

The plugin is now at point where I think it could be useful to other people although I"m aware that there are a lot of improvements that could be made. Please [let me know](http://www.don-benjamin.co.uk/wordpress/contact) if you have any suggestions or complaints and I will do my best to get them resolved.

== Installation ==
I will try to put more complete documentation together at some point but for now this is a quick start guide to building a search form.i

1. Install the plugin.  Upload the directory wp-content-search to your /wp-content/plugins/ directory.
2. Activate it through the plugins menu in Wordpress

Method 1: Add the search as a sidebar widget

1. Add the widget to the sidebar on which you want it to appear. Go to the appearance > widgets section of the admin page, in the list on the left find the widget "Custom Fields Search" and click the add button next to it. It should appear in the list on the right. Click save changes
2. You should now have a basic search form on the front of your site, to customise this further read on to see how to reconfigure your fields.

Method 2: Add the search code directly into your template

1. Go to the custom fields config page in your admin section, Settings > WP Custom Fields Search
2. Copy the example PHP code from this page into your template file.
3. You should now have a basic search form on the front of your site, to customise this further follow the instructions below for setting up fields.

Method 3: Add a tag into your posts/pages

1. Go to the custom fields config page in your admin section, Settings > WP Custom Fields Search
2. Copy the example tag for posts from this page into the content of the page or post in which you want the form to appear.
3. You should now have a basic search form in this page/post, to customise this further follow the instructions below for setting up fields.

== Freqently Asked Questions ==

= How do choose what options are shown in Drop Down lists or Radio Buttons =

Each of these widgets has a special input field called "Drop Down Options" and "Radio Button Options" respectively.  If you put a comma separated list of values in here these will be shown as the results.

If you want to show a label that is different to the search term you can separate each item with a colon ':' so if I have a category called Cat1 which has my favourite posts I could use Cat1:Favourite Posts to show one value while searching another.  This is particular useful for the any option so something like the following ":Any,1:Option 1,2,3" would produce a dropdown where the first element has the label any but has no value so will not actually restrict the search.

= Can the plugin search pages instead of posts? =

Yes, it can, although the interface for this is a bit awkard at the moment.

You will need to create a hidden field, which searches on the post_type data type, then give this field the value "Page".  This field will not show up in the interface but will affect the search results giving you page search.

== Screenshots ==
1. Front end search box
2. Back end admin screen showing field config

== Setting up the fields ==

Each field has a number of settings which control the way the input appears to the user and the way the search is performed. Most of the settings are hidden when you first go to edit the widget and you will need to click the show/hide config button to get access to many of the parameters.

**Label** sets the label displayed next to the field, you can set this to whatever you want

**Data Type** controls what type of data is being searched (i.e. what database table is being used). The standard options are "Post Field" for data such as post title and post content, from the standard wordpress post data, "Custom Field" for data from the custom fields, and "Category" to search on the categories a post is in.

**Data Field** makes a more specific selection from the data available. The drop down list should give some sensible options, or if you know the name of the database field you want to query you can type this in manually.

**Widget** controls what type of HTML input is created for the front end of the site.  It should be fairly obvious what these do.  

Some widgets will require extra options in the **Widget Options** field. At the moment this is just the drop down and radio button widgets which allow you to specify a list of values for the user to choose from. If you leave the options blank then they will be automatically populated with a list of all values currently in the database, this can be useful for fields like categories and tags, but is less useful for fields like title which would just generate an entry for every post in the blog. If you want to specify the values manually you can specify this as a comma separated list of values, "a,b,c" for three options with values "a", "b" and "c" respectively, or if you want to give "friendly" labels to the user you can separate the value from the label using a colon as follows "a:Group A,b:Group B,c:Group C".

Hidden constant fields allow you to add a fixed search parameter to the form which the user cannot edit.  The extra options allow you to specify what the value of this fixed parameter is.

The **Compare** field controls the way that the user input is compared to the data in the database. I would expect the most commonly used of these to be "equals" which requires an exact match between the user data and the database field (useful for category searches) and the "Words in" or "Phrase In" types which will search to see if the user input is a part of the data, rather than a full match, this is useful for things like searching the text of a post. The difference between "words" and "phrase" is that "words" splits the input into a series of words and searches for these individually whereas "Phrase" searches for all the words in sequence.

The "Less Than" and "More Than" comparisons ensure that the data is less than or more than the user input respectively. This is probably only relevant for numeric input but it can be used for strings as well and will search alphabetically. For numeric input it can help to also tick the "Numeric" checkbox as sometimes the database will be set up to compare numbers alphabetically (so that 10 is less than 2).

The "Range" comparison is a little more complicated and requires the user input to be of the form A-B to return results that are between A and B. This is primarily intended to be used for Drop-downs and radio buttons where the values can be set by the site administrator. If you specify the values for the dropdown like the following: "-10:Less Than 10,10-20:Between 10 and 20,20-:More than 20", then the user will be presented with the options "Less Than 10", "Betwen 10 and 20" and "More than 20" but the search will be done with the values "-10","10-20" and "20-". Again the numeric checkbox should be used for numeric data.

I hope that helps, please let me know if you have any trouble (or joy) with the plugin or if any of my instructions don"t make sense.

== Using Presets == 

Presets are a new feature in version 0.3, they allow search forms to be configured separately from the sidebar widgets, the configured form doesn"t directly appear on the site but can be included in two ways.

Firstly, by copying and pasting the php code from the preset config page into a template file you can place a search form anywhere you want in your template.

Secondly, by using a preset as the basis for another search form.  When setting up a search form you have the option of selecting one of the presets from a dropdown list.  Selecting one will include all of the fields from this preset in your form.

Presets are configured in exactly the same way as the sidebar widget, you can find the presets in the "Settings " WP Custom Fields Search" section of your admin site.

== Extending Custom Search == 

For developers, I"ve tried to build this in a modular way to allow extensions to be added, I will try to document this at some point in the future but if you can"t wait then the best places to get started would be to look at the filter "custom_search_get_classes" in custom-search/custom-search.php and the [Great Real Estate](http://www.rogertheriault.com/agents/plugins/great-real-estate-plugin/) bridging code in custom-search/bridges/greatrealestate.php.

This plugin is at quite an early stage in development though so it is likely that future versions may not be compatible with the same extension interface and it is likely that any extensions will need altering when new versions are released..

