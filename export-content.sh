#!/bin/bash
# WordPress Content Archiver - Export Helper Script
#
# This script automates the process of exporting WordPress content and creating an index.
#
# Usage: ./export-content.sh [format] [title] [post_types]
#   [format]: html or txt (default: html)
#   [title]: Title for the index page (default: "WordPress Archive")
#   [post_types]: Additional post types to export (comma-separated)

# Default values
FORMAT=${1:-html}
TITLE=${2:-"WordPress Archive"}
POST_TYPES=${3:-""}

# Verify we're in a WordPress directory
if [ ! -f "wp-config.php" ] && [ ! -d "wp-admin" ]; then
  echo "Error: This script must be run from the WordPress root directory."
  echo "Please copy all files to your WordPress root directory and try again."
  exit 1
fi

# Export content
echo "Starting WordPress content export..."
php convert-content.php $FORMAT $POST_TYPES

# Find the export directory
EXPORT_DIR=$(find . -maxdepth 1 -type d -not -path "./wp-*" -not -path "./.git*" -not -path "." | head -1)

if [ -z "$EXPORT_DIR" ]; then
  echo "Error: Could not find export directory."
  exit 1
fi

# Create index file
echo "Creating index file..."
php create-index.php "$EXPORT_DIR" "$TITLE"

echo ""
echo "Process complete!"
echo "You can find your exported files in: $EXPORT_DIR"
echo "Open $EXPORT_DIR/index.html in your browser to browse the archive."
