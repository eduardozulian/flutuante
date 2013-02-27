<?php
/**
 * Flutuante Theme Customizer
 *
 * @package Flutuante
 * @since Flutuante 1.2
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 *
 * @since Flutuante 1.2
 */
function flutuante_customize_register( $wp_customize ) {

	// Ajust changes on-the-fly
	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
	
	
	/**
	 * Customize Image Reloaded Class
	 *
	 * Extend WP_Customize_Image_Control allowing access to uploads made within
	 * the same context
	 * 
	 */
	class Flutuante_Customize_Image_Control extends WP_Customize_Image_Control {
		/**
		 * Constructor.
		 *
		 * @since 3.4.0
		 * @uses WP_Customize_Image_Control::__construct()
		 *
		 * @param WP_Customize_Manager $manager
		 */
		public function __construct( $manager, $id, $args = array() ) {
		
			parent::__construct( $manager, $id, $args );
		       
		}
		
		/**
		 * Search for images within the defined context
		 * If there's no context, it'll bring all images from the library
		 * 
		 */
		public function tab_uploaded() {
			$my_context_uploads = get_posts( array(
			    'post_type'  => 'attachment',
			    'meta_key'   => '_wp_attachment_context',
			    'meta_value' => $this->context,
			    'orderby'    => 'post_date',
			    'nopaging'   => true,
			) );
			
			?>
			
			<div class="uploaded-target"></div>
			
			<?php
			if ( empty( $my_context_uploads ) )
			    return;
			
			foreach ( (array) $my_context_uploads as $my_context_upload )
			    $this->print_tab_image( esc_url_raw( $my_context_upload->guid ) );
		}
		
	}
	
	/*
	 * Branding
	 * Logo, favicon, default image
	 */ 
	$wp_customize->add_section( 'flutuante_branding', array(
		'title'    => __( 'Branding', 'flutuante' ),
		'priority' => 30
	) );
	
	// Branding: logo
	$wp_customize->add_setting( 'flutuante_logo', array(
		'capability'	=> 'edit_theme_options',
	) );
	
    $wp_customize->add_control( new Flutuante_Customize_Image_Control( $wp_customize, 'flutuante_logo', array(
        'label'   	=> __( 'Logo', 'flutuante' ),
        'section'	=> 'flutuante_branding',
        'settings' 	=> 'flutuante_logo',
        'context'	=> 'flutuante-custom-logo'
    ) ) ); 
    	
}
add_action( 'customize_register', 'flutuante_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @since Flutuante 1.2
 */
function flutuante_customize_preview_js() {
	wp_enqueue_script( 'flutuante_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20120827', true );
}
add_action( 'customize_preview_init', 'flutuante_customize_preview_js' );
