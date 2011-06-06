=== Plugin Varnish Purges ===
Contributors: Olivier Dupuis
Tags: cache, caching, performance, varnish, purge, speed, plugins support
Requires at least: 2.9.2
Tested up to: 3.1.1
Stable tag: 0.95
Fork of wp-varnish : http://github.com/pkhamre/wp-varnish


== Description ==
Based on Varsnish plugins by pkhamre, wfelipe, eitch, but heavily forked for
extensibility.


WordPress Varnish Purges is a plugin that purges new and edited content, it's
intent is to be usable for a multi-site installation, to support all necessary
purge orders for wordpress and to be expandable via extensions.

This plugin purges your varnish cache it's intent is do do it for any action
taken. This plugin supports what is called extensions that can be added to add
special purges for specific wordpress plugins.

This plugins is mainly aimed at multi-sites installations (but can be used on a mono site installation) and requires some knowledge about varnish and vcl files.
 
== Installation ==
 

This section describes how to install the plugin and get it working.

1. Upload `wp-varnish/` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. a wordpress.vcl is provided, it contains directives for wordpresse's core. Modify it according to your platform/needs.
4. few extensions have additionnal vcl needs (such as wp-touch for useragent normalization), those files are located in the plugins directory. Additionnal informations are provided with those files.

== Frequently Asked Questions ==

= When will be V1.0 be relased =
This plugin will be in fully tested V1.0 by end of may 2011.

= Does this just work? =

Yes. Exdtensivly tested in multi-site with sub-domain install. But has to be
tested in non sub-domain (site.com/blogname)

= But how should my varnish configuration file look like? =

I have provided a simple VCL that can be used as a reference.

= Does it work for Multi-Site (or WPMU)? =

Yes. Activating the plugin site-wide will provide the functionality to all
blogs. Edit wp-config.php and
include these lines just before "That's all, stop editing!" message:

> global $varnish_servers;
> $varnish_servers = array('192.168.0.1:80','192.168.0.2:80');
> define('VARNISH_SHOWCFG',1);

The varnish servers array will configure multiple servers for sending the
purges. If VARNISH_SHOWCFG is defined, configuration will be shown to all
users who access the plugin configuration page (but they can't edit it).

== Planned in a really near future ==
  - update wordpress VCL (done commit
    d75458e688caa201cf353c54410b42d000f63140)
  - extension check for a warning when unsupported plugins or widget is
    installed.
  - support for nextgen gallery
  - wiki, especially for varnish configuration.

== Screenshots ==
(@TODO : add some)

== Upgrade Notice ==
* 0.93: configuration has become network wide, reconfigure it after upgrade via admin menu of the site
* 0.92 : Regression after renaming : default parameters where incorrectly set.
* if update from 0.92, just a normal update
* 0.91 : deactivate v0.9 on network, then uninstall the plugins. Then install 0.91 from scratch. This is due to intense renaming to avoid conflicts with original WP-Varnish plugin.
* 0.9: initial release, deactivate WP-Varnish

== Donate ==

== Changelog ==

= 0.95 = 
* Suppress plugins wp-touch because its not needed (number of purges divided
* by two with wp-touch, silly me)

= 0.94 =
* Add purge order when post status is modified.
* test existence of main object to avoid fatal error.

= 0.93 =
* Varnish configuration is now sitewide.
* refactor a bit the admin page (only shown to network admin)
* by default purge page and comment navigation
* Tidy up a bit, unused methods in main plugin object. this object has no method for purging anymore. Call to purge all blog in admin section didn t work.
* Corrected a bug in js that prevented servers to be configured via admin section  thanks ovidiubica.

= 0.92 = 
* correct regression after renaming : default parameters where incorrectly set

= 0.91 =
* rename classes, files and options to avoid conflicts with WP-Varnish,
* plugins name is now varnish-purger

= 0.9 =
* refactorisation of core for extensions
* added hook for tricky purges like wp-touch.
* only super admin has access to admin menu
* support for :
  - ajax-calendar 2.5.1 : http://urbangiraffe.com/plugins/ajax-calendar/
  - wptouch 1.9.25 : http://wordpress.org/extend/plugins/wptouch/
  - wp-socializer 1.0 :
    http://www.aakashweb.com/wordpress-plugins/wp-socializer/
  - wp-cumulus 1.23 :
    http://www.roytanck.com/2008/03/06/wordpress-plugin-wp-cumulus-flash-based-tag-cloud/
* All main wordpress action are supported :
  - post edition (purge post, home, archives, categories, tags, all pages if
    widget recent posts)
  - comment edition (purge post page, all pages if widget recent comments)
  - theme option changes
  - theme change
  - add/remove sidebar widget
  - background change
  - header change

All changelog before the fork have been removed for clarity's sake.
