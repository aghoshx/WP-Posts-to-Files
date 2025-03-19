@echo off
REM WordPress Content Archiver - Export Helper Script for Windows
REM
REM This script automates the process of exporting WordPress content and creating an index.
REM
REM Usage: export-content.bat [format] [title] [post_types]
REM   [format]: html or txt (default: html)
REM   [title]: Title for the index page (default: "WordPress Archive")
REM   [post_types]: Additional post types to export (comma-separated)

REM Default values
SET FORMAT=html
SET TITLE=WordPress Archive
SET POST_TYPES=

REM Parse arguments
IF NOT "%~1"=="" SET FORMAT=%~1
IF NOT "%~2"=="" SET TITLE=%~2
IF NOT "%~3"=="" SET POST_TYPES=%~3

REM Verify we're in a WordPress directory
IF NOT EXIST "wp-config.php" (
  IF NOT EXIST "wp-admin" (
    echo Error: This script must be run from the WordPress root directory.
    echo Please copy all files to your WordPress root directory and try again.
    exit /b 1
  )
)

REM Export content
echo Starting WordPress content export...
php convert-content.php %FORMAT% %POST_TYPES%

REM Create index file
echo Creating index file...
FOR /D %%G IN (*) DO (
  IF EXIST "%%G\style.css" (
    php create-index.php "%%G" "%TITLE%"
    echo.
    echo Process complete!
    echo You can find your exported files in: %%G
    echo Open %%G\index.html in your browser to browse the archive.
    exit /b 0
  )
)

echo Warning: Could not find export directory automatically.
echo Please run: php create-index.php [directory_name] "%TITLE%"
