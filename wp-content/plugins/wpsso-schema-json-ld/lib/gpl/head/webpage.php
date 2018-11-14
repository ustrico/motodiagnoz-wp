<?php
/*
 * IMPORTANT: READ THE LICENSE AGREEMENT CAREFULLY.
 *
 * BY INSTALLING, COPYING, RUNNING, OR OTHERWISE USING THE WPSSO SCHEMA JSON-LD
 * MARKUP (WPSSO JSON) PRO APPLICATION, YOU AGREE TO BE BOUND BY THE TERMS OF
 * ITS LICENSE AGREEMENT.
 * 
 * License: Nontransferable License for a WordPress Site Address URL
 * License URI: https://wpsso.com/wp-content/plugins/wpsso-schema-json-ld/license/pro.txt
 *
 * IF YOU DO NOT AGREE TO THE TERMS OF ITS LICENSE AGREEMENT, PLEASE DO NOT
 * INSTALL, RUN, COPY, OR OTHERWISE USE THE WPSSO SCHEMA JSON-LD MARKUP (WPSSO
 * JSON) PRO APPLICATION.
 * 
 * Copyright 2016-2017 Jean-Sebastien Morisset (https://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoJsonGplHeadWebPage' ) ) {

	class WpssoJsonGplHeadWebPage {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_https_schema_org_webpage' => array(
					'json_data_https_schema_org_webpage' => 5,	// $json_data, $mod, $mt_og, $type_id, $is_main
					'json_data_https_schema_org_blogposting' => 5,	// $json_data, $mod, $mt_og, $type_id, $is_main
				),
			) );
		}

		public function filter_json_data_https_schema_org_webpage( $json_data, $mod, $mt_og, $type_id, $is_main ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$ret = array();
			$lca = $this->p->cf['lca'];

			// blogposting is a child of article
			// use the article image size and add the headline property
			if ( $this->p->schema->is_schema_type_child_of( $type_id, 'article' ) ) {
				$org_logo_key = 'org_banner_url';		// use a banner for all article sub-types
				$size_name = $lca.'-schema-article';		// same size, but minimum width is 696px

				/*
				 * Property:
				 * 	headline
				 */
				$headline_max_len = WpssoJsonConfig::$cf['schema']['article']['headline']['max_len'];
				$ret['headline'] = $this->p->webpage->get_title( $headline_max_len, '...', $mod );

			} else {
				$org_logo_key = 'org_logo_url';
				$size_name = $this->p->cf['lca'].'-schema';
			}

			/*
			 * Property:
			 * 	datepublished
			 * 	datemodified
			 */
			WpssoSchema::add_data_itemprop_from_assoc( $ret, $mt_og, array(
				'datepublished' => 'article:published_time',
				'datemodified' => 'article:modified_time',
			) );

			/*
			 * Property:
			 *	inLanguage
			 */
			$ret['inLanguage'] = get_locale();

			/*
			 * Property:
			 *	publisher as https://schema.org/Organization
			 */
			$org_id = is_object( $mod['obj'] ) ?
				$mod['obj']->get_options( $mod['id'], 'schema_pub_org_id' ) : 'site';	// 'none', 'site', or number (including 0)

			if ( $this->p->debug->enabled )
				$this->p->debug->log( 'publisher / organization id is '.
					( empty( $org_id ) ? 'empty' : $org_id ) );

			WpssoSchema::add_single_organization_data( $ret['publisher'], $mod, $org_id, $org_logo_key, false );	// $list_element = false

			/*
			 * Property:
			 *	author as https://schema.org/Person
			 *	contributor as https://schema.org/Person
			 */
			WpssoSchema::add_author_coauthor_data( $ret, $mod );

			/*
			 * Property:
			 *	image as https://schema.org/ImageObject
			 *	video as https://schema.org/VideoObject
			 */
			WpssoJsonSchema::add_media_data( $ret, $mod, $mt_og, $size_name );

			if ( empty( $ret['image'] ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'creativework image is missing and required' );
				if ( is_admin() && ( ! $mod['is_post'] || $mod['post_status'] === 'publish' ) )
					$this->p->notice->err( $this->p->msgs->get( 'notice-missing-schema-image' ) );
			}

			/*
			 * Property:
			 *	commentCount
			 *	comment as https://schema.org/Comment
			 */
			WpssoJsonSchema::add_comment_list_data( $ret, $mod );

			return WpssoSchema::return_data_from_filter( $json_data, $ret );
		}
	}
}

?>
