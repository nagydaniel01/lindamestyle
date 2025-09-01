<?php
	/**
	 * Outputs the HTML structure for a single comment.
	 *
	 * This function is used to display each individual comment in the comment list, 
	 * including the comment author's name, avatar, comment content, rating (if available), 
	 * and comment metadata (such as the date and time). It can also handle replies and 
	 * edit links for comments.
	 *
	 * @param WP_Comment $comment The comment object.
	 * @param array $args An array of arguments passed to the comment template. This includes settings for how the comment is displayed (e.g., whether it should be displayed as a <div> or <li>, avatar size, etc.).
	 * @param int $depth The depth of the current comment (used to adjust the indent for replies).
	 */
	if ( ! function_exists( 'mytheme_comment' ) ) {
		function mytheme_comment($comment, $args, $depth) {
			if ( 'div' === $args['style'] ) {
				$tag       = 'div';
				$add_below = 'comment';
			} else {
				$tag       = 'li';
				$add_below = 'div-comment';
			}?>
			<<?php echo $tag; ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?> id="comment-<?php comment_ID() ?>"><?php 
			if ( 'div' != $args['style'] ) { ?>
				<div id="div-comment-<?php comment_ID() ?>" class="comment__inner"><?php
			} ?>
				<div class="comment__header vcard">
					<?php 
						if ( $args['avatar_size'] != 0 ) {
							echo get_avatar( $comment, $args['avatar_size'] ); 
						}
					?>
					<div class="comment__author-inner">
						<div class="comment__author">
							<cite class="fn"><?php comment_author(); ?></cite>
							<span class="says"><?php _e('says:'); ?></span>
						</div>
						<?php
						// Retrieve and display rating stars
						$rating = get_comment_meta( $comment->comment_ID, 'rating', true );
						if ( $rating ) {
							echo '<p class="comment__rating">';
							for ( $i = 1; $i <= 5; $i++ ) {
								if ( $i <= $rating ) {
									echo '<span class="dashicons dashicons-star-filled"></span>';
								} else {
									echo '<span class="dashicons dashicons-star-empty"></span>';
								}
							}
							echo '</p>';
						}
						?>
						</div>
				</div>
				<div class="comment__content">
					<?php if ( $comment->comment_approved == '0' ) { ?>
						<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></p>
					<?php } ?>
					<?php comment_text(); ?>
				</div>
				<div class="comment__footer">
					<time><?php
						/* translators: 1: date, 2: time */
						printf( 
							__('%1$s at %2$s'), 
							get_comment_date(),  
							get_comment_time() 
						); ?>
					</time>
					<div class="comment__link">
					<?php 
						edit_comment_link( __( 'Edit' ), '  ', '' ); 

						// Only show reply link if comment is approved
						if ( $comment->comment_approved != '0' ) {
							comment_reply_link( 
								array_merge( 
									$args, 
									array( 
										'add_below' => $add_below, 
										'depth'     => $depth, 
										'max_depth' => $args['max_depth'] 
									) 
								) 
							);
						}
					?>
					</div>
				</div><?php 
			if ( 'div' != $args['style'] ) : ?>
				</div><?php 
			endif;
		}
	}
