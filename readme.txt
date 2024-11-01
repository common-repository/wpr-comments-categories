=== Comments Categories ===
Stable tag: trunk
Contributors: Ovidiu Irodiu from WPRiders
Donate link: https://wpriders.com
Tags: comments, categories
Requires at least: 5.0
Tested up to: 5.2.2
Requires PHP: 5.6
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Add categories for the post comments on your blog

== Description ==

This plugin will help you categorize the comments on your blog posts.

If you are a food blogger, this plugin is very handy to sort the comments on your recipes, like, for example in "I have made this" and "I have not cooked this".

You can configure the list of categories from the Settings->Comments Categories page. 
Besides the categories' list, you can also customize basic look and feel of the comment filtering buttons.

In case the plugin is not working with your theme, please check the FAQ section for more info.

If you need support with this plugin or want to customize it, please submit your request here [https://wpriders.com/contact-us](https://wpriders.com/contact-us)
We are not offering free support on wordpress.org

== Installation ==

= Minimum Requirements =

* PHP version 7.1 or greater (PHP 7.2 or greater is recommended)
* MySQL version 5.7 or greater

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. To do an automatic install of Comments Categories, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type “Comments Categories” and click Search Plugins. Once you’ve found the plugin you can view details about it such as the point release, rating and description. Most importantly of course, you can install it by simply clicking “Install Now”.

= Manual installation =

The manual installation method involves downloading our plugin and uploading it to your webserver via your favourite FTP application OR from Admin Area -> Plugins -> Add New and choose 'Upload Plugin'. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

== Screenshots ==

1. Here's how the plugin works in practice.
2. A short movie depicting how this plugin works.
3. Back-end screen for configuring the list of comments
4. Back-end screens for configuring the look and feel of the buttons

== Frequently Asked Questions ==

= The plugin is not working on my theme and the comment filters do not appear =

This means your theme is using a custom structure for the comments list.
In this case, you need to go to the Settings->Comments Categories page, on the Styles tab and fill in the "Comments List CSS Class/ID" box, then press Save. 
TODO: please insert this image in the answer, if possible (image, not the link to the image) https://d.pr/i/58n3c4

Depending on your theme, you should have in that box:
- for Astra Theme theme use: .ast-comment-list
- for Enfold Theme use: .commentlist

Important: please don't forget the dot as that's a CSS class name.

== Upgrade Notice ==
= 1.0.0 =
Initial Release! Yey!

== Changelog ==
= 1.0.0 =
On date 2019-04-05:
* First version pushed to Wordpress