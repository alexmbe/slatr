<?php

/*
 * This file is part of the slatr project
 * Copyleft Alex Bello
 *
 * For more information visit: http://github.com/alexmbe/slatr 
 *
 * slatr is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * slatr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with slatr.  If not, see http://www.gnu.org/licenses/
 */

if (!defined('SLA')) {
	// Exit silently
	exit;
}
	

// Transforms a string so that it's not susceptible to XSS vulnerabilities
// The short name is intended: otherwise it would be too clumsy to use
function t($string) {
	return htmlentities($string);
}

// A shortcut to transform a string and print it at once
function et($string) {
	echo t($string);
}


// Security class
// Should be instantiated as near to the beginning of a script as possible
// Starts a session as part of normal routine so calling session_start() after it's instantiated is not required
class Security {

	// Pseudorandom secret key used only if native mechanisms for generating random numbers fail
	// Can be changed as frequently as desired
	private $SECRET_KEY = 'K_67S A^@m)&* >jd(~md)K !y!Kq,L&D 8z';

	// Max time before session ID is transparently regenerated (seconds)
	private $SESSION_TTL = 600;


	// Constructor
	public function Security() {
		if (session_id() == "") {
			session_start();
		}

		// Regenerate session ID if it has expired
		if (!isset($_SESSION['ttl']) || $_SESSION['ttl'] < time()) {
			$this->NewSessionId();
		}

		// Generate anti-CSRF token if there's no such
		if (!isset($_SESSION['csrf_token'])) {
			$this->NewCsrfToken();
		}
	}


	// Regenerates session ID and restarts the session
	public function NewSessionId() {
		if (session_id() == "") {
			session_start();
		}

		session_id($this->_RandomString());
		session_write_close();
		session_start();

		$_SESSION['ttl'] = time() + $this->SESSION_TTL;
	}


	// Regenerates an anti-CSRF token
	public function NewCsrfToken() {
		$_SESSION['csrf_token'] = $this->_RandomString();
		setcookie('token', $_SESSION['csrf_token']);
	}

	// Returns an active anti-CSRF token
	public function GetCsrfToken() {
		if (!isset($_SESSION['csrf_token'])) {
			$this->NewCsrfToken();
		}
		return $_SESSION['csrf_token'];
	}

	// Checks the anti-CSRF token
	public function CheckCsrfToken($token) {
		$result =	isset($_SESSION['csrf_token']) &&
					isset($_COOKIE['token']) &&
					$_SESSION['csrf_token'] == $_COOKIE['token'] &&
					$_SESSION['csrf_token'] == $token;
		return $result;
	}

	// Returns HTML code for a hidden field with an anti-CSRF token for forms
	public function CsrfFormHtml() {
		return '<input type="hidden" name="token" value="' . $this->GetCsrfToken() . '">';
	}

	// Generates a new 128-bit salt for a password
	// Salt is shorter than SHA-256 output to guarantee its concatenation with password yields a unique hash (see SHA-2 properties)
	public function NewSalt() {
		return substr($this->_RandomString(), 0, 32);
	}


	// Generates a cryptographically secure random string with up to 128 bits of entropy
	// Uses native Linux and Windows mechanisms; if they fail degrades gracefully
	private function _RandomString() {
		$rand_bits = '';

		// Linux platform
		$fp = @fopen('/dev/urandom', 'rb');
		if ($fp !== FALSE) {
		    $rand_bits .= @fread($fp, 16);
		    @fclose($fp);
		}

		// Windows platform
		if (@class_exists('COM')) {
    		try {
	        	$CAPI_Util = new COM('CAPICOM.Utilities.1');
	    	    $rand_bits .= $CAPI_Util->GetRandom(16, 0); // 0 - base64 encoding
		    } catch (Exception $ex) {
    		    // Fail silently; see OWASP 2007 A6
		    }
		}

		// If both above fail, fall back to a default method
		if (strlen($rand_bits) < 16) {
			$rand_bits .= substr(session_id(), 0, 8) . $this->SECRET_KEY . rand();
		}

		return hash('sha256', $rand_bits);
	}
}
?>
