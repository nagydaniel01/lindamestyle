<form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search" class="search">
	<label class="form-label visually-hidden" for="search"><?php esc_html_e( 'Keresés erre:', TEXT_DOMAIN ); ?></label>
	<input type="search" name="s" value="<?php echo get_search_query(); ?>" autocomplete="off" id="search" class="form-control" placeholder="<?php esc_attr_e( 'Keresés&hellip;', TEXT_DOMAIN ); ?>">
</form>