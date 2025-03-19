# WordPress Content Archiver

A powerful tool to export WordPress posts and pages to standalone HTML or TXT files, allowing content to be preserved and viewed without requiring a WordPress installation.

## 🚀 Purpose

This tool solves a common problem when sunsetting WordPress websites: preserving content in an accessible format. When migrating from WordPress or shutting down blogs, this script creates a static archive of all your posts and pages that can be:

- Easily browsed without a database or PHP
- Stored in version control (Git)
- Served as static files from any basic hosting
- Reused in other projects or sites

## ✨ Features

- Export all posts and pages to HTML or TXT format
- Option to include additional custom post types (products, testimonials, etc.)
- Preserves formatting and basic styling in HTML mode
- Includes metadata (publication date, author)
- Maintains categories and tags information
- Automatically processes WordPress shortcodes
- Handles images responsively (for HTML format)
- Creates a clean directory structure based on site URL

## 📋 Requirements

- WordPress installation
- PHP 5.6 or higher
- Command-line access to your WordPress server

## 🔧 Installation

1. Clone this repository:
   ```
   git clone https://github.com/yourusername/wordpress-content-archiver.git
   ```

2. Upload the `convert-content.php` file to the root of your WordPress installation (same directory level as wp-admin folder).

## 📚 Usage

### Basic Usage

Run from the command line:

```
php convert-content.php [format] [extra_post_types]
```

### Parameters

- `format` (optional): Output format - either `html` or `txt` (default: txt)
- `extra_post_types` (optional): Comma-separated list of custom post types to include

### Examples

Export posts and pages as TXT files (default):
```
php convert-content.php
```

Export posts and pages as HTML files:
```
php convert-content.php html
```

Export posts, pages, and products as HTML files:
```
php convert-content.php html product
```

Export multiple custom post types:
```
php convert-content.php html product,testimonial,event
```

## 📁 Output Structure

The script creates a directory named after your site's domain, containing:

- For TXT export: `{post_type}-{slug}.txt` files
- For HTML export: `{post_type}-{slug}.html` files and a `style.css` file

Example:
```
example.com/
  ├── page-about.html
  ├── page-contact.html
  ├── post-hello-world.html
  ├── post-my-second-post.html
  ├── product-awesome-product.html
  └── style.css
```

## 🔍 What's Included in the Export

### HTML Format

HTML files include:
- Complete HTML structure with responsive design
- Title, publication date, and author
- Full post content with formatting preserved
- Categories and tags (if applicable)
- Basic responsive styling via CSS

### TXT Format

Text files include:
- Title
- Publication date and author
- Post content (with HTML tags stripped)

## 🤔 Use Cases

- **Website Migration**: Create a browsable backup before moving to a new platform
- **Content Preservation**: Archive blogs or websites that are being shut down
- **Content Reuse**: Extract content for integration into new systems
- **Low-Resource Hosting**: Convert dynamic WordPress sites to static content for cheaper hosting
- **Security**: Create an offline backup of your content

## ⚙️ How It Works

The script:
1. Connects to the WordPress database via the WordPress API
2. Queries all published posts and pages (and custom post types if specified)
3. Processes each post/page to extract content and metadata
4. Applies WordPress filters to process shortcodes and formatting
5. Creates appropriately formatted files in the output directory

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🤝 Contributing

Contributions, issues, and feature requests are welcome! Feel free to check the [issues page](https://github.com/yourusername/wordpress-content-archiver/issues).

## 📧 Contact

Questions? Suggestions? Reach out to us at aghoshx@gmail.com

---

Made with ❤️ for the WordPress community
