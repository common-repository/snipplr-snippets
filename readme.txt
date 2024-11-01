=== Snipplr Snippets ===
Contributors: Matt Hobbs, Tyler Hall & Jan Stepien
Tags: snippet,code,snipplr,tool,syntax,highlight
Requires at least: 2.8
Tested up to: 3.0.0
Stable tag: 1.0.1

Plugin that ties in with the Snipplr code snippet API allowing you to embed snippets directly into your blog posts or add a sidebar of your latest snippets. 

== Description ==

An adaption and update of Tyler Halls original Snipplr Wordpress plugin.

Allows you to embed snippets directly into your Wordpress posts using the snippet ID.

Features:

* Code highlighting using GeSHi.
* Sidebar widget to show your latest code snippets.
* Easily embed snippets into the page using [snippet id=##]
* Customisable layout using CSS. Can disable default plug-in CSS from the admin settings
* Uninstall option in the plugin settings page.

* Sign up for a Snipplr account here: http://snipplr.com/
* [Support](http://nooshu.com/wordpress-plug-in-snipplr-snippets/)

**TODO:**
Double check GeSHi doesn't cause issues with other code colourer plugins.

== Installation ==
1. Download the zip file and extract. Upload all files to the '/wp-content/plugins/'. Make sure you upload in the correct.
folder structure e.g. /wp-content/plugins/snipplr-snippets/.
2. Activate the Snipplr Snippets plug-in through the 'Plugins' menu in WordPress.
3. The plug-in configeration panel is under settings -> Snipplr Snippets.
4. Add the sidebar widget via the appearance menu.
5. Embed a snippet into a post using [snippet id=##].
 
== Screenshots ==
1. Admin area screenshot
2. Embedded code snippet
3. Sidebar widget

== Frequently Asked Questions ==
= No Snippets are populated in the sidebar widget =
Double check your API key on the settings page. You can get an API key from the settings menu of your Snipplr account.

= I get a warning from CodeColourer agout GeSHi =
This is due to both plugins using the same library for syntax highlighting. I've tested on my own site and have had no issues as of yet, but please let me know if you do and I'll look into a fix.

== Changelog ==
= 1.0.1 =
* Initital version of the Snipplr Snippets plug-in released.
* Update / rewrite of the plug-in from 2006.
* Added the latest version of GeSHi.
* Added a sidebar widget.


== Upgrade Notice ==
= 1.0.1 =
* Initital version of the Snipplr Snippets plug-in released.