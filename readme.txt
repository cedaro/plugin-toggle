=== Plugin Toggle ===
Contributors: blazersix, bradyvercher, garyj
Tags: plugin, toggle, administration, toolbar
Requires at least: 3.8
Tested up to: 3.8.1
Stable tag: trunk
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Quickly toggle plugin activation status from the toolbar.

== Description ==

For those times when you're too lazy to visit the Plugins screen to toggle a plugin on or off.

It's also helpful when troubleshooting:

> This simple plugin is awesome, especially when it comes to diagnosing problems. One of the tenants of troubleshooting WordPress is to disable every plugin and re-enable them one by one in a process of elimination.
>
> Generally, this is accomplished by having the plugin management page in one browser tab with the front end of the website in another.  When a plugin is deactivated, switch browser tabs and refresh to see if the problem disappears. Depending on the speed of your site and the number of plugins installed, this can be a cumbersome experience.
>
> -- [Jeff Chandler, *WP Tavern*](http://wptavern.com/plugin-toggle-turns-wordpress-admin-bar-into-shortcut-to-enabledisable-plugins)

Or more succinctly:

> That's cute and really useful for developers. -- [@jason_coleman](https://twitter.com/jason_coleman)

= Additional Resources =

* [Write a review](http://wordpress.org/support/view/plugin-reviews/plugin-toggle#postform)
* [Have a question?](http://wordpress.org/support/plugin/plugin-toggle)
* [Contribute on GitHub](https://github.com/bradyvercher/plugin-toggle)

== Installation ==

Install Plugin Toggle like any other plugin. [Check out the codex](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins) if you have any questions.

== Screenshots ==

1. Toolbar dropdown listing plugins to toggle.

== Changelog ==

= 1.1.1 =
* Prevent a fatal error on activation due to stomping a variable passed by reference.

= 1.1.0 =
* Refactored the codebase to improve performance and legibility.

= 1.0 =
* Initial release.
