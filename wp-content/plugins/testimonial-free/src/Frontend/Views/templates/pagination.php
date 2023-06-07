<?php
/**
 * Pagination.
 *
 * This template can be overridden by copying it to yourtheme/testimonial-pro/templates/pagination.php
 *
 * @package    Testimonial_Free
 * @subpackage Testimonial_Free/Frontend
 */

if ( $post_query->max_num_pages > 1 ) {
	if ( ( 'grid' === $layout ) && $grid_pagination ) {
		echo '<div class="tfree-col-xl-1 sp-tfree-pagination-area">';
		$paged_format = '?paged' . $post_id . '=%#%';
		$paged_query  = 'paged' . $post_id;
		$big          = 999999999; // need an unlikely integer.
		$items        = paginate_links(
			array(
				'format'    => $paged_format,
				'prev_next' => true,
				'current'   => isset( $_GET[ "$paged_query" ] ) ? wp_unslash( absint( $_GET[ "$paged_query" ] ) ) : 1,
				'total'     => $post_query->max_num_pages,
				'type'      => 'array',
				'prev_text' => '<i class="fa fa-angle-left"></i>',
				'next_text' => '<i class="fa fa-angle-right"></i>',
			)
		);
		$pagination   = "<ul class=\"sp-tfree-pagination\">\n\t<li>";
		$pagination  .= join( "</li>\n\t<li>", $items );
		$pagination  .= "</li>\n</ul>\n";

		echo wp_kses_post( $pagination );
		echo '</div>';

	}
}

