<?php
if ( ! isset( $content_width ) )
	$content_width = 625;

function bantenit_setup() {
	load_theme_textdomain( 'bantenit', get_template_directory() . '/languages' );
	add_editor_style();
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-formats', array( 'aside', 'image', 'link', 'quote', 'status' ) );
	register_nav_menu( 'primary', __( 'Primary Menu', 'bantenit' ) );
	add_theme_support( 'custom-background', array(
		'default-color' => 'e6e6e6',
	) );
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 624, 9999 );
}
add_action( 'after_setup_theme', 'bantenit_setup' );

require( get_template_directory() . '/inc/custom-header.php' );
require( get_template_directory() . '/inc/custom-style.php' );

function bantenit_get_font_url() {
	$font_url = '';
	if ( 'off' !== _x( 'on', 'Open Sans font: on or off', 'bantenit' ) ) {
		$subsets = 'latin,latin-ext';
		$subset = _x( 'no-subset', 'Open Sans font: add new subset (greek, cyrillic, vietnamese)', 'bantenit' );
		if ( 'cyrillic' == $subset )
			$subsets .= ',cyrillic,cyrillic-ext';
		elseif ( 'greek' == $subset )
			$subsets .= ',greek,greek-ext';
		elseif ( 'vietnamese' == $subset )
			$subsets .= ',vietnamese';
		$protocol = is_ssl() ? 'https' : 'http';
		$query_args = array(
			'family' => 'Open+Sans:400italic,700italic,400,700',
			'subset' => $subsets,
		);
		$font_url = add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" );
	}
	return $font_url;
}

function bantenit_scripts_styles() {
	global $wp_styles;
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
	wp_enqueue_script( 'bantenit-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '1.0', true );
	$font_url = bantenit_get_font_url();
	if ( ! empty( $font_url ) )
		wp_enqueue_style( 'bantenit-fonts', esc_url_raw( $font_url ), array(), null );
	wp_enqueue_style( 'bantenit-style', get_stylesheet_uri() );
	wp_enqueue_style( 'bantenit-ie', get_template_directory_uri() . '/css/ie.css', array( 'bantenit-style' ), '20121010' );
	$wp_styles->add_data( 'bantenit-ie', 'conditional', 'lt IE 9' );
}
add_action( 'wp_enqueue_scripts', 'bantenit_scripts_styles' );

function bantenit_mce_css( $mce_css ) {
	$font_url = bantenit_get_font_url();
	if ( empty( $font_url ) )
		return $mce_css;
	if ( ! empty( $mce_css ) )
		$mce_css .= ',';
	$mce_css .= esc_url_raw( str_replace( ',', '%2C', $font_url ) );
	return $mce_css;
}
add_filter( 'mce_css', 'bantenit_mce_css' );

function bantenit_wp_title( $title, $sep ) {
	global $paged, $page;
	if ( is_feed() )
		return $title;
	$title .= get_bloginfo( 'name' );
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";
	if ( $paged >= 2 || $page >= 2 )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'bantenit' ), max( $paged, $page ) );
	return $title;
}
add_filter( 'wp_title', 'bantenit_wp_title', 10, 2 );

function bantenit_page_menu_args( $args ) {
	if ( ! isset( $args['show_home'] ) )
		$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'bantenit_page_menu_args' );

