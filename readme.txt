=== Multisite Language Switcher ===

Contributors: realloc
Donate link: http://www.greenpeace.org/international/
Tags: multisite, language, switcher, international, localization, multilingual
Requires at least: 3.0
Tested up to:  3.2
Stable tag: 0.4

A simple but powerful plugin that will help you to manage the relations of posts and pages in your multisite-multilingual-installation

== Description ==

A simple but powerful plugin that will help you to manage the relations of posts and pages in your multisite-multilingual-installation

The plugin is using the flag-icons from [FamFamFam](http://famfamfam.com). Thanks to Mark James for his great work.

= Translators =
German (de_DE) - [Dennis Ploetner](http://www.urlaub-und-reisen.net)
Italian (it_IT) - [Antonella Cucinelli](http://www.freely.de)

== Installation ==

- download the plugin
- uncompress it with your preferred unzip programme
- copy the entire directory in your plugin directory of your wordpress blog (/wp-content/plugins)
- activate the plugin in your plugin page
- set some configuration in Options -> Multisite Language Switcher
- set the relations of your pages and posts in Posts -> Edit or Page -> Edit
- you can use the widget and/or the content_filter which displays a hint if a translation is available
- optionally you can use a command like <?php if (function_exists("the_msls")) the_msls(); ?> in your theme files

== Changelog ==

= 0.4 =
* widget added
* hint for available translations as filter of the_content added
* bugfix: $this->options->before_output, $this->options->after_output

= 0.3 =
* new display-option added
* optimization/refactoring

= 0.2 =
* bugfix: Showstopper in MslsMain::__construct ()

= 0.1 =
* first version
