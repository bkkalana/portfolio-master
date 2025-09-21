# Portfolio Master

![Portfolio Master](https://img.shields.io/badge/Version-1.0.0-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-green.svg)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-orange.svg)
![License](https://img.shields.io/badge/License-GPL%20v2%2B-red.svg)

A comprehensive portfolio management plugin for WordPress with custom post types, taxonomies, and responsive grid display. Perfect for showcasing your work, client projects, or any creative portfolio.

## ✨ Features

### 🎯 Core Features
- **Custom Post Type (Projects)**: Full support for title, editor, thumbnail, custom fields, and revisions
- **Archive Support**: Projects accessible at `/projects` URL with custom templates
- **REST API Compatible**: Full Gutenberg editor support and modern WordPress integration
- **Custom Taxonomy (Project Type)**: Hierarchical taxonomy system for organizing projects
- **Admin Enhancements**: Custom portfolio icon and sortable columns in admin interface
- **Meta Box Integration**: Project Details with Client Name, Project URL, and Completion Date
- **Frontend Display**: Responsive grid shortcode `[portfolio_master]` with modern CSS
- **Security First**: Nonce validation, input sanitization, and output escaping

### 🎨 Admin Features
- **Custom Portfolio Icon**: Professional portfolio icon in WordPress admin menu
- **Sortable Columns**: 
  - Featured Image thumbnail preview
  - Project Type with taxonomy display
  - Client Name from meta fields
  - Published Date with sorting
- **Project Details Meta Box**:
  - Client Name (text field with validation)
  - Project URL (URL field with proper validation)
  - Completion Date (HTML5 date picker)

### 🌐 Frontend Features
- **Responsive Grid Layout**: CSS Grid with mobile-first design
- **Featured Image Display**: Optimized image loading with hover effects
- **Project Information**: Title, project type, and client name display
- **SEO Friendly**: Proper heading structure and semantic HTML
- **Mobile Optimized**: Responsive design that works on all devices

## 🚀 Installation

### Automatic Installation
1. Download the plugin ZIP file
2. Go to **Plugins > Add New** in your WordPress admin
3. Click **Upload Plugin** and select the ZIP file
4. Click **Install Now** and then **Activate**

### Manual Installation
1. Upload the `portfolio-master` folder to `/wp-content/plugins/` directory
2. Activate the plugin through the **Plugins** menu in WordPress
3. Go to **Projects** in the admin menu to start adding your portfolio items

### Requirements
- WordPress 5.0 or higher
- PHP 7.4 or higher
- Modern web browser with CSS Grid support

## 📖 Usage

### Adding Projects

1. Navigate to **Projects > Add New** in your WordPress admin
2. Add a compelling title and detailed description for your project
3. Set a high-quality featured image (recommended: 800x600px or larger)
4. Select or create a Project Type from the sidebar
5. Fill in the **Project Details** meta box:
   - **Client Name**: The client or company name
   - **Project URL**: Live project URL (optional)
   - **Completion Date**: When the project was completed
6. Publish your project

### Managing Project Types

1. Go to **Projects > Project Types** in your WordPress admin
2. Add, edit, or delete project types as needed
3. Project types are hierarchical (like categories) - you can create parent/child relationships
4. Examples: Web Design, Mobile Apps, Branding, Photography, etc.

### Displaying Projects

Use the `[portfolio_master]` shortcode to display your projects in a responsive grid:

#### Basic Usage
```
[portfolio_master]
```

#### Advanced Usage with Parameters
```
[portfolio_master posts_per_page="8" project_type="web-design" orderby="date" order="DESC"]
```

#### Shortcode Parameters

| Parameter | Description | Default | Options |
|-----------|-------------|---------|---------|
| `posts_per_page` | Number of projects to display | `12` | Any positive integer |
| `project_type` | Filter by project type slug | `all` | Any valid project type slug |
| `orderby` | Sort by field | `date` | `date`, `title`, `menu_order` |
| `order` | Sort order | `DESC` | `ASC`, `DESC` |

#### Usage Examples

**Display 6 recent projects:**
```
[portfolio_master posts_per_page="6"]
```

**Show only web design projects:**
```
[portfolio_master project_type="web-design"]
```

**Display projects sorted by title:**
```
[portfolio_master orderby="title" order="ASC"]
```

**Show 9 mobile app projects:**
```
[portfolio_master posts_per_page="9" project_type="mobile-apps"]
```

## 🎨 Customization

### CSS Customization

The plugin includes responsive CSS that can be customized. Key CSS classes:

```css
/* Main grid container */
.pm-portfolio-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

/* Individual project item */
.pm-portfolio-item {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

/* Project image container */
.pm-portfolio-image img {
    width: 100%;
    height: 250px;
    object-fit: cover;
}

/* Project content area */
.pm-portfolio-content {
    padding: 20px;
}

/* Project title */
.pm-portfolio-title a {
    color: #333;
    text-decoration: none;
}

/* Project metadata */
.pm-portfolio-meta {
    display: flex;
    gap: 10px;
    font-size: 14px;
}
```

### Template Customization

The plugin uses WordPress's standard template hierarchy. Create these files in your theme:

- `single-pm_project.php` - Single project template
- `archive-pm_project.php` - Projects archive template  
- `taxonomy-project_type.php` - Project type archive template

### Hooks and Filters

The plugin provides several hooks for customization:

```php
// Modify shortcode arguments
add_filter('pm_shortcode_args', function($args) {
    $args['posts_per_page'] = 8;
    return $args;
});

// Customize project query
add_action('pm_before_portfolio_query', function($query) {
    // Modify query before execution
});
```

## 🔒 Security

This plugin follows WordPress security best practices:

- ✅ **Nonce validation** for all form submissions
- ✅ **Input sanitization** for all user data using `sanitize_text_field()`
- ✅ **Output escaping** for all frontend display using `esc_html()`, `esc_attr()`
- ✅ **Capability checks** for admin functions
- ✅ **Namespaced functions** with `pm_` prefix to avoid conflicts
- ✅ **SQL injection prevention** using WordPress query methods
- ✅ **XSS protection** through proper escaping

## 🐛 Troubleshooting

### Common Issues

**Q: Projects not displaying in shortcode**
- A: Check if projects are published and have the correct post type
- A: Verify the shortcode syntax is correct
- A: Check for theme conflicts by switching to a default theme

**Q: Featured images not showing**
- A: Ensure images are properly set as featured images
- A: Check image file permissions
- A: Verify image URLs are accessible

**Q: Admin columns not sortable**
- A: Clear any caching plugins
- A: Check for plugin conflicts
- A: Ensure you're on the correct admin page

**Q: Meta box fields not saving**
- A: Check user permissions
- A: Verify nonce validation is working
- A: Check for JavaScript errors in browser console

### Debug Mode

Enable WordPress debug mode to see detailed error messages:

```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

## 📊 Performance

### Optimization Tips

1. **Image Optimization**: Use properly sized images (800x600px recommended)
2. **Caching**: Enable WordPress caching for better performance
3. **CDN**: Use a Content Delivery Network for faster image loading
4. **Database**: Regularly clean up unused project types and meta data

### Performance Features

- ✅ **Efficient Queries**: Uses WordPress WP_Query for optimal database performance
- ✅ **Inline CSS**: CSS is loaded inline to reduce HTTP requests
- ✅ **Responsive Images**: Proper image sizing and lazy loading support
- ✅ **Clean Code**: Optimized PHP code with minimal overhead

## 🔄 Changelog

### Version 1.0.0
- 🎉 Initial release
- ✨ Custom post type for Projects with full Gutenberg support
- ✨ Hierarchical Project Type taxonomy
- ✨ Admin enhancements with sortable columns
- ✨ Project Details meta box with client information
- ✨ Responsive portfolio grid shortcode
- ✨ Security and best practices implementation
- ✨ Mobile-first responsive design
- ✨ WordPress coding standards compliance

## 🤝 Contributing

We welcome contributions! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

### Development Setup

1. Clone the repository
2. Set up a local WordPress development environment
3. Activate the plugin
4. Make your changes
5. Test on multiple WordPress versions

## 📞 Support

### Getting Help

- **Documentation**: Check this README for common solutions
- **Issues**: Report bugs and request features on GitHub
- **Community**: Join our community discussions
- **Email**: Contact us directly for premium support

### Support Policy

- ✅ Free support for basic usage questions
- ✅ Bug reports and feature requests
- ✅ Documentation improvements
- ✅ Community contributions

## 📄 License

This plugin is licensed under the **GNU General Public License v2 or later**.

```
Portfolio Master WordPress Plugin
Copyright (C) 2025 Portfolio Master Team

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
```

## 🙏 Credits

- **WordPress Community**: For the amazing platform
- **Contributors**: All developers who helped improve this plugin
- **Testers**: Beta testers who provided valuable feedback
- **Users**: Everyone who uses and supports this plugin

---

**Portfolio Master** - Showcase your work with style and professionalism. 🚀

*Made with ❤️ for the WordPress community*