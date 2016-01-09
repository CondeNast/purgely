<?php

if ( ! defined( 'PURGELY_API_ENDPOINT' ) ) {
	define( 'PURGELY_API_ENDPOINT', 'https://example.org' );
}

if ( ! defined( 'PURGELY_FASTLY_SERVICE_ID' ) ) {
	define( 'PURGELY_FASTLY_SERVICE_ID', 'aaabbbccc111222333' );
}

if ( ! defined( 'PURGELY_FASTLY_KEY' ) ) {
	define( 'PURGELY_FASTLY_KEY', '000999888zzzxxxyyy' );
}

include __DIR__ . '/../src/config.php';
include __DIR__ . '/../src/utils.php';
include __DIR__ . '/../src/classes/settings.php';
include __DIR__ . '/../src/classes/related-urls.php';
include __DIR__ . '/../src/classes/purge-request-collection.php';
include __DIR__ . '/../src/classes/purge-request.php';
include __DIR__ . '/../src/classes/header.php';
include __DIR__ . '/../src/classes/header-cache-control.php';
include __DIR__ . '/../src/classes/header-surrogate-control.php';
include __DIR__ . '/../src/classes/header-surrogate-keys.php';
include __DIR__ . '/../src/classes/surrogate-key-collection.php';

// Bring in the base class
include __DIR__ . '/base.php';

class MockData {
	static public function purge_url_response_200() {
		return array(
			'headers'  =>
				array(
					'content-type'   => 'application/json',
					'content-length' => '50',
					'accept-ranges'  => 'bytes',
					'date'           => 'Sun, 11 Oct 2015 22:54:09 GMT',
					'x-varnish'      => '320223782',
					'via'            => '1.1 varnish',
					'connection'     => 'close',
				),
			'body'     => '{"status": "ok", "id": "324-1438859198-16748638"}
', // New line is important here. It matches a real Fastly response
			'response' =>
				array(
					'code'    => 200,
					'message' => 'OK',
				),
			'cookies'  =>
				array(),
			'filename' => null,
		);
	}

	static public function purge_url_response_405() {
		return array (
			'headers'  =>
				array(
					'server'         => 'Varnish',
					'retry-after'    => '0',
					'content-type'   => 'text/html; charset=utf-8',
					'content-length' => '429',
					'accept-ranges'  => 'bytes',
					'date'           => 'Sun, 11 Oct 2015 21:02:25 GMT',
					'via'            => '1.1 varnish',
					'connection'     => 'close',
					'x-served-by'    => 'cache-sjc3123-SJC',
					'x-cache'        => 'MISS',
					'x-cache-hits'   => '0',
					'x-timer'        => 'S1444597345.916861,VS0,VE1',
				),
			'body'     => '
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <title>405 Not allowed.</title>
  </head>
  <body>
    <h1>Error 405 Not allowed.</h1>
    <p>Not allowed.</p>
    <h3>Guru Mediation:</h3>
    <p>Details: cache-sjc3123-SJC 1444597345 360958686</p>
    <hr>
    <p>Varnish cache server</p>
  </body>
</html>
',
			'response' =>
				array(
					'code'    => 405,
					'message' => 'Not allowed.',
				),
			'cookies'  =>
				array(),
			'filename' => null,
		);
	}

	static public function purge_key_response_200() {
		return array (
			'headers' =>
				array (
					'content-type' => 'application/json',
					'content-length' => '49',
					'accept-ranges' => 'bytes',
					'date' => 'Sun, 11 Oct 2015 23:32:06 GMT',
					'x-varnish' => '4225674650',
					'via' => '1.1 varnish',
					'connection' => 'close',
				),
			'body' => '{"status": "ok", "id": "51-1443171419-20621638"}
',
			'response' =>
				array (
					'code' => 200,
					'message' => 'OK',
				),
			'cookies' =>
				array (
				),
			'filename' => NULL,
		);
	}

