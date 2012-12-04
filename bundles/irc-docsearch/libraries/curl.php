<?php

class curl_request {
	public $options;

	public function __construct($url = null, $params = array('set_default' => true)) {
		if($params['set_default'])
			$this->set_default();

		if($url)
			$this->set_url($url);
	}

	public function set_default() {
		$this->options[CURLOPT_RETURNTRANSFER] = true;
		$this->options[CURLOPT_FOLLOWLOCATION] = true;
		$this->options[CURLOPT_HEADER] = false;
		$this->options[CURLOPT_USERAGENT] = 'Mozilla/5.0 (Windows; U; Windows NT 5.2; en-US; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7';
		$this->options[CURLOPT_CONNECTTIMEOUT] = 15;
		$this->options[CURLOPT_TIMEOUT] = 15;
		$this->options[CURLOPT_CUSTOMREQUEST] = 'GET';
		$this->options[CURLOPT_MAXREDIRS] = 4;
	}

	public function set_timeout($timeout) {
		$this->options[CURLOPT_CONNECTTIMEOUT] = $timeout;
		$this->options[CURLOPT_TIMEOUT] = $timeout;
	}

	public function set_options($options) {
		foreach($options as $key => $value)
			$this->options[$key] = $value;
	}

	public function set_option($key, $value) {
		$this->options[$key] = $value;
	}

	public function get_option($key) {
		if(!isset($this->options[$key]))
			return NULL;

		return $this->options[$key];
	}

	public function get_options() {
		return $this->options;
	}

	public function set_url($url) {
		$this->options[CURLOPT_URL] = $url;

		if(strpos($url, 'https') === 0)
			$this->options[CURLOPT_SSL_VERIFYPEER] = false;
	}

	public function set_referer($url) {
		$this->options[CURLOPT_REFERER] = $url;
	}

	public function disable_redirects() {
		$this->options[CURLOPT_FOLLOWLOCATION] = false;
	}

	public function set_authentication($username, $password) {
		$this->options[CURLOPT_USERPWD] = $username . ':' . $password;
		$this->options[CURLOPT_HTTPAUTH] = CURLAUTH_ANY;
	}

	public function set_cookies($file_path) {
		// clear the cookies
		//fclose(fopen($file_path, 'w'));

		$this->options[CURLOPT_COOKIEJAR] = $file_path;
		$this->options[CURLOPT_COOKIEFILE] = $file_path;
	}

	public function set_proxy($type, $host, $port, $username = NULL, $password = NULL) {
		if($type == 'http')
			$this->options[CURLOPT_PROXYTYPE] = CURLPROXY_HTTP;
		else if($type == 'socks4')
			$this->options[CURLOPT_PROXYTYPE] = CURLPROXY_SOCKS5;
		else if($type == 'socks5')
			$this->options[CURLOPT_PROXYTYPE] = CURLPROXY_SOCKS5;

		$this->options[CURLOPT_PROXY] = $host . ':' . $port;

		if($username && $password)
			$this->options[CURLOPT_PROXYUSERPWD] = $username . ':' . $password;
	}

	public function set_post($data) {
		$this->options[CURLOPT_CUSTOMREQUEST] = 'POST';
		$this->options[CURLOPT_POST] = true;
		$this->options[CURLOPT_POSTFIELDS] = $data;
	}

	public function set_get() {
		$this->options[CURLOPT_CUSTOMREQUEST] = 'GET';
		$this->options[CURLOPT_POST] = false;
		$this->options[CURLOPT_POSTFIELDS] = '';
	}

	public function set_head() {
		$this->options[CURLOPT_CUSTOMREQUEST] = 'HEAD';
		$this->options[CURLOPT_POST] = false;
		$this->options[CURLOPT_POSTFIELDS] = '';
	}

	public function set_header($data) {
		$this->options[CURLOPT_HEADER] = true;
		$this->options[CURLOPT_HTTPHEADER] = $data;
	}
};

class curl_response {
	public $data;
	public $request;
	public $info;
	public $status_code;
	public $header_list;

	public function __construct() {
		$this->data = '';
		$this->info = array();
		$this->status_code = 0;
		$this->request = null;
		$this->header_list = array();
	}
}

