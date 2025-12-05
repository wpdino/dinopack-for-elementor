=== DinoPack for Elementor ===
Contributors: wpdino
Donate link: https://paypal.me/dinostd/10usd
Tags: elementor, widgets, page builder, woocommerce, mailchimp
Requires at least: 6.6
Tested up to: 6.9
Requires PHP: 7.0
Stable tag: 1.0.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A powerful collection of advanced Elementor widgets including WooCommerce products, Blog layouts, Newsletter with MailChimp and more.

== Description ==

DinoPack for Elementor is a collection of creative and advanced widgets, expertly crafted by the WPDINO team to enhance your Elementor experience.

= Key Features =

* **Advanced Heading Widget** - Create dynamic, animated headings with text rotator effects
* **Blog Widget** - Display your posts in grid, list, or masonry layouts with AJAX load more
* **Button Widget** - Stylish buttons with hover effects and animations
* **Gallery Widget** - Responsive image galleries with lightbox functionality
* **Icon Box Widget** - Feature boxes with icons, perfect for services and features
* **Newsletter Widget** - MailChimp integration for email subscriptions with AJAX submission
* **Popup Widget** - Modal popups and overlays for enhanced user experience
* **Price Table Widget** - Professional pricing tables with customizable features and popular badge
* **Progress Bar Widget** - Animated progress bars in line and circle formats
* **WooCommerce Products Widget** - Display products with customizable layouts and styling
* **Restaurant Menu Widget** - Perfect for restaurant websites with menu items, prices, images, and dietary badges
* **Car Specs Widget** - Ideal for automotive websites with vehicle specifications and customizable layouts
* **AI Product Description Generator** - AI-powered product description generator for WooCommerce products using OpenAI
* **AI Product Review Summarizer** - Automatically summarize and analyze product reviews using AI technology

= Widget Features =

* **Multiple Styles** - Each widget comes with multiple pre-designed styles
* **Fully Customizable** - Extensive styling options for colors, typography, spacing, and more
* **Responsive Design** - All widgets are fully responsive and mobile-friendly
* **MailChimp Integration** - Newsletter widget with secure API integration
* **WooCommerce Support** - Dedicated widget for displaying products
* **AI-Powered Widgets** - OpenAI integration for product descriptions and review summaries
* **AJAX Functionality** - Blog load more and newsletter submission without page reload
* **Performance Optimized** - Lightweight code with minimal impact on site speed
* **Cross-browser Compatible** - Works on all modern browsers

= Easy to Use =

* Drag and drop widgets into your Elementor page
* Customize everything through Elementor's intuitive interface
* No coding knowledge required
* Built-in admin settings panel for global configuration

= Requirements =

* WordPress 6.6 or higher
* Elementor 2.0.0 or higher
* PHP 7.0 or higher
* WooCommerce (required for WooCommerce Products widget and AI widgets)
* OpenAI API Key (required for AI-powered widgets, configure in DinoPack Settings)

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/dinopack-for-elementor` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Make sure Elementor is installed and activated.
4. Configure MailChimp API key in DinoPack Settings (optional, for Newsletter widget).
5. Start building with DinoPack widgets in Elementor!

== Frequently Asked Questions ==

= Do I need Elementor to use this plugin? =

Yes, DinoPack for Elementor requires Elementor to be installed and activated. It extends Elementor's functionality with additional widgets.

= Do I need WooCommerce? =

WooCommerce is only required if you want to use the WooCommerce Products widget. All other widgets work without WooCommerce.

= How do I set up the Newsletter widget? =

1. Go to DinoPack Settings in your WordPress admin
2. Enter your MailChimp API key
3. Add the Newsletter widget to your page
4. Select your MailChimp list from the dropdown

= Is this plugin compatible with my theme? =

DinoPack is designed to work with any WordPress theme that supports Elementor. It doesn't interfere with your theme's styling.

= Can I customize the widgets? =

Absolutely! All widgets come with extensive customization options through Elementor's style panel. You can change colors, typography, spacing, animations, and much more.

= Is the plugin mobile-friendly? =

Yes, all widgets are fully responsive and will look great on all devices.

= Do you provide support? =

Yes, we provide support through our website at wpdino.com. You can also find documentation and tutorials there.

== Screenshots ==

1. Advanced Heading Widget with text rotator effects
2. Blog Widget with grid, list, and masonry layouts
3. WooCommerce Products Widget with customizable styling
4. Price Table Widget with customizable features
5. Progress Bar Widget animations
6. Newsletter Widget with MailChimp integration
7. Icon Box Widget with hover effects
8. Gallery Widget with lightbox functionality

== Changelog ==

= 1.0.3 =
* Added AI Product Description Generator widget for WooCommerce products
* Added AI Product Review Summarizer widget for analyzing and summarizing product reviews
* AI widgets integrate with OpenAI API for content generation
* Added OpenAI API key configuration in DinoPack Settings
* Price Table widget now supports shortcodes in currency field
* Price Table widget now supports shortcode-based buttons as alternative to URL buttons
* Improved widget content saving in Elementor editor for AI-generated content
* Enhanced AJAX handling for AI widget content generation

= 1.0.2 =
* Added widget enable/disable feature in General settings tab
* Added "Widgets" subsection in settings with individual checkboxes for each widget
* Widget checkboxes now display in a clean 3-column grid layout (responsive)
* Widget registration now respects enabled/disabled status from settings
* All widgets are enabled by default for backward compatibility

= 1.0.1 =
* Added Restaurant Menu Widget with global title, dietary badges, adaptive alignment, and icon-box styling
* Added Car Specs Widget with grouped specifications, icon support, and dual layout modes
* Updated plugin documentation to cover the new widgets

= 1.0.0 =
* Initial release
* Advanced Heading Widget with text rotator
* Blog Widget with grid, list, and masonry layouts
* Blog Widget with AJAX load more pagination
* Button Widget with animations
* Gallery Widget with lightbox
* Icon Box Widget with hover effects
* Newsletter Widget with MailChimp API integration
* Popup Widget with trigger options
* Price Table Widget
* Progress Bar Widget (line, circle)
* WooCommerce Products Widget
* Admin settings panel
* MailChimp API integration
* Full Elementor editor compatibility
* Responsive design for all widgets
* Performance optimizations

== Upgrade Notice ==

= 1.0.3 =
This update adds two powerful AI-powered widgets for WooCommerce: AI Product Description Generator and AI Product Review Summarizer. These widgets require an OpenAI API key to be configured in DinoPack Settings. The Price Table widget now supports shortcodes in the currency field and shortcode-based buttons. All widgets remain enabled by default.

= 1.0.2 =
New widget management feature allows you to enable/disable individual widgets from the settings panel. All widgets remain enabled by default, so no action is required unless you want to hide specific widgets from the Elementor panel.

= 1.0.0 =
Initial release of DinoPack for Elementor. Install to get access to all widgets and features.

== Support ==

For support, documentation, and updates, visit [wpdino.com](https://wpdino.com)

== External Services ==

This plugin uses the following third-party/external services:

= OpenAI API Service =

**What the service is and what it is used for:**
OpenAI provides artificial intelligence services through their API. The AI Product Description Generator and AI Product Review Summarizer widgets use OpenAI's API to generate product descriptions and summarize product reviews automatically.

**What data is sent and when:**
- **Product information** (name, price, SKU, categories, attributes) - Only when generating product descriptions
- **Product reviews** (review text, ratings, reviewer names) - Only when summarizing product reviews
- **User prompts** - Custom prompts and instructions provided in widget settings
- **API key** - Your OpenAI API key (stored securely in WordPress database, never transmitted to external servers except OpenAI)

**When data is sent:**
Data is only sent when:
1. A user clicks the "Generate Content" or "Summarize Reviews" button in the Elementor editor
2. The OpenAI API key is properly configured in the plugin settings
3. A valid WooCommerce product is selected in the widget settings
4. The generation request is initiated from the Elementor editor

**Service provider information:**
- Service provided by: OpenAI, L.L.C.
- API Endpoint: `https://api.openai.com/v1/`
- Terms of Service: https://openai.com/policies/terms-of-use
- Privacy Policy: https://openai.com/policies/privacy-policy
- API Documentation: https://platform.openai.com/docs/

