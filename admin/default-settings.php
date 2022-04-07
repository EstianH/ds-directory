<?php
return array(
	'general' => array(
		'single'             => 1,
		'directory_template' => 'grid',
		'load_condition'     => 'all',
		'grid'               => array(
			'featured_images'    => 1,
			'columns'            => 4
		)
	),
	'directory' => array(
		'item_options' => array(
			'labels'             => array(
				array(
					'label'   => 'Contact number',
					'enabled' => 1
				)
			),
			'labels_show_text'    => 1,
			'labels_show_archive' => 1,
			'title_show'          => 1
		)
	),
	'design' => array(
		'padding' => array(
			'top'     => '',
			'right'   => '',
			'bottom'  => '',
			'left'    => ''
		),
		'text_color'              => '#515151',
		'button_color_bg'         => '#fff',
		'button_color_bg_hover'   => '#515151',
		'button_color_text'       => '#515151',
		'button_color_text_hover' => '#fff'
	)
);
