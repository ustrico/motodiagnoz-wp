<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2017 Jean-Sebastien Morisset (https://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoJsonSubmenuSchemaJsonLd' ) && class_exists( 'WpssoAdmin' ) ) {

	class WpssoJsonSubmenuSchemaJsonLd extends WpssoAdmin {

		public function __construct( &$plugin, $id, $name, $lib, $ext ) {
			$this->p =& $plugin;
			$this->menu_id = $id;
			$this->menu_name = $name;
			$this->menu_lib = $lib;
			$this->menu_ext = $ext;

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();
		}

		protected function add_meta_boxes() {
			add_meta_box( $this->pagehook.'_schema_json_ld', 
				_x( 'Schema JSON-LD Markup', 'metabox title', 'wpsso-schema-json-ld' ),
				array( &$this, 'show_metabox_schema_json_ld' ), $this->pagehook, 'normal' );
		}

		public function show_metabox_schema_json_ld() {
			$metabox = 'schema_json_ld';
			$this->p->util->do_table_rows( apply_filters( $this->p->cf['lca'].'_'.$metabox.'_general_rows', 
				$this->get_table_rows( $metabox, 'general' ), $this->form ), 'metabox-'.$metabox.'-general' );
		}

		protected function get_table_rows( $metabox, $key ) {
			$table_rows = array();
			switch ( $metabox.'-'.$key ) {
				case 'schema_json_ld-general':

					$table_rows['site_name'] = '<tr class="hide_in_basic">'.
					$this->form->get_th_html( _x( 'Website Name',
						'option label', 'wpsso-schema-json-ld' ), null, 'site_name', array( 'is_locale' => true ) ).
					'<td>'.$this->form->get_input( SucomUtil::get_key_locale( 'site_name', $this->p->options ),
						'long_name', null, null, get_bloginfo( 'name', 'display' ) ).'</td>';

					$table_rows['schema_alt_name'] = '<tr class="hide_in_basic">'.
					$this->form->get_th_html( _x( 'Website Alternate Name',
						'option label', 'wpsso-schema-json-ld' ), null, 'schema_alt_name', array( 'is_locale' => true ) ).
					'<td>'.$this->form->get_input( SucomUtil::get_key_locale( 'schema_alt_name', $this->p->options ),
						'long_name' ).'</td>';

					$table_rows['site_desc'] = '<tr class="hide_in_basic">'.
					$this->form->get_th_html( _x( 'Website Description',
						'option label', 'wpsso-schema-json-ld' ), null, 'site_desc', array( 'is_locale' => true ) ).
					'<td>'.$this->form->get_textarea( SucomUtil::get_key_locale( 'site_desc', $this->p->options ),
						null, null, null, get_bloginfo( 'description', 'display' ) ).'</td>';

					$table_rows['schema_logo_url'] = $this->form->get_th_html( 
						'<a href="https://developers.google.com/structured-data/customize/logos">'.
						_x( 'Organization Logo Image URL', 'option label', 'wpsso-schema-json-ld' ).'</a>',
							null, 'schema_logo_url' ).
					'<td>'.$this->form->get_input( 'schema_logo_url', 'wide' ).'</td>';

					$table_rows['schema_banner_url'] = $this->form->get_th_html( _x( 'Organization Banner (600x60) URL',
						'option label', 'wpsso-schema-json-ld' ), null, 'schema_banner_url' ).
					'<td>'.$this->form->get_input( 'schema_banner_url', 'wide' ).'</td>';

					$table_rows['schema_img_max'] = $this->form->get_th_html( _x( 'Maximum Images to Include',
						'option label', 'wpsso-schema-json-ld' ), null, 'schema_img_max' ).
					'<td>'.$this->form->get_select( 'schema_img_max', 
						range( 0, $this->p->cf['form']['max_media_items'] ), 'short', null, true ).
					( empty( $this->form->options['og_vid_prev_img'] ) ?
						'' : ' <em>'._x( 'video preview images are enabled (and included first)',
							'option comment', 'wpsso-schema-json-ld' ).'</em>' ).'</td>';

					$table_rows['schema_img'] = $this->form->get_th_html( _x( 'Schema Image Dimensions',
						'option label', 'wpsso-schema-json-ld' ), null, 'schema_img_dimensions' ).
					'<td>'.$this->form->get_image_dimensions_input( 'schema_img' ).'</td>';	// $use_opts = false

					$table_rows['schema_desc_len'] = '<tr class="hide_in_basic">'.
					$this->form->get_th_html( _x( 'Maximum Description Length',
						'option label', 'wpsso-schema-json-ld' ), null, 'schema_desc_len' ).
					'<td>'.$this->form->get_input( 'schema_desc_len', 'short' ).' '.
						_x( 'characters or less', 'option comment', 'wpsso-schema-json-ld' ).'</td>';

					$table_rows['schema_author_name'] = '<tr class="hide_in_basic">'.
					$this->form->get_th_html( _x( 'Author / Person Name Format',
						'option label', 'wpsso-schema-json-ld' ), null, 'schema_author_name' ).
					'<td>'.$this->form->get_select( 'schema_author_name', 
						$this->p->cf['form']['user_name_fields'] ).'</td>';

					$schema_types = $this->p->schema->get_schema_types_select();	// $add_none = true

					foreach ( array( 
						'home_index' => _x( 'Item Type for Blog Front Page', 'option label', 'wpsso-schema-json-ld' ),
						'home_page' => _x( 'Item Type for Static Front Page', 'option label', 'wpsso-schema-json-ld' ),
						'archive_page' => _x( 'Item Type for Archive Page', 'option label', 'wpsso-schema-json-ld' ),
						'user_page' => _x( 'Item Type for User / Author Page', 'option label', 'wpsso-schema-json-ld' ),
						'search_page' => _x( 'Item Type for Search Results Page', 'option label', 'wpsso-schema-json-ld' ),
					) as $type_name => $type_label ) {
						switch ( $type_name ) {
							case 'archive_page':
							case 'user_page':
							case 'search_page':
								$tr = '<tr class="hide_in_basic">';
								break;
							default:
								$tr = '';
								break;
						}
						$table_rows['schema_type_for_'.$type_name] = $tr.
						$this->form->get_th_html( $type_label, null, 'schema_type_for_'.$type_name ).
						'<td>'.$this->form->get_select( 'schema_type_for_'.$type_name, $schema_types, 'long_name' ).'</td>';
					}

					$schema_by_ptn = '';
					foreach ( $this->p->util->get_post_types() as $post_type )
						$schema_by_ptn .= '<p>'.$this->form->get_select( 'schema_type_for_'.$post_type->name,
							$schema_types, 'long_name' ).' for '.$post_type->label.'</p>'."\n";

					$table_rows['schema_type_for_ptn'] = $this->form->get_th_html( _x( 'Item Type by Post Type',
						'option label', 'wpsso-schema-json-ld' ), null, 'schema_type_for_ptn' ).
					'<td>'.$schema_by_ptn.'</td>';

					$table_rows['schema_review_item_type'] = '<tr class="hide_in_basic">'.
					$this->form->get_th_html( _x( 'Default Reviewed Item Type', 
						'option label', 'wpsso-schema-json-ld' ), null, 'schema_review_item_type' ).
					'<td>'.$this->form->get_select( 'schema_review_item_type', $schema_types, 'long_name' ).'</td>';

					$table_rows['plugin_cf_recipe_ingredients'] = '<tr class="hide_in_basic">'.
					$this->form->get_th_html( _x( 'Recipe Ingredients Custom Field',
						'option label', 'wpsso-schema-json-ld' ), null, 'plugin_cf_recipe_ingredients' ).
					'<td>'.$this->form->get_input( 'plugin_cf_recipe_ingredients' ).'</td>';

					break;
			}
			return $table_rows;
		}
	}
}

?>
