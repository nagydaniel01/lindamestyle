<?php
    if ( ! function_exists( 'register_taxonomy_name_taxonomy' ) ) {
		/**
		 * Registers a custom taxonomy 'taxonomy_name'.
		 * 
		 * This taxonomy is applied to posts and custom post types.
		 * It is non-hierarchical and has a default term.
		 */
		function register_taxonomy_name_taxonomy() {

			$labels = array(
				'name'                       => _x( 'Taxonómiák', 'Taxonomy General Name', TEXT_DOMAIN ),
				'singular_name'              => _x( 'Taxonómia', 'Taxonomy Singular Name', TEXT_DOMAIN ),
				'menu_name'                  => __( 'Taxonómia', TEXT_DOMAIN ),
				'all_items'                  => __( 'Összes elem', TEXT_DOMAIN ),
				'parent_item'                => __( 'Szülő elem', TEXT_DOMAIN ),
				'parent_item_colon'          => __( 'Szülő elem:', TEXT_DOMAIN ),
				'new_item_name'              => __( 'Új elem neve', TEXT_DOMAIN ),
				'add_new_item'               => __( 'Új elem hozzáadása', TEXT_DOMAIN ),
				'edit_item'                  => __( 'Elem szerkesztése', TEXT_DOMAIN ),
				'update_item'                => __( 'Elem frissítése', TEXT_DOMAIN ),
				'view_item'                  => __( 'Elem megtekintése', TEXT_DOMAIN ),
				'separate_items_with_commas' => __( 'Elemeket vesszővel válasszon el', TEXT_DOMAIN ),
				'add_or_remove_items'        => __( 'Elemet hozzáadni vagy eltávolítani', TEXT_DOMAIN ),
				'choose_from_most_used'      => __( 'A leggyakrabban használtak közül válasszon', TEXT_DOMAIN ),
				'popular_items'              => __( 'Népszerű elemek', TEXT_DOMAIN ),
				'search_items'               => __( 'Elemek keresése', TEXT_DOMAIN ),
				'not_found'                  => __( 'Nem található', TEXT_DOMAIN ),
				'no_terms'                   => __( 'Nincs elem', TEXT_DOMAIN ),
				'items_list'                 => __( 'Elemek listája', TEXT_DOMAIN ),
				'items_list_navigation'      => __( 'Elemek listájának navigációja', TEXT_DOMAIN ),
			);

			$rewrite = array(
				'slug'                       => 'taxonomy-category',
				'with_front'                 => true,
				'hierarchical'               => false,
			);

			$default_term = array(
				'name' 					 => 'Egyéb',
				'slug' 					 => 'egyeb',
				'description' 			 => '',
			);

			$args = array(
				'labels'                     => $labels,
				'hierarchical'               => false,
				'public'                     => true,
				'show_ui'                    => true,
				'show_admin_column'          => true,
				'show_in_nav_menus'          => true,
				'show_tagcloud'              => true,
				'rewrite'                    => $rewrite,
				'default_term'				 => $default_term
			);

			register_taxonomy( 'taxonomy_name', array( 'post', 'post_type' ), $args );
		}

		//add_action( 'init', 'register_taxonomy_name_taxonomy', 0 );
	}

	if ( ! function_exists( 'register_service_cat_taxonomy' ) ) {
		/**
		 * Registers a hierarchical taxonomy 'service_cat' for services.
		 * 
		 * This taxonomy is used for organizing services into categories.
		 * It has a default term and hierarchical structure.
		 */
		function register_service_cat_taxonomy() {
	
			$labels = array(
				'name'                       => _x( 'Szolgáltatás kategóriák', 'Taxonomy General Name', TEXT_DOMAIN ),
				'singular_name'              => _x( 'Szolgáltatás kategória', 'Taxonomy Singular Name', TEXT_DOMAIN ),
				'menu_name'                  => __( 'Szolgáltatás kategóriák', TEXT_DOMAIN ),
				'all_items'                  => __( 'Összes kategória', TEXT_DOMAIN ),
				'parent_item'                => __( 'Szülő kategória', TEXT_DOMAIN ),
				'parent_item_colon'          => __( 'Szülő kategória:', TEXT_DOMAIN ),
				'new_item_name'              => __( 'Új kategória neve', TEXT_DOMAIN ),
				'add_new_item'               => __( 'Új kategória hozzáadása', TEXT_DOMAIN ),
				'edit_item'                  => __( 'Kategória szerkesztése', TEXT_DOMAIN ),
				'update_item'                => __( 'Kategória frissítése', TEXT_DOMAIN ),
				'view_item'                  => __( 'Kategória megtekintése', TEXT_DOMAIN ),
				'separate_items_with_commas' => __( 'Kategóriákat vesszővel válasszon el', TEXT_DOMAIN ),
				'add_or_remove_items'        => __( 'Kategóriák hozzáadása vagy eltávolítása', TEXT_DOMAIN ),
				'choose_from_most_used'      => __( 'A leggyakrabban használtak közül válasszon', TEXT_DOMAIN ),
				'popular_items'              => __( 'Népszerű kategóriák', TEXT_DOMAIN ),
				'search_items'               => __( 'Kategóriák keresése', TEXT_DOMAIN ),
				'not_found'                  => __( 'Nem található', TEXT_DOMAIN ),
				'no_terms'                   => __( 'Nincs kategória', TEXT_DOMAIN ),
				'items_list'                 => __( 'Kategóriák listája', TEXT_DOMAIN ),
				'items_list_navigation'      => __( 'Kategóriák listájának navigációja', TEXT_DOMAIN ),
			);

			$rewrite = array(
				'slug'                       => 'service-category',
				'with_front'                 => true,
				'hierarchical'               => true,
			);

			$default_term = array(
				'name'                       => 'Egyéb',
				'slug'                       => 'egyeb',
				'description'                => '',
			);
	
			$args = array(
				'labels'                     => $labels,
				'hierarchical'               => true,
				'public'                     => true,
				'show_ui'                    => true,
				'show_admin_column'          => true,
				'show_in_nav_menus'          => true,
				'show_tagcloud'              => true,
				'rewrite'                    => $rewrite,
				'default_term'               => $default_term
			);
	
			register_taxonomy( 'service_cat', array( 'service' ), $args );
		}
	
		add_action( 'init', 'register_service_cat_taxonomy', 0 );
	}	
	
	