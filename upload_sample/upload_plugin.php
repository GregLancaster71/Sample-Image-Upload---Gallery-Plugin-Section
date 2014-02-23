<?php
/*
Plugin Name: Testing Pagination Options in WP
Plugin URI: http://www.private-partners.com/
Description: For JD Review
Author URI: http://www.private-partners.com
*/

register_activation_hook( __FILE__, 'page_creation' );
add_filter('query_vars', 'uploader_query_var', 10, 1 );
add_action('init', 'rewrite_rules');
add_filter('parse_request', 'uploader_parse_request', 10);
add_filter('parse_request', 'uploader_gallery_parse', 10);
add_filter('parse_request', 'uploader_single_parse', 10);
add_filter('parse_request', 'delete_attachment', 10);
add_action('wp_head', 'drop_header_code');




function drop_header_code()
{ ?>
	<script type="text/javascript" src="<?php echo plugins_url('/js/holder.js', __FILE__); ?>"></script>
	<script type="text/javascript" src="<?php echo plugins_url('/js/fileinput.min.js', __FILE__); ?>"></script>
	<link href="<?php echo plugins_url('/css/style.css', __FILE__); ?>" rel="stylesheet">
    <link href="<?php echo plugins_url('/css/fileinput.css', __FILE__); ?>" rel="stylesheet">
<?php }



function page_creation() {
    $uploaderPage = get_option('uploader_page');
    if ((!$uploaderPage) || (!get_post($uploaderPage))) {
        
    $uploaderPage_Info = array(
          'post_name' => 'uploadsby',
          'post_content' => '',
          'post_title' => 'Upload Page Title',
          'post_status' => 'publish',
          'post_type' => 'page'
          );        
        
    $createUploaderPage = wp_insert_post( $uploaderPage_Info, true );
    update_option('uploader_page', $createUploaderPage );
        
    }

}

function uploader_query_var($vars) {
    $vars[] = 'uploadsby';
    $vars[] = 'photo-gallery';
    $vars[] = 'photo-single';
    $vars[] = 'pg';
    return $vars;
}

function rewrite_rules() {
    add_rewrite_tag('%uploadsby%', '([^/]+)');
    add_rewrite_rule('uploadsby/([^/]+)$', 'index.php?uploadsby=$matches[1]', 'top');
    add_rewrite_rule('uploadsby/([^/]+)/photo-single/([0-9]+)/?', 'index.php?uploadsby=$matches[1]&photo-single=$matches[2]', 'bottom');
    add_rewrite_rule('uploadsby/([^/]+)/photo-gallery/?$', 'index.php?uploadsby=$matches[1]&photo-gallery=photo-gallery', 'bottom');
}

function uploader_parse_request($wp) {
    if (!is_admin()) {
        if (isset($wp->query_vars['uploadsby']) && $wp->query_vars['uploadsby']) {
            $uploader = get_user_by('login', $wp->query_vars['uploadsby']);
                if (false !== $uploader) {
                    global $_uploads_by;
                    $_uploads_by = $uploader;
                    $wp->query_vars['page_id'] = uploader_page_id();
                    add_filter('the_content', 'upload_content_filter', 0, 2);
                } else {
                    $wp->query_vars['error'] = '404';
            }
        }
    }
}


function uploader_gallery_parse($wp) {
    if (!is_admin()) {
        if (isset($wp->query_vars['photo-gallery'])) { 
        add_filter('the_content', 'uploader_gallery_content', 0, 2);
        }
    }
}

function uploader_single_parse($wp) {
    if (!is_admin()) {
        if (isset($wp->query_vars['photo-single'])) { 
        add_filter('the_content', 'uploader_single_content', 0, 2);
        }
    }
}

function delete_attachment($wp) {
    if (!is_admin()) {
        if ((isset($wp->query_vars['delete'])) && (isset($wp->query_vars['photo-single']))) {
        $deleteID = get_query_var('photo-single');
        wp_delete_attachment($deleteID, false);
        }
    }
}


function upload_content_filter($content, $id = false) {
    global $_uploads_by;
        if (( get_the_ID() == uploader_page_id()) && isset($_uploads_by)) {
        ob_start();
        include_once(ABSPATH . '/wp-content/plugins/upload_sample/sample.php');
        return apply_filters('upload_content_filter_output', ob_get_clean(), $_uploads_by);
        }
    return $content;
}

function uploader_gallery_content($content, $id = false) {
    global $_uploads_by;
        ob_start();
        include_once(ABSPATH . '/wp-content/plugins/upload_sample/gallery.php');
        return apply_filters('uploader_gallery_content_output', ob_get_clean(), $_uploads_by);
    return $content;
}

function uploader_single_content($content, $id = false) {
    global $_uploads_by;
        ob_start();
        include_once(ABSPATH . '/wp-content/plugins/upload_sample/gallery-single.php');
        return apply_filters('uploader_single_content_output', ob_get_clean(), $_uploads_by);
    return $content;
}


function uploader_page_id() {
    static $page_id;
        if (isset($page_id)) {
            return $page_id;
            }
        $page_id = get_option('uploader_page');
    return $page_id;
}


function insert_gallery_attachment($file_handler, $post_id, $setthumb = 'true')
{
    if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) {
        return __return_false();
    }
    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    require_once(ABSPATH . "wp-admin" . '/includes/media.php');
    $attach_id = media_handle_upload($file_handler, $post_id);
    $attached_file = get_attached_file( $attach_id, false );
    $attachment_metadata = wp_generate_attachment_metadata( $attach_id, $attached_file );
    wp_update_attachment_metadata( $attach_id, $attachment_metadata );
    if ($setthumb)
        update_post_meta($post_id, '_thumbnail_id', $attach_id);
    return $attach_id;
}



