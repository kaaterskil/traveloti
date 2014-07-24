<?php
/**
 * Traveloti Library
 *
 * @package		Traveloti_Application
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Application\Service;

use Zend\ServiceManager\ServiceManager;

/**
 * Wrapper for Openfire's User Service plugin
 * @author Blair
 *
 */
class OpenfireUserService {
	/**
	 * The mode to retrieve data (curl or fopen)
	 * @var string
	 */
	private $mode = '';

	/**
	 * The host location
	 * @var string
	 */
	private $host = '';

	/**
	 * The port Openfire is listening on
	 * @var int
	 */
	private $port = '';

	/**
	 * The secret key provided by the User Service Plugin
	 * @var string
	 */
	private $secret = '';

	/** @var ServiceManager */
	private $serviceManager;

	/**
	 * Sets the variables we need to access Openfire. This data should
	 * be injected on instantiation by a ZF2 factory.
	 *
	 * @param ServiceManager $serviceManager
	 * @param string $mode
	 * @param string $host
	 * @param string $secret
	 * @param int $port
	 */
	public function __construct($serviceManager, $mode, $host, $secret, $port = '9090') {
		$this->serviceManager = $serviceManager;
		$this->mode = $mode;
		$this->host = $host;
		$this->secret = $secret;
		$this->port = $port;
	}

	/** @return ServiceManager */
	public function getServiceManager() {
		return $this->serviceManager;
	}

	public function setServiceManager(ServiceManager $serviceManager) {
		$this->serviceManager = $serviceManager;
	}

	/**
	 * Sends the request to the Openfire server
	 *
	 * IMPORTANT NOTE: The Openfire system limits passwords to 32 characters. ZfcUser
	 * Bcrypts all passwords prior to triggering the event that calls this service,
	 * so there is no way to deliver the plantext password to Openfire. Since the
	 * delivery protocol is to Openfire is via HTTP, all requests need to be url
	 * encoded, resulting in an encrypted password string that is too long for the
	 * Openfire server. Our workaround, after days of hair pulling and swearing,
	 * is not to subclass the ZfcUser Service to deliver the plaintext password, but
	 * to use the username as both the username and password for the Openfire
	 * system and to keep the Bcrypted password within the application.
	 *
	 * @param string $type		The type of request (add, delete, update)
	 * @param string $username
	 * @param string $password	Bcrypted password provided by ZfcUser
	 * @param string $name
	 * @param string $email
	 * @param string $groups	Comma delimited list of groups (optional)
	 * @return string			The XML response
	 */
	public function query(
			$type, $username, $password = null, $name = null, $email = null, $groups = null) {
				
		$encoded_query = http_build_query(array(
			'secret' => $this->secret,
			'type' => $type,
			'username' => $username,
			'password' => $username, // Use username as plaintext password
			'name' => $name,
			'email' => $email,
			'groups' => $groups,
		));
		
		$plain_query = 'secret=' . $this->secret
		. '&type=' . $type
		. '&username=' . $username
		. '&password=' . $username // Use username as plaintext password
		. '&name=' . $name
		. '&email=' . $email
		. '&groups=' . $groups;

		$url = 'http://' . $this->host . ':' . $this->port
		. '/plugins/userService/userservice?' . $encoded_query;

		$result = '<error>No transport mode configured</error>';
		if($this->mode == 'curl') {
			$result = $this->mode_curl($url);
		} elseif ($this->mode == 'fopen') {
			$result = $this->mode_fopen($url);
		}
		return $result;
	}

	protected function mode_curl($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		// curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		$data = curl_exec($ch);
			
		if($data === false) {
			$error = curl_errno($ch) . ': ' . curl_error($ch);
			curl_close($ch);
			return '<error>' . $error . '</error>';
		}
		curl_close($ch);

		return $data;
	}

	protected function mode_fopen($url) {
		$handle = @fopen($url, 'r');
		if($handle === false) {
			throw new \RuntimeException("Application failed to connect with remote server");
		}

		$data = fread($handle, 1024);
		fclose($handle);
		return $data;
	}
}
?>