class curl {
	public function __construct($options = array('active' => true, 'max_connections' => 30)) {
		$this->setting_list = $options;
		$this->connection_list = array();
		$this->job_list = array();

		$this->mc = curl_multi_init();
	}

	public function run($request, $callback = null) {
		$c = curl_init();

		foreach($request->get_options() as $key => $value)
			curl_setopt($c, $key, $value);

		// we've got a callback so let's go asynchronous
		if($callback) {
			$this->job_list[] = array('request' => $request, 'handle' => $c, 'callback' => $callback);
		}
		// nope, no asio for us today
		else {
			$r = new curl_response();

			$header_list = array();

			/*if(phpversion() >= 5.3)
			curl_setopt($c, CURLOPT_HEADERFUNCTION, function($c, $header) use(&$r)
			{
				sleep(1000);

				if(strstr($header, ":"))
				{
					$h = explode(":", $header);

					$key = $h[0];

					array_shift($h);

					$r->header_list[$key] = implode(":", $h);
				}

				return strlen($header);
			});*/

			ob_start();
			$r->data = curl_exec($c);
			ob_end_clean();

			$r->request = $request;
			$r->info = curl_getinfo($c);
			$r->status_code = $r->info['http_code'];

			curl_close($c);

			return $r;
		}

		$this->last_request = $request;
	}

	public function update() {
		if(!$this->setting_list['active'])
			return;

		while(count($this->connection_list) < $this->setting_list['max_connections'] && count($this->job_list) > 0) {
			$job = array_shift($this->job_list);

			$host = $job['request']->get_option(CURLOPT_URL);

			if(!$host)
				return $job['callback'](null);

			if(strpos($host, 'http') !== 0)
				$job['request']->set_option(CURLOPT_URL, 'http://' . $host);

			$host = parse_url($job['request']->get_option(CURLOPT_URL), PHP_URL_HOST);

			// check if the domain is bad and will block multicurl
			if(!$this->is_host_active($host)) {
				if($job['callback'] != null)
					if(phpversion() >= 5.3)
						$job['callback'](null);
					else
						call_user_func_array($job['callback'], array(null));

				continue;
			}

			$this->connection_list[$job['handle']] = array(
				'request' => $job['request'],
				'handle' => $job['handle'],
				'callback' => $job['callback']
			);

			curl_multi_add_handle($this->mc, $job['handle']);
		}

		while(($status = curl_multi_exec($this->mc, $running)) == CURLM_CALL_MULTI_PERFORM)
			continue;

		if($status != CURLM_OK)
			return;

		while($item = curl_multi_info_read($this->mc)) {
			usleep(20000);

			$handle = $item['handle'];

			$connection = $this->connection_list[$handle];

			$info = curl_getinfo($handle);

			$data = curl_multi_getcontent($handle);

			curl_multi_remove_handle($this->mc, $handle);

			unset($this->connection_list[$handle]);

			$response = new curl_response();
			$response->request = $connection['request'];
			$response->data = $data;
			$response->info = $info;
			$response->status_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

			$this->last_response = $response;

			if($connection['callback'] != null)
				if(phpversion() >= 5.3)
					$connection['callback']($response);
				else
					call_user_func_array($connection['callback'], array($response));
		}
	}

	public function is_host_active($host) {
		if(!$host)
			return false;

		// if this isn't linux don't check it
		if(!stristr(PHP_OS, "linux"))
			return true;

		// if this is an IP don't check it
		if(long2ip(ip2long($host)) == $host)
			return true;

		//$x1 = shell_exec("nslookup " . $host);

		return true;//!stristr($x1, " find");
	}

	public function get_last_request() {
		return $this->last_request;
	}

	public function get_last_response() {
		return $this->last_response;
	}

	public function set_setting_list($setting_list) {
		foreach($setting_list as $name => $value)
			$this->setting_list[$name] = $value;
	}

	public function set_setting($name, $value) {
		$this->setting_list[$name] = $value;
	}

	public function get() {
		return $this->mc;
	}

	protected $setting_list;
	protected $mc;
	public $connection_list;
	public $job_list;
	protected $last_request;
	protected $last_response;
}