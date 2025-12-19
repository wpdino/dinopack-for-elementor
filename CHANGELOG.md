# Changelog

All notable changes to DinoPack for Elementor will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.4] - 2025-12-XX

### Added
- AI Product Image Generator widget for WooCommerce products
  - Generate product images using DALL-E AI based on product information
  - Multiple image styles: Photorealistic, Illustration, 3D Render, Lifestyle, Product Shot, Minimal
  - Multiple image sizes: Square (1024x1024), Portrait (1024x1792), Landscape (1792x1024)
  - Custom prompt support for specific image requirements
  - Auto-upload generated images to WordPress media library
  - Option to automatically set generated image as product featured image
  - Fully customizable styling (width, height, alignment, border, shadow)
  - Real-time preview update in Elementor editor
- AI Product SEO Meta Generator widget for WooCommerce products
  - Generate SEO-optimized titles (50-60 characters recommended)
  - Generate compelling meta descriptions (150-160 characters recommended)
  - Generate focus keywords (5-8 relevant keywords)
  - Multiple generation types: All, Title only, Description only, Keywords only
  - Custom prompt support for specific SEO requirements
  - Auto-save generated meta to product (Yoast SEO compatible)
  - Character count display for SEO optimization guidance
  - Display controls to show/hide each SEO element
  - Fully customizable styling for all elements
  - Real-time preview update in Elementor editor

### Changed
- Improved preview refresh in Elementor editor for all AI widgets
  - Enhanced editor scripts with comprehensive preview update logic
  - Multiple fallback methods to ensure preview updates correctly
  - Better handling of media controls and text controls
- Enhanced error messages for AI widgets
  - Specific error message when OpenAI API key is missing
  - Clear instructions on where to configure the API key
  - Better error message display duration (8 seconds for important errors)
  - Improved error handling for all AI API requests

### Technical
- Added AJAX handlers for new AI widgets (`dinopack_generate_product_image`, `dinopack_generate_product_seo`)
- Enhanced AI Helper class with DALL-E image generation support
- Improved editor.js scripts for all AI widgets with comprehensive preview refresh logic
- Added API key validation before making AI requests
- Better error message handling and user feedback
- Updated widget registration to include new AI widgets

### Requirements
- DALL-E API access required for AI Product Image Generator (included with OpenAI API key)

## [1.0.3] - 2025-12-03

### Added
- AI Product Description Generator widget for WooCommerce products
  - Automatically generates product descriptions using OpenAI
  - Customizable prompts, content types, and tone options
  - Word count control for generated content
  - Editable generated content in Elementor editor
- AI Product Review Summarizer widget for WooCommerce products
  - Analyzes and summarizes multiple product reviews using AI
  - Multiple summary types (overview, pros/cons, detailed analysis)
  - Configurable review count (1-50 reviews)
  - Editable generated summaries in Elementor editor
- OpenAI API integration
  - AI Settings tab in DinoPack Settings page
  - Configurable OpenAI API key, model, temperature, and other settings
  - Secure API key storage in WordPress database
- Price Table widget enhancements
  - Shortcode support in currency field for dynamic currency display
  - Shortcode-based button option as alternative to URL buttons
- Enhanced content saving in Elementor editor for AI-generated content
- Improved AJAX handling for AI widget content generation

### Changed
- Price Table widget now supports shortcodes in currency field
- Price Table widget now supports shortcode-based buttons as alternative to URL buttons
- Improved widget content persistence in Elementor editor
- Updated documentation to include AI widgets and OpenAI integration

### Technical
- Added `class-dinopack-ai-helper.php` for OpenAI API integration
- Added AJAX handlers for AI content generation (`dinopack_generate_product_description`, `dinopack_summarize_product_reviews`)
- Enhanced Elementor editor scripts for AI widgets with proper content saving
- Updated widget registration to include AI widgets
- Improved error handling for AI API requests

### Privacy & Security
- Added OpenAI privacy policy information to readme files
- Updated plugin privacy policy to include OpenAI data transmission details
- All API keys stored securely in WordPress database
- Content generation requires manual trigger from Elementor editor

### Requirements
- WooCommerce now required for AI widgets
- OpenAI API key required for AI widgets (configure in DinoPack Settings)

## [1.0.2] - 2025-11-17

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
