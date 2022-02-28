<?php
	/**
	 * Contains a static class with utility methods
	 *
	 * @copyright Copyright (c) 2004-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * miUtil static class. Contains various utility methods
	 * @copyright Copyright (c) 2004-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miUtil {
	
		/**
		 * Mails the message specified in $text to the receiver specified in $to
		 * The function uses the mail() php builtin and doesn't preform any additional checks of the params
		 * 
		 * @access public
		 * @param string $to recipient email address
		 * @param string $from sender email address
		 * @param string $subject subject of the email
		 * @param string $text email contents
		 * @return boolean true if the mail was successfully accepted for delivery, false otherwise
		 */
		public static function sendEmail($to, $from, $subject, $text)
		{
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-Type: text/html; charset=iso-8859-1\n";
			$headers .= "From: $from\n";
			return mail($to, $subject, $text, $headers);
		}
	
		/**
		 * Read values from a DB and return them as an array
		 * 
		 * @access public
		 * @param string $query the query that will be executed
		 * @param string $field_key the name of the field which values will be the array keys
		 * @param string $field_value the name of the field which values will be the values associate to the keys
		 * @return array array with the returned from DB keys => values or empty array if an error appear or there is no returned rows
		 */
		public static function getDBArray($query, $field_key, $field_value)
		{
			$result = array();
	
			$rows = miStaticDBUtil::execSelect($query);
			foreach ($rows as $key => $value) {
				$result[$value[$field_key]]=$value[$field_value];
			}
			return $result;
		}
	}
?>