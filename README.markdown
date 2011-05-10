WordPress VarnishMia
====================

Based on Varsnish plugins by pkhamre, wfelipe, eitch, but heavily forked for extensibility.

* Contributor: Olivier Dupuis
* Tags: cache, caching, performance, varnish, purge, speed, plugins support
* Requires at least: 2.9.2
* Tested up to: 3.1.1
* Stable tag: 0.92
* Fork of wp-varnish : http://github.com/pkhamre/wp-varnish

WordPress VarnishMia is a plugin that purges new and edited content, it's intent is to be usable for a multi-site installation, to support all necessary purge orders for wordpress and to be expandable via extensions.

Description
-----------

This plugin purges your varnish cache it's intent is do do it for any action taken. This plugin supports what is called extensions that can be added to add special purges for specific wordpress plugins
 
Installation (@TODO add details about varnish vcl)
------------

This section describes how to install the plugin and get it working.

1. Upload `wp-varnish/` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. use the vcl provided to configure your varnish instance

Frequently Asked Questions 
--------------------------

### Does this just work?

Yes.

### But how should my varnish configuration file look like?

I have provided a simple VCL that can be used as a reference.

### Does it work for Multi-Site (or WPMU)?

Yes. Activating the plugin site-wide will provide the functionality to all
blogs. Edit wp-config.php and
include these lines just before "That's all, stop editing!" message:

> global $varnish_servers;
> $varnish_servers = array('192.168.0.1:80','192.168.0.2:80');
> define('VARNISH_SHOWCFG',1);

The varnish servers array will configure multiple servers for sending the
purges. If VARNISH_SHOWCFG is defined, configuration will be shown to all
users who access the plugin configuration page (but they can't edit it).

Planned in a really near future :
---------------------------------
  - update wordpress VCL (done commit d75458e688caa201cf353c54410b42d000f63140)
  - extension check for a warning when unsupported plugins or widget is installed.
  - support for nextgen gallery (done commit ee1328b078520cdde55eca84ca05bcec5befc48e)
  - wiki, especially for varnish configuration.

Upgrade Notice
--------------
* 0.92 : Regression after renaming : default parameters where incorrectly set. from 0.91 update normally.
* 0.91 : deactivate v0.9 on network, then uninstall the plugins. Then install 0.91 from scratch. This is due to intense renaming to avoid conflicts with original WP-Varnish plugin.
* 0.9: initial release, deactivate WP-Varnish

Changelog
---------
### 0.91 
* rename classes, files and options to avoid conflicts with WP-Varnish,
* plugins name is now varnish-purger


### 0.9
* refactorisation of core for extensions
* added hook for tricky purges like wp-touch.
* only super admin has access to admin menu
* support for :
  - ajax-calendar 2.5.1 : http://urbangiraffe.com/plugins/ajax-calendar/
  - wptouch 1.9.25 : http://wordpress.org/extend/plugins/wptouch/
  - wp-socializer 1.0 : http://www.aakashweb.com/wordpress-plugins/wp-socializer/
  - wp-cumulus 1.23 : http://www.roytanck.com/2008/03/06/wordpress-plugin-wp-cumulus-flash-based-tag-cloud/
* All main wordpress action are supported :
  - post edition (purge post, home, archives, categories, tags, all pages if widget recent posts)
  - comment edition (purge post page, all pages if widget recent comments)
  - theme option changes
  - theme change
  - add/remove sidebar widget
  - background change
  - header change

All changelog before the fork have been removed for clarity's sake.
