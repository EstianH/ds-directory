<?php
if( !defined( 'ABSPATH' ) ) exit;

$dsfm = DS_FLOATED_MENU::get_instance();

if ( !empty( $dsfm->settings['floated']['menu_focus_panel'] ) )
	echo '<div id="dsfm-focus-panel"></div>';

foreach( $dsfm->menu_locations as $location => $name ) {
	if ( has_nav_menu( $location ) ) {
		$menu_container_classes = array();

		if ( !empty( $dsfm->settings['floated']['menu_height_100'] ) )
			$menu_container_classes[] = 'dsfm-height-100';

		$menu_html = '<div id="' . $location . '-container" class="' . implode( ' ', apply_filters( 'dsfm_menu_container_classes', $menu_container_classes ) ) . '">';

		$menu_icon_classes = array(
			'dsfm-hamburger-icon',
			'ds-d-flex',
			'ds-flex-wrap',
			'ds-flex-align-center'
		);

		if ( !empty( $dsfm->settings['floated']['animate_icon'] ) )
			$menu_icon_classes[] = 'dsfm-animate';

		$menu_html .= '<div class="' . implode( ' ', apply_filters( 'dsfm_menu_icon_classes', $menu_icon_classes ) ) . '">' .
			'<span></span>' .
			'<span></span>' .
			'<span></span>' .
		'</div>';

		$menu_classes = array(
			'menu'
		);

		if ( !empty( $dsfm->settings['floated']['menu_submenu_links_clickable'] ) )
			$menu_classes[] = 'dsfm-submenu-links-clickable';

		$menu_html .= wp_nav_menu( array(
			'theme_location' => $location,                                                      // Menu location.
			'container'      => '',                                                             // Menu HTML wrapper.
			'menu_id'        => $location,                                                      // Menu element id.
			'menu_class'     => implode( ' ', apply_filters( 'dsfm_menu_classes', $menu_classes ) ), // Menu element classes.
			'link_after'     => '<span></span>',                                                // Menu link element text/html.
			'before'         => '',                                                             // HTML before each menu list item element.
			'depth'          => 0,                                                              // Menu hierarchy depth. 0 = infinite.
			'fallback_cb'    => false,                                                          // Menu fallback.
			'echo'           => false                                                           // Echo the menu on true, return on false.
		) );

		$menu_html .= '</div>';

		echo $menu_html;
	}
}
?>
