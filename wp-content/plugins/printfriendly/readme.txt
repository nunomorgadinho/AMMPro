=== Print Friendly and PDF Button===
Contributors: printfriendly,joostdevalk
Tags: print, pdf, printer, printing, printable, widget, plugin
Requires at least: 2.8
Tested up to: 3.2.1
Stable tag: 3.0.8

The #1 Print and PDF button for your WordPress site. Printer Friendly pages without coding, css, or print.css. Fast, easy, and professional.

== Description ==

The Print Friendly & PDF button saves paper and ink when printing or creating a PDF. It's fast, easy, and looks great when printed. Add the button now, and your users will see the difference.

**How Print Friendly & PDF Works**

The Print Friendly & PDF button automatically creates printer friendly and PDF versions of your pages without the hassle of having to create a print CSS file. No coding, hacking or programming required. Simply install the Print Friendly & PDF plugin, activate, and choose settings for full customization. It also gives your user the ability to remove images and paragraphs of text, so they really only have to print exactly what they want.

**Cool Features For You:**

* Get a Printer Friendly and PDF button for your users.
* Fully Customizable! Choose your favorite print and PDF button, use a text link, or use your own graphic. 
* Get precision placement with easy to change margins, alignment, and pages for your Print and PDF button.
* On-Page-Lightbox. Have you noticed that other printer friendly buttons open new windows, or tabs? Not with Print Friendly & PDF button. It opens in a Lightbox so you your users stay on the page, and your wp website.
* Professional looking print and PDF buttons and user experience.
* More repeats and new users. Your brand/URL are printed on the page or saved in the PDF so users remember your site and new users can find you.

**Cool Print Features for Users:**
* Optimizes pages for printing and PDF so you save money and the environment.
* You can Print or get a PDF. 
* Edit the page before printing or getting a PDF: remove the images and paragraphs you don't need to save ink!

PrintFriendly and PDF is the #1 print optimization technology, **as featured in [Lifehacker](http://lifehacker.com/5272212/print-friendly-optimizes-web-pages-for-printing "PrintFriendly & PDF in Lifehacker"), [Mashable](http://mashable.com/2009/05/18/print-friendly/ "PrintFriendly & PDF in Mashable") & [makeuseof](http://www.makeuseof.com/dir/printfriendly-save-on-paper-and-ink/#comment-95052)**.

**Give PrintFriendly & PDF a test drive at [PrintFriendly.com](http://www.printfriendly.com "PrintFriendly & PDF")**

== Installation ==

1. Search for PrintFriendly in your WordPress backend and click install, or download the printfriendly.zip file and unzip it.
2. If you downloaded the zip, upload the printfriendly folder into wp-content/plugins folder 
3. Activate the plugin in your WordPress Admin area.
4. Select "Settings" to customize button style and placement.

== Frequently Asked Questions ==

= I set the button to align left / right and it doesn't align right! =
Check whether your theme includes the [required CSS class](http://codex.wordpress.org/CSS#WordPress_Generated_Classes), PrintFriendly uses these to align your button. If that doesn't work for you, uncheck the "Add CSS to Pages" checkbox and style the button yourself.

= The Print Friendly button is loading but it's not doing anything, what's wrong? =
Check the "JavaScript fallback" checkbox in the settings and check again. If the button starts working now, the JavaScript isn't loading correctly. Check your source for a mention of `cdn.printfriendly.com/printfriendly.js`. If it's not there, make sure your theme has the required [wp_footer](http://codex.wordpress.org/Function_Reference/wp_footer) call. 

= Some of the input fields in the admin are disabled! =
If you've disabled the loading of CSS and / or set "Add PrintFriendly To" to Manual, some of the input boxes will be disabled as you've basically disabled that functionality.

= I'm manually adding the button but it's aligning wrong! =
If you're getting unexpected results, you might want to set Horizontal Alignment to "None".

= I still need help! =
If you have any other issues with the plugin or the PrintFriendly widget, please write to support@printfriendly.com.

== Screenshots ==

1. The Print Friendly widget optimizes and formats your pages for print. Users can remove images and text before printing plus get a PDF
2. The Settings Page: choose your print button, text link, or use your own text or graphic.

== Changelog ==

= 3.0.8 =

* Reordered PrintFriendly & PDF buttons. CSS stylesheet option is now checked by default.

= 3.0.7 =

* Added additional images for print button.

= 3.0.6 =

* Fix bug that displays button on category pages when posts and pages is selected.

= 3.0.5 =

* PrintFriendly & PDF button will now display on category pages (archive pages), if "Homepage, Archives, Posts, and Pages" was selected in the Settings for PrintFriendly & PDF (button placement).

= 3.0.4 =

* Align-right and align-center support for themes that remove WordPress core css.

= 3.0.3 =

* Support for bad themes that alter template tags and prevent JavaScript from loading in footer.

= 3.0.2 =

* Fixed JS bug with Google Chrome not submitting and fixed input validation issues.

= 3.0.1 =

* Fixed minor JS bug.

= 3.0 =

* A complete overhaul of the plugin by renowned WordPress plugin developer <a href="http://yoast.com/">Joost de Valk</a>.
* Code Changes:
	* Redone the admin page.
	* Plugin is now fully i18n ready.
	* Plugin now uses the settings API.
	* CSS loading is now optional.
	* JavaScript loading is now optional.
	* Plugin now uses WordPress default align classes for outlining.
* Documentation changes:
	* Added an FAQ.
	* Added Screenshots.

= 2.1.8 =

* The Print Button was showing up on printed, or PDF, pages. Junk! Print or PDF button no longer displayed on printed out page or PDF.

= 2.1.7 =

* Changed print button from `<span>` to `<div>` to support floating the print and PDF button to right.

= 2.1.6 =

* Added rel="nofollow" to the print links to avoid search engines indexing the print pages.
* Changed print button from link `<a>` to `<span>`. Some tracking plugins added target_new/blank to link, therefore breaking print friendly.

= 2.1.5 =

* To avoid conflicts with Google Analytics widgets, changed link structure. No longer uses onclick to call PrintFriendly Javascript. Now the javascript is called in the href.
* Custom image support for hosted solutions