function query_user_uploads($attr, $attachID = null) {
    global $wpdb, $current_user;
    get_currentuserinfo();
    

$wpPosts = $wpdb->prefix.'posts';
$wpPostMeta = $wpdb->prefix.'postmeta';    
    
$requestingUser = $current_user->ID;
$custom_sql = null;
    
if ($attr == 'first') {
    $custom_sql = " ORDER BY p.post_date ASC LIMIT 1";
} elseif ($attr == 'last') {
    $custom_sql = " ORDER BY p.post_date DESC LIMIT 1";
} elseif ($attr == 'all') {
    $custom_sql = " ORDER BY p.post_date DESC";
} elseif ($attr == 'single') {
    $custom_sql = " AND p.ID = {$attachID} ORDER BY p.post_date DESC LIMIT 1";
}

    

$newQuery = 
$wpdb->get_results(
    $wpdb->prepare("
    SELECT 
        p.ID,
        p.post_date,
        p.guid,
        m1.meta_value AS user_title, 
        m2.meta_value AS user_desc,
        m3.meta_key
    FROM 
        $wpPosts p
    LEFT OUTER JOIN 
        $wpPostMeta m1 
            ON p.ID = m1.post_id
            AND m1.meta_key = 'user_title'
    LEFT OUTER JOIN 
        $wpPostMeta m2 
            ON p.ID = m2.post_id
            AND m2.meta_key = 'user_desc'
    LEFT OUTER JOIN 
        $wpPostMeta m3
            ON p.ID = m3.post_id
    WHERE
        p.post_type = %s
    AND 
        p.post_author = %d
    AND
        m3.meta_key = %s
    {$custom_sql}"
    , 'attachment'
    , $requestingUser
    , 'user_upload'
    ));

    $count = $wpdb->num_rows;
    
    if ($attr == 'first' || $attr == 'last' ) 
        foreach ($newQuery as $photo) {
            $photoInfo['id'] = $photo->ID;
            $photoInfo['date'] = date('M jS, Y', strtotime($photo->post_date));
            $photoInfo['path'] = $photo->guid;
        return $photoInfo;
    }
        
    elseif ($attr == 'single')
        foreach ($newQuery as $photo) {
            $photoInfo['id'] = $photo->ID;
            $photoInfo['date_taken'] = date('M jS, Y', strtotime($photo->post_date));
            $photoInfo['user_title'] = $photo->user_title;
            $photoInfo['path'] = $photo->guid;
        return $photoInfo;
    }

    elseif ($attr == 'all')
        return $newQuery;
        
    elseif ($attr == 'count')
        return $count;
    
    else
        return 'options are "first", "last", "all", or "count"';
    
}


function locate_buttons($id, $array) {
   foreach ($array as $key => $val) {
       if ($val->ID === $id) {
          $current = $key;
           if (isset($array[$current+1]))
               $next = $array[$key+1];
            else
               $next = $array[$key];
               
           if (isset($array[$current-1]))
               $prev = $array[$key-1];
            else
               $prev = $array[$key];
           
           $button['next'] = $next;
           $button['prev'] = $prev;
           $button['current'] = $array[$key];
           return $button;
       }
   }
   return null;
}



function photo_gallery_paginated($ppp) {
    global $wpdb, $current_user;
    get_currentuserinfo();
    

$wpPosts = $wpdb->prefix.'posts';
$wpPostMeta = $wpdb->prefix.'postmeta';    
    
$requestingUser = $current_user->ID;
    
    
    $pg = (get_query_var('pg')) ? get_query_var('pg') : 1;
    $offset = ($pg - 1)*$ppp;

$galleryQuery = 
$wpdb->get_results(
    $wpdb->prepare("
    SELECT SQL_CALC_FOUND_ROWS
        p.ID AS attachment_id,
        p.post_date AS upload_date,
        m1.meta_value AS user_title, 
        m2.meta_value AS user_desc,
        m3.meta_value AS date_taken,
        m4.meta_key
    FROM 
        $wpPosts p
    LEFT OUTER JOIN 
        $wpPostMeta m1 
            ON p.ID = m1.post_id
            AND m1.meta_key = 'user_title'
    LEFT OUTER JOIN 
        $wpPostMeta m2 
            ON p.ID = m2.post_id
            AND m2.meta_key = 'user_desc'
    LEFT OUTER JOIN 
        $wpPostMeta m3
            ON p.ID = m3.post_id
            AND m3.meta_key = 'date_taken'
    LEFT OUTER JOIN 
        $wpPostMeta m4
            ON p.ID = m4.post_id
    WHERE
        p.post_type = %s
    AND 
        p.post_author = %d
    AND
        m4.meta_key = %s
    ORDER BY
        p.post_date DESC
    LIMIT 
        %d, %d"
    , 'attachment'
    , $requestingUser
    , 'user_upload'
    , $offset
    , $ppp
    ));
    
    $sql_posts_total = $wpdb->get_var( "SELECT FOUND_ROWS();" );
    $max_num_pages = ceil($sql_posts_total / $ppp);

    
    $galleryData['array'] = $galleryQuery;
    $galleryData['sql_posts_total'] = $sql_posts_total;
    $galleryData['max_num_pages'] = $max_num_pages;
    
    return $galleryData;


    
}