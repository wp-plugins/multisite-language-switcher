=== Multisite Language Switcher ===

Contributors: realloc
Donate link: http://www.greenpeace.org/international/
Tags: multisite, language, switcher, international, localization, multilingual
Requires at least: 3.0
Tested up to:  3.2.1
Stable tag: 0.6.1

A simple but powerful plugin that will help you to manage the relations of posts, pages, categories and tags in your multisite-multilingual-installation.

== Description ==

A simple but powerful plugin that will help you to manage the relations of posts, pages, categories and tags in your multisite-multilingual-installation

The plugin is using the flag-icons from [FamFamFam](http://famfamfam.com). Thanks to Mark James for his great work.

= Translators =
* German (de_DE) - [Dennis Ploetner](http://www.urlaub-und-reisen.net/)
* Italian (it_IT) - [Antonella Cucinelli](http://www.freely.de/it/)
* Dutch (nl_NL) - [Alexandra Kleijn](http://www.buurtaal.de/)

If you have created your own language pack, or have an update of an existing one, you can [send me](mailto:re@lloc.de) your gettext PO and MO so that I can bundle it into the Multisite Language Switcher. You can download the latest POT file [from here](http://plugins.svn.wordpress.org/multisite-language-switcher/trunk/languages/default.pot).

== Installation ==

* download the plugin
* uncompress it with your preferred unzip programme
* copy the entire directory in your plugin directory of your wordpress blog (/wp-content/plugins)
* activate the plugin in your plugin page
* set some configuration in Options -> Multisite Language Switcher
* set the relations of your pages and posts in Posts -> Edit or Page -> Edit
* set the relations of your categories and tags in Posts -> Categories or Post -> Tags
* now you can use the widget and/or the content_filter which displays a hint if a translation is available
* optionally you can use a line like _<?php if (function_exists("the_msls")) the_msls(); ?>_ directly in your theme-files

== Changelog ==

= 0.6.1 =
* bugfix: notice when Msls::$txt was requested for output of the link-title

= 0.6 =
* new: relations between categories and tags in different languages

= 0.5 =
* language file for nl_NL added

= 0.4 =
* widget added
* hint for available translations as filter of the_content added
* bugfix: $this->options->before_output, $this->options->after_output

= 0.3 =
* new display-option added
* optimization/refactoring

= 0.2 =
* bugfix: showstopper in MslsMain::__construct ()

= 0.1 =
* first version
