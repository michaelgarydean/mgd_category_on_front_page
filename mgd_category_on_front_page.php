<?php
/**
 * Plugin Name:     Category on Front Page
 * Plugin URI:      https://github.com/mykedean/mgd_category_on_front_page
 * Description:     mgd_category_on_front_page
 * Author:          Michael Gary Dean
 * Author URI:      michaeldean.ca
 * Text Domain:     mgd_category_on_front_page
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Mgd_category_on_front_page
 */

function mgd_set_category_on_homepage() {
	
	if ( is_home() && is_front_page() ) :

		/*
		 * Get posts from a category, as selected in the customizer.
		 *
		 * Once a category has been set, alter the query for the category chosen. Otherwise, just get everything.
		 */
		
		$front_page_category = get_theme_mod( 'mgd_category_id_for_posts_on_front_page' );

		//Check that the setting has been selected in the customizer.
		if( $front_page_category != false) {

			//Get the category name
			$front_page_category = strtolower( 
				get_the_category_by_ID( 
					get_theme_mod( 'mgd_category_id_for_posts_on_front_page' )
				)
			);

			//Alter the WordPress query with the selected category
			query_posts( array ( 'category_name' => $front_page_category, 'posts_per_page' => -1 ) );
		}

	endif;
}

add_action( 'pre_get_posts', 'mgd_set_category_on_homepage' ); 

/*
 * Add a category setting and control to be used in the Customizer in the Colors section
 *
 */
function mgd_add_homepage_controls( $wp_customize ) {

	/**
	 * @description 	A class to create a dropdown for all categories in your wordpress site
	 * @see 			https://github.com/paulund/wordpress-theme-customizer-custom-controls
	 */

	class Mgd_Category_Dropdown_Custom_Control extends WP_Customize_Control {
	    private $cats = false;

	    public function __construct($manager, $id, $args = array(), $options = array())
	    {
	        $this->cats = get_categories($options);

	        parent::__construct( $manager, $id, $args );
	    }

	    /**
	     * Render the content of the category dropdown
	     *
	     * @return HTML
	     */
	    public function render_content()
	       {
	            if(!empty($this->cats))
	            {
	                ?>
	                    <label>
	                      <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
	                      <select <?php $this->link(); ?>>
	                           <?php
	                                foreach ( $this->cats as $cat )
	                                {
	                                    printf('<option value="%s" %s>%s</option>', $cat->term_id, selected($this->value(), $cat->term_id, false), $cat->name);
	                                }
	                           ?>
	                      </select>
	                    </label>
	                <?php
	            }
	       }
	 }

//Add a setting to the customizer
$wp_customize->add_setting(
      'mgd_category_id_for_posts_on_front_page', //give it an ID
      array(
          'default' => 1, // Default - uncategorized
      )
  );

//Add
 $wp_customize->add_control(
     new Mgd_Category_Dropdown_Custom_Control (
         $wp_customize, 'mgd_category_id_for_posts_on_front_page', array(
            'label'   => __( 'Show Posts From Category' ),
            'section' => 'static_front_page',
            'settings'   => 'mgd_category_id_for_posts_on_front_page'
        ) ) );

}

add_action('customize_register','mgd_add_homepage_controls');