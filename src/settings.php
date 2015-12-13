<?php
/**
 * Singleton for setting up Purgely settings.
 */
class Purgely_Settings {
	/**
	 * The one instance of Purgely_Settings.
	 *
	 * @since 1.0.0.
	 *
	 * @var Purgely_Settings
	 */
	private static $instance;

	/**
	 * The settings values for the plugin.
	 *
	 * @since 1.0.0.
	 *
	 * @var array Holds all of the individual settings for the plugin.
	 */
	var $settings = array();

	/**
	 * Instantiate or return the one Purgely_Settings instance.
	 *
	 * @since 1.0.0.
	 *
	 * @return Purgely_Settings
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initiate actions.
	 *
	 * @since 1.0.0.
	 *
	 * @return Purgely_Settings
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
	}

	/**
	 * Setup the configuration screen for the plugin.
	 *
	 * @since 1.0.0.
	 *
	 * @return void
	 */
	function add_admin_menu() {
		add_submenu_page(
			'tools.php',
			__( 'Purgely', 'purgely' ),
			__( 'Purgely', 'purgely' ),
			'manage_options',
			'purgely',
			array( $this, 'options_page' )
		);
	}

	/**
	 * Initialize all of the settings.
	 *
	 * @since 1.0.0.
	 *
	 * @return void
	 */
	function settings_init() {
		// Set up the option name, "purgely-settings". All values will be in this array.
		register_setting(
			'purgely-settings',
			'purgely-settings',
			array( $this, 'sanitize_settings' )
		);

		// Set up the settings section.
		add_settings_section(
			'purgely-settings_section',
			__( 'Configure your site\'s caching behavior by overriding default cache setting values using the settings below.', 'purgely' ),
			array( $this, 'settings_section_callback' ),
			'purgely-settings'
		);

		// Register all of the individual settings.
		add_settings_field(
			'fastly_key',
			__( 'Fastly API Key', 'purgely' ),
			array( $this, 'fastly_key_render' ),
			'purgely-settings',
			'purgely-settings_section'
		);

		add_settings_field(
			'fastly_service_id',
			__( 'Fastly Service ID', 'purgely' ),
			array( $this, 'fastly_service_id_render' ),
			'purgely-settings',
			'purgely-settings_section'
		);

		add_settings_field(
			'allow_purge_all',
			__( 'Allow Purge All?', 'purgely' ),
			array( $this, 'allow_purge_all_render' ),
			'purgely-settings',
			'purgely-settings_section'
		);

		add_settings_field(
			'api_endpoint',
			__( 'Fastly API Endpoint', 'purgely' ),
			array( $this, 'api_endpoint_render' ),
			'purgely-settings',
			'purgely-settings_section'
		);

		add_settings_field(
			'enable_stale_while_revalidate',
			__( 'Enable Stale While Revalidate', 'purgely' ),
			array( $this, 'enable_stale_while_revalidate_render' ),
			'purgely-settings',
			'purgely-settings_section'
		);

		add_settings_field(
			'stale_while_revalidate_ttl',
			__( 'Stale While Revalidate TTL', 'purgely' ),
			array( $this, 'stale_while_revalidate_ttl_render' ),
			'purgely-settings',
			'purgely-settings_section'
		);

		add_settings_field(
			'enable_stale_while_error',
			__( 'Enable Stale While Error', 'purgely' ),
			array( $this, 'enable_stale_while_error_render' ),
			'purgely-settings',
			'purgely-settings_section'
		);

		add_settings_field(
			'stale_while_error_ttl',
			__( 'Stale While Error TTL', 'purgely' ),
			array( $this, 'stale_while_error_ttl_render' ),
			'purgely-settings',
			'purgely-settings_section'
		);

		add_settings_field(
			'surrogate_control_ttl',
			__( 'Surrogate Control TTL', 'purgely' ),
			array( $this, 'surrogate_control_render' ),
			'purgely-settings',
			'purgely-settings_section'
		);

		add_settings_field(
			'default_purge_type',
			__( 'Default Purge Type', 'purgely' ),
			array( $this, 'default_purge_type_render' ),
			'purgely-settings',
			'purgely-settings_section'
		);
	}

	/**
	 * Render the setting input.
	 *
	 * @since 1.0.0.
	 *
	 * @return void
	 */
	public function fastly_key_render() {
		$options = $this->get_settings();
		?>
		<input type='text' name='purgely-settings[fastly_key]' value='<?php echo esc_attr( $options['fastly_key'] ); ?>'>
		<?php
	}

