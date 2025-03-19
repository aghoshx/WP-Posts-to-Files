# Quick Start Guide

This guide will help you quickly set up and run the WordPress Content Archiver to export your WordPress content to standalone HTML or TXT files.

## Setup Process

### Step 1: Copy Files to WordPress

1. Download or clone this repository
2. Copy all the files to the root directory of your WordPress installation (the same directory that contains wp-admin, wp-content, etc.)

### Step 2: Choose Your Export Method

#### Option A: Using Helper Scripts (Recommended)

**For Linux/Mac users:**
```bash
# Make the script executable
chmod +x export-content.sh

# Run with default settings (HTML format)
./export-content.sh

# Or specify format, title, and additional post types
./export-content.sh html "My Site Archive" product,testimonial
```

**For Windows users:**
```
export-content.bat

# Or specify format, title, and additional post types
export-content.bat html "My Site Archive" product,testimonial
```

#### Option B: Manual Execution

**For HTML export:**
```
php convert-content.php html
```

**For TXT export:**
```
php convert-content.php txt
```

**For additional post types:**
```
php convert-content.php html product,event,testimonial
```

### Step 3: Create an Index Page (if not using helper scripts)

After the export is complete, run:

```
php create-index.php [directory-name] "My Archive Title"
```

Replace `[directory-name]` with the name of the directory created by the export (usually your domain name).

## Viewing Your Archived Content

1. Navigate to the export directory (named after your domain)
2. Open `index.html` in any web browser
3. Navigate through the categorized list of your content

## Troubleshooting

### Common Issues

**Script times out during export:**
- Increase the `set_time_limit()` value in convert-content.php
- For large sites, consider running the export in smaller batches by category or date

**PHP memory limit errors:**
- Add `ini_set('memory_limit', '256M');` near the top of convert-content.php
- Or modify your php.ini file to increase memory limits

**Missing images in HTML exports:**
- The script doesn't download images; they will still point to their original URLs
- For complete offline archives, consider using a tool like HTTrack alongside this script

## Next Steps

- Deploy the exported files to any static web hosting
- Add to version control for content tracking
- Use the content in new projects or platforms

For more detailed information, see the full [README.md](README.md).
