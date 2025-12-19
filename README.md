# DinoPack for Elementor

<div align="center">

![WordPress Plugin](https://img.shields.io/badge/WordPress-6.6+-blue.svg)
![PHP Version](https://img.shields.io/badge/PHP-7.0+-purple.svg)
![Elementor](https://img.shields.io/badge/Elementor-2.0+-pink.svg)
![License](https://img.shields.io/badge/License-GPL%20v2-green.svg)
![Version](https://img.shields.io/badge/Version-1.0.3-orange.svg)

**A powerful collection of advanced Elementor widgets to supercharge your WordPress website**

[Features](#-features) • [Installation](#-installation) • [Widgets](#-widgets) • [Documentation](#-documentation) • [Support](#-support)

</div>

---

## 📖 Description

**DinoPack for Elementor** is a collection of creative and advanced widgets, expertly crafted by the WPDINO team to enhance your Elementor page building experience. Build stunning websites with our premium-quality widgets that are fully customizable, responsive, and performance-optimized.

## ✨ Features

- 🎨 **16+ Premium Widgets** - Advanced widgets for every need
- 🎯 **Multiple Styles** - Pre-designed styles for each widget
- 📱 **Fully Responsive** - Mobile-first design approach
- ⚡ **Performance Optimized** - Lightweight and fast-loading
- 🛠️ **Highly Customizable** - Extensive styling options
- 🔌 **MailChimp Integration** - Mailchimp Newsletter widget with API support
- 🛒 **WooCommerce Support** - Dedicated products widget
- 🤖 **AI-Powered Features** - OpenAI integration for product descriptions and review summaries
- 🌐 **Translation Ready** - WPML and multilingual compatible
- ♿ **Accessibility** - Built with accessibility in mind
- 🔒 **Privacy Focused** - No data collection or external requests

## 🧩 Widgets

### 1. **Advanced Heading Widget**
Create dynamic, animated headings with text rotator effects to grab visitor attention.

### 2. **Blog Widget**
Display your posts in multiple layouts:
- Grid Layout
- List Layout
- Masonry Layout
- AJAX Load More functionality

### 3. **Button Widget**
Stylish buttons with:
- Multiple hover effects
- Smooth animations
- Icon support
- Custom styling options

### 4. **Gallery Widget**
Responsive image galleries with:
- Lightbox functionality (GLightbox)
- Multiple layouts
- Hover effects
- Caption support

### 5. **Icon Box Widget**
Feature boxes perfect for showcasing services and features with customizable icons and styling.

### 6. **Newsletter Widget**
MailChimp integration for email subscriptions:
- AJAX form submission
- Custom styling
- Success/error messages
- Double opt-in support

### 7. **Popup Widget**
Modal popups and overlays with:
- Multiple trigger options
- Custom animations
- Close button styling
- Content flexibility

### 8. **Price Table Widget**
Professional pricing tables with:
- Customizable title and description
- Price display with currency and period
- Feature list with icons
- Popular badge option
- Call-to-action button

### 9. **Progress Bar Widget**
Animated progress bars in multiple formats:
- Line style
- Circle style
- Custom colors and animations

### 10. **WooCommerce Products Widget**
Display WooCommerce products with:
- Customizable layouts
- Product filtering
- Rating display
- Add to cart functionality

### 11. **Restaurant Menu Widget**
Perfect for restaurant, café, and bar menus with:
- Global menu title replacing individual category headings
- Menu item name, description, price, and dietary badges (Spicy, Vegetarian, Gluten Free)
- Optional item imagery that respects Elementor's placeholder and hides cleanly when removed
- Automatic content alignment shifts for imageless rows
- Icon-box inspired container styling with radius, shadow, and item separators
- Toggleable list and grid layouts with responsive controls

### 12. **Car Specs Widget**
Ideal for automotive, dealership, and rental sites with:
- Vehicle title block featuring name, subtitle, and featured image
- Repeater-based specification groups (Engine, Transmission, Dimensions, etc.)
- Individual spec items supporting icons, labels, and custom values
- Vertical or horizontal layout modes per breakpoint
- Global styling controls for cards, typography, spacing, and separators

### 13. **AI Product Description Generator**
AI-powered product description generator for WooCommerce products:
- Automatically generates product descriptions using OpenAI
- Customizable prompts and content types
- Multiple tone options (professional, friendly, casual, etc.)
- Word count control
- Generates SEO-optimized content
- Editable generated content in Elementor editor
- Requires OpenAI API key (configure in DinoPack Settings)

### 14. **AI Product Review Summarizer**
Automatically summarize and analyze product reviews using AI:
- Analyzes multiple product reviews at once
- Generates comprehensive summaries
- Multiple summary types (overview, pros/cons, detailed analysis)
- Configurable review count (1-50 reviews)
- Editable generated summaries in Elementor editor
- Requires OpenAI API key (configure in DinoPack Settings)

### 15. **AI Product Image Generator**
Generate product images using DALL-E AI:
- Automatically generates product images based on product information
- Multiple image styles (photorealistic, illustration, 3D render, lifestyle, product shot, minimal)
- Multiple image sizes (square, portrait, landscape)
- Custom prompt support for specific image requirements
- Auto-upload to WordPress media library
- Option to set as product featured image
- Fully customizable styling (width, height, alignment, border, shadow)
- Requires OpenAI API key with DALL-E access (configure in DinoPack Settings)

### 16. **AI Product SEO Meta Generator**
Generate SEO-optimized meta data for WooCommerce products:
- Generates SEO-optimized titles (50-60 characters)
- Creates compelling meta descriptions (150-160 characters)
- Generates focus keywords (5-8 relevant keywords)
- Multiple generation types (all, title only, description only, keywords only)
- Custom prompt support for specific SEO requirements
- Auto-save to product meta fields (Yoast SEO compatible)
- Character count display for SEO optimization
- Display controls to show/hide each element
- Fully customizable styling
- Requires OpenAI API key (configure in DinoPack Settings)

## 📋 Requirements

- WordPress 6.6 or higher
- Elementor 2.0.0 or higher
- PHP 7.0 or higher
- WooCommerce (required for Products widget and AI widgets)
- OpenAI API Key (required for AI-powered widgets, configure in DinoPack Settings)

## 🚀 Installation

### Method 1: WordPress Admin

1. Go to **Plugins** → **Add New** in your WordPress admin
2. Search for "DinoPack for Elementor"
3. Click **Install Now** and then **Activate**
4. Make sure Elementor is installed and activated

### Method 2: Manual Installation

1. Download the plugin ZIP file from this repository
2. Go to **Plugins** → **Add New** → **Upload Plugin**
3. Choose the downloaded ZIP file and click **Install Now**
4. Click **Activate** after installation

### Method 3: FTP Upload

1. Download and extract the plugin ZIP file
2. Upload the `dinopack-for-elementor` folder to `/wp-content/plugins/`
3. Go to **Plugins** in WordPress admin and activate the plugin

## ⚙️ Configuration

### MailChimp Setup (for Newsletter Widget)

1. Go to **DinoPack Settings** in WordPress admin
2. Navigate to the MailChimp tab
3. Enter your MailChimp API key
4. Save settings
5. Add the Newsletter widget to your page
6. Select your MailChimp list from the dropdown

### OpenAI Setup (for AI Widgets)

1. Go to **DinoPack Settings** in WordPress admin
2. Navigate to the AI Settings tab
3. Enter your OpenAI API key (get one from https://platform.openai.com/api-keys)
4. Select your preferred AI model (default: gpt-3.5-turbo)
5. Configure temperature and other settings (optional)
6. Save settings
7. Add any AI widget to your page:
   - **AI Product Description Generator** - Select a product and click "Generate Description"
   - **AI Product Review Summarizer** - Select a product and click "Summarize Reviews"
   - **AI Product Image Generator** - Select a product, choose style and size, then click "Generate Image"
   - **AI Product SEO Meta Generator** - Select a product, choose SEO type, then click "Generate SEO Meta"
8. Generated content will appear in the widget and can be edited directly in the Elementor editor

## 🎯 Usage

1. Edit any page with Elementor
2. Search for "DinoPack" in the widgets panel
3. Drag and drop any widget onto your page
4. Customize using Elementor's intuitive interface
5. Preview and publish your page

## 🎨 Customization

All widgets come with extensive customization options:

- **Content Controls** - Edit text, links, and media
- **Style Controls** - Colors, typography, spacing
- **Advanced Controls** - Animations, custom CSS
- **Responsive Settings** - Different styles per device

## 📱 Responsive Design

Every widget is fully responsive with:
- Desktop, tablet, and mobile views
- Device-specific styling options
- Touch-friendly interactions
- Optimized for all screen sizes

## 🔧 Developer Features

### Custom Controls

DinoPack includes a custom Elementor control:
- Select Image Control (with Image Picker functionality)

### Hooks & Filters

Developers can extend functionality using WordPress hooks and filters throughout the plugin.

### Code Structure

```
dinopack-for-elementor/
├── assets/              # Plugin assets
├── inc/                 # Core files
│   ├── admin/          # Admin settings
│   ├── elementor/      # Widgets and controls
│   └── class-dinopack-elementor-ajax-handlers.php
├── languages/          # Translation files
└── dinopack-for-elementor.php
```

## 🐛 Bug Reports

Found a bug? Please create an issue on [GitHub Issues](https://github.com/wpdino/dinopack-for-elementor/issues) with:
- WordPress version
- Elementor version
- PHP version
- Steps to reproduce
- Expected vs actual behavior

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to discuss what you would like to change.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📚 Documentation

For detailed documentation, tutorials, and guides, visit:
- [Official Website](https://wpdino.com)
- [Documentation](https://wpdino.com/docs)
- [Video Tutorials](https://wpdino.com/tutorials)

## 💬 Support

Need help? We're here for you!

- 🌐 Visit [wpdino.com](https://wpdino.com)
- 📧 Email: support@wpdino.com

## 📄 License

This plugin is licensed under the GPL v2 or later.

```
DinoPack for Elementor
Copyright (C) 2024 WPDINO

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

See [LICENSE](LICENSE) file for full license text.

## 🌐 External Services

This plugin uses the following third-party services:

### OpenAI API Service

**What it is:** OpenAI provides artificial intelligence services through their API for generating text content.

**What it's used for:** The AI Product Description Generator and AI Product Review Summarizer widgets use OpenAI's API to automatically generate product descriptions and summarize product reviews.

**What data is sent and when:**
- **Product information** (name, price, SKU, categories, attributes) - Only when generating product descriptions
- **Product reviews** (review text, ratings, reviewer names) - Only when summarizing product reviews
- **User prompts** - Custom prompts and instructions provided in widget settings
- **API key** - Your OpenAI API key (stored securely in WordPress database, transmitted only to OpenAI's API)

**When data is sent:** Data is only sent when:
1. A user clicks the "Generate Content" or "Summarize Reviews" button in the Elementor editor
2. The OpenAI API key is properly configured in the plugin settings
3. A valid WooCommerce product is selected in the widget settings
4. The generation request is manually initiated from the Elementor editor

**Service Provider:** OpenAI, L.L.C.
- **Terms of Service:** [https://openai.com/policies/terms-of-use](https://openai.com/policies/terms-of-use)
- **Privacy Policy:** [https://openai.com/policies/privacy-policy](https://openai.com/policies/privacy-policy)
- **API Documentation:** [https://platform.openai.com/docs/](https://platform.openai.com/docs/)
- **API Pricing:** [https://openai.com/pricing](https://openai.com/pricing)

**Important notes:**
- This service is only used when you configure an OpenAI API key in the plugin settings
- No data is sent to external services unless you explicitly configure the OpenAI integration
- Content generation must be manually triggered by the site administrator from the Elementor editor
- All data transmission is encrypted using HTTPS
- OpenAI may use API data to improve their services (see OpenAI's privacy policy for details)
- You are responsible for ensuring compliance with OpenAI's usage policies
- API usage may incur costs based on OpenAI's pricing - check their pricing page for current rates
- Generated content is stored in your WordPress database as part of the widget settings

### MailChimp API Service

**What it is:** MailChimp is an email marketing service that provides API endpoints for managing email subscribers and campaigns.

**What it's used for:** The Newsletter widget connects to MailChimp to subscribe users to your email lists when they submit newsletter signup forms.

**What data is sent and when:**
- **Email addresses** - Only when users submit newsletter signup forms
- **Optional merge fields** (name, phone, etc.) - Only if provided by the user in the form
- **Tags** - Only if configured in the widget settings
- **List ID** - To identify which MailChimp list to subscribe users to

**When data is sent:** Data is only sent when:
1. A user submits a newsletter signup form on your website
2. The MailChimp API key is properly configured in the plugin settings
3. The form submission is valid and passes validation

**Service Provider:** MailChimp (Intuit Inc.)
- **Terms of Service:** [https://mailchimp.com/legal/terms/](https://mailchimp.com/legal/terms/)
- **Privacy Policy:** [https://www.intuit.com/privacy/statement/](https://www.intuit.com/privacy/statement/)
- **API Documentation:** [https://mailchimp.com/developer/](https://mailchimp.com/developer/)

**Data Processing:** All data processing is handled by MailChimp according to their terms and privacy policy. This plugin only facilitates the transmission of user-provided data to MailChimp's servers.

## 🔐 Privacy Policy

**DinoPack for Elementor respects your privacy:**

- ✅ No data collection by the plugin itself
- ✅ No external requests (except MailChimp and OpenAI when configured)
- ✅ All data stays on your server (except newsletter signups sent to MailChimp and product data sent to OpenAI for content generation)
- ✅ GDPR compliant
- ✅ No tracking or analytics
- ✅ Users must explicitly submit newsletter forms to send data to MailChimp
- ✅ AI content generation must be manually triggered by administrators

**Data Transmission:**
- **MailChimp**: The Newsletter widget only connects to MailChimp API when explicitly configured, and only sends the email addresses and optional information collected through your newsletter signup forms.
- **OpenAI**: The AI widgets only connect to OpenAI API when explicitly configured, and only send product information and review data when you manually trigger content generation from the Elementor editor. OpenAI may use this data to improve their services as outlined in their privacy policy.

**Data Storage:**
- API keys are stored securely in your WordPress database
- Generated content is stored in your WordPress database as part of the widget settings
- No personal user data is collected or stored by the plugin itself

## 📝 Changelog

### Version 1.0.3 - 2025-01-XX

#### Added
- AI Product Description Generator widget for WooCommerce products
- AI Product Review Summarizer widget for analyzing and summarizing product reviews
- OpenAI API integration with configurable API key, model, and settings
- AI Settings tab in DinoPack Settings page
- Shortcode support in Price Table currency field
- Shortcode-based button option for Price Table widget (alternative to URL buttons)
- Enhanced content saving in Elementor editor for AI-generated content
- Improved AJAX handling for AI widget content generation

#### Changed
- Price Table widget now supports shortcodes in currency field for dynamic currency display
- Price Table widget now supports shortcode-based buttons as alternative to URL buttons
- Improved widget content persistence in Elementor editor

#### Technical
- Added `class-dinopack-ai-helper.php` for OpenAI API integration
- Added AJAX handlers for AI content generation
- Enhanced Elementor editor scripts for AI widgets
- Updated widget registration to include AI widgets

### Version 1.0.2 - 2025-11-17

#### Added
- Widget enable/disable feature in General settings tab
- "Widgets" subsection in General settings with individual checkboxes for each widget
- Subsection field type support in settings page renderer
- CSS Grid layout for widget checkboxes (3 columns on desktop, responsive on mobile/tablet)
- Widget filtering system that checks enabled status before registering widgets with Elementor

#### Changed
- Widget registration now respects enabled/disabled status from settings
- Settings page field rendering improved to support subsection grouping
- Widget checkboxes now display in a clean 3-column grid layout

#### Technical
- Modified `class-dinopack-settings-page.php` to add widget management functionality
- Updated `class-dinopack-elementor-widgets.php` to check widget enabled status before registration
- Enhanced `class-dinopack-field-renderer.php` to support subsection field type
- Added CSS grid styling in `admin.css` for responsive widget checkbox layout
- All widgets are enabled by default for backward compatibility

### Version 1.0.1 - 2025-11-08

#### Added
- Restaurant Menu Widget with menu title, dietary badges, adaptive alignment, and icon-box styling
- Car Specs Widget with grouped specifications, icon support, and dual layout modes

#### Updated
- README documentation describing the new widgets and features

### Version 1.0.0 - Initial Release

#### Added
- Advanced Heading Widget with text rotator
- Blog Widget with 3 layouts (Grid, List, Masonry) and AJAX load more
- Button Widget with animations
- Gallery Widget with GLightbox
- Icon Box Widget
- Newsletter Widget with MailChimp integration
- Popup Widget
- Price Table Widget
- Progress Bar Widget (line, circle)
- WooCommerce Products Widget
- Admin settings panel
- Custom Elementor controls
- Full responsive design
- Performance optimizations

See [CHANGELOG.md](CHANGELOG.md) for complete version history.

## 🙏 Credits

**Developed by:** [WPDINO Team](https://wpdino.com)

**Libraries Used:**
- [GLightbox](https://github.com/biati-digital/glightbox) - Lightbox functionality
- [Image Picker](https://rvera.github.io/image-picker/) - Image selection control

## ⭐ Show Your Support

If you like this plugin, please:
- ⭐ Star this repository
- 🐦 Share on social media
- ✍️ Write a review
- 🤝 Contribute to the code

---

<div align="center">

**Made with ❤️ by [WPDINO](https://wpdino.com)**

[Website](https://wpdino.com) • [Documentation](https://wpdino.com/docs) • [Support](https://wpdino.com/support)

</div>

