=====Update Image Tag Alt Attribute====
Contributors: mauimarketing,jobnavajo
Donate link: none
Tags: alt,seo,image,meta,attribute,marketing
Requires PHP: 7.0
Requires at least: 3.0.1
Tested up to: 5.9
Stable tag: 2.4.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
== Description ==

This is an alt text modification plugin. It will take all empty alt tags and create a tag based upon the page they are attached to. This is best used when you have properly named files or have loaded images into the library in bulk.

<h4>Plugin actions in wordpress</h4>

This plugin searches for all images within the library which are missing alt-text attributes. The alt text will be chosen from page or post title elements if the image is attached to a page or post. Otherwise, file name will be used as the alt-text attribute of unattached images in media library.

The purpose is to help describe those images as best as possible for interpretation by search bots. Moreover, after updating, the plugin creates a file which lists all the images that was found to be missing alt-text attribute and then updated with alt-text.  This will allow you to then tweak where necessary.

== Technical support ==
Dear users, our plugins are available for free download. If you have any questions or recommendations regarding the functionality of our plugins (existing options, new options, current issues), please feel free to [contact us](https://support.mauimarketing.com). Please note that we accept requests in English only. All messages in another languages won't be accepted.

## Privacy Policy 
Update Image Tag Alt Attribute uses [Appsero](https://appsero.com) SDK to collect some telemetry data upon user's confirmation. This helps us to troubleshoot problems faster & make product improvements.

Appsero SDK **does not gather any data by default.** The SDK only starts gathering basic telemetry data **when a user allows it via the admin notice**. We collect the data to ensure a great user experience for all our users. 

Integrating Appsero SDK **DOES NOT IMMEDIATELY** start gathering data, **without confirmation from users in any case.**

Learn more about how [Appsero collects and uses this data](https://appsero.com/privacy-policy/).

== Installation ==
1. Upload the zip file "alt-attribute.zip" to the Wordpress plugin.
2. Activate the plugin through the "Plugins" menu in Wordpress.


== Screenshots ==
1. The administration panel : activate update-alt-attribute plugin
2. Image checkout: sample image without alt-text in media
3. Update Alt for the website : notice before run update alt
4. Result after update : list of picture were found and added alt-text
5. Image checkout : alt-text field in image detail updated

== Changelog ==
=2.4.5=
* Instant alt replacement function update
=2.4.4=
* Add word remove feature in settings so it will remove all words from image in input box.
=2.4.1=
* Remove frontend js
=2.4.0=
* Fixed
	select not show nummber
	Undefined post_type
	Warning preg_match in php 7.0+
	text image replace wrong 
	Editor Alt Media  : do not load the entire image
	Editor Alt Media  : not load .SVG
* New
	Editor Alt Media  :add sort by title
	Improve the performance
	Improved Cron performance
=2.3.5=
* Fixed order by
=2.3.4=
* Fixed search
=2.3.3=
* Added link
=2.3.2=
* Added Log file
=2.3.1=
* Added Cron
=2.3.0=
* Added "Used" column
=2.2.0=
* Added wxh for images
=2.0.7=
* Updated some text
=2.0.6=
* Addded bulk edit 
=2.0.5=
* Split to media and content
* Priority change alt by file name
=1.0.1=
Initial version