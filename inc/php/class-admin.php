<?php


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

			if ( isset( $_GET['page'] ) && $_GET['page'] == $this->adminpagename_mainoptions ) {
				wp_enqueue_script( 'dzsytb-mo', DZSYTB_BASE_URL . 'assets/admin-mo.js' );
			}
		}
		add_action( 'admin_menu', array( $this, 'handle_admin_menu' ) );

	}




	function handle_admin_menu() {


		$admin_cap   = $this->capability_admin;
		$dzsvcs_page = add_management_page( __( 'Debug ', 'dzsytb' ), __( 'Debug', 'dzsytb' ), $admin_cap, $this->adminpagename_mainoptions, array(
			$this,
			'admin_page_mainoptions'
		), 5 );

	}


	function admin_page_mainoptions() {

		?>



      <?php
	}

}