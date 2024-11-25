<?php



/**--------------------------------------------- WEBSITE SPEED OPTIMIZATION ------------------------------------------------------------------**/


/* Convert website images to webp */
add_filter( 'wp_handle_upload', 'wpturbo_handle_upload_convert_to_webp' );

function wpturbo_handle_upload_convert_to_webp( $upload ) {
    if ( $upload['type'] == 'image/jpeg' || $upload['type'] == 'image/png' || $upload['type'] == 'image/gif' ) {
        $file_path = $upload['file'];

        // Check if ImageMagick or GD is available
        if ( extension_loaded( 'imagick' ) || extension_loaded( 'gd' ) ) {
            $image_editor = wp_get_image_editor( $file_path );
            if ( ! is_wp_error( $image_editor ) ) {
                $file_info = pathinfo( $file_path );
                $dirname   = $file_info['dirname'];
                $filename  = $file_info['filename'];

                // Create a new file path for the WebP image
                $new_file_path = $dirname . '/' . $filename . '.webp';

                // Attempt to save the image in WebP format
                $saved_image = $image_editor->save( $new_file_path, 'image/webp' );
                if ( ! is_wp_error( $saved_image ) && file_exists( $saved_image['path'] ) ) {
                    // Success: replace the uploaded image with the WebP image
                    $upload['file'] = $saved_image['path'];
                    $upload['url']  = str_replace( basename( $upload['url'] ), basename( $saved_image['path'] ), $upload['url'] );
                    $upload['type'] = 'image/webp';

                    // Optionally remove the original image
                    @unlink( $file_path );
                }
            }
        }
    }

    return $upload;
}



/**---------------------------------------------------------------------------------------------------------------**/



/* Ensure Webfont is Loaded  */
function custom_font_display( $current_value, $font_family, $data ) {
	return 'swap';
}
add_filter( 'font_display', 'custom_font_display', 10, 3 );



/**---------------------------------------------------------------------------------------------------------------**/


/* Fix Website Explicit Width and Height */
add_filter( 'the_content', 'add_image_dimensions' );

function add_image_dimensions( $content ) {

    preg_match_all( '/<img[^>]+>/i', $content, $images);

    if (count($images) < 1)
        return $content;

    foreach ($images[0] as $image) {
        preg_match_all( '/(alt|title|src|width|class|id|height)=("[^"]*")/i', $image, $img );

        if ( !in_array( 'src', $img[1] ) )
            continue;

        if ( !in_array( 'width', $img[1] ) || !in_array( 'height', $img[1] ) ) {
            $src = $img[2][ array_search('src', $img[1]) ];
            $alt = in_array( 'alt', $img[1] ) ? ' alt=' . $img[2][ array_search('alt', $img[1]) ] : '';
            $title = in_array( 'title', $img[1] ) ? ' title=' . $img[2][ array_search('title', $img[1]) ] : '';
            $class = in_array( 'class', $img[1] ) ? ' class=' . $img[2][ array_search('class', $img[1]) ] : '';
            $id = in_array( 'id', $img[1] ) ? ' id=' . $img[2][ array_search('id', $img[1]) ] : '';
            list( $width, $height, $type, $attr ) = getimagesize( str_replace( "\"", "" , $src ) );

            $image_tag = sprintf( '<img src=%s%s%s%s%s width="%d" height="%d" />', $src, $alt, $title, $class, $id, $width, $height );
            $content = str_replace($image, $image_tag, $content);
        }
    }

    return $content;
}



/**---------------------------------------------------------------------------------------------------------------**/



/**
 * We will Dequeue the jQuery UI script as example.
 *
 * Hooked to the wp_print_scripts action, with a late priority (99),
 * so that it is after the script was enqueued.
 */
function wp_remove_scripts() {
    // check if user is admin
     if (current_user_can( 'update_core' )) {
                return;
            }
     else {
        // Check for the page you want to target
        if ( is_page( 'homepage' ) ) {
            // Remove Scripts
      wp_dequeue_style( 'jquery-ui-core' );
         }
     }
    }
    add_action( 'wp_enqueue_scripts', 'wp_remove_scripts', 99 );



/**---------------------------------------------------------------------------------------------------------------**/


/**
 * Remove font awesome and icomoom
 */
function disable_avada_fonts() {
    // Deregister Font Awesome solid and regular fonts
    wp_dequeue_style('avada-fontawesome');
    wp_deregister_style('avada-fontawesome');
    
    // Deregister Avada Icomoon fonts
    wp_dequeue_style('avada-icomoon');
    wp_deregister_style('avada-icomoon');
}
add_action('wp_enqueue_scripts', 'disable_avada_fonts', 20);



/**---------------------------------------------------------------------------------------------------------------**/


/**
 * Remove google fonts on mobile responsive
 */
function remove_all_google_fonts() {
    global $wp_styles;

    // Loop through all registered styles
    foreach ($wp_styles->registered as $handle => $style) {
        // Check if the style is loading from Google Fonts
        if (strpos($style->src, 'fonts.gstatic.com') !== false) {
            wp_dequeue_style($handle); // Remove the style from the queue
            wp_deregister_style($handle); // Deregister it completely
        }
    }
}
add_action('wp_enqueue_scripts', 'remove_all_google_fonts', 20);



/**---------------------------------------------------------------------------------------------------------------**/


/**
 * Remove google fonts
 */
function disable_google_fonts() {
	return false;
}
add_filter( 'print_google_fonts', 'disable_google_fonts' );



/**---------------------------------------------------------------------------------------------------------------**/



/* Disable lazy loading for lcp image 
function wphelp_no_lazy_load_id( $value, $image, $context ) {
    if ( 'the_content' === $context ) {
    $image_url = wp_get_attachment_image_url( 10576, 'large' );
    if ( false !== strpos( $image, ' src='' . $image_url . '' )) {
    return false;
    }
    }
    return $value;
    }
    add_filter( 'wp_img_tag_add_loading_attr', 'wphelp_no_lazy_load_id', 10576, 10576 );
*/


/**
 * Remove unuse javascript
 */
function wp_remove_scripts() {
    // check if user is admin
     if (current_user_can( 'update_core' )) {
                return;
            }
     else {
        // Check for the page you want to target
        if ( is_page( 'homepage' ) ) {
            // Remove Scripts
      wp_dequeue_style( 'jquery-ui-core' );
         }
     }
    }
    add_action( 'wp_enqueue_scripts', 'wp_remove_scripts', 99 );



