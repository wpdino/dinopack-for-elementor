# Changelog

All notable changes to DinoPack for Elementor will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.2] - 2025-01-XX

### Added
- Widget enable/disable feature in General settings tab
- "Widgets" subsection in General settings with individual checkboxes for each widget
- Subsection field type support in settings page renderer
- CSS Grid layout for widget checkboxes (3 columns on desktop, responsive on mobile/tablet)
- Widget filtering system that checks enabled status before registering widgets with Elementor

### Changed
- Widget registration now respects enabled/disabled status from settings
- Settings page field rendering improved to support subsection grouping
- Widget checkboxes now display in a clean 3-column grid layout

### Technical
- Modified `class-dinopack-settings-page.php` to add widget management functionality
- Updated `class-dinopack-elementor-widgets.php` to check widget enabled status before registration
- Enhanced `class-dinopack-field-renderer.php` to support subsection field type
- Added CSS grid styling in `admin.css` for responsive widget checkbox layout
- All widgets are enabled by default for backward compatibility

## [1.0.1] - 2025-11-08

### Added
- Restaurant Menu Widget featuring global menu title, dietary badges, adaptive layouts, and enhanced styling controls
- Car Specs Widget with grouped specification repeater, icon support, and vertical/horizontal layout modes

### Updated
- Documentation to highlight the new widgets and their capabilities

## [1.0.0] - 2024-01-01

### Added
- Initial release of DinoPack for Elementor
- Advanced Heading Widget with text rotator effects
- Blog Widget with 3 layout options (Grid, List, Masonry)
- Button Widget with hover animations and effects
- Carousel Widget with Swiper.js integration
- Gallery Widget with lightbox functionality
- Icon Box Widget with multiple styles
- Newsletter Widget with email validation
- Popup Widget with modal functionality
- Price Table Widget with customizable features
- Progress Bar Widget (Line, Circle)
- Template library with 50+ professional templates
- Full Elementor editor compatibility
- Responsive design for all widgets
- Performance optimizations
- WordPress.org repository compliance
- GPL v2 license
- Comprehensive documentation
- Translation ready with .pot file

### Features
- Drag and drop widgets in Elementor
- Extensive customization options
- Multiple pre-designed styles
- Cross-browser compatibility
- Mobile-first responsive design
- SEO-friendly code structure
- Accessibility features
- Clean, optimized code
- No external dependencies (except Swiper.js for carousel)

### Requirements
- WordPress 5.2 or higher
- Elementor 2.0.0 or higher
- PHP 7.0 or higher

### Security
- All inputs properly sanitized
- Nonce verification where needed
- XSS protection implemented
- CSRF protection in place
