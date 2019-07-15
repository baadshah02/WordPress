<?php
/**
 * REST API: REST_REST_FB_Events_Controller class
 *
 * @package SRMD
 * @subpackage REST_API
 * @since 4.7.0
 */

/**
 * FB Events controller that updates when FB Page is updated.
 *
 * @since 4.7.0
 *
 */
class SRMD_REST_FB_Events_Controller extends WP_REST_Controller {

	const EVENT_HASHTAG_KEY = '#event';

  /**
	 * Constructor.
	 *
	 * @since 5.0.0
	 *
	 * @param string $parent_post_type Post type of the parent.
	 */
	public function __construct() {
		$this->rest_namespace       = 'srmd/v1';
		$this->rest_base            = 'fb_events';
	}

  public function register_routes() {
    register_rest_route(
			$this->rest_namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods' => WP_REST_Server::CREATABLE,
		      'callback' => array($this, 'handle_fb_event'),
					'args' => $this->get_context_params_for_create()
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'validate_fb_request' ),
					'args'                => $this->get_context_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
  }

	/**
	 * Creates a single attachment.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response Response object on success, WP_Error object on failure.
	 */
	public function handle_fb_event( $request ) {

		if ( ! empty( $request['post'] )) {
			return new WP_Error( 'rest_invalid_param', __( 'Invalid parent type.' ), array( 'status' => 400 ) );
		}

		$headers = $request->get_headers();
		$json_params = $request->get_json_params();

		if (self::validate_fb_signature($request)) {
			// TODO handle fb event
			self::save_event($json_params);
			error_log(json_encode($headers));
			error_log(json_encode($json_params));
		} else {
			// TODO handle signature not valid case
			return new WP_Error( 'fb_invalid_sha_signature', __( 'Facebook signature validation failed.' ), array( 'status' => 401 ) );
		}
		return new WP_REST_Response($json_params, 200);
	}

	public function validate_fb_request($request) {
		// TODO validate this query params
		// NOTE: "Verify Token" set in webhook subscription settings
		$verify_token = '648092485622867|4g4xouNOe8JY_50oblKRTEcXND0';
		$token = $request->get_param('hub_verify_token');
		if ($request->get_param('hub_verify_token') == null || $token != $verify_token) {
			error_log('There is an issue with verify token, handle this case correctly: ' . $request->get_param('hub_verify_token'));
			return false;
		}
		error_log('FB initial request validated');
		$params = $request->get_query_params();
		$challenge_param = $request->get_param('hub_challenge');
		if ($challenge_param != null) {
			return new WP_REST_Response($challenge_param, 200);
		}
	}

	/**
	* Save FB event to DB as a post
	*
	*/
	private function save_event($json_params) {
		if ($json_params['entry'][0]['changes'][0]['field'] === 'feed') {
			$response_json = $json_params['entry'][0]['changes'][0]['value'];


			// TODO FIX ME
			if (!$this->is_status_post_event($response_json)) {
				 // TODO save event from the status post
				 $post_title = 'Programmatically Created Post';
				 $post_content = $response_json['message'];
				 $post_data = compact('post_title', 'post_content');
				 $post_data = wp_slash( $post_data );
				 $post_ID = wp_insert_post( $post_data );

				 if ( is_wp_error( $post_ID ) ) {
				 	error_log("\n" . $post_ID->get_error_message());
				 }
				 $src = $this->upload_image_test($post_ID);

				 update_field('post_title', $post_title, $post_ID);
				 update_field('post_message', $post_content, $post_ID);
				 update_field('post_images', '66376624_2584919758214575_66587373365886976_o-3.jpg', $post_ID);

				 error_log('src image: ' . $src);
			}

			error_log('event value: ' . json_encode($response_json));
		}
	}

	private function upload_image_test($post_id) {
				// Need to require these files
		if ( !function_exists('media_handle_upload') ) {
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			require_once(ABSPATH . "wp-admin" . '/includes/file.php');
			require_once(ABSPATH . "wp-admin" . '/includes/media.php');
		}

		$url = "https://scontent.fykz1-1.fna.fbcdn.net/v/t1.0-9/66376624_2584919758214575_66587373365886976_o.jpg?_nc_cat=111&_nc_oc=AQlZna8GBrNBXfGpFTPPOcXaOdp562K7hGOzV-RsQ4_O0ebbGYJsEQX1kYJKhpaeRzZrApFt-KyoLl0HHjl7laT_&_nc_ht=scontent.fykz1-1.fna&oh=fb783c4834439ff47ef75f10d5049a51&oe=5DBE2286";
		$tmp = download_url( $url );
		if( is_wp_error( $tmp ) ){
			error_log('error downloading to temp file');
			// download failed, handle error
		}
		$desc = "The WordPress Logo";
		$file_array = array();

		// Set variables for storage
		// fix file filename for query strings
		preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $url, $matches);
		$file_array['name'] = basename($matches[0]);
		$file_array['tmp_name'] = $tmp;

		// If error storing temporarily, unlink
		if ( is_wp_error( $tmp ) ) {
			@unlink($file_array['tmp_name']);
			$file_array['tmp_name'] = '';
		}

		// do the validation and storage stuff
		$id = media_handle_sideload( $file_array, $post_id, $desc );

		// If error storing permanently, unlink
		if ( is_wp_error($id) ) {
			@unlink($file_array['tmp_name']);
			return $id;
		}

		$src = wp_get_attachment_url( $id );
		return $src;
	}

	/**
	* Return true if the status contains hashtag that indicates new event creation
	*/
	private function is_status_post_event($json) {
		  return preg_match('/{$self::EVENT_HASHTAG_KEY}/', $json['message']);
	}

	/**
	 * Validate if the received webhook is from a valid facebook source, otherwise
	 * @return true if the sha-signature is valid, false otherwise
	*/
	private function validate_fb_signature($request) {
		$header_signature = $request->get_header('X-Hub-Signature');
		// TODO change this secret to a valid secret (or make this configurable)
		$appsecret = '50e7390422b754a7d327b445e3644605';
		// $appsecret = 'c3a284c6eede693a7479e3482c7c1965';
		$raw_post_data = file_get_contents('php://input');

		// Signature matching
		$expected_signature = hash_hmac('sha1', $raw_post_data, $appsecret);

		$signature = '';
		if(
		    strlen($header_signature) == 45 &&
		    substr($header_signature, 0, 5) == 'sha1='
		  ) {
		  $signature = substr($header_signature, 5);
		}
		if (hash_equals($signature, $expected_signature)) {
		  error_log('SIGNATURE_VERIFIED');
			return true;
		}
		return false;
	}

	private function get_context_params_for_create() {
		$query_params = parent::get_collection_params();
		$query_params['field'] = array(
			'description' => __( 'field with the values received from FB.' ),
			'type'        => 'string',
		);
		return $query_params;
	}


	private function get_context_params() {
		$query_params = parent::get_collection_params();
		$query_params['hub_mode'] = array(
			'description' => __( 'Hub mode from facebook.' ),
			'type'        => 'string',
		);
		$query_params['hub_challenge'] = array(
			'description' => __( 'Hub challenge from facebook.' ),
			'type'        => 'integer',
		);
		$query_params['hub_verify_token'] = array(
			'description' => __( 'Hub verify token from facebook.' ),
			'type'        => 'string',
		);
		return $query_params;
	}
}
