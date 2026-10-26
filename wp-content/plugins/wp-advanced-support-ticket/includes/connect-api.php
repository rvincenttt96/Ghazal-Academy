<?php
defined( 'ABSPATH' ) || exit( 'No Access!' );

/**
 * Base class for ParsGreen Company
 * @url https://www.parsgreen.com/
 * Class ConnectToApi
 */
if ( ! class_exists( 'ConnectToApi' ) ) {
	class ConnectToApi {
		var $url;
		var $apikey;

		function __construct( $MainUrl, $apikey ) {
			$this->url = $MainUrl;
			$this->apikey = $apikey;
		}

		function Exec( $urlpath, $req ) {
			try {
				$this->url = $this->url . '/Apiv2/' . $urlpath;
				$ch = curl_init( $this->url );
				$jsonDataEncoded = json_encode( $req );
				curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $jsonDataEncoded );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
				$header = array(
					'authorization: BASIC APIKEY:' . $this->apikey,
					'Content-Type: application/json;charset=utf-8',
				);
				curl_setopt( $ch, CURLOPT_HTTPHEADER, $header );
				$result = curl_exec( $ch );
				$res = json_decode( $result );
				curl_close( $ch );

				return $res;
			} catch ( Exception $ex ) {
				return '';
			}
		}
	}
}

if ( ! class_exists( 'SendSms_Req' ) ) {
	class SendSms_Req {
		public $SmsBody;
		public $Mobiles;
		public $SmsNumber;
	}
}