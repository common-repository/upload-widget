=== Upload Widget ===
Contributors: monpelaud
Plugin URI: http://wordpress.org/extend/plugins/upload-widget/
Tags: upload-widget, upload widget, upload, upload files,
Requires at least: 2.8
Tested up to: 2.9
Stable tag: 1.5

The simple way to upload files.

== Description ==

This plugin allows you to upload files in a folder.

You can define:

* the title of the widget,
* the wp-role allowed to upload files,
* the target folder since '/wp-content/',
* the name of files you can upload (using wilcard characters),
* the max size of files.

You can use multi-widgets.

== Installation ==

The plugin is simple to install:

1. Upload `upload-widget.zip` and unzip it into the `/wp-content/plugins/` folder.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Use the widget like any other widget.

== Frequently Asked Questions ==

With the field "Authorized WP Role" you choose which role is allowed to upload files.
Only this role and admin see this widget to upload files.
Upload Widget is multi-widget, you have to create so much of widget that of roles authorized to download.
Set the correct "Authorized WP Role" for each widget.
Every role will see only his widget.
The admin will be the only one to see all the widgets.

== Change Log ==

= v1.5 2010/05/15 =

* Fix bug: 'Visitor' did not see the widget even when "Authorized WP Role:" was set to 'Visitor'.

= v1.4 2010/04/27 =

* Fix bug: In file name the character '_' is no longer replaced by the character '-'.

= v1.3 2010/04/22 =

* Fix bug about 'Upload path': set '/wp-content/' as root 'Upload path' for Wordpress and Wordpress Mu.
* Fix other bugs. If existing "Upload Widgets" bug, please save them one time with this new version.

= v1.2 2010/04/21 =

* Fix bugs with wordpress 2.9

= v1.1 2010/04/19 =

* Fix minors bugs

= v1 2010/04/12 =

* First public release

== Screenshots ==

1. Showing the upload widget screen in the admin area
2. The output of the widget on the site.
