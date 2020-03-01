<?php

if( class_exists( 'WXR_Importer') ) {

	class Fusion_WXR_Importer extends WXR_Importer {

		public function __construct( $options = array() ) {
			parent::__construct( $options );
		}

		/**
		 * Parse a post node into post data.
		 *
		 * @param DOMElement $node Parent node of post data (typically `item`).
		 * @return array|WP_Error Post data array on success, error otherwise.
		 */
		protected function parse_post_node( $node ) {
			$data = array();
			$meta = array();
			$comments = array();
			$terms = array();

			foreach ( $node->childNodes as $child ) {
				// We only care about child elements
				if ( $child->nodeType !== XML_ELEMENT_NODE ) {
					continue;
				}

				switch ( $child->tagName ) {
					case 'wp:post_type':
						$data['post_type'] = $child->textContent;
						break;

					case 'title':
						$data['post_title'] = $child->textContent;
						break;

					case 'guid':
						$data['guid'] = $child->textContent;
						break;

					case 'dc:creator':
						$data['post_author'] = $child->textContent;
						break;

					case 'content:encoded':
						$data['post_content'] = $child->textContent;
						break;

					case 'excerpt:encoded':
						$data['post_excerpt'] = $child->textContent;
						break;

					case 'wp:post_id':
						$data['post_id'] = $child->textContent;
						break;

					case 'wp:post_date':
						$data['post_date'] = $child->textContent;
						break;

					case 'wp:post_date_gmt':
						$data['post_date_gmt'] = $child->textContent;
						break;

					case 'wp:comment_status':
						$data['comment_status'] = $child->textContent;
						break;

					case 'wp:ping_status':
						$data['ping_status'] = $child->textContent;
						break;

					case 'wp:post_name':
						$data['post_name'] = $child->textContent;
						break;

					case 'wp:status':
						$data['post_status'] = $child->textContent;

						if ( $data['post_status'] === 'auto-draft' ) {
							// Bail now
							return new WP_Error(
								'wxr_importer.post.cannot_import_draft',
								__( 'Cannot import auto-draft posts', 'Avada' ),
								$data
							);
						}
						break;

					case 'wp:post_parent':
						$data['post_parent'] = $child->textContent;
						break;

					case 'wp:menu_order':
						$data['menu_order'] = $child->textContent;
						break;

					case 'wp:post_password':
						$data['post_password'] = $child->textContent;
						break;

					case 'wp:is_sticky':
						$data['is_sticky'] = $child->textContent;
						break;

					case 'wp:attachment_url':
						$data['attachment_url'] = $child->textContent;
						break;

					case 'wp:postmeta':
						$meta_item = $this->parse_meta_node( $child );
						// Start ThemeFusion edit.
						/*
						if ( ! empty( $meta_item ) ) {
							$meta[] = $meta_item;
						}
						*/
						// End ThemeFusion edit.
						$meta[] = $meta_item;

						break;

					case 'wp:comment':
						$comment_item = $this->parse_comment_node( $child );
						if ( ! empty( $comment_item ) ) {
							$comments[] = $comment_item;
						}
						break;

					case 'category':
						$term_item = $this->parse_category_node( $child );
						if ( ! empty( $term_item ) ) {
							$terms[] = $term_item;
						}
						break;
				}
			}

			// Start ThemeFusion edit.
			// $meta[] = array( 'key' => 'fusion_demo_import', 'value' => 'true' );
			// End ThemeFusion edit.

			return compact( 'data', 'meta', 'comments', 'terms' );
		}

		/**
		 * Parse a meta node into meta data.
		 *
		 * @param DOMElement $node Parent node of meta data (typically `wp:postmeta` or `wp:commentmeta`).
		 * @return array|null Meta data array on success, or null on error.
		 */
		protected function parse_meta_node( $node ) {
			foreach ( $node->childNodes as $child ) {
				// We only care about child elements
				if ( $child->nodeType !== XML_ELEMENT_NODE ) {
					continue;
				}

				switch ( $child->tagName ) {
					case 'wp:meta_key':
						$key = $child->textContent;
						break;

					case 'wp:meta_value':
						$value = $child->textContent;
						break;
				}
			}

			// Start ThemeFusion edit.
			/*
			if ( empty( $key ) || empty( $value ) ) {
				return null;
			}
			*/
			// End ThemeFusion edit.

			return compact( 'key', 'value' );
		}

		/**
		 * Process and import post meta items.
		 *
		 * @param array $meta List of meta data arrays
		 * @param int $post_id Post to associate with
		 * @param array $post Post data
		 * @return int|WP_Error Number of meta items imported on success, error otherwise.
		 */
		protected function process_post_meta( $meta, $post_id, $post ) {
			// Start ThemeFusion edit.
			/*
			if ( empty( $meta ) ) {
				return true;
			}
			*/
			// End ThemeFusion edit.

			foreach ( $meta as $meta_item ) {
				/**
				 * Pre-process post meta data.
				 *
				 * @param array $meta_item Meta data. (Return empty to skip.)
				 * @param int $post_id Post the meta is attached to.
				 */
				$meta_item = apply_filters( 'wxr_importer.pre_process.post_meta', $meta_item, $post_id );
				// Start ThemeFusion edit.
				/*
				if ( empty( $meta_item ) ) {
					return false;
				}
				*/
				// End ThemeFusion edit.

				$key = apply_filters( 'import_post_meta_key', $meta_item['key'], $post_id, $post );
				$value = false;

				if ( '_edit_last' === $key ) {
					$value = intval( $meta_item['value'] );
					if ( ! isset( $this->mapping['user'][ $value ] ) ) {
						// Skip!
						continue;
					}

					$value = $this->mapping['user'][ $value ];
				}

				if ( $key ) {
					// export gets meta straight from the DB so could have a serialized string
					if ( ! $value ) {
						$value = maybe_unserialize( $meta_item['value'] );
					}

					add_post_meta( $post_id, $key, $value );
					do_action( 'import_post_meta', $post_id, $key, $value );

					// if the post has a featured image, take note of this in case of remap
					if ( '_thumbnail_id' === $key ) {
						$this->featured_images[ $post_id ] = (int) $value;
					}
				}
			}

			return true;
		}

		/**
		 * Attempt to download a remote file attachment
		 *
		 * @param string $url URL of item to fetch
		 * @param array $post Attachment details
		 * @return array|WP_Error Local file location details on success, WP_Error otherwise
		 */
		protected function fetch_remote_file( $url, $post ) {
			// extract the file name and extension from the url
			$file_name = basename( $url );

			// get placeholder file in the upload dir with a unique, sanitized filename
			$upload = wp_upload_bits( $file_name, 0, '', $post['upload_date'] );
			if ( $upload['error'] ) {
				return new WP_Error( 'upload_dir_error', $upload['error'] );
			}

			// fetch the remote url and write it to the placeholder file.
			// Start ThemeFusion edit.
			/*
			$response = wp_remote_get( $url, array(
				'stream' => true,
				'filename' => $upload['file'],
			) );
			*/

			$headers = avada_wp_get_http( $url, $upload['file'] );
			// End ThemeFusion edit.

			// request failed
			if ( is_wp_error( $headers ) ) {
				unlink( $upload['file'] );
				return $headers;
			}

			// Start ThemeFusion edit.
			/*
			$code = (int) wp_remote_retrieve_response_code( $response );
			*/
			$code = $headers['response'];
			// End ThemeFusion edit.

			// make sure the fetch was successful
			if ( $code !== 200 ) {
				unlink( $upload['file'] );
				return new WP_Error(
					'import_file_error',
					sprintf(
						__( 'Remote server returned %1$d %2$s for %3$s', 'Avada' ),
						$code,
						get_status_header_desc( $code ),
						$url
					)
				);
			}

			$filesize = filesize( $upload['file'] );
			// Start ThemeFusion edit.
			/*
			$headers = wp_remote_retrieve_headers( $response );
			*/
			// End ThemeFusion edit.

			if ( isset( $headers['content-length'] ) && $filesize !== (int) $headers['content-length'] ) {
				unlink( $upload['file'] );
				return new WP_Error( 'import_file_error', __( 'Remote file is incorrect size', 'Avada' ) );
			}

			if ( 0 === $filesize ) {
				unlink( $upload['file'] );
				return new WP_Error( 'import_file_error', __( 'Zero size file downloaded', 'Avada' ) );
			}

			$max_size = (int) $this->max_attachment_size();
			if ( ! empty( $max_size ) && $filesize > $max_size ) {
				unlink( $upload['file'] );
				$message = sprintf( __( 'Remote file is too large, limit is %s', 'Avada' ), size_format( $max_size ) );
				return new WP_Error( 'import_file_error', $message );
			}

			return $upload;
		}

		/**
		 * Does the post exist?
		 *
		 * @param array $data Post data to check against.
		 * @return int|bool Existing post ID if it exists, false otherwise.
		 */
		protected function post_exists( $data ) {

			// Constant-time lookup if we prefilled
			$exists_key = $data['guid'];

			/**
			 * ThemeFusion edit.
			 * This had to be disabled as after 'xml replacements' are made importing post might have same GUID as already existing post.
			 * Note that 'prefill_existing_posts' option should be set to false.
			 */
			/*
			if ( $this->options['prefill_existing_posts'] ) {
				return isset( $this->exists['post'][ $exists_key ] ) ? $this->exists['post'][ $exists_key ] : false;
			}
			*/

			// No prefilling, but might have already handled it
			/*
			// This one had to be disabled as well, XML file contains posts with same GUID
			if ( isset( $this->exists['post'][ $exists_key ] ) ) {
				return $this->exists['post'][ $exists_key ];
			}
			*/

			// ThemeFusion edit.
			// We're just returning false if this post isn't already imported.
			return false;

			/**
			 * ThemeFusion edit.
			 * This had to be disabled as menu items, which have empty title and content, could be published at exact same date (including seconds).
			 */
			/*
			// Still nothing, try post_exists, and cache it
			$exists = post_exists( $data['post_title'], $data['post_content'], $data['post_date'] );
			$this->exists['post'][ $exists_key ] = $exists;

			return $exists;
			*/
		}

		protected function parse_term_node( $node, $type = 'term' ) {
			$data = array();
			$meta = array();

			$tag_name = array(
				'id'          => 'wp:term_id',
				'taxonomy'    => 'wp:term_taxonomy',
				'slug'        => 'wp:term_slug',
				'parent'      => 'wp:term_parent',
				'name'        => 'wp:term_name',
				'description' => 'wp:term_description',
			);
			$taxonomy = null;

			// Special casing!
			switch ( $type ) {
				case 'category':
					$tag_name['slug']        = 'wp:category_nicename';
					$tag_name['parent']      = 'wp:category_parent';
					$tag_name['name']        = 'wp:cat_name';
					$tag_name['description'] = 'wp:category_description';
					$tag_name['taxonomy']    = null;

					$data['taxonomy'] = 'category';
					break;

				case 'tag':
					$tag_name['slug']        = 'wp:tag_slug';
					$tag_name['parent']      = null;
					$tag_name['name']        = 'wp:tag_name';
					$tag_name['description'] = 'wp:tag_description';
					$tag_name['taxonomy']    = null;

					$data['taxonomy'] = 'post_tag';
					break;
			}

			foreach ( $node->childNodes as $child ) {
				// We only care about child elements
				if ( $child->nodeType !== XML_ELEMENT_NODE ) {
					continue;
				}

				$key = array_search( $child->tagName, $tag_name );
				if ( $key ) {
					$data[ $key ] = $child->textContent;
				} elseif ( $child->tagName === 'wp:termmeta' ) {
					$meta_item = $this->parse_meta_node( $child );
					if ( ! empty( $meta_item ) ) {
						$meta[] = $meta_item;
					}
				}
				// ThemeFusion edit: elseif condition is added ^.
			}

			if ( empty( $data['taxonomy'] ) ) {
				return null;
			}

			// Compatibility with WXR 1.0
			if ( $data['taxonomy'] === 'tag' ) {
				$data['taxonomy'] = 'post_tag';
			}

			return compact( 'data', 'meta' );
		}

		protected function process_term( $data, $meta ) {
			/**
			 * Pre-process term data.
			 *
			 * @param array $data Term data. (Return empty to skip.)
			 * @param array $meta Meta data.
			 */
			$data = apply_filters( 'wxr_importer.pre_process.term', $data, $meta );
			if ( empty( $data ) ) {
				return false;
			}

			$original_id = isset( $data['id'] )      ? (int) $data['id']      : 0;
			$parent_id   = isset( $data['parent'] )  ? (int) $data['parent']  : 0;

			$mapping_key = sha1( $data['taxonomy'] . ':' . $data['slug'] );
			$existing = $this->term_exists( $data );
			if ( $existing ) {

				/**
				 * Term processing already imported.
				 *
				 * @param array $data Raw data imported for the term.
				 */
				do_action( 'wxr_importer.process_already_imported.term', $data );

				$this->mapping['term'][ $mapping_key ] = $existing;
				$this->mapping['term_id'][ $original_id ] = $existing;
				return false;
			}

			// WP really likes to repeat itself in export files
			if ( isset( $this->mapping['term'][ $mapping_key ] ) ) {
				return false;
			}

			$termdata = array();
			$allowed = array(
				'slug' => true,
				'description' => true,
			);

			// Map the parent comment, or mark it as one we need to fix
			// TODO: add parent mapping and remapping
			/*$requires_remapping = false;
			if ( $parent_id ) {
				if ( isset( $this->mapping['term'][ $parent_id ] ) ) {
					$data['parent'] = $this->mapping['term'][ $parent_id ];
				} else {
					// Prepare for remapping later
					$meta[] = array( 'key' => '_wxr_import_parent', 'value' => $parent_id );
					$requires_remapping = true;

					// Wipe the parent for now
					$data['parent'] = 0;
				}
			}*/

			foreach ( $data as $key => $value ) {
				if ( ! isset( $allowed[ $key ] ) ) {
					continue;
				}

				$termdata[ $key ] = $data[ $key ];
			}

			$result = wp_insert_term( $data['name'], $data['taxonomy'], $termdata );
			if ( is_wp_error( $result ) ) {
				$this->logger->warning( sprintf(
					__( 'Failed to import %s %s', 'Avada' ),
					$data['taxonomy'],
					$data['name']
				) );
				$this->logger->debug( $result->get_error_message() );
				do_action( 'wp_import_insert_term_failed', $result, $data );

				/**
				 * Term processing failed.
				 *
				 * @param WP_Error $result Error object.
				 * @param array $data Raw data imported for the term.
				 * @param array $meta Meta data supplied for the term.
				 */
				do_action( 'wxr_importer.process_failed.term', $result, $data, $meta );
				return false;
			}

			$term_id = $result['term_id'];

			$this->mapping['term'][ $mapping_key ] = $term_id;
			$this->mapping['term_id'][ $original_id ] = $term_id;

			$this->logger->info( sprintf(
				__( 'Imported "%s" (%s)', 'Avada' ),
				$data['name'],
				$data['taxonomy']
			) );
			$this->logger->debug( sprintf(
				__( 'Term %d remapped to %d', 'Avada' ),
				$original_id,
				$term_id
			) );

			// ThemeFusion edit.
			$this->process_term_meta( $meta, $term_id, $data );

			do_action( 'wp_import_insert_term', $term_id, $data );

			/**
			 * Term processing completed.
			 *
			 * @param int $term_id New term ID.
			 * @param array $data Raw data imported for the term.
			 */
			do_action( 'wxr_importer.processed.term', $term_id, $data );
		}

		/**
		 * Process and import term meta items.
		 *
		 * @param array $meta List of meta data arrays
		 * @param int $term_id Term ID to associate with
		 * @param array $term Term data
		 * @return int|bool Number of meta items imported on success, false otherwise.
		 */
		protected function process_term_meta( $meta, $term_id, $data ) {
			if ( empty( $meta ) ) {
				return true;
			}
			foreach ( $meta as $meta_item ) {
				/**
				 * Pre-process term meta data.
				 *
				 * @param array $meta_item Meta data. (Return empty to skip.)
				 * @param int $term_id Term the meta is attached to.
				 */
				$meta_item = apply_filters( 'wxr_importer.pre_process.term_meta', $meta_item, $term_id );
				if ( empty( $meta_item ) ) {
					return false;
				}
				$key = apply_filters( 'import_term_meta_key', $meta_item['key'], $term_id, $data );
				if ( ! $key ) {
					continue;
				}
				// export gets meta straight from the DB so could have a serialized string
				$value = maybe_unserialize( $meta_item['value'] );
				add_term_meta( $term_id, $key, $value );
				do_action( 'import_term_meta', $term_id, $key, $value );
			}
			return true;
		}
	}

} // class_exists( 'WXR_Importer' )
