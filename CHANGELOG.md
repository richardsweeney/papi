# Papi Changelog

## [2.4.0](https://github.com/wp-papi/papi/releases/tag/v2.4.0) - 2015-10-XX

### Added

* Added items setting to relationship so you can add your own data source!
* Added image urls to image sizes array.
* Added `Papi_Attachment_Type`
* Added `papi-after-html` and `papi-before-class`
* Added term property, thanks [@rasmusbe](https://github.com/rasmusbe)!
* Added default property value option. See issue [#95](https://github.com/wp-papi/papi/issues/95).
* Added `max`, `min` and `step` in number property. See issue [#91](https://github.com/wp-papi/papi/issues/91).
* Added `after_class` and `before_class` to property option. So you can add your own css class to after and before div.

### Changed

* Changed so standard type is hidden by default instead of showed. This will effect all standard type filters.

### Fixed

* Fixed issue with link property when slug was same as a property name. See issue [#99](https://github.com/wp-papi/papi/issues/99).
* Fixed issue with add new link when you have a post type on second menu level. See issue [#94](https://github.com/wp-papi/papi/issues/94).
* Fixed issue with relationship property in flexible or repeater. See issue [#93](https://github.com/wp-papi/papi/issues/93).

## [2.3.5](https://github.com/wp-papi/papi/releases/edit/v2.3.5) - 2015-10-07

* Fixed cache issue where Papi din't delete cache on save. See issue [#98](https://github.com/wp-papi/papi/issues/98).
* Fixed issue with `editor` property where `the_content` filter was run on saved.
* Fixed issue with `allow_html` on `string` and `text` properties. See issue [#79](https://github.com/wp-papi/papi/issues/70).

## [2.3.4](https://github.com/wp-papi/papi/releases/tag/v2.3.4) - 2015-09-18

* Fixed get field issue when query string is used.
* Fixed relationship right list so it only contains objects.

## [2.3.3](https://github.com/wp-papi/papi/releases/tag/v2.3.3) - 2015-09-18

* Fixed issue with Papi ajax when multisite use subdirectories See issue [#90](https://github.com/wp-papi/papi/issues/90).

## [2.3.2](https://github.com/wp-papi/papi/releases/tag/v2.3.2) - 2015-09-09

* Fixed column issue with custom post types.

## [2.3.1](https://github.com/wp-papi/papi/releases/tag/v2.3.1) - 2015-09-08

* Fixed issue with `papi_html_tag` when the text is a callable function.

## [2.3.0](https://github.com/wp-papi/papi/releases/tag/v2.3.0) - 2015-09-08

### Added

* Added `before_html` and `after_html` options that you can use to output html before property html and after property html.
* Added `child_types` (old `page_types`).
* Added `standard_type` for show/hide standard page type when having a page type in a parent post.
* Added new filter for show/hide standard page type from filter dropdown. `papi/settings/show_standard_page_type_in_filter_{$post_type}`
* Added new property for adding links with the link popup that exists in the editor.
* Added support for dot templates in page type, so instead of `pages/article.php` can you write `pages.article.php` or `pages.article` without `.php` extension. The old way will continue to work.
* Added import and export layer that can be used when importing data to Papi or exporting.
* Added `papi_get_page_type_key` function that will return the meta key that Papi use to save page type id in `postmeta` table.
* Added `papi_set_page_type_id` function so you can set page type id to a post.
* Added `papi_page_type_exists` function so you can check if a page type file exists.
* Added `papi_option_type_exists` function so you can check if a option type file exists.

### Changed

* Changed `Add new page type` to `Add New Page` (`Page` is the post type name).
* Changed filter title from `Show all page types` to `All types`
* Changed column title from `Page Type` to `Type`

### Fixed

* Fixed so `wp-admin` is not hardcoded on the management page.
* Fixed so `2%F` is replaced with `/` when setting the current menu item.
* Fixed so `post_type` query for post post type works as it should.
* Fixed so all h2 tags are h1, see [Headings in Admin screens change in WordPress 4.3](https://make.wordpress.org/core/2015/07/31/headings-in-admin-screens-change-in-wordpress-4-3/) for more info.