	/**
	 * Render the setting input.
	 *
	 * @since 1.0.0.
	 *
	 * @return void
	 */
	public function fastly_service_id_render() {
		$options = $this->get_settings();
		?>
		<input type='text' name='purgely-settings[fastly_service_id]' value='<?php echo esc_attr( $options['fastly_service_id'] ); ?>'>
		<?php
	}

	/**
	 * Render the setting input.
	 *
	 * @since 1.0.0.
	 *
	 * @return void
	 */
	public function allow_purge_all_render() {
		$options = $this->get_settings();
		?>
		<input type='radio' name='purgely-settings[allow_purge_all]' <?php checked( isset( $options['allow_purge_all'] ) && true === $options['allow_purge_all'] ); ?> value='true'>Yes
		<input type='radio' name='purgely-settings[allow_purge_all]' <?php checked( isset( $options['allow_purge_all'] ) && false === $options['allow_purge_all'] ); ?> value='false'>No
		<?php
	}

	/**
	 * Render the setting input.
	 *
	 * @since 1.0.0.
	 *
	 * @return void
	 */
	public function api_endpoint_render() {
		$options = $this->get_settings();
		?>
		<input type='text' name='purgely-settings[api_endpoint]' value='<?php echo esc_attr( $options['api_endpoint'] ); ?>'>
		<?php
	}

	/**
	 * Render the setting input.
	 *
	 * @since 1.0.0.
	 *
	 * @return void
	 */
	public function enable_stale_while_revalidate_render() {
		$options = $this->get_settings();
		?>
		<input type='radio' name='purgely-settings[enable_stale_while_revalidate]' <?php checked( isset( $options['enable_stale_while_revalidate'] ) && true === $options['enable_stale_while_revalidate'] ); ?> value='true'>Yes
		<input type='radio' name='purgely-settings[enable_stale_while_revalidate]' <?php checked( isset( $options['enable_stale_while_revalidate'] ) && false === $options['enable_stale_while_revalidate']); ?> value='false'>No
		<?php
	}

	/**
	 * Render the setting input.
	 *
	 * @since 1.0.0.
	 *
	 * @return void
	 */
	public function stale_while_revalidate_ttl_render() {
		$options = $this->get_settings();
		?>
		<input type='text' name='purgely-settings[stale_while_revalidate_ttl]' value='<?php echo esc_attr( $options['stale_while_revalidate_ttl'] ); ?>'>
		<?php
	}

	/**
	 * Render the setting input.
	 *
	 * @since 1.0.0.
	 *
	 * @return void
	 */
	public function enable_stale_while_error_render() {
		$options = $this->get_settings();
		?>
		<input type='radio' name='purgely-settings[enable_stale_while_error]' <?php checked( isset( $options['enable_stale_while_error'] ) && true === $options['enable_stale_while_error'] ); ?> value='true'>Yes
		<input type='radio' name='purgely-settings[enable_stale_while_error]' <?php checked( isset( $options['enable_stale_while_error'] ) && false === $options['enable_stale_while_error'] ); ?> value='false'>No
		<?php
	}

	/**
	 * Render the setting input.
	 *
	 * @since 1.0.0.
	 *
	 * @return void
	 */
	public function stale_while_error_ttl_render() {
		$options = $this->get_settings();
		?>
		<input type='text' name='purgely-settings[stale_while_error_ttl]' value='<?php echo esc_attr( $options['stale_while_error_ttl'] ); ?>'>
		<?php
	}

	/**
	 * Render the setting input.
	 *
	 * @since 1.0.0.
	 *
	 * @return void
	 */
	public function surrogate_control_render() {
		$options = $this->get_settings();
		?>
		<input type='text' name='purgely-settings[surrogate_control_ttl]' value='<?php echo esc_attr( $options['surrogate_control_ttl'] ); ?>'>
		<?php
	}

