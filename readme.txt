=== Purgely ===
Contributors: tollmanz
Tags: caching, fastly, page cache, cache invalidation
Requires at least: 4.2.0
Tested up to: trunk
Stable tag: 1.0.1
License: MIT
License URI: https://opensource.org/licenses/MIT

A plugin to manage Fastly caching behavior and purging.

== Description ==

Purgely manages caching behavior for WordPress sites using Fastly as an edge caching solution. The plugin exposes useful
APIs to help control how pages on your site are cached, as well as provides sane defaults to make this a plug and play
solution for Fastly cache management.

The plugin handles the following:

* Sets the `Surrogate-Control` header to control the expiration time for pages
* Sets the `stale-while-revalidate` and `stale-if-error` `Cache-Control` directives for managing these special Fastly
behaviors
* Set groups of `Surrogate-Keys` for all pages to provide purging across multiple pages with one command
* Provides invalidation of posts and related posts on save

Each of these items have sane defaults with ways to override them and configure them to your liking.

Additionally, the plugin exposes a WP CLI command to provide more flexible purging options.

== Installation ==

### Manual installation

1. Upload the plugin directory to `/wp-content/plugins/`
1. Activate the plugin through the 'Plugins' menu in WordPress

### WP CLI installation

1. Run `wp plugin install --activate purgely`

### Configuration

Purgely provides a number of constants that can be used to control the behavior of the plugin and Fastly's cache. Users
who wish to change these values should define the constants in `wp-config.php`.

After installing, you should define `PURGELY_FASTLY_KEY` and `PURGELY_FASTLY_SERVICE_ID` in `wp-config.php`. The plugin will work without them; however, you will not be able to purge by surrogate key or purge all without configuring these options. To define them copy the following code to your `wp-config.php` file, update the key to use your key, and the service ID to match the fastly service you're using:

```
define( 'PURGELY_FASTLY_KEY', '39c4820390d8f050giweda50268c7583' );
define( 'PURGELY_FASTLY_SERVICE_ID', 'abcdefghijklmn1234567890' );
```

Configuring other constants is similarly done by defining the constant in `wp-config.php`. All constants are explained
below.

**PURGELY_API_ENDPOINT**

Defines the API endpoint for Fastly. This should not usually need to be changed, but is added in the event that Fastly
decides to use a different API endpoint, or if there is a need for a user to have a special endpoint.

*default: (string) ''*

**PURGELY_ALLOW_PURGE_ALL**

Determines whether or not the plugin can issue a purge all request. Purge all can have dire consequences for a website.
As such, this behavior is disabled by default. If can be turned on by setting this value to `true`.

*default: (bool) false*

**PURGELY_ENABLE_STALE_WHILE_REVALIDATE**

Determines whether or not the plugin sets the `stale-while-revalidate` directive for the `Cache-Control` header. Setting
this value to `false` will turn off the `stale-while-revalidate` behavior. Note that you can manually control this
behavior via the functions exposed in the plugin regardless of this configuration option. This option only controls the
default plugin behavior.

*default: (bool) true*

**PURGELY_STALE_WHILE_REVALIDATE_TTL**

Sets the TTL for the `stale-while-revalidate` directive in seconds. The value instructs Fastly to continue serving stale
content while new content is generated for the duration of the value that is set.

*default: (int) 86400*

**PURGELY_ENABLE_STALE_IF_ERROR**

Determines whether or not the plugin sets the `stale-if-error` directive for the `Cache-Control` header. Setting
this value to `false` will turn off the `stale-if-error` behavior. Note that you can manually control this
behavior via the functions exposed in the plugin regardless of this configuration option. This option only controls the
default plugin behavior.

*default: (bool) true*

**PURGELY_STALE_IF_ERROR_TTL**

Sets the TTL for the `stale-if-error` directive in seconds. The value instructs Fastly to continue serving stale
content while the origin site is serving an error for the duration of the value that is set.

*default: (int) 86400*

**PURGELY_SURROGATE_CONTROL_TTL**

Sets the TTL for the `Surrogate-Control` header in seconds. This value is the default TTL for all pages on your site,
unless it is cached within the app. Changing this value changes the value for the whole site.

*default: (int) 300*

== Changelog ==

= 1.0.1 =
* Update key saniziation to allow all capital letters, not just A-B.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.1 =
Update key saniziation to allow all capital letters, not just A-B.

= 1.0.0 =
Initial release.