**Important notes:**
- This service is only used when you configure an OpenAI API key in the plugin settings
- No data is sent to external services unless you explicitly configure the OpenAI integration
- Content generation must be manually triggered from the Elementor editor
- All data transmission is encrypted using HTTPS
- All data processing is handled by OpenAI according to their terms and privacy policy
- OpenAI may use API data to improve their services (see OpenAI's privacy policy for details)
- You are responsible for ensuring compliance with OpenAI's usage policies
- API usage may incur costs based on OpenAI's pricing (see https://openai.com/pricing)

= MailChimp API Service =

**What the service is and what it is used for:**
MailChimp is an email marketing service that provides API endpoints for managing email subscribers and campaigns. The Newsletter widget connects to MailChimp to subscribe users to your email lists when they submit newsletter signup forms.

**What data is sent and when:**
- **Email addresses** - Only when users submit newsletter signup forms
- **Optional merge fields** (name, phone, etc.) - Only if provided by the user in the form
- **Tags** - Only if configured in the widget settings
- **List ID** - To identify which MailChimp list to subscribe users to

**When data is sent:**
Data is only sent when:
1. A user submits a newsletter signup form on your website
2. The MailChimp API key is properly configured in the plugin settings
3. The form submission is valid and passes validation

**Service provider information:**
- Service provided by: MailChimp (Intuit Inc.)
- API Endpoint: `https://<dc>.api.mailchimp.com/3.0/`
- Terms of Service: https://mailchimp.com/legal/terms/
- Privacy Policy: https://www.intuit.com/privacy/statement/
- API Documentation: https://mailchimp.com/developer/

**Important notes:**
- This service is only used when you configure a MailChimp API key in the plugin settings
- No data is sent to external services unless you explicitly configure the MailChimp integration
- Users must actively submit the newsletter form for their data to be transmitted
- All data transmission is encrypted using HTTPS
- All data processing is handled by MailChimp according to their terms and privacy policy
- This plugin only facilitates the transmission of user-provided data to MailChimp's servers

== Privacy Policy ==

This plugin does not collect, store, or share any personal data by default. All data remains on your website and is not transmitted to external servers unless you configure the MailChimp or OpenAI integrations.

**Data Transmission:**
- **MailChimp Integration**: When configured, only email addresses and optional merge fields from newsletter subscription forms are sent to MailChimp's secure API endpoint.
- **OpenAI Integration**: When configured, product information and review data are sent to OpenAI's API for content generation. This includes product names, descriptions, prices, SKUs, categories, attributes, and review text. OpenAI may use this data to improve their services as outlined in their privacy policy.

**Data Storage:**
- API keys are stored securely in your WordPress database
- Generated content is stored in your WordPress database as part of the widget settings
- No personal user data is collected or stored by the plugin itself

**User Control:**
- All external service integrations are optional and must be explicitly configured
- Content generation must be manually triggered by the site administrator
- You can disable AI widgets entirely through the plugin settings
