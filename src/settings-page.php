<?php
/**
 * Singleton for setting up Purgely settings.
 */
class Purgely_Settings_Page {
	/**
	 * The one instance of Purgely_Settings_Page.
	 *
	 * @since 1.0.0.
	 *
	 * @var Purgely_Settings_Page
	 */
	private static $instance;

	/**
	 * Instantiate or return the one Purgely_Settings_Page instance.
	 *
	 * @since 1.0.0.
	 *
	 * @return Purgely_Settings_Page
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
	 * @return Purgely_Settings_Page
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
			'purgely-fastly_settings',
			__( 'Fastly settings', 'purgely' ),
			array( $this, 'fastly_settings_callback' ),
			'purgely-settings'
		);

		// Register all of the individual settings.
		add_settings_field(
			'fastly_key',
			__( 'Fastly API Key', 'purgely' ),
			array( $this, 'fastly_key_render' ),
			'purgely-settings',
			'purgely-fastly_settings'
		);

		add_settings_field(
			'fastly_service_id',
			__( 'Fastly Service ID', 'purgely' ),
			array( $this, 'fastly_service_id_render' ),
			'purgely-settings',
			'purgely-fastly_settings'
		);

		add_settings_field(
			'api_endpoint',
			__( 'Fastly API Endpoint', 'purgely' ),
			array( $this, 'api_endpoint_render' ),
			'purgely-settings',
			'purgely-fastly_settings'
		);

		// Set up the general settings.
		add_settings_section(
			'purgely-general_settings',
			__( 'General settings', 'purgely' ),
			array( $this, 'general_settings_callback' ),
			'purgely-settings'
		);

		add_settings_field(
			'allow_purge_all',
			__( 'Allow Purge All?', 'purgely' ),
			array( $this, 'allow_purge_all_render' ),
			'purgely-settings',
			'purgely-general_settings'
		);

		add_settings_field(
			'surrogate_control_ttl',
			__( 'Surrogate Control TTL', 'purgely' ),
			array( $this, 'surrogate_control_render' ),
			'purgely-settings',
			'purgely-general_settings'
		);

		add_settings_field(
			'default_purge_type',
			__( 'Default Purge Type', 'purgely' ),
			array( $this, 'default_purge_type_render' ),
			'purgely-settings',
			'purgely-general_settings'
		);

		// Set up the stale content settings.
		add_settings_section(
			'purgely-stale_settings',
			__( 'Stale content settings', 'purgely' ),
			array( $this, 'stale_settings_callback' ),
			'purgely-settings'
		);

		add_settings_field(
			'enable_stale_while_revalidate',
			__( 'Enable Stale While Revalidate', 'purgely' ),
			array( $this, 'enable_stale_while_revalidate_render' ),
			'purgely-settings',
			'purgely-stale_settings'
		);

		add_settings_field(
			'stale_while_revalidate_ttl',
			__( 'Stale While Revalidate TTL', 'purgely' ),
			array( $this, 'stale_while_revalidate_ttl_render' ),
			'purgely-settings',
			'purgely-stale_settings'
		);

		add_settings_field(
			'enable_stale_while_error',
			__( 'Enable Stale While Error', 'purgely' ),
			array( $this, 'enable_stale_while_error_render' ),
			'purgely-settings',
			'purgely-stale_settings'
		);

		add_settings_field(
			'stale_while_error_ttl',
			__( 'Stale While Error TTL', 'purgely' ),
			array( $this, 'stale_while_error_ttl_render' ),
			'purgely-settings',
			'purgely-stale_settings'
		);
	}

	/**
	 * Print the description general settings section.
	 *
	 * @since 1.0.0.
	 *
	 * @return void
	 */
	public function general_settings_callback() {
		esc_html_e( 'Configure the general settings to control caching behavior.', 'purgely' );
	}

	/**
	 * Render the setting input.
	 *
	 * @since 1.0.0.
	 *
	 * @return void
	 */
	public function fastly_key_render() {
		$options = Purgely_Settings::get_settings();
		?>
		<input type='text' name='purgely-settings[fastly_key]' value='<?php echo esc_attr( $options['fastly_key'] ); ?>'>
		<p class="description">
			<?php esc_html_e( 'API key for the Fastly account associated with this site.', 'purgely' ); ?>
		</p>
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
		$options = Purgely_Settings::get_settings();
		?>
		<input type='text' name='purgely-settings[fastly_service_id]' value='<?php echo esc_attr( $options['fastly_service_id'] ); ?>'>
		<p class="description">
			<?php esc_html_e( 'Fastly service ID for this site.', 'purgely' ); ?>
		</p>
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
		$options = Purgely_Settings::get_settings();
		?>
		<input type='text' name='purgely-settings[api_endpoint]' value='<?php echo esc_attr( $options['api_endpoint'] ); ?>'>
		<p class="description">
			<?php esc_html_e( 'API endpoint for this service.', 'purgely' ); ?>
		</p>
		<?php
	}

