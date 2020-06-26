<?php
/**
 * The file that defines the Google My Business Service specifics.
 *
 * NOTE: Extending abstract class but not making use of some of the methods with new authentication workflow.
 * 			 Abstract class will be cleaned up once we move all services to one click sign on and drop users connecting own apps.
 *
 * A class that is used to interact with Google My Business.
 * It extends the Rop_Services_Abstract class.
 *
 * @link       https://themeisle.com/
 * @since      8.5.9
 *
 * @package    Rop
 * @subpackage Rop/includes/admin/services
 */

/**
 * Class Rop_Gmb_Service
 *
 * @since   8.5.9
 * @link    https://themeisle.com/
 */
class Rop_Gmb_Service extends Rop_Services_Abstract{

  /**
	 * Defines the service name in slug format.
	 *
	 * @since  8.5.9
	 * @access protected
	 * @var    string $service_name The service name.
	 */
	protected $service_name = 'gmb';

	/**
	 * Method to inject functionality into constructor.
	 * Defines the defaults and settings for this service.
	 *
	 * @since  8.5.9
	 * @access public
	 */
	public function init() {
		$this->display_name = 'Gmb';
	}

		/**
		 * Returns information for the current service.
		 *
		 * @since  8.5.9
		 * @access public
		 * @return mixed
		 */
		public function get_service() {
			return $this->service;
		}

  /**
   * Abstract function, not in Use. Method to expose desired endpoints.
   * This should be invoked by the Factory class
   * to register all endpoints at once.
   *
   * @since  8.5.9
   * @access public
   */
  public function expose_endpoints() {
    return;
  }

	/**
	 * Abstract function, not in Use. Method to register credentials for the service.
	 *
	 * @since  8.0.0
	 * @access public
	 *
	 * @param array $args The credentials array.
	 */
	public function set_credentials( $args ) {
		return;
	}

	/**
	 * Abstract function, not in Use. Method to request a token from api.
	 *
	 * @codeCoverageIgnore
	 *
	 * @since  8.5.9
	 * @access protected
	 * @return mixed
	 */
	public function request_api_token() {
		return;
	}

	/**
	 * Abstract function, not in Use. Method to retrieve the api object.
	 *
	 * @since  8.5.9
	 * @access public
	 *
	 * @param string $app_id The APP ID. Default empty.
	 * @param string $secret The APP Secret. Default empty.
	 *
	 * @return null abstract method not used for this service specifically.
	 */
	public function get_api( $app_id = '', $secret = '' ) {
		return;
	}

	/**
	 * Abstract function, not in Use. Method to define the api.
	 *
	 * @since  8.5.9
	 * @access public
	 *
	 * @param string $app_id The APP ID. Default empty.
	 * @param string $secret The APP Secret. Default empty.
	 *
	 * @return mixed
	 */
	public function set_api( $app_id = '', $secret = '' ) {
		return;
	}

	/**
	 * Abstract function, not in Use. Method for authenticate the service.
	 *
	 * @codeCoverageIgnore
	 *
	 * @since  8.5.9
	 * @access public
	 * @return mixed
	 */
	public function maybe_authenticate() {
		return;
	}

	/**
	 * Abstract function, not in Use. Method to authenticate an user based on provided credentials.
	 * Used in DB upgrade.
	 *
	 * @param array $args The arguments for service auth.
	 *
	 * @return bool
	 */
	public function authenticate( $args = array() ) {
		return;
	}

	/**
	 * This method will load and prepare the account data for Google My Business user.
	 * Used in Rest Api.
	 *
	 * @since   8.5.9
	 * @access  public
	 *
	 * @param   array $accounts_data Buffer accounts data.
	 *
	 * @return  bool
	 */
	public function add_account_with_app( $accounts_data ) {

		if ( ! $this->is_set_not_empty( $accounts_data, array( 'id' ) ) ) {
			$this->logger->alert_error( 'Google My Business error: No valid accounts found. Please make sure you have access to a Google My Business account.' );
			return false;
		}

		$the_id       = unserialize( base64_decode( $accounts_data['id'] ) );
		$accounts_array = unserialize( base64_decode( $accounts_data['pages'] ) );

		$accounts = array();


		for ( $i = 0; $i < sizeof( $accounts_array ); $i++ ) {

			$account = $this->user_default;

			$account_data = $accounts_array[ $i ];

			$account['id'] = $account_data['id'];
			$account['img'] = $account_data['img'];
			$account['account'] = $account_data['account'];
			$account['user'] = $account_data['user'];
			$account['access_token'] = $account_data['access_token'];
			$account['refresh_token'] = $account_data['refresh_token'];

			if ( $i === 0 ) {
				$account['active'] = true;
			} else {
				$account['active'] = false;
			}

			$accounts[] = $account;
		}

		// Prepare the data that will be saved as new account added.
		$this->service = array(
			'id'                 => $the_id,
			'service'            => $this->service_name,
			'credentials'        => array(
					'access_token' 	 => $account['access_token'],
					'refresh_token'  => $account['refresh_token'],
				),
			'available_accounts' => $accounts,
		);

		return true;
	}

	/**
	 * Method for publishing with Buffer service.
	 *
	 * @since  8.5.9
	 * @access public
	 *
	 * @param array $post_details The post details to be published by the service.
	 * @param array $args Optional arguments needed by the method.
	 *
	 * @return mixed
	 */
	public function share( $post_details, $args = array() ) {
		return;
	}

}
