=== Easy Pie Maintenance Mode ===
Contributors: bobriley
Donate link: http://easypiewp.com
Tags: maintenance, admin, administration, construction, under construction, maintenance mode, offline, unavailable, launch
Requires at least: 3.5
Tested up to: 3.7
Stable tag: 0.5.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Let website visitors know your site is temporarily down while you're working on it.

== Description ==
Need to let your visitors know your site is temporarily down or is under construction? The Easy Pie maintenance mode plugin makes it easy. 

### Features
* **Simple.** No confusing options or complex setup
* **Mini themes.** Includes four professionally-designed, responsive mini-themes
* **Pre-styled text.** Title, header, headline and message text gets styled without requiring HTML or CSS
* **Add your own logo.** Easily add your own logo using the WordPress Media Library.
* **Expandable.** Modular architecture allows for easy addition of future mini-themes.

Thanks to the developers of [bxSlider](http://bxslider.com) for their cool image viewer.
== Installation ==

1. Click Plugins/Add New from the WordPress admin panel
1. Search for "easy pie maintenance mode" and install

-or-

1. Download the .zip package
1. Unzip into the subdirectory "easy-pie-maintenance-mode" within your local WordPress plugins directory
1. Refresh plugin page and activate plugin
1. Configure plugin using *settings* link under plugin name or by going to Settings/Maintenance mode

== Frequently Asked Questions ==

= What happens if a search engine hits my site while it's in maintenance mode? =
The plugin returns a '503' status with 'retry later' HTTP header when in maintenance mode. This lets search engines know that your site is temporarily down and to come back 24 hours later.

= Can I create my own mini-theme? =
In version 0.5, there are ways of doing it, but it's not easy for a beginner. I recommend waiting till the next release. If by chance, you have managed to hack a theme, please be aware that since the themes for v0.5 reside in the plugins directory that all themes will be wiped and reinstalled during the next upgrade so make sure you create a backup of your hacked theme before performing the next upgrade. The next version will not have this problem - user themes will be retained between updates.

== Screenshots ==
 
1. Plugin configuration
2. Site in maintenance mode when using the 'temporarily closed' mini-theme.

== Changelog ==

= 0.5.1 =
* Fix for PHP 5.2.x

= 0.5 =
* Initial release

== Upgrade Notice ==

= 0.5.1 =
* Small fix for PHP 5.2.x. If you aren't running PHP 5.2.x you don't need this although it won't hurt anything if you update anyway.

= 0.5 =
* Initial release