	static public function purge_key_response_405() {
		return array (
			'headers'  =>
				array(
					'server'         => 'Varnish',
					'retry-after'    => '0',
					'content-type'   => 'text/html; charset=utf-8',
					'content-length' => '429',
					'accept-ranges'  => 'bytes',
					'date'           => 'Sun, 11 Oct 2015 21:02:25 GMT',
					'via'            => '1.1 varnish',
					'connection'     => 'close',
					'x-served-by'    => 'cache-sjc3123-SJC',
					'x-cache'        => 'MISS',
					'x-cache-hits'   => '0',
					'x-timer'        => 'S1444597345.916861,VS0,VE1',
				),
			'body'     => '
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <title>405 Not allowed.</title>
  </head>
  <body>
    <h1>Error 405 Not allowed.</h1>
    <p>Not allowed.</p>
    <h3>Guru Mediation:</h3>
    <p>Details: cache-sjc3123-SJC 1444597345 360958686</p>
    <hr>
    <p>Varnish cache server</p>
  </body>
</html>
',
			'response' =>
				array(
					'code'    => 405,
					'message' => 'Not allowed.',
				),
			'cookies'  =>
				array(),
			'filename' => null,
		);
	}

	static public function purge_all_response_200() {
		return array(
			'headers'  =>
				array(
					'status'           => '200 OK',
					'content-type'     => 'application/json',
					'cache-control'    => 'no-cache',
					'content-encoding' => 'gzip',
					'via'              =>
						array(
							0 => '1.1 varnish',
							1 => '1.1 varnish',
						),
					'content-length'   => '41',
					'accept-ranges'    => 'bytes',
					'date'             => 'Sun, 11 Oct 2015 23:54:05 GMT',
					'connection'       => 'close',
					'x-served-by'      => 'app-slwdc9051-SL, cache-jfk1025-JFK',
					'x-cache'          => 'MISS, MISS',
					'x-cache-hits'     => '0, 0',
					'vary'             => 'Accept-Encoding',
				),
			'body'     => '{"status":"ok"}',
			'response' =>
				array(
					'code'    => 200,
					'message' => 'OK',
				),
			'cookies'  =>
				array(),
			'filename' => null,
		);
	}

	// Bad service ID
	static public function purge_all_response_400() {
		return array(
			'headers'  =>
				array(
					'status'           => '400 Bad Request',
					'content-type'     => 'text/json',
					'content-encoding' => 'gzip',
					'via'              =>
						array(
							0 => '1.1 varnish',
							1 => '1.1 varnish',
						),
					'content-length'   => '158',
					'accept-ranges'    => 'bytes',
					'date'             => 'Mon, 12 Oct 2015 00:21:26 GMT',
					'connection'       => 'close',
					'x-served-by'      => 'app-slwdc9051-SL, cache-sjc3121-SJC',
					'x-cache'          => 'MISS, MISS',
					'x-cache-hits'     => '0, 0',
					'vary'             => 'Accept-Encoding',
				),
			'body'     => '{"msg":"An error occurred while connecting to the fastly API, please try your request again.","detail":"Cannot find service \'000999888zzzyyyxxx\'"}',
			'response' =>
				array(
					'code'    => 400,
					'message' => 'Bad Request',
				),
			'cookies'  =>
				array(),
			'filename' => null,
		);
	}

	// Bad credentials
	static public function purge_all_response_403() {
		return array (
			'headers' =>
				array (
					'status' => '403 Forbidden',
					'content-type' => 'text/json',
					'content-encoding' => 'gzip',
					'via' =>
						array (
							0 => '1.1 varnish',
							1 => '1.1 varnish',
						),
					'x-pad' => 'avoid browser bug',
					'content-length' => '84',
					'accept-ranges' => 'bytes',
					'date' => 'Mon, 12 Oct 2015 00:23:32 GMT',
					'connection' => 'close',
					'x-served-by' => 'app-slwdc9051-SL, cache-sjc3131-SJC',
					'x-cache' => 'MISS, MISS',
					'x-cache-hits' => '0, 0',
					'vary' => 'Accept-Encoding',
				),
			'body' => '{"msg":"aaabbbccc11222333 is not allowed"}',
			'response' =>
				array (
					'code' => 403,
					'message' => 'Forbidden',
				),
			'cookies' =>
				array (
				),
			'filename' => NULL,
		);
	}
}