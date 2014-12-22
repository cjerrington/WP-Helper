<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Helper_Master_Settings {
    	private $dir;
	private $file;
	private $assets_dir;
	private $assets_url;
	private $settings_base;
	private $settings;

	public function __construct( $file ) {
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );
		$this->settings_base = 'wphm_';

		// Initialise settings
		add_action( 'admin_init', array( $this, 'init' ) );

		// Register plugin settings
		add_action( 'admin_init' , array( $this, 'register_settings' ) );

		// Add settings page to menu
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->file ) , array( $this, 'add_settings_link' ) );
	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function init() {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_item() {
		$page = add_options_page( __( 'WP Helper', 'plugin_textdomain' ) , __( 'WP Helper', 'plugin_textdomain' ) , 'manage_options' , 'plugin_settings' ,  array( $this, 'settings_page' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
	}

	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function settings_assets() {

		// We're including the farbtastic script & styles here because they're needed for the colour picker
		// If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
		wp_enqueue_style( 'farbtastic' );
    wp_enqueue_script( 'farbtastic' );

    // We're including the WP media scripts here because they're needed for the image upload field
    // If you're not including an image upload then you can leave this function call out
    wp_enqueue_media();

    wp_register_script( 'wpt-admin-js', $this->assets_url . 'js/settings.js', array( 'farbtastic', 'jquery' ), '1.0.0' );
    wp_enqueue_script( 'wpt-admin-js' );
	}

	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=plugin_settings">' . __( 'Settings', 'plugin_textdomain' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}

	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields() {

		$settings['wpcleanup'] = array(
			'title'					=> __( 'WP Clean Up', 'plugin_textdomain' ),
			'description'			=> __( 'Clean up WP header. Checking the box will enable to removal of the item.', 'plugin_textdomain' ),
			'fields'				=> array(
				array(
					'id' 			=> 'rsd_links',
					'label'			=> __( 'RSD Links', 'plugin_textdomain' ),
					'description'	=> __( '- Remove the link to the Really Simple Discovery', 'plugin_textdomain' ),
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),
				array(
					'id' 			=> 'cononical_links',
					'label'			=> __( 'Canonical Links', 'plugin_textdomain' ),
					'description'	=> __( '- Remove Canonical Links', 'plugin_textdomain' ),
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),
				array(
					'id' 			=> 'manifest_file',
					'label'			=> __( 'Windows Live Writer manifest file', 'plugin_textdomain' ),
					'description'	=> __( '- Removes the link to the Windows Live Writer manifest file.', 'plugin_textdomain' ),
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),
				array(
					'id' 			=> 'short_link',
					'label'			=> __( 'Short Link', 'plugin_textdomain' ),
					'description'	=> __( '- Removes the short links.', 'plugin_textdomain' ),
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),
				array(
					'id' 			=> 'wp_generator',
					'label'			=> __( 'WP Generator', 'plugin_textdomain' ),
					'description'	=> __( '- Removes the WordPress version i.e. - 4.0.2.', 'plugin_textdomain' ),
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),
				array(
					'id' 			=> 'adjacent_posts',
					'label'			=> __( 'Adjacent Posts', 'plugin_textdomain' ),
					'description'	=> __( '- Removes the relational links for the posts adjacent to the current post.', 'plugin_textdomain' ),
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),
				array(
					'id' 			=> 'index_rel_link',
					'label'			=> __( 'Index Link', 'plugin_textdomain' ),
					'description'	=> __( '- Removes the index link.', 'plugin_textdomain' ),
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),
				array(
					'id' 			=> 'post_rel_link',
					'label'			=> __( 'Parent and Post links', 'plugin_textdomain' ),
					'description'	=> __( '- Removes the Parent and Post Link link.', 'plugin_textdomain' ),
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),
				array(
					'id' 			=> 'feed_links',
					'label'			=> __( 'RSS feeds for post, category and comments', 'plugin_textdomain' ),
					'description'	=> __( '- Removes the post, category and comments RSS feeds.', 'plugin_textdomain' ),
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),
				array(
					'id' 			=> 'version',
					'label'			=> __( 'Display WP Version?', 'plugin_textdomain' ),
					'description'	=> __( '- Removes the WP Version.', 'plugin_textdomain' ),
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),
				array(
					'id' 			=> 'disable_feed',
					'label'			=> __( 'Disable RSS Feeds', 'plugin_textdomain' ),
					'description'	=> __( '- Disables RSS Feeds', 'plugin_textdomain' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
			)
		);
		
		$settings['performance'] = array(
			'title'					=> __( 'Performance', 'plugin_textdomain' ),
			'description'			=> __( 'Help speed up WP with these options', 'plugin_textdomain' ),
			'fields'				=> array(
				array(
					'id' 			=> 'GZip',
					'label'			=> __( 'Enable GZIP compression', 'plugin_textdomain' ),
					'description'	=> __( 'This will speed up your WordPress website drastically and reduces bandwidth usage as well.', 'plugin_textdomain' ),
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),
				array(
					'id' 			=> 'query_version',
					'label'			=> __( 'Remove Query Version', 'plugin_textdomain' ),
					'description'	=> __( '- Remove the version query string from scripts and styles - allows for better caching. i.e. jquery.js?ver=1.8', 'plugin_textdomain' ),
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),
				array(
					'id' 			=> 'xmlrpc',
					'label'			=> __( 'Prevent SSL Check when XMLRPC is not utilized? ', 'plugin_textdomain' ),
					'description'	=> __( '- Prevents WordPress from testing SSL capability on domain.com/xmlrpc.php?rsd when XMLRPC not in use', 'plugin_textdomain' ),
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),
				array(
					'id' 			=> 'autoformat',
					'label'			=> __( 'Disable Auto-Formatting', 'plugin_textdomain' ),
					'description'	=> __( '- Disable Auto-Formatting in Content and Excerpt', 'plugin_textdomain' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				)
			)
		);
		
		$settings['security'] = array(
			'title'					=> __( 'Security', 'plugin_textdomain' ),
			'description'			=> __( 'Add more security to WP', 'plugin_textdomain' ),
			'fields'				=> array(
				array(
					'id' 			=> 'login',
					'label'			=> __( 'Hide login form error messages', 'plugin_textdomain' ),
					'description'	=> __( '- Hide login form error messages', 'plugin_textdomain' ),
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),
				array(
					'id' 			=> 'wp_version_number',
					'label'			=> __( 'Remove WordPress version number', 'plugin_textdomain' ),
					'description'	=> __( '- Remove WordPress version number', 'plugin_textdomain' ),
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),
				array(
					'id' 			=> 'disable_XMLRPC',
					'label'			=> __( 'Disable XMLRPC', 'plugin_textdomain' ),
					'description'	=> __( '- Disable XMLRPC', 'plugin_textdomain' ),
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),
				array(
					'id' 			=> 'post_revisions',
					'label'			=> __( 'Post Revisions', 'plugin_textdomain' ),
					'description'	=> __( '- Set the maximum number of post revisions unless the constant is already set in wp-config.php.<br /> True = enable post revisions. False = disables post revisions. Or 3 or 5 post revisions.', 'plugin_textdomain' ),
					'type'			=> 'radio',
					'options'		=> array( 'true' => 'true', 'false' => 'false', '3' => '3', '5' => '5' ),
					'default'		=> '5'
				),
				array(
					'id' 			=> 'auto_linking',
					'label'			=> __( 'Disable Auto Linking', 'plugin_textdomain' ),
					'description'	=> __( '- Disable Auto Linking of URLs in comments', 'plugin_textdomain' ),
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),
				array(
					'id' 			=> 'self_ping',
					'label'			=> __( 'Disable self-ping', 'plugin_textdomain' ),
					'description'	=> __( '- Disable self-ping', 'plugin_textdomain' ),
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),
				array(
					'id' 			=> 'lost_pass',
					'label'			=> __( 'Remove lost password link', 'plugin_textdomain' ),
					'description'	=> __( '- Remove lost password link if you do not want people trying to reset your password or theirs.', 'plugin_textdomain' ),
					'type'			=> 'checkbox',
					'default'		=> 'on'
				)
			)
		);
		
		$settings['admin'] = array(
			'title'					=> __( 'Admin Modifications', 'plugin_textdomain' ),
			'description'			=> __( 'Add more security to WP', 'plugin_textdomain' ),
			'fields'				=> array(
				array(
					'id' 			=> 'greeting',
					'label'			=> __( 'Greeting for Admin Page' , 'plugin_textdomain' ),
					'description'	=> __( 'Change the standard Howdy to something you like.', 'plugin_textdomain' ),
					'type'			=> 'text',
					'default'		=> 'Welcome, ',
					'placeholder'	=> __( 'Welcome, ', 'plugin_textdomain' )
				),
				array(
					'id' 			=> 'all_settings',
					'label'			=> __( 'All Settings', 'plugin_textdomain' ),
					'description'	=> __( 'Add new Admin menu item "All Settings"', 'plugin_textdomain' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
			)
		);

		$settings = apply_filters( 'plugin_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings() {
		if( is_array( $this->settings ) ) {
			foreach( $this->settings as $section => $data ) {

				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), 'plugin_settings' );

				foreach( $data['fields'] as $field ) {

					// Validation callback for field
					$validation = '';
					if( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field
					$option_name = $this->settings_base . $field['id'];
					register_setting( 'plugin_settings', $option_name, $validation );

					// Add field to page
					add_settings_field( $field['id'], $field['label'], array( $this, 'display_field' ), 'plugin_settings', $section, array( 'field' => $field ) );
				}
			}
		}
	}

	public function settings_section( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * Generate HTML for displaying fields
	 * @param  array $args Field data
	 * @return void
	 */
	public function display_field( $args ) {

		$field = $args['field'];

		$html = '';

		$option_name = $this->settings_base . $field['id'];
		$option = get_option( $option_name );

		$data = '';
		if( isset( $field['default'] ) ) {
			$data = $field['default'];
			if( $option ) {
				$data = $option;
			}
		}

		switch( $field['type'] ) {

			case 'text':
			case 'password':
			case 'number':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>' . "\n";
			break;

			case 'text_secret':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value=""/>' . "\n";
			break;

			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea><br/>'. "\n";
			break;

			case 'checkbox':
				$checked = '';
				if( $option&& 'on' == $option){
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
			break;

			case 'checkbox_multi':
				foreach( $field['options'] as $k => $v ) {
					$checked = false;
					if( in_array( $k, $data ) ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;

			case 'radio':
				foreach( $field['options'] as $k => $v ) {
					$checked = false;
					if( $k == $data ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;

			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
				foreach( $field['options'] as $k => $v ) {
					$selected = false;
					if( $k == $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
			break;

			case 'select_multi':
				$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple="multiple">';
				foreach( $field['options'] as $k => $v ) {
					$selected = false;
					if( in_array( $k, $data ) ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '" />' . $v . '</label> ';
				}
				$html .= '</select> ';
			break;

			case 'image':
				$image_thumb = '';
				if( $data ) {
					$image_thumb = wp_get_attachment_thumb_url( $data );
				}
				$html .= '<img id="' . $option_name . '_preview" class="image_preview" src="' . $image_thumb . '" /><br/>' . "\n";
				$html .= '<input id="' . $option_name . '_button" type="button" data-uploader_title="' . __( 'Upload an image' , 'plugin_textdomain' ) . '" data-uploader_button_text="' . __( 'Use image' , 'plugin_textdomain' ) . '" class="image_upload_button button" value="'. __( 'Upload new image' , 'plugin_textdomain' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '_delete" type="button" class="image_delete_button button" value="'. __( 'Remove image' , 'plugin_textdomain' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '" class="image_data_field" type="hidden" name="' . $option_name . '" value="' . $data . '"/><br/>' . "\n";
			break;

			case 'color':
				?><div class="color-picker" style="position:relative;">
			        <input type="text" name="<?php esc_attr_e( $option_name ); ?>" class="color" value="<?php esc_attr_e( $data ); ?>" />
			        <div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;" class="colorpicker"></div>
			    </div>
			    <?php
			break;

		}

		switch( $field['type'] ) {

			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				$html .= '<br/><span class="description">' . $field['description'] . '</span>';
			break;

			default:
				$html .= '<label for="' . esc_attr( $field['id'] ) . '"><span class="description">' . $field['description'] . '</span></label>' . "\n";
			break;
		}

		echo $html;
	}

	/**
	 * Validate individual settings field
	 * @param  string $data Inputted value
	 * @return string       Validated value
	 */
	public function validate_field( $data ) {
		if( $data && strlen( $data ) > 0 && $data != '' ) {
			$data = urlencode( strtolower( str_replace( ' ' , '-' , $data ) ) );
		}
		return $data;
	}

	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page() {

		// Build page HTML
		$html = '<div class="wrap" id="plugin_settings">' . "\n";
			$html .= '<h2>' . __( 'WP Helper Settings' , 'plugin_textdomain' ) . '</h2>' . "\n";
			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

				// Setup navigation
				$html .= '<ul id="settings-sections" class="subsubsub hide-if-no-js">' . "\n";
					$html .= '<li><a class="tab all current" href="#all">' . __( 'All' , 'plugin_textdomain' ) . '</a></li>' . "\n";

					foreach( $this->settings as $section => $data ) {
						$html .= '<li>| <a class="tab" href="#' . $section . '">' . $data['title'] . '</a></li>' . "\n";
					}

				$html .= '</ul>' . "\n";

				$html .= '<div class="clear"></div>' . "\n";

				// Get settings fields
				ob_start();
				settings_fields( 'plugin_settings' );
				do_settings_sections( 'plugin_settings' );
				$html .= ob_get_clean();

				$html .= '<p class="submit">' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'plugin_textdomain' ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";
			$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";

		echo $html;
	}

}