	/**
	 * Render the setting input.
	 *
	 * @since 1.0.0.
	 *
	 * @return void
	 */
	public function default_purge_type_render() {
		$options = $this->get_settings();

		$purge_types = array(
			'soft'    => __( 'Soft', 'purgely' ),
			'instant' => __( 'Instant', 'purgely' ),
		);
		?>
		<select name="purgely-settings[default_purge_type]">
			<?php foreach ( $purge_types as $key => $label ) : ?>
			<option value="<?php echo esc_attr( $key ); ?>"<?php selected( $options['default_purge_type'], $key ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Print the description for the settings page.
	 *
	 * @since 1.0.0.
	 *
	 * @return void
	 */
	public function settings_section_callback() {
		esc_html_e( 'Configure caching and purging behaviors for Fastly.', 'purgely' );
	}

	/**
	 * Print the settings page.
	 *
	 * @since 1.0.0.
	 *
	 * @return void
	 */
	public function options_page() {
		?>
		<form action='options.php' method='post'>

			<h1>
				<?php esc_html_e( 'Purgely Settings', 'purgely' ); ?>
			</h1>

			<?php
			settings_fields( 'purgely-settings' );
			do_settings_sections( 'purgely-settings' );
			submit_button();
			?>

		</form>
		<?php
	}

	/**
	 * Sanitize all of the setting.
	 *
	 * @since 1.0.0.
	 *
	 * @param  array $settings The unsanitized settings.
	 * @return array           The sanitized settings.
	 */
	public function sanitize_settings( $settings ) {
		$clean_settings = array();
		$registered_settings = $this->get_registered_settings();

		foreach ( $settings as $key => $value ) {
			if ( isset( $registered_settings[ $key ] ) ) {
				$clean_settings[ $key ] = call_user_func( $registered_settings[ $key ]['sanitize_callback'], $value );
			}
		}

		return $settings;
	}

	/**
	 * Get the valid settings for the plugin.
	 *
	 * @since 1.0.0.
	 *
	 * @return array The valid settings including default values and sanitize callback.
	 */
	public function get_registered_settings() {
		return array(
			'fastly_key'                    => array(
				'sanitize_callback' => 'purgely_sanitize_key',
				'default'           => PURGELY_FASTLY_KEY,
			),
			'fastly_service_id'             => array(
				'sanitize_callback' => 'purgely_sanitize_key',
				'default'           => PURGELY_FASTLY_SERVICE_ID,
			),
			'allow_purge_all'               => array(
				'sanitize_callback' => 'purgely_sanitize_checkbox',
				'default'           => PURGELY_ALLOW_PURGE_ALL,
			),
			'api_endpoint'                  => array(
				'sanitize_callback' => 'esc_url',
				'default'           => PURGELY_API_ENDPOINT,
			),
			'enable_stale_while_revalidate' => array(
				'sanitize_callback' => 'purgely_sanitize_checkbox',
				'default'           => PURGELY_ENABLE_STALE_WHILE_REVALIDATE,
			),
			'stale_while_revalidate_ttl'    => array(
				'sanitize_callback' => 'absint',
				'default'           => PURGELY_STALE_WHILE_REVALIDATE_TTL,
			),
			'enable_stale_while_error'      => array(
				'sanitize_callback' => 'purgely_sanitize_checkbox',
				'default'           => PURGELY_ENABLE_STALE_WHILE_ERROR,
			),
			'stale_while_error_ttl'         => array(
				'sanitize_callback' => 'absint',
				'default'           => PURGELY_STALE_WHILE_ERROR_TTL,
			),
			'surrogate_control_ttl'         => array(
				'sanitize_callback' => 'absint',
				'default'           => PURGELY_SURROGATE_CONTROL_TTL,
			),
			'default_purge_type'            => array(
				'sanitize_callback' => 'purgely_sanitize_checkbox',
				'default'           => PURGELY_DEFAULT_PURGE_TYPE,
			),
		);
	}

	/**
	 * Get an array of settings values.
	 *
	 * This method negotiates the database values and the constant values to determine what the current value should be.
	 * The database value takes precedence over the database value.
	 *
	 * @since 1.0.0.
	 *
	 * @return array The current settings values.
	 */
	public function get_settings() {
		$negotiated_settings = $this->settings;

		if ( empty( $negotiated_settings ) ) {
			$registered_settings = $this->get_registered_settings();
			$saved_settings      = get_option( 'purgely-settings', array() );
			$negotiated_settings = array();

			foreach ( $registered_settings as $key => $values ) {
				$value = '';

				if ( isset( $saved_settings[ $key ] ) ) {
					$value = $saved_settings[ $key ];
				} else if ( isset( $values['default'] ) ) {
					$value = $values['default'];
				}

				if ( isset( $values['sanitize_callback'] ) ) {
					$value = call_user_func( $values['sanitize_callback'], $value );
				}

				$negotiated_settings[ $key ] = $value;
			}

			$this->set_settings( $negotiated_settings );
		}

		return $negotiated_settings;
	}

	/**
	 * Set the settings values.
	 *
	 * @since 1.0.0.
	 *
	 * @param  array $settings The current settings values.
	 * @return void
	 */
	public function set_settings( $settings ) {
		$this->settings = $settings;
	}
}

/**
 * Instantiate or return the one Purgely_Settings instance.
 *
 * @since 1.0.0.
 *
 * @return Purgely_Settings
 */
function get_purgely_settings_instance() {
	return Purgely_Settings::instance();
}

get_purgely_settings_instance();
