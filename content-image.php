<?php
/**
 * The template for displaying posts in the Image post format
 * Visit http://www.nurulimam.com/theme to more theme
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="entry-content">
			<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'bantenit' ) ); ?>
		</div><!-- .entry-content -->

		<footer class="entry-meta">
			<section class="center">
			<a href="<?php the_permalink(); ?>">
				<h1><?php the_title(); ?></h1>
			</a>
				<p><time class="entry-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo get_the_date(); ?></time></p>
			<?php edit_post_link( __( 'Edit', 'bantenit' ), '<span class="edit-link">', '</span>' ); ?>
			</section>
			<?php if ( comments_open() ) : ?>
			<div class="comments-link">
				<?php comments_popup_link( '<span class="leave-reply">' . __( 'Leave a reply', 'bantenit' ) . '</span>', __( '1 Reply', 'bantenit' ), __( '% Replies', 'bantenit' ) ); ?>
			</div><!-- .comments-link -->
			<?php endif; // comments_open() ?>
		</footer><!-- .entry-meta -->
	</article><!-- #post -->
