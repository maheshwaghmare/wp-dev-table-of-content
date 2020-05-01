<?php
/**
 * Table of Content
 * 
 * @param  [type] $tag     [description]
 * @param  string $content [description]
 * @return [type]          [description]
 *
 * @since 1.0.0
 */
if( ! class_exists('WP_Dev_Table_of_Content') ) :

	class WP_Dev_Table_of_Content {

		private static $instance;

		/**
		 *  Initiator
		 */
		public static function get_instance(){
			if ( ! isset( self::$instance ) ) {
				self::$instance = new WP_Dev_Table_of_Content();
			}
			return self::$instance;
		}

		/**
		 *  Constructor
		 */
		public function __construct() {
		}

		function get_tags( $tag, $content = '' ) {
			if ( empty( $content ) )
				$content = get_the_content();
			preg_match_all( "/(<{$tag}>)(.*)(<\/{$tag}>)/", $content, $matches, PREG_SET_ORDER );
			return $matches;
		}

		function get_table_of_content( $content = '', $link = '' ) {

			$toc = '';

			$items = $this->get_tags( 'h([1-4])', $content );

			if ( $items ) {
				$contents_header = 'h' . $items[0][2]; // Duplicate the first <h#> tag in the document.
				// $toc .= $this->styles;
				$toc .= '<div class="table-of-contents">';
				// $toc .= "<$contents_header>" . esc_html( $this->args->header_text ) . "</$contents_header><ul class=\"items\">";
				$toc .= "<ul class=\"items\">";
				$last_item = false;
				$used_ids = [];

				foreach ( $items as $item ) {
					if ( $last_item ) {
						if ( $last_item < $item[2] )
							$toc .= "\n<ul>\n";
						elseif ( $last_item > $item[2] )
							$toc .= "\n</ul></li>\n";
						else
							$toc .= "</li>\n";
					}

					$last_item = $item[2];

					$id = sanitize_title_with_dashes( $item[3] );
					// Append unique suffix if anchor ID isn't unique.
					$count = 2;
					$orig_id = $id;
					while ( in_array( $id, $used_ids ) && $count < 50 ) {
						$id = $orig_id . '-' . $count;
						$count++;
					}
					$used_ids[] = $id;

					$toc .= '<li><a target="_blank" href="' . esc_attr( $link  ) . '#' . esc_attr( $id  ) . '">' . $item[3]  . '</a>';
				}
				$toc .= "</ul>\n</div>\n";
			}

			return $toc;
		}

	}

	/**
	 *  Kicking this off by calling 'get_instance()' method
	 */
	WP_Dev_Table_of_Content::get_instance();

endif;

if( ! function_exists( 'wp_dev_get_table_of_content' ) ) :
	function wp_dev_get_table_of_content( $content = '', $link = '' ) {
		return WP_Dev_Table_of_Content::get_instance()->get_table_of_content( $content, $link );
	}
endif;