function bantenit_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Main Sidebar', 'bantenit' ),
		'id' => 'sidebar-1',
		'description' => __( 'Appears on posts and pages except the optional Front Page template, which has its own widgets', 'bantenit' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	register_sidebar( array(
		'name' => __( 'First Front Page Widget Area', 'bantenit' ),
		'id' => 'sidebar-2',
		'description' => __( 'Appears when using the optional Front Page template with a page set as Static Front Page', 'bantenit' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	register_sidebar( array(
		'name' => __( 'Second Front Page Widget Area', 'bantenit' ),
		'id' => 'sidebar-3',
		'description' => __( 'Appears when using the optional Front Page template with a page set as Static Front Page', 'bantenit' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
add_action( 'widgets_init', 'bantenit_widgets_init' );

if ( ! function_exists( 'bantenit_content_nav' ) ) :
function bantenit_content_nav( $html_id ) {
	global $wp_query;
	$html_id = esc_attr( $html_id );
	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo $html_id; ?>" class="navigation" role="navigation">
			<h3 class="assistive-text"><?php _e( 'Post navigation', 'bantenit' ); ?></h3>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'bantenit' ) ); ?></div>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'bantenit' ) ); ?></div>
		</nav><!-- #<?php echo $html_id; ?> .navigation -->
	<?php endif;
}
endif;
if ( ! function_exists( 'bantenit_comment' ) ) :
function bantenit_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<p><?php _e( 'Pingback : ', 'bantenit' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'bantenit' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
		global $post;
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<header class="comment-meta comment-author vcard">
				<?php
					echo get_avatar( $comment, 44 );
					printf( '<cite><b class="fn">%1$s</b> %2$s</cite>',
						get_comment_author_link(),
						// If current post author is also comment author, make it known visually.
						( $comment->user_id === $post->post_author ) ? '<span>' . __( 'Author', 'bantenit' ) . '</span>' : ''
					);
					printf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
						esc_url( get_comment_link( $comment->comment_ID ) ),
						get_comment_time( 'c' ),
						/* translators: 1: date, 2: time */
						sprintf( __( '%1$s at %2$s', 'bantenit' ), get_comment_date(), get_comment_time() )
					);
				?>
			</header>
			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'bantenit' ); ?></p>
			<?php endif; ?>
			<section class="comment-content comment">
				<?php comment_text(); ?>
				<?php edit_comment_link( __( 'Edit', 'bantenit' ), '<p class="edit-link">', '</p>' ); ?>
			</section>
			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'bantenit' ), 'after' => ' <span></span>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div>
		</article>
	<?php
		break;
	endswitch;
}
endif;
if ( ! function_exists( 'bantenit_entry_meta' ) ) :

function bantenit_entry_meta() {
	$categories_list = get_the_category_list( __( ', ', 'bantenit' ) );
	$tag_list = get_the_tag_list( '', __( ', ', 'bantenit' ) );
	$date = sprintf( '<a href="%1$s" title="%2$s"><time class="entry-date" datetime="%3$s">%4$s</time></a>',
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);
	$author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'bantenit' ), get_the_author() ) ),
		get_the_author()
	);
	if ( $tag_list ) {
		$utility_text = __( 'This entry was posted in %1$s and tagged %2$s on %3$s<span class="by-author"> by %4$s</span>.', 'bantenit' );
	} elseif ( $categories_list ) {
		$utility_text = __( 'This entry was posted in %1$s on %3$s<span class="by-author"> by %4$s</span>.', 'bantenit' );
	} else {
		$utility_text = __( 'This entry was posted on %3$s<span class="by-author"> by %4$s</span>.', 'bantenit' );
	}

	printf(
		$utility_text,
		$categories_list,
		$tag_list,
		$date,
		$author
	);
}
endif;

function bantenit_body_class( $classes ) {
	$background_color = get_background_color();
	$background_image = get_background_image();

	if ( ! is_active_sidebar( 'sidebar-1' ) || is_page_template( 'page-templates/full-width.php' ) )
		$classes[] = 'full-width';

	if ( is_page_template( 'page-templates/front-page.php' ) ) {
		$classes[] = 'template-front-page';
		if ( has_post_thumbnail() )
			$classes[] = 'has-post-thumbnail';
		if ( is_active_sidebar( 'sidebar-2' ) && is_active_sidebar( 'sidebar-3' ) )
			$classes[] = 'two-sidebars';
	}

	if ( empty( $background_image ) ) {
		if ( empty( $background_color ) )
			$classes[] = 'custom-background-empty';
		elseif ( in_array( $background_color, array( 'fff', 'ffffff' ) ) )
			$classes[] = 'custom-background-white';
	}
	if ( wp_style_is( 'bantenit-fonts', 'queue' ) )
		$classes[] = 'custom-font-enabled';

	if ( ! is_multi_author() )
		$classes[] = 'single-author';

	return $classes;
}
add_filter( 'body_class', 'bantenit_body_class' );

function bantenit_content_width() {
	if ( is_page_template( 'page-templates/full-width.php' ) || is_attachment() || ! is_active_sidebar( 'sidebar-1' ) ) {
		global $content_width;
		$content_width = 960;
	}
}
add_action( 'template_redirect', 'bantenit_content_width' );

function bantenit_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
}
add_action( 'customize_register', 'bantenit_customize_register' );

function bantenit_customize_preview_js() {
	wp_enqueue_script( 'bantenit-customizer', get_template_directory_uri() . '/js/theme-customizer.js', array( 'customize-preview' ), '20130301', true );
}
add_action( 'customize_preview_init', 'bantenit_customize_preview_js' );