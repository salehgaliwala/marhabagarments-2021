<?php

/**
 * Include Theme Customizer.
 *
 * @since v1.0
 */
$theme_customizer = get_template_directory() . '/inc/customizer.php';
if ( is_readable( $theme_customizer ) ) {
	require_once $theme_customizer;
}


/**
 * Include Support for wordpress.com-specific functions.
 * 
 * @since v1.0
 */
$theme_wordpresscom = get_template_directory() . '/inc/wordpresscom.php';
if ( is_readable( $theme_wordpresscom ) ) {
	require_once $theme_wordpresscom;
}


/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since v1.0
 */
if ( ! isset( $content_width ) ) {
	$content_width = 800;
}


/**
 * General Theme Settings.
 *
 * @since v1.0
 */
if ( ! function_exists( 'marhaba_setup_theme' ) ) :
	function marhaba_setup_theme() {
		// Make theme available for translation: Translations can be filed in the /languages/ directory.
		load_theme_textdomain( 'marhaba', get_template_directory() . '/languages' );

		// Theme Support.
		add_theme_support( 'title-tag' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'script',
				'style',
				'navigation-widgets',
			)
		);

		// Add support for Block Styles.
		add_theme_support( 'wp-block-styles' );
		// Add support for full and wide alignment.
		add_theme_support( 'align-wide' );
		// Add support for editor styles.
		add_theme_support( 'editor-styles' );
		// Enqueue editor styles.
		add_editor_style( 'style-editor.css' );

		// Default Attachment Display Settings.
		update_option( 'image_default_align', 'none' );
		update_option( 'image_default_link_type', 'none' );
		update_option( 'image_default_size', 'large' );

		// Custom CSS-Styles of Wordpress Gallery.
		add_filter( 'use_default_gallery_style', '__return_false' );
	}
	add_action( 'after_setup_theme', 'marhaba_setup_theme' );

	// Disable Block Directory: https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/filters/editor-filters.md#block-directory
	remove_action( 'enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets' );
	remove_action( 'enqueue_block_editor_assets', 'gutenberg_enqueue_block_editor_assets_block_directory' );
endif;


/**
 * Fire the wp_body_open action.
 *
 * Added for backwards compatibility to support pre 5.2.0 WordPress versions.
 *
 * @since v2.2
 */
if ( ! function_exists( 'wp_body_open' ) ) :
	function wp_body_open() {
		/**
		 * Triggered after the opening <body> tag.
		 *
		 * @since v2.2
		 */
		do_action( 'wp_body_open' );
	}
endif;


/**
 * Add new User fields to Userprofile.
 *
 * @since v1.0
 */
if ( ! function_exists( 'marhaba_add_user_fields' ) ) :
	function marhaba_add_user_fields( $fields ) {
		// Add new fields.
		$fields['facebook_profile'] = 'Facebook URL';
		$fields['twitter_profile']  = 'Twitter URL';
		$fields['linkedin_profile'] = 'LinkedIn URL';
		$fields['xing_profile']     = 'Xing URL';
		$fields['github_profile']   = 'GitHub URL';

		return $fields;
	}
	add_filter( 'user_contactmethods', 'marhaba_add_user_fields' ); // get_user_meta( $user->ID, 'facebook_profile', true );
endif;


/**
 * Test if a page is a blog page.
 * if ( is_blog() ) { ... }
 *
 * @since v1.0
 */
function is_blog() {
	global $post;
	$posttype = get_post_type( $post );

	return ( ( is_archive() || is_author() || is_category() || is_home() || is_single() || ( is_tag() && ( 'post' === $posttype ) ) ) ? true : false );
}


/**
 * Disable comments for Media (Image-Post, Jetpack-Carousel, etc.)
 *
 * @since v1.0
 */
function marhaba_filter_media_comment_status( $open, $post_id = null ) {
	$media_post = get_post( $post_id );
	if ( 'attachment' === $media_post->post_type ) {
		return false;
	}
	return $open;
}
add_filter( 'comments_open', 'marhaba_filter_media_comment_status', 10, 2 );


/**
 * Style Edit buttons as badges: https://getbootstrap.com/docs/5.0/components/badge
 *
 * @since v1.0
 */
function marhaba_custom_edit_post_link( $output ) {
	return str_replace( 'class="post-edit-link"', 'class="post-edit-link badge badge-secondary"', $output );
}
add_filter( 'edit_post_link', 'marhaba_custom_edit_post_link' );

function marhaba_custom_edit_comment_link( $output ) {
	return str_replace( 'class="comment-edit-link"', 'class="comment-edit-link badge badge-secondary"', $output );
}
add_filter( 'edit_comment_link', 'marhaba_custom_edit_comment_link' );


/**
 * Responsive oEmbed filter: https://getbootstrap.com/docs/5.0/helpers/ratio
 *
 * @since v1.0
 */
function marhaba_oembed_filter( $html ) {
	return '<div class="ratio ratio-16x9">' . $html . '</div>';
}
add_filter( 'embed_oembed_html', 'marhaba_oembed_filter', 10, 4 );


if ( ! function_exists( 'marhaba_content_nav' ) ) :
	/**
	 * Display a navigation to next/previous pages when applicable.
	 *
	 * @since v1.0
	 */
	function marhaba_content_nav( $nav_id ) {
		global $wp_query;

		if ( $wp_query->max_num_pages > 1 ) :
	?>
<div id="<?php echo esc_attr( $nav_id ); ?>" class="d-flex mb-4 justify-content-between">
    <div><?php next_posts_link( '<span aria-hidden="true">&larr;</span> ' . esc_html__( 'Older posts', 'marhaba' ) ); ?>
    </div>
    <div>
        <?php previous_posts_link( esc_html__( 'Newer posts', 'marhaba' ) . ' <span aria-hidden="true">&rarr;</span>' ); ?>
    </div>
</div><!-- /.d-flex -->
<?php
		else :
			echo '<div class="clearfix"></div>';
		endif;
	}

	// Add Class.
	function posts_link_attributes() {
		return 'class="btn btn-secondary btn-lg"';
	}
	add_filter( 'next_posts_link_attributes', 'posts_link_attributes' );
	add_filter( 'previous_posts_link_attributes', 'posts_link_attributes' );
endif;


/**
 * Init Widget areas in Sidebar.
 *
 * @since v1.0
 */
