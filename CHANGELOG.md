# Changelog

## v6.0.6 - 2026-10-05
### Security
* Generic-content plugin: remove hardcoded global width override
* Page-editor: internationalize the React UI (was hardcoded French)
### Added
* **plugin-config:** full-React config tabs for melis-front plugins
### Changed
* I18n: fix wrong-language values and mismatched keys in interface translations

## v6.0.5 - 2026-09-25
### Security
* **security:** load CSRF emitter in page edition iframe
### Fixed
* **security:** version-stamp melisCsrf.js in the page edition iframe

## v6.0.4 - 2026-09-23
### Security
* **security:** parameterised SQL for date filters, ORDER BY and hand-quoted values (audit item 11.0)

## v6.0.3 - 2026-08-20
### Fixed
* **front-gdpr:** show the banner "agree" label translated on all routes
### Docs
* **melisai:** React back-office AI documentation for MelisFront

## v6.0.2 - 2026-08-10
### Dependencies & build
* **composer:** update docs/homepage links, swap zf2 keyword for laminas, bump php constraint to ^8.3|^8.5

## v6.0.1 - 2026-08-10
### Dependencies & build
* **deps:** allow laminas-serializer ^2.17 instead of pinning 2.17

## v6.0.0 - 2026-08-10
### Security
* **security:** add SECURITY.md (private vulnerability reporting policy)
* Fix audit findings
### Added
* **melis-front:** add developer code examples to the technical reference
* Add MelisAI module documentation for AI consumption
### Fixed
* **front:** do not gate getPluginAction against the front-office render path
* **security:** harden legacy file/dir creation & output escaping
* **melis-front:** restore prominent §0 'the trio' section at the top
### Dependencies & build
* **composer:** bump melis-core constraint to ^6.0
* **sync:** align melis-react branch with parent deliverable
### Docs
* **melisfront:** detail the standard content blocks (plugins) with screenshots
* **melisfront:** rewrite as two-part doc (functional guide + technical reference)

## v5.3.9 - 2026-06-02
### Security
* Fixed xss problem on uri

## v5.3.8 - 2026-05-13
### Added
* Added handling in directories inside public/minitemplatetinymce, wont get content for a folder, only for files
* Added css center content vertically
### Changed
* Site lang homepage
* Debug update
* Lang status
* Sitemap
* Dnd row update
* Grid col padding reset to zero
* Layouts updates

## v5.3.4 - 2025-07-09
### Added
* Added option to disable dynamic dnd
* Added button title attribute translations
* Added divi style column layouts
* Add dnd
* Added dupkicate template
* Added dynamic drag drop init
* Add arrow button at the bottom
### Fixed
* Fix 8505
* Fixed new zone inverted
* Fixed conflict
* Fixed plugin saving
### Changed
* Tinymce issue
* Updated icon
* Check value if empty
* Update disabling dynamic dnd
* Update dnd view file folder
* Check issue 8466
* Edit on layout buttons title attribute removed
* Edit plugin sub tools buttons
* Changes on .dnd-layout-buttons and added the custom column layouts
* Remove title attribute on dragdropzone container
* Update on ui
* Update duplicate
* Changes on melis-container,  melis-container-buttons and additional layout templates
* Update dnd copy
* Update copy dnd
* Update column rendering
* Update plugin saving
* Update saving plugins
* Buttons displayed within dragdropzone
* Displayed none on buttons for the meantime
* Revert to original position of .dnd-plus-button
* Dnd layout
* Update plugins xml rendering
* Edit on dragdropzone
* Update on dragdropzone buttons and container
* Update dnd to make dynamic
* Update plugin.melisGdprBanner.init.js

## v5.3.3 - 2024-12-09
### Added
* Added parameter to check if its from attribute

## v5.3.2 - 2024-12-06
### Changed
* Update site translations to popup translation key

## v5.3.1 - 2024-10-23
* Maintenance release.

## v5.3.0 - 2024-09-25
### Changed
* Jquery migration related

## v5.2.0 - 2024-06-06
### Dependencies & build
* Update dependency

## v5.1.1 - 2024-04-08
### Changed
* Tinymce updates
* Checking page edition issue
* Check issue on page edition tinymce drag and drop of mini template
* Temporarily remove engine
* Update on tinymce

## v5.1.0 - 2024-02-13
### Fixed
* Fix plang_lang_id warning on Module
* Handles warnings
* Fixed deprecated problem
* Fixed streplace on null
### Changed
* Update cache config structure
* Change minimum stability
* Temp change engine req version

## v5.0.3 - 2023-01-09
### Changed
* Zend libraries issue fixed

## v5.0.2 - 2022-11-09
### Fixed
* Fixed config not getting from the db
* Fixed getting domain info on 404 catcher
### Changed
* Changed psr to interop container
### Dependencies & build
* Update composer.json

## v5.0.1 - 2022-09-26
### Added
* Added allowed_classes=false param to unserialize function and changed interop container to psr container

## v5.0.0 - 2022-06-22
### Fixed
* Fixed problem getting page seo
### Changed
* Used the Module service of asset module instead of the core module
* Changed deprecated ArraySerializable to ArraySerializableHydrator and updated other functions affected by php 8
* Remove zend on namespace
* Removed default value of minWidth param as it is followed by a required param
