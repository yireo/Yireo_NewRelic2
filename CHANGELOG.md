# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.4.0] - 25 November 2026
### Added
- New settings "Send Customer ID", "Track Blocks" and "Track Models", all disabled by default
- PHP 8.5 support

### Changed
- Customer name and email are no longer sent to New Relic; only the customer ID (or "guest") is sent, and only when "Send Customer ID" is enabled
- The full request URI and request parameters are no longer sent to New Relic; only the route is reported
- The license key is now stored encrypted; existing plain-text keys are encrypted during setup:upgrade
- Tracking of rendered blocks and model saves/deletes is now opt-in, and the block plugin and controller observers only run in the area they belong to
- Dropped PHP 8.1 support; PHP 8.2 to 8.5 is required
- Missing module dependencies are now declared in composer.json and module.xml
- Removed Magento 1 leftover reindex observers

### Fixed
- The `magento_store_id` custom parameter was always empty (#6)
- Custom parameters were never sent, because a non-existing New Relic function was called
- Agent calls were logged even with debugging disabled, and the license key ended up in the log
- RUM scripts are now rendered via SecureHtmlRenderer, so they work with a strict Content Security Policy
- The ACL resource `Yireo_NewRelic2::config` used by the configuration section was missing
- Deprecation warnings with PHP 8.6 on `trim()` calls

## [0.3.5] - 5 November 2020
### Fixed
- Fix type errors (@FrankStappers)

## [0.3.4] - 29 July 2020
### Added
- Magento 2.4 compatibility

## Older versions are not documented here
