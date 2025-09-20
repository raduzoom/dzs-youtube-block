<?php
if ( ! defined( 'ABSPATH' ) ) exit;

#[AllowDynamicProperties]
class DZSYtBlockAdmin {
	public $capability_admin = 'manage_options';
	public $dzsytb;
	private $adminpagename_mainoptions = 'dzsytb-mo';

	/**
	 * @param DZSYtBlock $dzsytb
	 */
	function __construct( $dzsytb ) {

		$this->dzsytb = $dzsytb;

		add_action( 'init', array( $this, 'handle_init' ) );
	}


	function handle_init() {


		if ( is_admin() ) {
			wp_enqueue_script( 'jquery' );

      $getPage = wp_unslash($_GET['page'] ?? '');
			// Sanitize the page parameter before using it
			$page = $getPage;
			if ( $page === $this->adminpagename_mainoptions ) {
				wp_enqueue_script( 'dzsytb-mo', DZSYTB_BASE_URL . 'assets/admin-mo.js' , array(),DZSYTB_VERSION);
			}
		}
		add_action( 'admin_menu', array( $this, 'handle_admin_menu' ) );

	}




	function handle_admin_menu() {

		// Check if user has the required capability
		if (!current_user_can($this->capability_admin)) {
			return;
		}

		$admin_cap   = $this->capability_admin;
		$dzsvcs_page = add_management_page( esc_html__( 'Debug ', 'dzs-youtube-block' ), esc_html__( 'Debug', 'dzs-youtube-block' ), $admin_cap, $this->adminpagename_mainoptions, array(
			$this,
			'admin_page_mainoptions'
		), 5 );

	}


	function admin_page_mainoptions() {
		// Verify user has the required capability
		if (!current_user_can($this->capability_admin)) {
			wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'dzs-youtube-block'));
		}

		// Add nonce field for security
		wp_nonce_field('dzsytb_admin_action', 'dzsytb_admin_nonce');

		?>



      <?php
	}

	/**
	 * Verify nonce for admin actions
	 *
	 * @param string $nonce_name The name of the nonce field
	 * @param string $action The action name
	 * @return bool True if nonce is valid, false otherwise
	 */
	private function verify_admin_nonce($nonce_name, $action) {
		if (!isset($_POST[$nonce_name]) || !wp_verify_nonce(sanitize_key($_POST[$nonce_name]), $action)) {
			wp_die(esc_html__('Security check failed. Please try again.', 'dzs-youtube-block'));
		}
		return true;
	}

	/**
	 * Sanitize and validate admin form data
	 *
	 * @param array $data The form data to sanitize
	 * @return array The sanitized data
	 */
	private function sanitize_admin_data($data) {
		$sanitized = array();

		if (is_array($data)) {
			foreach ($data as $key => $value) {
				if (is_string($value)) {
					$sanitized[$key] = sanitize_text_field($value);
				} elseif (is_array($value)) {
					$sanitized[$key] = $this->sanitize_admin_data($value);
				} else {
					$sanitized[$key] = $value;
				}
			}
		}

		return $sanitized;
	}
}
