<form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search" class="search">
	<label class="form-label visually-hidden" for="search"><?php echo esc_html__( 'Search for:', TEXT_DOMAIN ); ?></label>
	<input type="search" name="s" value="<?php echo get_search_query(); ?>" autocomplete="off" id="search" class="form-control" placeholder="<?php echo esc_attr__( 'What would you like to search for?', TEXT_DOMAIN ); ?>">
</form>