	/**
	 * Print the description for the settings page.
	 *
	 * @since 1.0.0.
	 *
	 * @return void
	 */
	public function fastly_settings_callback() {
		esc_html_e( 'Configure settings to integrate with Fastly\'s API. These settings are critical for controlling the purging behaviors.', 'purgely' );
	}

	/**
	 * Render the setting input.
	 *
	 * @since 1.0.0.
	 *
	 * @return void
	 */
	public function allow_purge_all_render() {
		$options = Purgely_Settings::get_settings();
		?>
		<input type='radio' name='purgely-settings[allow_purge_all]' <?php checked( isset( $options['allow_purge_all'] ) && true === $options['allow_purge_all'] ); ?> value='true'>Yes
		<input type='radio' name='purgely-settings[allow_purge_all]' <?php checked( isset( $options['allow_purge_all'] ) && false === $options['allow_purge_all'] ); ?> value='false'>No
		<p class="description">
			<?php esc_html_e( 'Enable or disable purge all.', 'purgely' ); ?>
		</p>
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
		$options = Purgely_Settings::get_settings();
		?>
		<input type='text' name='purgely-settings[surrogate_control_ttl]' value='<?php echo esc_attr( $options['surrogate_control_ttl'] ); ?>'>
		<p class="description">
			<?php esc_html_e( 'The Time to Live (TTL) value for the whole site.', 'purgely' ); ?>
		</p>
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
		$options = Purgely_Settings::get_settings();

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
		<p class="description">
			<?php esc_html_e( 'The default purge behavior for purges issued via Purgely.', 'purgely' ); ?>
		</p>
		<?php
	}

	/**
	 * Print the description for the stale content settings.
	 *
	 * @since 1.0.0.
	 *
	 * @return void
	 */
	public function stale_settings_callback() {
		esc_html_e( 'Configure settings to control handling of stale content.', 'purgely' );
	}

	/**
	 * Render the setting input.
	 *
	 * @since 1.0.0.
	 *
	 * @return void
	 */
	public function enable_stale_while_revalidate_render() {
		$options = Purgely_Settings::get_settings();
		?>
		<input type='radio' name='purgely-settings[enable_stale_while_revalidate]' <?php checked( isset( $options['enable_stale_while_revalidate'] ) && true === $options['enable_stale_while_revalidate'] ); ?> value='true'>Yes
		<input type='radio' name='purgely-settings[enable_stale_while_revalidate]' <?php checked( isset( $options['enable_stale_while_revalidate'] ) && false === $options['enable_stale_while_revalidate'] ); ?> value='false'>No
		<p class="description">
			<?php esc_html_e( 'Turn stale while revalidate behavior on or off.', 'purgely' ); ?>
		</p>
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
		$options = Purgely_Settings::get_settings();
		?>
		<input type='text' name='purgely-settings[stale_while_revalidate_ttl]' value='<?php echo esc_attr( $options['stale_while_revalidate_ttl'] ); ?>'>
		<p class="description">
			<?php esc_html_e( 'The Time to Live (TTL) value for the stale while revalidate behavior.', 'purgely' ); ?>
		</p>
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
		$options = Purgely_Settings::get_settings();
		?>
		<input type='radio' name='purgely-settings[enable_stale_while_error]' <?php checked( isset( $options['enable_stale_while_error'] ) && true === $options['enable_stale_while_error'] ); ?> value='true'>Yes
		<input type='radio' name='purgely-settings[enable_stale_while_error]' <?php checked( isset( $options['enable_stale_while_error'] ) && false === $options['enable_stale_while_error'] ); ?> value='false'>No
		<p class="description">
			<?php esc_html_e( 'Turn stale while error behavior on or off.', 'purgely' ); ?>
		</p>
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
		$options = Purgely_Settings::get_settings();
		?>
		<input type='text' name='purgely-settings[stale_while_error_ttl]' value='<?php echo esc_attr( $options['stale_while_error_ttl'] ); ?>'>
		<p class="description">
			<?php esc_html_e( 'The Time to Live (TTL) value for the stale while error behavior.', 'purgely' ); ?>
		</p>
		<?php
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
		<div class="wrap">
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
		</div>
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
		$registered_settings = Purgely_Settings::get_registered_settings();

		foreach ( $settings as $key => $value ) {
			if ( isset( $registered_settings[ $key ] ) ) {
				$clean_settings[ $key ] = call_user_func( $registered_settings[ $key ]['sanitize_callback'], $value );
			}
		}

		return $settings;
	}
}

/**
 * Instantiate or return the one Purgely_Settings_Page instance.
 *
 * @since 1.0.0.
 *
 * @return Purgely_Settings_Page
 */
function get_purgely_settings_page_instance() {
	return Purgely_Settings_Page::instance();
}

get_purgely_settings_page_instance();