function marhaba_widgets_init() {
	// Area 1.
	register_sidebar(
		array(
			'name'          => 'Primary Widget Area (Sidebar)',
			'id'            => 'primary_widget_area',
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	// Area 2.
	register_sidebar(
		array(
			'name'          => 'Secondary Widget Area (Header Navigation)',
			'id'            => 'secondary_widget_area',
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	// Area 3.
	register_sidebar(
		array(
			'name'          => 'Third Widget Area (Footer)',
			'id'            => 'third_widget_area',
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'marhaba_widgets_init' );


if ( ! function_exists( 'marhaba_article_posted_on' ) ) :
	/**
	 * "Theme posted on" pattern.
	 *
	 * @since v1.0
	 */
	function marhaba_article_posted_on() {
		printf(
			wp_kses_post( __( '<span class="sep">Posted on </span><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a><span class="by-author"> <span class="sep"> by </span> <span class="author-meta vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'marhaba' ) ),
			esc_url( get_the_permalink() ),
			esc_attr( get_the_date() . ' - ' . get_the_time() ),
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() . ' - ' . get_the_time() ),
			esc_url( get_author_posts_url( (int) get_the_author_meta( 'ID' ) ) ),
			sprintf( esc_attr__( 'View all posts by %s', 'marhaba' ), get_the_author() ),
			get_the_author()
		);
	}
endif;


/**
 * Template for Password protected post form.
 *
 * @since v1.0
 */
function marhaba_password_form() {
	global $post;
	$label = 'pwbox-' . ( empty( $post->ID ) ? rand() : $post->ID );

	$output = '<div class="row">';
		$output .= '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">';
		$output .= '<h4 class="col-md-12 alert alert-warning">' . esc_html__( 'This content is password protected. To view it please enter your password below.', 'marhaba' ) . '</h4>';
			$output .= '<div class="col-md-6">';
				$output .= '<div class="input-group">';
					$output .= '<input type="password" name="post_password" id="' . esc_attr( $label ) . '" placeholder="' . esc_attr__( 'Password', 'marhaba' ) . '" class="form-control" />';
					$output .= '<div class="input-group-append"><input type="submit" name="submit" class="btn btn-primary" value="' . esc_attr__( 'Submit', 'marhaba' ) . '" /></div>';
				$output .= '</div><!-- /.input-group -->';
			$output .= '</div><!-- /.col -->';
		$output .= '</form>';
	$output .= '</div><!-- /.row -->';
	return $output;
}
add_filter( 'the_password_form', 'marhaba_password_form' );


if ( ! function_exists( 'marhaba_comment' ) ) :
	/**
	 * Style Reply link.
	 *
	 * @since v1.0
	 */
	function marhaba_replace_reply_link_class( $class ) {
		return str_replace( "class='comment-reply-link", "class='comment-reply-link btn btn-outline-secondary", $class );
	}
	add_filter( 'comment_reply_link', 'marhaba_replace_reply_link_class' );

	/**
	 * Template for comments and pingbacks:
	 * add function to comments.php ... wp_list_comments( array( 'callback' => 'marhaba_comment' ) );
	 *
	 * @since v1.0
	 */
	function marhaba_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		switch ( $comment->comment_type ) :
			case 'pingback':
			case 'trackback':
	?>
<li class="post pingback">
    <p><?php esc_html_e( 'Pingback:', 'marhaba' ); ?>
        <?php comment_author_link(); ?><?php edit_comment_link( esc_html__( 'Edit', 'marhaba' ), '<span class="edit-link">', '</span>' ); ?>
    </p>
    <?php
				break;
			default:
	?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
    <article id="comment-<?php comment_ID(); ?>" class="comment">
        <footer class="comment-meta">
            <div class="comment-author vcard">
                <?php
							$avatar_size = ( '0' !== $comment->comment_parent ? 68 : 136 );
							echo get_avatar( $comment, $avatar_size );

							/* translators: 1: comment author, 2: date and time */
							printf(
								wp_kses_post( __( '%1$s, %2$s', 'marhaba' ) ),
								sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
								sprintf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
									esc_url( get_comment_link( $comment->comment_ID ) ),
									get_comment_time( 'c' ),
									/* translators: 1: date, 2: time */
									sprintf( esc_html__( '%1$s ago', 'marhaba' ), human_time_diff( (int) get_comment_time( 'U' ), current_time( 'timestamp' ) ) )
								)
							);

							edit_comment_link( esc_html__( 'Edit', 'marhaba' ), '<span class="edit-link">', '</span>' );
						?>
            </div><!-- .comment-author .vcard -->

            <?php if ( '0' === $comment->comment_approved ) : ?>
            <em
                class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'marhaba' ); ?></em>
            <br />
            <?php endif; ?>
        </footer>

        <div class="comment-content"><?php comment_text(); ?></div>

        <div class="reply">
            <?php
						comment_reply_link(
							array_merge(
								$args,
								array(
									'reply_text' => esc_html__( 'Reply', 'marhaba' ) . ' <span>&darr;</span>',
									'depth'      => $depth,
									'max_depth'  => $args['max_depth'],
								)
							)
						);
					?>
        </div><!-- /.reply -->
    </article><!-- /#comment-## -->
    <?php
				break;
		endswitch;
	}

	/**
	 * Custom Comment form.
	 *
	 * @since v1.0
	 * @since v1.1: Added 'submit_button' and 'submit_field'
	 * @since v2.0.2: Added '$consent' and 'cookies'
	 */
	function marhaba_custom_commentform( $args = array(), $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = get_the_ID();
		}

		$commenter     = wp_get_current_commenter();
		$user          = wp_get_current_user();
		$user_identity = $user->exists() ? $user->display_name : '';

		$args = wp_parse_args( $args );

		$req      = get_option( 'require_name_email' );
		$aria_req = ( $req ? " aria-required='true' required" : '' );
		$consent  = ( empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"' );
		$fields   = array(
			'author'  => '<div class="form-floating mb-3">
							<input type="text" id="author" name="author" class="form-control" value="' . esc_attr( $commenter['comment_author'] ) . '" placeholder="' . esc_html__( 'Name', 'marhaba' ) . ( $req ? '*' : '' ) . '"' . $aria_req . ' />
							<label for="author">' . esc_html__( 'Name', 'marhaba' ) . ( $req ? '*' : '' ) . '</label>
						</div>',
			'email'   => '<div class="form-floating mb-3">
							<input type="email" id="email" name="email" class="form-control" value="' . esc_attr( $commenter['comment_author_email'] ) . '" placeholder="' . esc_html__( 'Email', 'marhaba' ) . ( $req ? '*' : '' ) . '"' . $aria_req . ' />
							<label for="email">' . esc_html__( 'Email', 'marhaba' ) . ( $req ? '*' : '' ) . '</label>
						</div>',
			'url'     => '',
			'cookies' => '<p class="form-check mb-3 comment-form-cookies-consent">
							<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" class="form-check-input" type="checkbox" value="yes"' . $consent . ' />
							<label class="form-check-label" for="wp-comment-cookies-consent">' . esc_html__( 'Save my name, email, and website in this browser for the next time I comment.', 'marhaba' ) . '</label>
						</p>',
		);

		$defaults = array(
			'fields'               => apply_filters( 'comment_form_default_fields', $fields ),
			'comment_field'        => '<div class="form-floating mb-3">
											<textarea id="comment" name="comment" class="form-control" aria-required="true" required placeholder="' . esc_attr__( 'Comment', 'marhaba' ) . ( $req ? '*' : '' ) . '"></textarea>
											<label for="comment">' . esc_html__( 'Comment', 'marhaba' ) . '</label>
										</div>',
			/** This filter is documented in wp-includes/link-template.php */
			'must_log_in'          => '<p class="must-log-in">' . sprintf( wp_kses_post( __( 'You must be <a href="%s">logged in</a> to post a comment.', 'marhaba' ) ), wp_login_url( apply_filters( 'the_permalink', get_the_permalink( get_the_ID() ) ) ) ) . '</p>',
			/** This filter is documented in wp-includes/link-template.php */
			'logged_in_as'         => '<p class="logged-in-as">' . sprintf( wp_kses_post( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'marhaba' ) ), get_edit_user_link(), $user->display_name, wp_logout_url( apply_filters( 'the_permalink', get_the_permalink( get_the_ID() ) ) ) ) . '</p>',
			'comment_notes_before' => '<p class="small comment-notes">' . esc_html__( 'Your Email address will not be published.', 'marhaba' ) . '</p>',
			'comment_notes_after'  => '',
			'id_form'              => 'commentform',
			'id_submit'            => 'submit',
			'class_submit'         => 'btn btn-primary',
			'name_submit'          => 'submit',
			'title_reply'          => '',
			'title_reply_to'       => esc_html__( 'Leave a Reply to %s', 'marhaba' ),
			'cancel_reply_link'    => esc_html__( 'Cancel reply', 'marhaba' ),
			'label_submit'         => esc_html__( 'Post Comment', 'marhaba' ),
			'submit_button'        => '<input type="submit" id="%2$s" name="%1$s" class="%3$s" value="%4$s" />',
			'submit_field'         => '<div class="form-submit">%1$s %2$s</div>',
			'format'               => 'html5',
		);

		return $defaults;
	}
	add_filter( 'comment_form_defaults', 'marhaba_custom_commentform' );

endif;


/**
 * Nav menus.
 *
 * @since v1.0
 */
if ( function_exists( 'register_nav_menus' ) ) {
	register_nav_menus(
		array(
			'main-menu'   => 'Main Navigation Menu',
			'footer-menu' => 'Footer Menu',
		)
	);
}

// Custom Nav Walker: wp_bootstrap_navwalker().
$custom_walker = get_template_directory() . '/inc/wp_bootstrap_navwalker.php';
if ( is_readable( $custom_walker ) ) {
	require_once $custom_walker;
}

$custom_walker_footer = get_template_directory() . '/inc/wp_bootstrap_navwalker_footer.php';
if ( is_readable( $custom_walker_footer ) ) {
	require_once $custom_walker_footer;
}


/**
 * Loading All CSS Stylesheets and Javascript Files.
 *
 * @since v1.0
 */
function marhaba_scripts_loader() {
	$theme_version = wp_get_theme()->get( 'Version' );

	// 1. Styles.
	wp_enqueue_style( 'style', get_template_directory_uri() . '/style.css', array(), $theme_version, 'all' );
	wp_enqueue_style( 'main', get_template_directory_uri() . '/assets/css/main.css', array(), $theme_version, 'all' ); // main.scss: Compiled Framework source + custom styles.
	wp_enqueue_style( 'datatable-css', get_template_directory_uri() . '/assets/css/datatables.min.css', array(), $theme_version, 'all' ); 

	if ( is_rtl() ) {
		wp_enqueue_style( 'rtl', get_template_directory_uri() . '/assets/css/rtl.css', array(), $theme_version, 'all' );
	}

	// 2. Scripts.
	wp_enqueue_script( 'mainjs', get_template_directory_uri() . '/assets/js/main.bundle.js',  array(), '1.0.0', true );
	wp_enqueue_script( 'datatable-js', get_template_directory_uri() . '/assets/js/datatables.min.js', array(), '1.0.0', true );
	wp_enqueue_script( 'button-js', get_template_directory_uri() . '/assets/js/dataTables.buttons.min.js', array(), '1.0.0', true );
	wp_enqueue_script( 'button-jszip', get_template_directory_uri() . '/assets/js/jszip.min.js', array(), '1.0.0', true );
	wp_enqueue_script( 'button-pdfmake', get_template_directory_uri() . '/assets/js/pdfmake.min.js', array(), '1.0.0', true );
	wp_enqueue_script( 'button-vfs_fonts', get_template_directory_uri() . '/assets/js/vfs_fonts.js', array(), '1.0.0', true );
	wp_enqueue_script( 'button-html5', get_template_directory_uri() . '/assets/js/buttons.html5.min.js', array(), '1.0.0', true );
	wp_enqueue_script( 'button-printjs', get_template_directory_uri() . '/assets/js/buttons.print.min.js', array(), '1.0.0', true );
	wp_enqueue_script( 'dymo-framework', get_template_directory_uri() . '/assets/js/DYMO.Label.Framework.2.0.2.js', array(), '1.0.0', true );
	wp_enqueue_script( 'dymo-print-js', get_template_directory_uri() . '/assets/js/dymo_print_1.9.js', array(), '1.0.0', true );
	

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	 wp_localize_script( 'ajax-script', 'my_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}
add_action( 'wp_enqueue_scripts', 'marhaba_scripts_loader' );

function save_posted_data( $posted_data ) {
	 global $wp_session;
	 $submission = WPCF7_Submission::get_instance();
     $invalid_fields = $submission->get_invalid_fields();

	
	   if( $_POST['_wpcf7_groups_count']['garments'] > 0 ){
		    
			 $max = $_POST['_wpcf7_groups_count']['garments'];
	  		 for($i=1;$i<=$max;$i++){	  
				$data_3 = $posted_data['lpo_number'];  
				$data_4 = $posted_data['ref_num__'.$i];  
				if(empty($data_3) or empty($data_4))
					return;
			   }
	   }
	   else
	   {
		   return;
	   }
	  $series = get_option('_order_series');	
	  $barcode_series  = get_option('_barcode_series');
	  $series = $series + 1;
	  $barcode_series = $barcode_series +1;
	
       $args = array(
         'post_type' => 'orders',
         'post_status'=>'draft',
         'post_title'=>'N'.$series  ,
      
       );
       $post_id = wp_insert_post($args);
	   $wp_session['orderid'] = 'N'.$series;
	   update_option('_order_series',$series);
	
       if(!is_wp_error($post_id)){
		 update_post_meta( $post_id,'order_series',$series);
		/* if( isset($posted_data['ref_num']) ){
           update_post_meta($post_id, 'reference_number', $posted_data['ref_num']);
         }

         if( isset($posted_data['employee_id']) ){
           update_post_meta($post_id, 'employee_id', $posted_data['employee_id']);
         }
		  if( isset($posted_data['employee_name']) ){
           update_post_meta($post_id, 'employee_name', $posted_data['employee_name']);
         }
		  if( isset($posted_data['company_name']) ){
           update_post_meta($post_id, 'company_name', $posted_data['company_name']);
         }
		  if( isset($posted_data['location']) ){
           update_post_meta($post_id, 'location', $posted_data['location']);
         }
		 if( isset($posted_data['lpo_number']) ){
           update_post_meta($post_id, 'lpo_number', $posted_data['lpo_number']);
         }
		 if( isset($posted_data['notes']) ){
           update_post_meta($post_id, 'notes', $posted_data['notes']);
         }*/
		 
	   if( $_POST['_wpcf7_groups_count']['garments'] > 0 ){
	   $max = $_POST['_wpcf7_groups_count']['garments'];
	   $printids = array();	
	   for($i=1;$i<=$max;$i++){
 			global $wpdb;     
		//	$data_1 = $posted_data['garment__'.$i][0];
		//	$data_2 = $posted_data['qty__'.$i]; 
			$data_3 = $posted_data['ref_num__'.$i]; 
			$data_4 = $posted_data['lpo_number']; 
			$data_5 = $posted_data['employee_id__'.$i]; 
			$data_6 = $posted_data['employee_name__'.$i]; 
			$data_7 = $posted_data['company_name']; 
			$data_8 = $posted_data['location__'.$i]; 
			$data_9 = $posted_data['notes__'.$i]; 	
			$lastid = $wpdb->insert_id;	
			$series = get_option('_order_series');
			$barcode="N".$series."N".$barcode_series;			
			
			$table_name = $wpdb->prefix . 'garments'; 
	
			if($posted_data['qty_shirt__'.$i] > 0)    
				$wpdb->insert($table_name, array('barcode' => $barcode ,'post_id'=>$post_id,'garment' => 'Shirt', 'qty' => $posted_data['qty_shirt__'.$i], 'ref_num' => $data_3, 'lpo_number' => $data_4, 'employee_id' => $data_5, 'employee_name' => $data_6, 'company_name' => $data_7, 'location' => $data_8, 'notes' => $data_9));
			if($posted_data['qty_trousers__'.$i] > 0)    
				$wpdb->insert($table_name, array('barcode' => $barcode ,'post_id'=>$post_id,'garment' => 'Trouser', 'qty' => $posted_data['qty_trousers__'.$i], 'ref_num' => $data_3, 'lpo_number' => $data_4, 'employee_id' => $data_5, 'employee_name' => $data_6, 'company_name' => $data_7, 'location' => $data_8, 'notes' => $data_9));
			if($posted_data['qty_waist__'.$i] > 0)    
				$wpdb->insert($table_name, array('barcode' => $barcode,'post_id'=>$post_id,'garment' => 'Waist', 'qty' => $posted_data['qty_waist__'.$i], 'ref_num' => $data_3, 'lpo_number' => $data_4, 'employee_id' => $data_5, 'employee_name' => $data_6, 'company_name' => $data_7, 'location' => $data_8, 'notes' => $data_9));
			
			if($posted_data['qty_jackets__'.$i] > 0)    
				$wpdb->insert($table_name, array('barcode' => $barcode ,'post_id'=>$post_id,'garment' => 'Jacket', 'qty' => $posted_data['qty_jackets__'.$i], 'ref_num' => $data_3, 'lpo_number' => $data_4, 'employee_id' => $data_5, 'employee_name' => $data_6, 'company_name' => $data_7, 'location' => $data_8, 'notes' => $data_9));
			
			if($posted_data['qty_aprons__'.$i] > 0)    
				$wpdb->insert($table_name, array('barcode' => $barcode ,'post_id'=>$post_id,'garment' => 'Doc Coat', 'qty' => $posted_data['qty_aprons__'.$i], 'ref_num' => $data_3, 'lpo_number' => $data_4, 'employee_id' => $data_5, 'employee_name' => $data_6, 'company_name' => $data_7, 'location' => $data_8, 'notes' => $data_9));
			
			if($posted_data['qty_skirt__'.$i] > 0)    
				$wpdb->insert($table_name, array('barcode' => $barcode ,'post_id'=>$post_id,'garment' => 'Skirt', 'qty' => $posted_data['qty_skirt__'.$i], 'ref_num' => $data_3, 'lpo_number' => $data_4, 'employee_id' => $data_5, 'employee_name' => $data_6, 'company_name' => $data_7, 'location' => $data_8, 'notes' => $data_9));
			
			if($posted_data['qty_tie__'.$i] > 0)    
				$wpdb->insert($table_name, array('barcode' => $barcode ,'post_id'=>$post_id,'garment' => 'Tie/Scarf', 'qty' => $posted_data['qty_shirt__'.$i], 'ref_num' => $data_3, 'lpo_number' => $data_4, 'employee_id' => $data_5, 'employee_name' => $data_6, 'company_name' => $data_7, 'location' => $data_8, 'notes' => $data_9));
			if($posted_data['qty_belt__'.$i] > 0)    
				$wpdb->insert($table_name, array('barcode' => $barcode ,'post_id'=>$post_id,'garment' => 'Belt', 'qty' => $posted_data['qty_belt__'.$i], 'ref_num' => $data_3, 'lpo_number' => $data_4, 'employee_id' => $data_5, 'employee_name' => $data_6, 'company_name' => $data_7, 'location' => $data_8, 'notes' => $data_9));
			if($posted_data['qty_others__'.$i] > 0)    
				$wpdb->insert($table_name, array('barcode' => $barcode ,'post_id'=>$post_id,'garment' => 'Others', 'qty' => $posted_data['qty_others__'.$i], 'ref_num' => $data_3, 'lpo_number' => $data_4, 'employee_id' => $data_5, 'employee_name' => $data_6, 'company_name' => $data_7, 'location' => $data_8, 'notes' => $data_9));
			if($posted_data['qty_tshirt__'.$i] > 0)    
				$wpdb->insert($table_name, array('barcode' => $barcode ,'post_id'=>$post_id,'garment' => 'Tshirt', 'qty' => $posted_data['qty_tshirt__'.$i], 'ref_num' => $data_3, 'lpo_number' => $data_4, 'employee_id' => $data_5, 'employee_name' => $data_6, 'company_name' => $data_7, 'location' => $data_8, 'notes' => $data_9));
			if($posted_data['qty_winter_jacket__'.$i] > 0)    
				$wpdb->insert($table_name, array('barcode' => $barcode,'post_id'=>$post_id,'garment' => 'Winter Jacket', 'qty' => $posted_data['qty_winter_jacket__'.$i], 'ref_num' => $data_3, 'lpo_number' => $data_4, 'employee_id' => $data_5, 'employee_name' => $data_6, 'company_name' => $data_7, 'location' => $data_8, 'notes' => $data_9));
				
				
				
			
			update_option('_barcode_series',$barcode_series + 1);
			$printids[] = $barcode;
			}
			$printids = implode(",",$printids);
			update_post_meta($post_id, 'barcode_batch', $printids);
			update_post_meta($postid,'print_status','false');
		}
		else	
		{
			echo _('Garments Data Missing');
			die();
		}

      //and so on ...
      return $posted_data;
     }
 }

add_filter( 'wpcf7_posted_data', 'save_posted_data' );
/**
 * Calls the class on the post edit screen
 */
function call_someClass() 
{
    return new someClass();
}
if ( is_admin() )
    add_action( 'load-post.php', 'call_someClass' );

/** 
 * The Class
 */
class someClass
{
    const LANG = 'some_textdomain';

    public function __construct()
    {
        add_action( 'add_meta_boxes', array( &$this, 'add_some_meta_box' ) );
    }

    /**
     * Adds the meta box container
     */
    public function add_some_meta_box()
    {
        add_meta_box( 
             'some_meta_box_name'
            ,__( 'Garments Data', self::LANG )
            ,array( &$this, 'render_meta_box_content' )
            ,'orders' ,
           
        );
    }


    /**
     * Render Meta Box content
     */
    public function render_meta_box_content() 
    {
        ?>
    <style>
    .garment-data td input {width:100px}
    .garment-data td {
        text-align: center
    }

    .garment-data table {
        width: 100%;
    }

	 .garment-data table td .qty{width:50px}
    </style>
	<?php
	// code to save data
		
		
	?>
    <div class='garment-data'>

        <table>
            <tr>
                <th>Barcode</th>
				<th>Ref Number</th>
				<th>LPO</th>
				<th>Employee ID</th>
				<th>Employee Name</th>
				<th>Company Name</th>				
                <th>Location</th>		
                
            </tr>
            <?php 
		global $wpdb;
		$table_name = $wpdb->prefix . 'garments';
		$results = $wpdb->get_results( "SELECT * FROM $table_name WHERE post_id = ".get_the_ID() );	
		foreach ($results as $result){
			echo "<tr data-id='".$result->id."' data-ref='".$result->ref_num."' data-lpo='".$result->lpo_number."' data-employeeid='".$result->employee_id."' data-employeename='".$result->employee_name."' data-companyname='".$result->company_name."' data-location='".$result->location."' data-garment='".$result->garment."' data-qty='".$result->qty."' data-status='".$result->status."'><td>".$result->barcode."</td><td>".$result->ref_num."</td><td>".$result->lpo_number."</td><td>".$result->employee_id."</td><td>".$result->employee_name."</td><td>".$result->company_name."</td><td>".$result->location."</td><td>".$result->garment."</td><td>".$result->qty."</td><td>".$result->status."</td><td><button class='btn btn-info btn-xs btn-edit'>Edit</button><button class='btn btn-danger btn-xs btn-delete'>Delete</button></td></tr>";
		}
		?>
        </table>
		<p><strong> Add Garments</strong> </p>
		<form action="" method="post" >
		<table>
            <tr>
                
				<th>Ref Number</th>
				<th>LPO</th>
				<th>Employee ID</th>
				<th>Employee Name</th>
				<th>Company Name</th>				
                <th>Location</th>
				<th>Notes</th>
				
                
            </tr>
			<tr>				
				<td><input type="text"  name="ref" value="" /></td>
				<td><input type="text"  name="lpo" value="" /></td>
				<td><input type="text"  name="employee_id" value="" /></td>
				<td><input type="text"  name="employee_name" value="" /></td>
				<td><input type="text"  name="company_name" value="" /></td>
				<td><input type="text"  name="location" value="" /></td>	
				<td><input type="text"  name="notes" value="" /></td>
			</tr>
			<tr>
                
				<th>Shirt</th>
				<th>Trousers</th>
				<th>Waist Coat</th>
				<th>Jackets</th>
				<th>Doc Coat</th>				
                <th>Scarf</th>
		
            </tr>
			<tr>				
				<td><input type="number" class="qty" name="qty_shirt" value="" /></td>
				<td><input type="number" class="qty" name="qty_trousers" value="" /></td>
				<td><input type="number" class="qty" name="qty_coat" value="" /></td>
				<td><input type="number" class="qty" name="qty_jacket" value="" /></td>
				<td><input type="number" class="qty" name="qty_aprons" value="" /></td>
				<td><input type="number" class="qty" name="qty_skirt" value="" /></td>
		
			</tr>
			<tr>
				<th>Tie/Scarf</th>
                <th>Belt</th>			
				<th>Tshirt</th>
				<th>Sleeves</th>
				<th>Others</th>
			</tr>	
			<tr>
				<td><input type="number" class="qty" name="qty_tie" value="" /></td>
				<td><input type="number" class="qty" name="qty_belt" value="" /></td>
				<td><input type="number" class="qty" name="qty_others" value="" /></td>
				<td><input type="number" class="qty" name="qty_tshirt" value="" /></td>
				<td><input type="number" class="qty" name="qty_winter_jacket" value="" /></td>	

			</tr>

			<tr>
			<td colspan="9"	><input type="submit" name="submit" value="Save" id="save_order" /></td>
			</tr>
			
			
			
		</table>
	</form>	
    </div>
    <script>
    jQuery(document).ready(function() {
        // On click call deleData function using action deleteData
		 jQuery("body").on("click", "#save_order", function(e) {
			  e.preventDefault();
			  jQuery.get(ajaxurl, {
                    'action': 'saveOrderData',              
					'ref':jQuery("input[name=ref]").val(),
					'lpo':jQuery("input[name=lpo]").val(),
					'post':<?php echo $_REQUEST['post'] ?>,
					'employee_id': jQuery("input[name=employee_id]").val(),
					'employee_name':jQuery("input[name=employee_name]").val(),
					'company_name':jQuery("input[name=company_name]").val(),
					'location':jQuery("input[name=location]").val(),
                    'notes': jQuery("input[name=notes]").val(),
                    'qty_shirt': jQuery("input[name=qty_shirt]").val(),
					'qty_trousers': jQuery("input[name=qty_trousers]").val(),
					'qty_coat': jQuery("input[name=qty_coat]").val(),
					'qty_jacket': jQuery("input[name=qty_jacket]").val(),
					'qty_aprons': jQuery("input[name=qty_aprons]").val(),
					'qty_skirt': jQuery("input[name=qty_skirt]").val(),
					'qty_tie': jQuery("input[name=qty_tie]").val(),
					'qty_belt': jQuery("input[name=qty_belt]").val(),
					'qty_others': jQuery("input[name=qty_others]").val(),
					'qty_tshirt': jQuery("input[name=qty_tshirt]").val(),
					'qty_winter_jacket': jQuery("input[name=qty_winter_jacket]").val(),
                   
                },
                function(msg) {
					if(msg == 'success')
					{
						location.reload();
					}
                  		
                });

		 });
        jQuery("body").on("click", ".btn-delete", function(e) {
            e.preventDefault();
            var id = jQuery(this).parents("tr").attr('data-id');
            jQuery(this).parents("tr").remove();
            jQuery.get(ajaxurl, {
                    'action': 'deleteData',
                    'id': id
                },
                function(msg) {

                });
        });

        // Post the updated value to wordpress update function
        jQuery("body").on("click", ".btn-update", function(e) {
            e.preventDefault();
            var id = jQuery(this).parents("tr").attr('data-id');

            var garment = jQuery(this).parents("tr").find("input[name='garment']").val();
            var qty = jQuery(this).parents("tr").find("input[name='qty']").val();
            var status = jQuery(this).parents("tr").find("input[name='status']").val();
			var ref = jQuery(this).parents("tr").find("input[name='ref']").val();
			var employeeid = jQuery(this).parents("tr").find("input[name='employeeid']").val();
			var lpo = jQuery(this).parents("tr").find("input[name='lpo']").val();
			var employeename = jQuery(this).parents("tr").find("input[name='employeename']").val();
			var companyname = jQuery(this).parents("tr").find("input[name='companyname']").val();
			var location = jQuery(this).parents("tr").find("input[name='location']").val();

			jQuery(this).parents("tr").find("td:eq(1)").text(ref);
			jQuery(this).parents("tr").find("td:eq(2)").text(lpo);
			jQuery(this).parents("tr").find("td:eq(3)").text(employeeid);
            jQuery(this).parents("tr").find("td:eq(4)").text(employeename);
            jQuery(this).parents("tr").find("td:eq(5)").text(companyname);
            jQuery(this).parents("tr").find("td:eq(6)").text(location);
			jQuery(this).parents("tr").find("td:eq(7)").text(garment);
			jQuery(this).parents("tr").find("td:eq(8)").text(qty);
			jQuery(this).parents("tr").find("td:eq(9)").text(status);

			jQuery(this).parents("tr").attr('data-ref', ref);
			jQuery(this).parents("tr").attr('data-lpo', lpo);
			jQuery(this).parents("tr").attr('data-employeeid', employeeid);
			jQuery(this).parents("tr").attr('data-employeename', employeename);
			jQuery(this).parents("tr").attr('data-companyname', companyname);
			jQuery(this).parents("tr").attr('data-location', location);
            jQuery(this).parents("tr").attr('data-garment', garment);
            jQuery(this).parents("tr").attr('data-qty', qty);
            jQuery(this).parents("tr").attr('data-status', status);

            jQuery(this).parents("tr").find(".btn-edit").show();
            jQuery(this).parents("tr").find(".btn-cancel").remove();
            jQuery(this).parents("tr").find(".btn-update").remove();

            var t = jQuery(this);

            jQuery.get(ajaxurl, {
                    'action': 'updateData',
                    'id': id,
					'ref':ref,
					'lpo':lpo,
					'employeeid': employeeid,
					'employeename':employeename,
					'companyname':companyname,
					'location':location,
                    'garment': garment,
                    'qty': qty,
                    'status': status
                },
                function(msg) {
                    t.parents("tr").find("td:eq(9)").html(msg);
                });
        });

    });
    // Change the columns to form elements for update
    jQuery("body").on("click", ".btn-edit", function(e) {
        e.preventDefault();
        var garment = jQuery(this).parents("tr").attr('data-garment');
        var qty = jQuery(this).parents("tr").attr('data-qty');
        var status = jQuery(this).parents("tr").attr('data-status');
        var id = jQuery(this).parents("tr").attr('data-id');
		var ref = jQuery(this).parents("tr").attr('data-ref');
		var lpo = jQuery(this).parents("tr").attr('data-lpo');
		var employeeid = jQuery(this).parents("tr").attr('data-employeeid');
		var employeename = jQuery(this).parents("tr").attr('data-employeename');
		var companyname = jQuery(this).parents("tr").attr('data-companyname');
		var location = jQuery(this).parents("tr").attr('data-location');
		

		
		jQuery(this).parents("tr").find("td:eq(1)").html('<input id="ref" name="ref" value="' +
         ref + '">');
        jQuery(this).parents("tr").find("td:eq(2)").html('<input id="lpo" name="lpo" value="' +
           lpo + '">');
		jQuery(this).parents("tr").find("td:eq(3)").html('<input id="employeeid" name="employeeid" value="' +
            employeeid + '">');
		jQuery(this).parents("tr").find("td:eq(4)").html('<input id="employeename" name="employeename" value="' +
           employeename + '">');
		   jQuery(this).parents("tr").find("td:eq(5)").html('<input id="companyname" name="companyname" value="' +
           companyname + '">');	
		   jQuery(this).parents("tr").find("td:eq(6)").html('<input id="location" name="location" value="' +
           location + '">');
		   	jQuery(this).parents("tr").find("td:eq(7)").html('<input id="garment" name="garment" value="' +
        garment + '">');		
        jQuery(this).parents("tr").find("td:eq(8)").html('<input id="qty" name="qty" value="' + qty + '">');
        jQuery(this).parents("tr").find("td:eq(9)").html('<input id="status" name="status" value="' + status +
            '">');
        jQuery(this).parents("tr").find("td:eq(10)").prepend(
            "<button class='btn btn-info btn-xs btn-update'>Update</button><button class='btn btn-warning btn-xs btn-cancel'>Cancel</button>"
        )
        jQuery(this).hide();

        // cancel the data garment edits on post admin
        jQuery("body").on("click", ".btn-cancel", function(e) {
            e.preventDefault();
			var garment = jQuery(this).parents("tr").attr('data-garment');
			var qty = jQuery(this).parents("tr").attr('data-qty');
			var status = jQuery(this).parents("tr").attr('data-status');
			var id = jQuery(this).parents("tr").attr('data-id');
			var ref = jQuery(this).parents("tr").attr('data-ref');
			var lpo = jQuery(this).parents("tr").attr('data-lpo');
			var employeeid = jQuery(this).parents("tr").attr('data-employeeid');
			var employeename = jQuery(this).parents("tr").attr('data-employeename');
			var companyname = jQuery(this).parents("tr").attr('data-companyname');
			var location = jQuery(this).parents("tr").attr('data-location');

			jQuery(this).parents("tr").find("td:eq(1)").text(ref);
			jQuery(this).parents("tr").find("td:eq(2)").text(lpo);
			jQuery(this).parents("tr").find("td:eq(3)").text(employeeid);
			jQuery(this).parents("tr").find("td:eq(4)").text(employeename);
			jQuery(this).parents("tr").find("td:eq(5)").text(companyname);
			jQuery(this).parents("tr").find("td:eq(6)").text(location);

            jQuery(this).parents("tr").find("td:eq(7)").text(garment);
            jQuery(this).parents("tr").find("td:eq(8)").text(qty);
            jQuery(this).parents("tr").find("td:eq(9)").text(status);


            jQuery(this).parents("tr").find(".btn-edit").show();
            jQuery(this).parents("tr").find(".btn-update").remove();
            jQuery(this).parents("tr").find(".btn-cancel").remove();
        });


    });
    </script>
    <?php

    }
}

add_action('wp_ajax_nopriv_saveOrderData', 'saveOrderData_function');
add_action('wp_ajax_saveOrderData', 'saveOrderData_function');
function saveOrderData_function(){

	
			global $wpdb;
			$table_name = $wpdb->prefix . 'garments'; 
			$series = get_post_meta($_REQUEST['post'],'order_series',true) ;
			$barcode_series  = get_option('_barcode_series');	
			$barcode="N".$series."N".$barcode_series;	
			echo $_REQUEST['qty_trousers'];
			if(isset($_REQUEST['qty_shirt']) and !empty($_REQUEST['qty_shirt']))
                $wpdb->insert($table_name, array('barcode' => $barcode ,'post_id'=>$_REQUEST['post'],'garment' => 'Shirt', 'qty' => $_REQUEST['qty_shirt'], 'ref_num' => $_REQUEST['ref'], 'lpo_number' => $_REQUEST['lpo'], 'employee_id' => $_REQUEST['employee_id'], 'employee_name' => $_REQUEST['employee_name'], 'company_name' => $_REQUEST['company_name'], 'location' => $_REQUEST['location'], 'notes' => $_REQUEST['notes']));

            //Trousers
            if(isset($_REQUEST['qty_trousers']) and !empty($_REQUEST['qty_trousers']))
                $wpdb->insert($table_name, array('barcode' => $barcode ,'post_id'=>$_REQUEST['post'],'garment' => 'trousers', 'qty' => $_REQUEST['qty_trousers'], 'ref_num' => $_REQUEST['ref'], 'lpo_number' => $_REQUEST['lpo'], 'employee_id' => $_REQUEST['employee_id'], 'employee_name' => $_REQUEST['employee_name'], 'company_name' => $_REQUEST['company_name'], 'location' => $_REQUEST['location'], 'notes' => $_REQUEST['notes']));
            //coat
            if(isset($_REQUEST['qty_coat']) and !empty($_REQUEST['qty_coat']))
                $wpdb->insert($table_name, array('barcode' => $barcode ,'post_id'=>$_REQUEST['post'],'garment' => 'Coat', 'qty' => $_REQUEST['qty_coat'], 'ref_num' => $_REQUEST['ref'], 'lpo_number' => $_REQUEST['lpo'], 'employee_id' => $_REQUEST['employee_id'], 'employee_name' => $_REQUEST['employee_name'], 'company_name' => $_REQUEST['company_name'], 'location' => $_REQUEST['location'], 'notes' => $_REQUEST['notes']));
            
            if(isset($_REQUEST['qty_jacket']) and !empty($_REQUEST['qty_jacket']))
                $wpdb->insert($table_name, array('barcode' => $barcode ,'post_id'=>$_REQUEST['post'],'garment' => 'Jacket', 'qty' => $_REQUEST['qty_jacket'], 'ref_num' => $_REQUEST['ref'], 'lpo_number' => $_REQUEST['lpo'], 'employee_id' => $_REQUEST['employee_id'], 'employee_name' => $_REQUEST['employee_name'], 'company_name' => $_REQUEST['company_name'], 'location' => $_REQUEST['location'], 'notes' => $_REQUEST['notes']));

            if(isset($_REQUEST['qty_apron']) and !empty($_REQUEST['qty_apron']))
                $wpdb->insert($table_name, array('barcode' => $barcode ,'post_id'=>$_REQUEST['post'],'garment' => 'Aprons', 'qty' => $_REQUEST['qty_apron'], 'ref_num' => $_REQUEST['ref'], 'lpo_number' => $_REQUEST['lpo'], 'employee_id' => $_REQUEST['employee_id'], 'employee_name' => $_REQUEST['employee_name'], 'company_name' => $_REQUEST['company_name'], 'location' => $_REQUEST['location'], 'notes' => $_REQUEST['notes']));

            if(isset($_REQUEST['qty_skirt']) and !empty($_REQUEST['qty_skirt']))
                $wpdb->insert($table_name, array('barcode' => $barcode ,'post_id'=>$_REQUEST['post'],'garment' => 'Skirt', 'qty' => $_REQUEST['qty_scarf'], 'ref_num' => $_REQUEST['ref'], 'lpo_number' => $_REQUEST['lpo'], 'employee_id' => $_REQUEST['employee_id'], 'employee_name' => $_REQUEST['employee_name'], 'company_name' => $_REQUEST['company_name'], 'location' => $_REQUEST['location'], 'notes' => $_REQUEST['notes']));

            if(isset($_REQUEST['qty_tie']) and !empty($_REQUEST['qty_tie']))
                $wpdb->insert($table_name, array('barcode' => $barcode ,'post_id'=>$_REQUEST['post'],'garment' => 'Tie / Scarf', 'qty' => $_REQUEST['qty_tie'], 'ref_num' => $_REQUEST['ref'], 'lpo_number' => $_REQUEST['lpo'], 'employee_id' => $_REQUEST['employee_id'], 'employee_name' => $_REQUEST['employee_name'], 'company_name' => $_REQUEST['company_name'], 'location' => $_REQUEST['location'], 'notes' => $_REQUEST['notes']));

            if(isset($_REQUEST['qty_belt']) and !empty($_REQUEST['qty_belt']))
                $wpdb->insert($table_name, array('barcode' => $barcode ,'post_id'=>$_REQUEST['post'],'garment' => 'Belt', 'qty' => $_REQUEST['qty_belt'], 'ref_num' => $_REQUEST['ref'], 'lpo_number' => $_REQUEST['lpo'], 'employee_id' => $_REQUEST['employee_id'], 'employee_name' => $_REQUEST['employee_name'], 'company_name' => $_REQUEST['company_name'], 'location' => $_REQUEST['location'], 'notes' => $_REQUEST['notes']));
            
            if(isset($_REQUEST['qty_others']) and !empty($_REQUEST['qty_others']))
                $wpdb->insert($table_name, array('barcode' => $barcode ,'post_id'=>$_REQUEST['post'],'garment' => 'Others', 'qty' => $_REQUEST['qty_shoes'], 'ref_num' => $_REQUEST['ref'], 'lpo_number' => $_REQUEST['lpo'], 'employee_id' => $_REQUEST['employee_id'], 'employee_name' => $_REQUEST['employee_name'], 'company_name' => $_REQUEST['company_name'], 'location' => $_REQUEST['location'], 'notes' => $_REQUEST['notes']));

            if(isset($_REQUEST['qty_tshirt']) and !empty($_REQUEST['qty_tshirt']))
                $wpdb->insert($table_name, array('barcode' => $barcode ,'post_id'=>$_REQUEST['post'],'garment' => 'Tshirt', 'qty' => $_REQUEST['qty_cap'], 'ref_num' => $_REQUEST['ref'], 'lpo_number' => $_REQUEST['lpo'], 'employee_id' => $_REQUEST['employee_id'], 'employee_name' => $_REQUEST['employee_name'], 'company_name' => $_REQUEST['company_name'], 'location' => $_REQUEST['location'], 'notes' => $_REQUEST['notes']));
    
            if(isset($_REQUEST['qty_winter_jacket']) and !empty($_REQUEST['qty_winter_jacket']))
                $wpdb->insert($table_name, array('barcode' => $barcode ,'post_id'=>$_REQUEST['post'],'garment' => 'Sleeves', 'qty' => $_REQUEST['qty_sleeves'], 'ref_num' => $_REQUEST['ref'], 'lpo_number' => $_REQUEST['lpo'], 'employee_id' => $_REQUEST['employee_id'], 'employee_name' => $_REQUEST['employee_name'], 'company_name' => $_REQUEST['company_name'], 'location' => $_REQUEST['location'], 'notes' => $_REQUEST['notes']));
    

			update_option('_barcode_series',$barcode_series + 1);
			echo 'success';
			die();
	

}
add_action('wp_ajax_nopriv_updateData', 'updateData_function');
add_action('wp_ajax_updateData', 'updateData_function');
function updateData_function(){
	global $wpdb;     
			$id = $_REQUEST['id'];
			$table_name = $wpdb->prefix . 'garments';     				
			$wpdb->query($wpdb->prepare("UPDATE $table_name SET garment= '".$_REQUEST['garment']."' WHERE id=".$id));
			$wpdb->query($wpdb->prepare("UPDATE $table_name SET qty= '".$_REQUEST['qty']."' WHERE id=".$id));
			$wpdb->query($wpdb->prepare("UPDATE $table_name SET status= '".$_REQUEST['status']."' WHERE id=".$id));
			$wpdb->query($wpdb->prepare("UPDATE $table_name SET ref_num= '".$_REQUEST['ref']."' WHERE id=".$id));
			$wpdb->query($wpdb->prepare("UPDATE $table_name SET lpo= '".$_REQUEST['lpo']."' WHERE id=".$id));
			$wpdb->query($wpdb->prepare("UPDATE $table_name SET employee_id= '".$_REQUEST['employeeid']."' WHERE id=".$id));
			$wpdb->query($wpdb->prepare("UPDATE $table_name SET employee_name= '".$_REQUEST['employeename']."' WHERE id=".$id));
			$wpdb->query($wpdb->prepare("UPDATE $table_name SET company_name= '".$_REQUEST['companyname']."' WHERE id=".$id));
			$wpdb->query($wpdb->prepare("UPDATE $table_name SET location= '".$_REQUEST['location']."' WHERE id=".$id));
			echo "success";
	exit();
}

add_action('wp_ajax_nopriv_deleteData', 'deleteData_function');
add_action('wp_ajax_deleteData', 'deleteData_function');
function deleteData_function(){
	global $wpdb; 
	$table_name = $wpdb->prefix . 'garments'; 
	$id = $_REQUEST['id'];
	$wpdb->delete( $table_name, array( 'id' => $id ) );

}

add_shortcode('update-orders','updateOrders');
function updateOrders()
{
	 ob_start();
    ?>
    <div class='container'>

        <div class='row'>
            <div class='col-md-6 offset-md-3'>
                <div class='search-box'>
                    <form class='search-form'>
                        <input id="scanner" class='form-control'
                            placeholder='ex: N1021N11111	' type='text'>
                        <button class='btn btn-link search-btn'>
                            <i class='glyphicon glyphicon-search'></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class='container'>

        <div class='row'>
            <div class='col-md-6 offset-md-3'>
                <div class="status-dropdown">
                    <select name="status" id="status">
                        <option value="">Select Status </option>
                        <?php $status = get_option("order_status");
				$status = explode(",",$status);
				foreach($status as $status)
				{
					echo "<option value='".$status."'>".$status."</option>";
				}
				?>
                    </select>
                </div>
            </div>
        </div>
    </div>
	<div class='row'>
		<div class='col-md-10'>
		<p><strong>Date: </strong> <?php echo date('d-m-Y, H:m') ?></p>
		</div>	
		<div class='col-md-2'>
			<p><strong>Batch: </strong><?php echo str_pad(get_option('_batch_series'),5,"0",STR_PAD_LEFT) ?></p>
		</div>		
	</div>
    <div class="container " id="savepdf">
		<p> </p>		
        <ul class="responsive-table">
            <li class="table-header  bg-light">
                <div class="col col-1">LPO Number</div>
                <div class="col col-1">Barcode</div>
                <div class="col col-1">Company Name</div>
                <div class="col col-1">Employee ID</div>
                <div class="col col-1">Employee Name</div>
                <div class="col col-1">Material</div>
                <div class="col col-1">Qty</div>
                <div class="col col-1">Notes</div>

            </li>


        </ul>
    </div>
    <div class="container">
        <div class='row'>
            <div class='col-md-6 offset-md-3'>
                <input type="submit" value="Save" id="save" disabled="disabled" />
            </div>
            <span class="error"></span>
        </div>
    </div>

    <script>
		function on_scanner() {
				let is_event = false; // for check just one event declaration
				let input = document.getElementById("scanner");
				input.addEventListener("focus", function () {
					if (!is_event) {
						is_event = true;
						input.addEventListener("keypress", function (e) {
							setTimeout(function () {
								if (e.keyCode == 13) {
									scanner(input.value); // use value as you need
									input.select();
								}
							}, 500)
						})
					}
				});
				document.addEventListener("keypress", function (e) {
					if (e.target.tagName !== "INPUT") {
						input.focus();
					}
				});
			}

			function scanner(value) {
				if (value == '') return;
					console.log(value)
				jQuery.get(ajaxurl, {
                    'action': 'getData',
                    'id': value,
                },
                function(data) {
                    // Read Json and append the table rows.

					console.log(data);
					jQuery.each(data, function(index, msg) {   

                    {
                        var html = '<li class="table-row" data-id="' + msg.id + '" data-lpo="' + msg.lponumber + '" data-barcode="' + msg.barcode +'" data-lpo="' + msg.lponumber + '"   data-companyname = "' + msg.companyname +'" data-employeeid = "' + msg.employeeid +'" data-employeename="' + msg.employeename +'" data-item="' + msg.item +'" data-qty="' + msg.qty +'" data-notes="' + msg.notes +'"><div class="col col-sm-1 col-12 " data-label="LPO Number " data-lpo="' + msg.lponumber + '">' + msg.lponumber + '</div><div class="col col-sm-1 col-12" data-barcode="' + msg.barcode + '" data-label="Barcode">' + msg.barcode +'</div><div class="col col-sm-1 col-12" data-cname="' + msg.companyname + '" data-label="Company Name">' + msg.companyname +'</div><div class="col col-sm-1 col-12" data-empid="' + msg.employeeid + '" data-label="Employee ID">' + msg.employeeid +'</div><div class="col col-sm-1 col-12" data-empname="' + msg.employeename + '" data-label="Emplyee Name">' + msg.employeename + '</div><div class="col col-sm-1 col-12" data-item="' + msg.item + '" data-label="Item">' + msg.item +'</div><div class="col col-sm-1 col-12" data-qty="' + msg.qty +'" data-label="Qty">' + msg.qty +'</div><div class="col col-sm-1 col-12" data-label="Notes"><textarea placeholder="notes" id="note' + msg.id +'">'+msg.notes+'</textarea><a href="#" class="delete-update"><i class="fa fa-trash" aria-hidden="true"></i></a></div></li>';
                        jQuery('.responsive-table').append(html);
                        jQuery('input[type="submit"]').prop('disabled', false);
                    }
					});
                    


                }, 'json');
			}
    jQuery(document).ready(function() {
		 on_scanner() // init function

		 
        var ajaxurl = "<?php echo esc_url(admin_url('admin-ajax.php')); ?>";
        jQuery('.search-box input').blur(function() {
            console.log(jQuery(this).val());
            var t = jQuery(this);
            jQuery.get(ajaxurl, {
                    'action': 'getData',
                    'id': jQuery(this).val(),
                },
                function(data) {
                    // Read Json and append the table rows.

					console.log(data);
					jQuery.each(data, function(index, msg) {   

                    {
                        var html = '<li class="table-row" data-id="' + msg.id + '" data-lpo="' + msg.lponumber + '" data-barcode="' + msg.barcode +'" data-lpo="' + msg.lponumber + '"   data-companyname = "' + msg.companyname +'" data-employeeid = "' + msg.employeeid +'" data-employeename="' + msg.employeename +'" data-item="' + msg.item +'" data-qty="' + msg.qty +'" data-notes="' + msg.notes +'"><div class="col col-sm-1 col-12 " data-label="LPO Number " data-lpo="' + msg.lponumber + '">' + msg.lponumber + '</div><div class="col col-sm-1 col-12" data-barcode="' + msg.barcode + '" data-label="Barcode">' + msg.barcode +'</div><div class="col col-sm-1 col-12" data-cname="' + msg.companyname + '" data-label="Company Name">' + msg.companyname +'</div><div class="col col-sm-1 col-12" data-empid="' + msg.employeeid + '" data-label="Employee ID">' + msg.employeeid +'</div><div class="col col-sm-1 col-12" data-empname="' + msg.employeename + '" data-label="Emplyee Name">' + msg.employeename + '</div><div class="col col-sm-1 col-12" data-item="' + msg.item + '" data-label="Item">' + msg.item +'</div><div class="col col-sm-1 col-12" data-qty="' + msg.qty +'" data-label="Qty">' + msg.qty +'</div><div class="col col-sm-1 col-12" data-label="Notes"><textarea placeholder="notes" id="note' + msg.id +'">'+msg.notes+'</textarea><a href="#" class="delete-update"><i class="fa fa-trash" aria-hidden="true"></i></a></div></li>';
                        jQuery('.responsive-table').append(html);
                        jQuery('input[type="submit"]').prop('disabled', false);
                    }
					});
                    t.val('');


                }, 'json');
        });

        jQuery("#save").click(function() {
			var batchid = '<?php echo str_pad(get_option('_batch_series'),5,"0",STR_PAD_LEFT) ?>';  
			var batchdate = '<?php echo date('d-m-Y, H:m') ?>';
			var csv = 'Batch ID: '+ batchid +','+','+','+','+','+','+','+'Date:'+batchdate;
			var status;
			csv += "\n";  
            csv +=  'barcode,LPO,Company Name,Employee ID,Employee Name,Items,Qty,Status, Notes\n';
            jQuery('.table-row').each(function(i, obj) {
                //test
                var t = jQuery(this);
                status = jQuery("#status").val();
                var id = jQuery(this).attr('data-id');
				var barcode = jQuery(this).attr('data-barcode');
				var lpo = jQuery(this).attr('data-lpo');
				var companyname = jQuery(this).attr('data-companyname');
				var employeeid = jQuery(this).attr('data-employeeid');
				var employeename = jQuery(this).attr('data-employeename');
				var item = jQuery(this).attr('data-item');
				var qty = jQuery(this).attr('data-qty');
				var notes = jQuery("#note"+id).val();
				
				console.log(jQuery(this).attr('data-id'));
				
				csv += barcode+','+lpo+','+companyname+','+employeeid +','+employeename+','+item +','+qty+','+status+','+notes;
				csv += "\n";  
                if (status == '') {
                    jQuery(".error").html("Status cannot be empty")
                    return;
                }
                jQuery.get(ajaxurl, {
                        'action': 'updateStatus',
                        'id': id,
                        'status': status,
						'notes': notes
                    },
                    function(msg) {
                        if (msg == 'success') {

                            
                            t.remove();




                        }

                    });
            }).promise().done( function(){
				console.log(status);
				if(status == 'Under Production S1' || status == 'Delivery Executed' || status == 'Delivery Prepration'){
				jQuery.get(ajaxurl, {
                        'action': 'saveBatch',
                        'csv': csv 
                       
                    },
                    function(msg) {
                        if (msg != '') {

                            
                            var hiddenElement = document.createElement('a');  
							hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csv);  
							hiddenElement.target = '_blank';  
							
							//provide the name for the CSV file to be downloaded  
							hiddenElement.download = 'batch-'+msg+'.csv';  
							hiddenElement.click(); 




                        }

                    });

				}
			 } );
			 

        })
        jQuery("body").on('click', '.delete-update', function(e) {

            e.preventDefault();
            jQuery(this).parent().parent().remove();

        });


    });

    function isEmpty(obj) {
        for (var prop in obj) {
            if (Object.prototype.hasOwnProperty.call(obj, prop)) {
                return false;
            }
        }
        return JSON.stringify(obj) === JSON.stringify({});
    }
    </script>
    <?php
$output = ob_get_contents();
    ob_end_clean();
    return $output;
}

add_action('wp_ajax_nopriv_getData', 'getData_function');
add_action('wp_ajax_getData', 'getData_function');
function getData_function(){
	// Multiple use getdata function
		global $wpdb;
		$data = array();
		$table_name = $wpdb->prefix . 'garments';
		$results = $wpdb->get_results( "SELECT * FROM $table_name WHERE barcode = '".$_REQUEST['id']."'");	
		
		foreach ($results as $result){
			$data['item'] = $result->garment;
			$data['barcode'] = $result->barcode;
			$data['qty'] = $result->qty;
			$data['status'] = $result->status;
			$data['postid'] = $result->post_id;
			$data['id']=$result->id;
			$data['refnunmber'] =$result->ref_num;
			$data['employeeid'] = $result->employee_id;
			$data['employeename'] = $result->employee_name;
			$data['lponumber']= $result->lpo_number;
			$data['companyname'] = $result->company_name;
			$data['location'] = $result->location;
			$data['notes'] = $result->notes;
			$response[] = $data;
		}
		
		$post = get_post($data['postid']);
	//		$data['title'] = $post->post_title;
		
		echo json_encode($response);
		die();
}

// We are updating sttatus
add_action('wp_ajax_nopriv_updateStatus', 'updateStatus_function');
add_action('wp_ajax_updateStatus', 'updateStatus_function');
function updateStatus_function(){
	global $wpdb;     
			$id = $_REQUEST['id'];
			$table_name = $wpdb->prefix . 'garments';     							
			$wpdb->query($wpdb->prepare("UPDATE $table_name SET status= '".$_REQUEST['status']."' WHERE id=".$id));			
			$wpdb->query($wpdb->prepare("UPDATE $table_name SET notes= '".$_REQUEST['notes']."' WHERE id=".$id));
			echo "success";
	exit();
}

// Saving Batch for future retrieval
add_action('wp_ajax_nopriv_saveBatch', 'saveBatch_function');
add_action('wp_ajax_saveBatch', 'saveBatch_function');
function saveBatch_function(){
	global $wpdb;   
	$table_name = $wpdb->prefix . 'batches';      
	$csv = $_REQUEST['csv'];		
	$wpdb->insert($table_name, array('content'=>$csv));
	$lastid = $wpdb->insert_id;			
	update_option('_batch_series',$lastid + 1);
	echo $lastid;
	exit();
}

// Tracking Page 
add_shortcode('track-orders','trackOrders');
function trackOrders()
{
	 ob_start();
    ?>
    <div class='container'>

        <div class='row'>
            <div class='col-md-6 offset-md-3'>
                <div class='track-box'>
                    <form class='search-form'>
                        <textarea name="trackingIDS" id="trackingIDS"
                            placeholder="Each Tracking id on one line"></textarea>
                        <button class='btn btn-link search-btn'>
                            <i class='glyphicon glyphicon-search'></i>
                        </button>
                    </form>
                </div>
                <div class="track-select">
                    <select name="tracktype" id="tracktype">
                        <option value="ref_num">Reference Number</option>
                        <option value="lpo_number">LPO Number</option>
                        <option value="employee_id">Employee ID</option>
                        <option value="employee_name">Employee Name</option>
                        <option value="barcode">Barcode</option>
                    </select>
                </div>
                <button class="track-btn">Track</button>
            </div>
        </div>
    </div>

    <div class="container">

        <ul class="responsive-table">
            <li class="table-header  bg-light">
                <div class="col col-1">LPO Number</div>
                <div class="col col-1">Barcode</div>
                <div class="col col-1">Company Name</div>
                <div class="col col-1">Employee ID</div>
                <div class="col col-1">Employee Name</div>
                <div class="col col-1">Material</div>
                <div class="col col-1">Qty</div>
                <div class="col col-1">Status</div>

            </li>


        </ul>
    </div>


    <script>
    jQuery(document).ready(function() {
        var ajaxurl = "<?php echo esc_url(admin_url('admin-ajax.php')); ?>";
        jQuery('.track-btn').click(function() {

            var criteria = jQuery("#tracktype").val();
            var t = jQuery(this);
            var lines = jQuery('#trackingIDS').val().split('\n');
            for (var i = 0; i < lines.length; i++) {
                //code here using lines[i] which will give you each line
                var search = lines[i]
                console.log(search);
                jQuery.get(ajaxurl, {
                        'action': 'trackStatus',
                        'search': search,
                        'criteria': criteria
                    },
                    function(json) {
                        // Read Json and append the table rows.


                        for (var i = 0; i < json.length; i++) {
                            var msg = json[i];

                            console.log(msg.id);
                            if (msg.title != null) {
                                var html = '<li class="table-row" data-id="' + msg.id +
                                    '"><div class="col col-sm-1 col-12" data-label="LPO Number" data-lpo="' +
                                    msg.lponumber + '">' + msg.lponumber +
                                    '</div><div class="col col-sm-1 col-12" data-barcode="' + msg
                                    .barcode + '" data-label="Barcode">' + msg.barcode +
                                    '</div><div class="col col-sm-1 col-12" data-cname="' + msg
                                    .companyname + '" data-label="Company Name">' + msg
                                    .companyname +
                                    '</div><div class="col col-sm-1 col-12" data-empid="' + msg
                                    .employeeid + '" data-label="Employee ID">' + msg.employeeid +
                                    '</div><div class="col col-sm-1 col-12" data-empname="' + msg
                                    .employeename + '" data-label="Emplyee Name">' + msg
                                    .employeename +
                                    '</div><div class="col col-sm-1 col-12" data-item="' + msg
                                    .item + '" data-label="Item">' + msg.item +
                                    '</div><div class="col col-sm-1 col-12" data-qty="' + msg.qty +
                                    '" data-label="Qty">' + msg.qty +
                                    '</div><div class="col col-sm-1 col-12" data-label="Status">' +
                                    msg.status + '</div></li>';
                                jQuery('.responsive-table').append(html);

                            }
                        }




                    }, 'json');
            }

        });





    });
    </script>
    <?php
$output = ob_get_contents();
    ob_end_clean();
    return $output;
}

add_action('wp_ajax_nopriv_trackStatus', 'trackStatus_function');
add_action('wp_ajax_trackStatus', 'trackStatus_function');

function trackStatus_function()
{
	global $wpdb;
	$data = array();
	$records = array();
	
		if($_REQUEST['criteria'] == 'barcode')
		{
			$table_name = $wpdb->prefix . 'garments';
			$results = $wpdb->get_results( "SELECT * FROM $table_name WHERE barcode = '".$_REQUEST['search']."'");	
		//	echo "SELECT * FROM $table_name WHERE barcode = '".$_REQUEST['search']."'";
			foreach ($results as $result){
				$data['item'] = $result->garment;
				$data['barcode'] = $result->barcode;
				$data['qty'] = $result->qty;
				$data['status'] = $result->status;
				$data['postid'] = $result->post_id;
				$data['id']=$result->id;
				$data['refnunmber'] =$result->ref_num;
				$data['employeeid'] = $result->employee_id;
				$data['employeename'] = $result->employee_name;
				$data['lponumber']= $result->lpo_number;
				$data['companyname'] = $result->company_name;
				$data['location'] = $result->location;
				$data['notes'] = $result->notes;
			}
			$post = get_post($data['postid']);
			$data['title'] = $post->post_title;
			
			$records[] = $data;
			echo json_encode($records);
			die();
	}
	else
	{
	
			$table_name = $wpdb->prefix . 'garments';
			$criteria = $_REQUEST['criteria'];
			$results = $wpdb->get_results( "SELECT * FROM $table_name WHERE ".$criteria."= '".$_REQUEST['search']."'");
		//	echo  "SELECT * FROM $table_name WHERE ".$criteria."= '".$_REQUEST['search']."'";	
		//	echo "SELECT * FROM $table_name WHERE barcode = '".$_REQUEST['search']."'";
			foreach ($results as $result){
				$data['item'] = $result->garment;
				$data['barcode'] = $result->barcode;
				$data['qty'] = $result->qty;
				$data['status'] = $result->status;
				$data['postid'] = $result->post_id;
				$data['id']=$result->id;
				$data['refnunmber'] =$result->ref_num;
				$data['employeeid'] = $result->employee_id;
				$data['employeename'] = $result->employee_name;
				$data['lponumber']= $result->lpo_number;
				$data['companyname'] = $result->company_name;
				$data['location'] = $result->location;
				$data['notes'] = $result->notes;
			}
			$post = get_post($data['postid']);
			$data['title'] = $post->post_title;
			
			$records[] = $data;
			echo json_encode($records);
			die();

	}
}

add_shortcode('report-1','report1Function');
function report1Function()
{ ob_start();
	?>
<div >		<!-- Filter Table -->
				<table class="filters">
					<tr>
					<td><label>Search By LPO</label><br />
						<input type='text' id='searchByLPO' placeholder='Enter Text'>
					</td>
					<td>
						<label>Search By Ref</label><br />
						<input type='text' id='searchByref' placeholder='Enter Text'>
					</td>
					<td>
						<label>Search By Location</label><br />
						<input type='text' id='searchBylocation' placeholder='Enter Text'>
					</td>
					<td>
						<label>Search By Garments</label><br />
						<input type='text' id='searchByGarments' placeholder='Enter Text'>
					</td>
					<td>
						<label>Search By Co Name</label><br />
						<input type='text' id='searchByCompanyName' placeholder='Enter Text'>
					</td>
					<td>
						<label>Search By Emp ID</label><br />
						<input type='text' id='searchByEmployeeID' placeholder='Enter Text'>
					</td>
					<td>
						<label>Search By Emp Name</label><br />
						<input type='text' id='searchByEmployeeName' placeholder='Enter Text'>
					</td>
					<td>
						<label>Search By Status</label><br />
						<input type='text' id='searchByStatus' placeholder='Enter Text'>
					</td>						
					
					</tr>
				</table>

            <!-- Table -->
            <table id='orderTable' class='display dataTable'>
                <thead>
                <tr>
                    <th>Barcode</th>
                    <th>Ref Number</th>
                    <th>LPO</th>
                    <th>Employee ID</th>
                    <th>Employee Name</th>
					<th>Company Name</th>
					<th>Location</th>
					<th>Garment</th>
					<th>Qty</th>
					<th>Status</th>
					<th>Created</th>
					<th>Updated</th>
                </tr>
                </thead>
                
            </table>
        </div>
        
        <!-- Script -->
        <script>
        jQuery(document).ready(function(){
			 var ajaxurl = "<?php echo esc_url(admin_url('admin-ajax.php')); ?>";
				var dataTable = jQuery('#orderTable').DataTable({
					dom: 'Bfrtip',
					buttons: [
						'excel',
					],
					'processing': true,
					'serverSide': true,
					'serverMethod': 'post',
					'searching': false,							
					'ajax': {
						url: ajaxurl + '?action=reportOne',					
					'data': function(data){
							// Read values
							var lpo = jQuery('#searchByLPO').val();
							var ref = jQuery('#searchByref').val();
							var location = jQuery('#searchBylocation').val();
							var garments = jQuery('#searchByGarments').val();
							var companyname = jQuery('#searchByCompanyName').val();
							var employeeid = jQuery('#searchByEmployeeID').val();
							var employeename = jQuery('#searchByEmployeeName').val();
							var status = jQuery('#searchByStatus').val();

							// Append to data
							data.lpo = lpo;
							data.ref = ref;
							data.location = location;
							data.garments = garments;
							data.companyname = companyname;
							data.employeeid = employeeid;
							data.employeename = employeename;
							data.status = status;
						
							}	
						},
					'columns': [
						{ data: 'barcode' },
						{ data: 'ref_num' },
						{ data: 'lpo' },
						{ data: 'employeed_id' },
						{ data: 'employeed_name' },
						{ data: 'company_name' },
						{ data: 'location' },
						{ data: 'garment' },
						{ data: 'qty' },
						{ data: 'status' },
						{ data: 'date_created' },
						{ data: 'date_updated' }
					]
					
				});
			 dataTable.draw();
			 jQuery('#searchByLPO').keyup(function(){
				dataTable.draw();
			});

			jQuery('#searchByref').keyup(function(){
				dataTable.draw();
			});

			jQuery('#searchBylocation').keyup(function(){
				dataTable.draw();
			});


			jQuery('#searchByGarments').keyup(function(){
				dataTable.draw();
			});

			jQuery('#searchByCompanyName').keyup(function(){
				dataTable.draw();
			});

			jQuery('#searchByEmployeeID').keyup(function(){
				dataTable.draw();
			});

			jQuery('#searchByEmployeeName').keyup(function(){
				dataTable.draw();
			});

			
        });
        </script>
<?php
	$output = ob_get_contents();
    ob_end_clean();
    return $output;		
}
add_action('wp_ajax_nopriv_reportOne', 'reportOne_function');
add_action('wp_ajax_reportOne', 'reportOne_function');
function reportOne_function()
{
global $wpdb;
$table_name = $wpdb->prefix . 'garments';
	## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value

## Custom Field value
$searchByLPO = $_POST['lpo'];
$searchByRef = $_POST['ref'];
$searchByLocation = $_POST['location'];
$searchByGarments = $_POST['garments'];
$searchByCompanyname = $_POST['companyname'];
$searchByEmployeeId = $_POST['employeeid'];
$searchByEmployeeName = $_POST['employeename'];
$searchByStatus= $_POST['status'];

## Search 
$searchQuery = " ";
if($searchByLPO != ''){
   $searchQuery .= " and (lpo_number like '%".$searchByLPO ."%' ) ";
}
if($searchByRef != ''){
   $searchQuery .= " and (ref_num like '%".$searchByLPO ."%' ) ";
}
if($searchByLocation != ''){
   $searchQuery .= " and (location like '%".$searchByLocation ."%' ) ";
}
if($searchByGarments != ''){
   $searchQuery .= " and (garment like '%".$searchByGarments ."%' ) ";
}
if($searchByCompanyname != ''){
   $searchQuery .= " and (company_name like '%".$searchByCompanyname ."%' ) ";
}
if($searchByEmployeeId != ''){
   $searchQuery .= " and (employee_id like '%".$searchByEmployeeId ."%' ) ";
}
if($searchByEmployeeName != ''){
   $searchQuery .= " and (employee_name like '%".$searchByEmployeeName ."%' ) ";
}
if($searchByStatus != ''){
   $searchQuery .= " and (status like '%".$searchByStatus ."%' ) ";
}










## Total number of records without filtering
$count_query = "select count(*) from $table_name";
$totalRecords = $wpdb->get_var($count_query);

## Total number of records with filtering
$count_query = "select count(*) from $table_name WHERE 1 ".$searchQuery;
$totalRecordwithFilter = $wpdb->get_var($count_query);

## Fetch records
$results = $wpdb->get_results( "SELECT * FROM $table_name  WHERE 1 ".$searchQuery."order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage );
//echo "SELECT * FROM $table_name  WHERE 1 ".$searchQuery;
	foreach ($results as $result){
		$data[] = array(
		"ref_num"=>$result->ref_num,	
		"barcode"=>$result->barcode,
		"lpo"=>$result->lpo_number,
		"employeed_id"=>$result->employee_id,
		"employeed_name"=>$result->employeed_name,
		"company_name"=>$result->company_name,
		"location"=>$result->location,
		"garment"=>$result->garment,
		"qty"=>$result->qty,
		"status"=>$result->status,
		"date_created"=>$result->created,
		"date_updated"=>$result->updated,
		
	);
	}
	## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);
die();
}

add_shortcode('print','print_func');
function print_func()
{
	$args = array(
	'posts_per_page' => 50,
	'paged' => $paged,
	'post_type' => 'orders',
	'post_status' => 'draft'
	);
	 ob_start();
?>	 
	<style>
		.printTable td {width:20%}
		.printTable {width:100%}
		.printTable table {
  border-collapse: collapse;
  width: 100%;
}
.printTable button {font-size: 12px;
    border: none;
    background: #000;
    color: #fff;}
.printTable th, td {
  text-align: left;
  padding: 8px;
}

.printTable tr:nth-child(even) {background-color: #f2f2f2;}
	</style>

	 <table class="printTable"><tr><th> Date Created</th><th> Order ID</th><th>Action</th><th>Barcode print status<th></tr>
<?php	 
	 $postslist = new WP_Query( $args );
	  if ( $postslist->have_posts() ) :
		while ( $postslist->have_posts() ) : $postslist->the_post(); 
		$print_status = get_post_meta(get_the_ID(),'print_status',true);
		if($print_status == 'true')
			$status = "Printed";
		else
			$status = "print Pending";
?>
		<tr><td><?php the_date(); ?></td><td><?php the_title(); ?></td><td><button onclick="window.open('<?php echo get_permalink('150') ?>?postid=<?php echo get_the_ID() ?>', '_blank')" data-id="<?php echo get_the_ID()?>">Print</button></td><td><?php echo $status ?></td></tr>
<?php
		endwhile;  
		?>
		</table>
		<?php
		 	 next_posts_link( 'Older Entries', $postslist->max_num_pages );
             previous_posts_link( 'Next Entries &raquo;' ); 
       		 wp_reset_postdata();
	  endif;	
	  $output = ob_get_contents();
    ob_end_clean();
    return $output;
}

add_shortcode('batch','batch_func');

function batch_func()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'batches';
	$customPagHTML     = "";
	$query             = "SELECT * FROM $table_name";
	$total_query     = "SELECT COUNT(1) FROM (${query}) AS combined_table";
	$total             = $wpdb->get_var( $total_query );
	$items_per_page = 30 ;
	$page             = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
	$offset         = ( $page * $items_per_page ) - $items_per_page;
	$results        = $wpdb->get_results( $query . " ORDER BY id DESC LIMIT ${offset}, ${items_per_page}" );
	$totalPage         = ceil($total / $items_per_page);

	if($totalPage > 1){
	$customPagHTML     =  '<div><span>Page '.$page.' of '.$totalPage.'</span>'.paginate_links( array(
	'base' => add_query_arg( 'cpage', '%#%' ),
	'format' => '',
	'prev_text' => __('&laquo;'),
	'next_text' => __('&raquo;'),
	'total' => $totalPage,
	'current' => $page
	)).'</div>';
	}

	 ob_start();

?>	
		<style>
			.printTable td {width:20%}
			.printTable {width:100%}
			.printTable button {font-size: 12px;
			border: none;
			background: #000;
			color: #fff;}
			.printTable table {border-collapse: collapse;width: 100%;}
			.printTable th, td {
				text-align: left;
				padding: 8px;
			}
			.printTable th, td {
			text-align: left;
			padding: 8px;
			}
			.printTable tr:nth-child(even) {background-color: #f2f2f2;}
		</style>
	<table class="printTable"><tr><th>  Batch ID</th><th>Date Created</th><th>&nbsp;</th></tr>
	<?php
	foreach ($results as $result)
	{
	?>
	<tr><td><?php echo $result->id ?></td><td><?php echo $result->date_created ?></td><td><button class="export" data-id="<?php echo $result->id ?>"> Export </button></td></tr>	
	<?php		
	}
	?>
	</table>
	<?php echo $customPagHTML ?>
	<script>
		 jQuery(".export").click(function() {
			var ajaxurl = "<?php echo esc_url(admin_url('admin-ajax.php')); ?>";
			 var id = jQuery(this).data('id');
			jQuery.get(ajaxurl, {
                        'action': 'getBatch', 
						'id' : id                   
                       
                    },
                    function(msg) {
                        if (msg != '') {

                            
                            var hiddenElement = document.createElement('a');  
							hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(msg);  
							hiddenElement.target = '_blank';  
							
							//provide the name for the CSV file to be downloaded  
							hiddenElement.download = 'batch-'+id+'.csv';  
							hiddenElement.click(); 




                        }

                    });
		 })
	</script>	
<?php	
}

add_action('wp_ajax_nopriv_getBatch', 'getBatch_function');
add_action('wp_ajax_getBatch', 'getBatch_function');
function getBatch_function(){
	global $wpdb;   
	$table_name = $wpdb->prefix . 'batches'; 
	$results = $wpdb->get_results("Select * from $table_name where id =".$_REQUEST['id']);
	foreach($results as $result)     
	{
		$csv = $result->content;
	}
	echo $csv;	
	exit();
}


add_filter('wpcf7_display_message', 'change_submission_msg',10,2);
function change_submission_msg($message, $status){
	global $wp_session;
  if('mail_sent_ok' == $status){
    $message= 'Order ID '.$wp_session['orderid'].' is saved';
  }
  return $message;
}

add_filter("admin_body_class", "my_folded_menu", 10, 1);

function my_folded_menu($classes){
    return $classes." folded";
}

add_action('wp_ajax_nopriv_getPrint', 'getBatch_Print');
add_action('wp_ajax_getPrint', 'getPrint_function');
function getPrint_function(){
	global $wpdb;   
	$table_name = $wpdb->prefix . 'garments'; 
	$results = $wpdb->get_results("Select * from $table_name where id =".$_REQUEST['id']);
	
	foreach($results as $result)     
	{
		$barcodes['barcode'] = $result->barcode;
		$barcodes['employee_name'] = $result->employee_name;
		$barcodes['company_name'] = $result->company_name;
	}
	 update_post_meta($result->post_id,'print_status','true');
	echo json_encode($barcodes);	
	exit();
}

add_shortcode('barcode','barcode_func');
function barcode_func()
{
 ob_start();
	?>

	<div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4 text-center">
                <?php
                    global $wpdb;
                    $postid = $_REQUEST['postid'];
                    $table_name = $wpdb->prefix . 'garments';
			        $criteria = $_REQUEST['criteria'];
			        $results = $wpdb->get_results( "SELECT * FROM $table_name where post_id =". $postid);

                   // $ids = get_post_meta($postid, 'barcode_batch', true);
                    //$ids = explode(",",$ids);
                    update_post_meta($postid,'print_status','true');
                    foreach ($results as $result)   
                   
                    {
                        $string = $result->barcode;
                        $type='code128';
                        $orientation='horizontal';
                        $size='20';
                        $print='true';

                    if($string != '') {
                        echo '</br>';
                        echo '<p style="font-size:12px"><input type="checkbox" class="printbarcode" data-id="'.$result->id.'" value="'.$result->id.'">&nbsp;&nbsp;'.$result->employee_name.' - '.$result->company_name.'</p>';                       
                        echo '<img class="barcode" alt="'.$string.'" src="'.home_url().'/barcode/barcode.php?text='.$string.'&codetype='.$type.'&orientation='.$orientation.'&size='.$size.'&print='.$print.'"/>';
                        echo '</br>';                      
                        echo '</br>';
                    
                    }

                }
                    
                ?>
            </div>
            <div class="col-md-4"></div>
        </div>
        <div class="row">
            <div class="col-md-4"></div>
                <div class="col-md-4 text-center">
                    <button class="printBarcode">Print</button>
                    <button class="checkall">Check All</button>
                </div>
             <div class="col-md-4"></div>
        </div>  
        <br />      <br />  
		<script>
			 jQuery(document).ready(function() {
				 jQuery(".checkall").click(function(){
					
					 jQuery('input[type=checkbox]').each(function () {						 
						jQuery(this).attr('checked',true);
					});
					 
				});
			 });
		</script>	
<?php
		  $output = ob_get_contents();
    ob_end_clean();
    return $output;
}