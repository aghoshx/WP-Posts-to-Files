<?php
/**
 * WordPress Content Backup Tool
 *
 * This script exports WordPress posts and pages to standalone HTML or TXT files,
 * allowing content to be preserved and viewed without a WordPress installation.
 * Ideal for archiving content from websites being decommissioned or migrated.
 *
 * Usage: php convert-content.php [format] [extra_post_types]
 * Where:
 *   [format] (optional): 'html' or 'txt' (default: txt)
 *   [extra_post_types] (optional): Comma-separated list of additional post types to export
 *
 * Example: php convert-content.php html product,testimonial
 *
 * @version 1.1.0
 * @license MIT
 */

// Security: Exit if accessed directly
if (!defined('ABSPATH') && !isset($argv)) {
    exit('Direct script access not allowed.');
}

// Process command line arguments
$format = 'txt'; // Default format
$extra_post_types = [];

if (isset($argv) && count($argv) > 1) {
    // Check for format argument
    if (in_array(strtolower($argv[1]), ['txt', 'html'])) {
        $format = strtolower($argv[1]);
    }
    
    // Check for additional post types
    if (isset($argv[2])) {
        $extra_post_types = explode(',', $argv[2]);
    }
}

/* Load WordPress */
define('WP_USE_THEMES', false);
require ('../wp-blog-header.php');

/* Create a folder based on the domain name */
$folder = str_replace("http://","", get_bloginfo('url'));
$folder = str_replace("https://","", $folder);
$folder = sanitize_file_name($folder);

// Create directory if it doesn't exist
if (!file_exists($folder)) {
    mkdir($folder, 0755, true);
}

// Define post types to export
$post_types = array_merge(['post', 'page'], $extra_post_types);

// Create a CSS file for HTML exports
if ($format === 'html') {
    $css_content = "body { font-family: Arial, sans-serif; line-height: 1.6; max-width: 800px; margin: 0 auto; padding: 20px; }\n";
    $css_content .= "h1 { color: #333; }\n";
    $css_content .= "img { max-width: 100%; height: auto; }\n";
    $css_content .= ".meta { color: #666; font-size: 0.9em; margin-bottom: 20px; }\n";
    $css_content .= ".content { margin-top: 20px; }\n";
    $css_content .= ".categories, .tags { margin-top: 30px; font-size: 0.9em; }\n";
    
    file_put_contents($folder.'/style.css', $css_content);
    echo "Created CSS file for styling HTML exports\n";
}

/* Get all the posts and pages */
$new_posts = new WP_Query(array(
    'post_type' => $post_types,
    'posts_per_page' => -1,
    'post_status' => 'publish'
));

echo "Starting export of " . $new_posts->found_posts . " items in '{$format}' format...\n";

if ($new_posts->have_posts()) : 
    $count = 0;
    while ($new_posts->have_posts()) : $new_posts->the_post();
        global $post;
        
        // Build filename
        $file_extension = ($format === 'html') ? '.html' : '.txt';
        $post_type = get_post_type($post->ID);
        $file = $folder.'/'.$post_type.'-'.$post->post_name.$file_extension;
        
        // Get post data
        $title = get_the_title();
        $content = $post->post_content;
        $date = get_the_date();
        $author = get_the_author_meta('display_name', $post->post_author);
        
        // Process content with WordPress filters to handle shortcodes, etc.
        $content = apply_filters('the_content', $content);
        
        // Format output based on format type
        if ($format === 'html') {
            // Get categories and tags
            $categories = get_the_category();
            $tags = get_the_tags();
            
            $cat_list = '';
            if (!empty($categories)) {
                $cat_list = '<div class="categories">Categories: ';
                foreach ($categories as $category) {
                    $cat_list .= $category->name . ', ';
                }
                $cat_list = rtrim($cat_list, ', ');
                $cat_list .= '</div>';
            }
            
            $tag_list = '';
            if (!empty($tags)) {
                $tag_list = '<div class="tags">Tags: ';
                foreach ($tags as $tag) {
                    $tag_list .= $tag->name . ', ';
                }
                $tag_list = rtrim($tag_list, ', ');
                $tag_list .= '</div>';
            }
            
            // Create HTML file
            $html_content = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>{$title}</h1>
    <div class="meta">
        Published on {$date} by {$author}
    </div>
    <div class="content">
        {$content}
    </div>
    {$cat_list}
    {$tag_list}
</body>
</html>
HTML;
            file_put_contents($file, $html_content);
        } else {
            // Create text file with minimal formatting
            $txt_content = "{$title}\n\n";
            $txt_content .= "Published on {$date} by {$author}\n\n";
            $txt_content .= strip_tags($content);
            file_put_contents($file, $txt_content);
        }
        
        set_time_limit(30);
        $count++;
        echo "Exported: {$title} ({$post_type})\n";
    endwhile;
    
    echo "\nExport complete! {$count} items exported to '{$folder}/' directory.\n";
else :
    echo "No content found to export.\n";
endif;

wp_reset_postdata();
?>