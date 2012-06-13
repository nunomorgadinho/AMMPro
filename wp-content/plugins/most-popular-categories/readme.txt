=== Most Popular Categories ===
Contributors: blueinstyle
Tags: widget, categories, category list, popular categories, top categories
Requires at least: 2.8
Tested up to: 3.1
Stable tag: 1.1.1

Display your most popular categories in a widget

== Description ==

This is just a small WordPress plugin that lists the most popular categories by post count in a widget. Includes several options.

Useful for blogs with many categories and just want to list the most popular.

For Themes that do not support Widgets, you can now put this code anywhere in your template:
` <?php if(function_exists('jme_category_list')) { jme_category_list(); } ?> `

Support and Feature requests are on my forums at http://justmyecho.com/forums/

== Installation ==

1. Download and extract most-popular-categories.zip file.
2. Upload the folder containing the Plugin files to your WordPress Plugins folder (usually ../wp-content/plugins/ folder).
3. Activate the Plugin via the 'Plugins' menu in WordPress.
4. Go to Widgets section to add the widget to sidebar.

== Screenshots ==

1. The widget

== Changelog ==

= 1.1.1 =
* Fixed bug with incorrect category permalinks
= 1.1 =
* Added function for themes that don't support widgets
* Option to exclude categories with 0 posts
= 1.0 =
* Initial release
