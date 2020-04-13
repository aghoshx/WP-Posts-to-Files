<?php
/* Load WordPress */
define('WP_USE_THEMES', false);
require ('../wp-blog-header.php');

/* Create a folder based on the domain name */
$folder = str_replace("http://","", get_bloginfo('url'));
$folder = str_replace("https://","", $folder);
mkdir($folder);

/* Get all the posts and pages */
$new_posts = new WP_Query(array(
  'post_type' => array('post', 'page'),
  'posts_per_page' => -1
));
if ( $new_posts->have_posts() ) : while ( $new_posts->have_posts() ) : $new_posts->the_post();
  $file = $folder.'/'.$folder.'-'.$post->post_name.'.txt';
  $title =  '<h1>'.get_the_title().'</h1>';
  $content = $title.$post->post_content;
  file_put_contents($file, $content);
  set_time_limit(20);
  echo get_the_title().'
';
endwhile; endif; 
?>