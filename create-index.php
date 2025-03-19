<?php
/**
 * WordPress Content Archiver - Index Generator
 *
 * This script creates an index.html file that lists all the exported posts and pages,
 * making it easier to browse the archived content.
 * 
 * Usage: php create-index.php [directory] [title]
 * Where:
 *   [directory] (optional): The directory containing exported files (default: auto-detect)
 *   [title] (optional): Title for the index page (default: "Content Archive")
 *
 * Example: php create-index.php example.com "Example.com Archive"
 *
 * @version 1.0.0
 * @license MIT
 */

// Process command line arguments
$directory = isset($argv[1]) ? $argv[1] : '';
$title = isset($argv[2]) ? $argv[2] : 'Content Archive';

// If no directory provided, try to find one
if (empty($directory)) {
    $dirs = glob('*', GLOB_ONLYDIR);
    foreach ($dirs as $dir) {
        if (file_exists($dir . '/style.css') || count(glob($dir . '/*.{html,txt}', GLOB_BRACE)) > 0) {
            $directory = $dir;
            break;
        }
    }
    
    if (empty($directory)) {
        die("Error: No export directory found. Please specify a directory.\n");
    }
}

// Check if directory exists
if (!file_exists($directory) || !is_dir($directory)) {
    die("Error: Directory '$directory' not found.\n");
}

// Get all HTML and TXT files
$html_files = glob($directory . '/*.html');
$txt_files = glob($directory . '/*.txt');
$files = array_merge($html_files, $txt_files);

// Remove style.css from the list if it exists
$files = array_filter($files, function($file) {
    return basename($file) !== 'style.css';
});

if (empty($files)) {
    die("Error: No HTML or TXT files found in '$directory'.\n");
}

// Group by post type
$grouped_files = [];
foreach ($files as $file) {
    $filename = basename($file);
    $parts = explode('-', $filename, 2); // Split only at the first hyphen
    
    if (count($parts) > 1) {
        $post_type = $parts[0];
        
        if (!isset($grouped_files[$post_type])) {
            $grouped_files[$post_type] = [];
        }
        
        // Get title from file
        $title_from_file = '';
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        
        if ($extension === 'html') {
            $content = file_get_contents($file);
            if (preg_match('/<title>(.*?)<\/title>/s', $content, $matches)) {
                $title_from_file = $matches[1];
            }
        } else {
            // For TXT files, use the first line as the title
            $handle = fopen($file, 'r');
            if ($handle) {
                $title_from_file = trim(fgets($handle));
                fclose($handle);
            }
        }
        
        if (empty($title_from_file)) {
            // Fallback to filename
            $title_from_file = str_replace(["-$post_type-", ".$extension"], ['', ''], $filename);
            $title_from_file = ucwords(str_replace('-', ' ', $title_from_file));
        }
        
        $grouped_files[$post_type][] = [
            'file' => $filename,
            'title' => $title_from_file
        ];
    }
}

// Sort each group alphabetically by title
foreach ($grouped_files as $post_type => &$files) {
    usort($files, function($a, $b) {
        return strcasecmp($a['title'], $b['title']);
    });
}

// Generate the HTML content
$index_content = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            color: #333;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        h2 {
            margin-top: 30px;
            color: #3498db;
            text-transform: capitalize;
        }
        ul {
            padding-left: 20px;
        }
        li {
            margin-bottom: 8px;
        }
        a {
            color: #2980b9;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .post-count {
            color: #7f8c8d;
            font-size: 0.9em;
        }
        footer {
            margin-top: 50px;
            text-align: center;
            font-size: 0.9em;
            color: #7f8c8d;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <h1>{$title}</h1>
    
    <p>This is an archive of content exported from WordPress. Click on any link below to view the content.</p>
    
HTML;

$total_count = 0;

foreach ($grouped_files as $post_type => $files) {
    $count = count($files);
    $total_count += $count;
    $post_type_label = ucfirst($post_type) . 's';
    
    $index_content .= "    <h2>{$post_type_label} <span class=\"post-count\">({$count})</span></h2>\n";
    $index_content .= "    <ul>\n";
    
    foreach ($files as $file_info) {
        $index_content .= "        <li><a href=\"{$file_info['file']}\">{$file_info['title']}</a></li>\n";
    }
    
    $index_content .= "    </ul>\n\n";
}

$date = date('F j, Y');

$index_content .= <<<HTML
    <footer>
        <p>Archive contains {$total_count} items • Generated on {$date}</p>
    </footer>
</body>
</html>
HTML;

// Write index.html file
$index_file = $directory . '/index.html';
file_put_contents($index_file, $index_content);

echo "Successfully created index file at '{$index_file}' with {$total_count} items.\n";
?>