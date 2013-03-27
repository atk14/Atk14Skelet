<?
if(!defined("MY_BLOWFISH_ESCAPE_NON_ASCII_CHARS")){
	// existuji jiste problemy pri hashovaniu non-ascii hesel
	// http://www.php.net/security/crypt_blowfish.php
	define("MY_BLOWFISH_ESCAPE_NON_ASCII_CHARS",true);
}

if(!defined("MY_BLOWFISH_ROUNDS")){
	// the higher this constant is the more time consumption a hash calculation is
	define("MY_BLOWFISH_ROUNDS",12);
}

/**
 * Provides methods for hashing passwords and comparing hashed passwords.
 * It uses blowfish hash algorithm.
 *  
 * <code>
 * $hash = MyBlowfish::GetHash("secret");
 * if(MyBlowfish::CheckPassword("secret",$hash)){
 *  // good password
 * }
 * </code>
 *
 * Inspiration:
 *	http://stackoverflow.com/questions/4795385/how-do-you-use-bcrypt-for-hashing-passwords-in-php
 *
 * TODO:
 * 	Consider $2x$ and $2y$ hashes
 */
class MyBlowfish{

	/**
	* Hashes a password
	*
	* <code>
  * $hash = MyBlowfish::GetHash("secret");
  * $hash = MyBlowfish::GetHash("secret","SomeSalt");
  * $hash = MyBlowfish::GetHash("secret","$2a$08$GEw8HjtpaK0WfdILVMby7u");
	* </code>
	* 
	* @static
	*	@access public
	*	@param string $password								plain v citelne podobe
	* @param string $salt										volitelny salt
	*																				salt musi zacinat frazi "$2a$DD$", kde DD jsou desitkove cislice
	*																				delka saltu musi byt celkem 29
	*																				doporucuju salt vynechavat -> bude automaticky a nahodne urcen
	* @return string 												zahashovane heslo
	*																				nebo null, pokud PHP Blowfish nepodporuje nebo je spatne salt
	*/
	static function GetHash($password,$salt = "",$options = array()){
		if(is_array($salt)){
			$options = $salt;
			$salt = "";
		}

		if(!defined("CRYPT_BLOWFISH") || CRYPT_BLOWFISH!=1) {
      throw new Exception("Blowfish not supported in this installation. See http://php.net/crypt");
    }

		$options += array(
			"salt" => $salt,
			"escape_non_ascii_chars" => MY_BLOWFISH_ESCAPE_NON_ASCII_CHARS,
			"rounds" => MY_BLOWFISH_ROUNDS,
		);

		$password = (string)$password;
		$salt = (string)$options["salt"];

		if($options["escape_non_ascii_chars"]){
			$password = MyBlowfish::_EscapeNonAsciiChars($password);
		}

		// the higher ROUNDS is, the more expensive hash calculation is
		$__salt = sprintf('$2a$%02d$',$options["rounds"]);

		if(strlen($salt)==0){
			$__salt .= MyBlowfish::RandomString(22);
		}else{
			$salt_prefix = $__salt;
			$salt_random = "";
			if(preg_match('/^(\$..\$[0-9]+\$)(.*)/',$salt,$matches)){
				$salt_prefix = $matches[1];
				$salt_random = $matches[2];
			}else{
				$salt_random = $salt;
			}

			if($salt_random==""){ $salt_random = MyBlowfish::RandomString(22); }

			while(strlen($salt_random)<22){
				$salt_random .= $salt_random;
			}
			$salt_random = substr($salt_random,0,22);

			$salt = "$salt_prefix$salt_random";

			if(strlen($salt)!=29){
				return null;
			}
			if(!preg_match('/^\$2a\$[0-9]{2}\$/',$salt)){
				return null;
			}
			$__salt = $salt;
		}

		$__hash = crypt($password,$__salt);
		if(strlen($__hash)!=60){
			return null;
		}
		return $__hash;
	}

	/**
	* Overi, zda si odpovidaji citelne heslo a Blowfish hash.
	*
	* if(MyBlowfish::CheckPassword('kolovrat','$2a$08$GEw8HjtpaK0WfdILVMby7uvjpWSvu0aF/U6Qx6r.xg.qdDSFg9zBm')){
	*  // spravne heslo....
	*	}
	*
	* @static
	* @access public
	* @param string $password			citelne heslo
	* @param string $hash					predpokladany hash hesla
	* @return boolean 						true -> heslo a hash sobe odpovidaji
	*															false -> heslo hashi neodpovida; toto neni spravne heslo 
	*/
	static function CheckPassword($password,$hash,$options = array()){
		$password = (string)$password;
		$hash = (string)$hash;

		$options += array(
			"escape_non_ascii_chars" => MY_BLOWFISH_ESCAPE_NON_ASCII_CHARS,
		);

		if(!MyBlowfish::IsHash($hash)){ return false; }

		$exp_h1 = MyBlowfish::GetHash($password,$hash,$options);

		if(MyBlowfish::_CompareHashes($exp_h1,$hash)){ return true; }

		// zkusime prepnout prepinac pro konvertovani znaku do ascii a overit hashe znovu!
		$options["escape_non_ascii_chars"] = !$options["escape_non_ascii_chars"];
		$exp_h2 = MyBlowfish::GetHash($password,$hash,$options);

		return MyBlowfish::_CompareHashes($exp_h2,$hash);
	}

	/**
	 * Is the given value a valid BLOWFISH hash?
	 */
	static function IsHash($value){
		return strlen($value)==60 && preg_match('/^\$2a\$[0-9]{2}\$/',$value);
	}

	static function RandomString($length = 22){
		$bytes = null;

    if(function_exists('openssl_random_pseudo_bytes')){
      $bytes = openssl_random_pseudo_bytes($length);

    }elseif(function_exists('mcrypt_create_iv')){
			$bytes = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);

		}

		if(isset($bytes)){
			$out = MyBlowfish::_EncodeBytes($bytes);
			if(strlen($out)==$length){
				return $out;
			}
		}

		// weak randomness
		return (string)String::RandomString($length);
	}

  private static function _EncodeBytes($input) {
    // The following is code from the PHP Password Hashing Framework
    $itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    $output = '';
    $i = 0;
    do {
      $c1 = ord($input[$i++]);
      $output .= $itoa64[$c1 >> 2];
      $c1 = ($c1 & 0x03) << 4;
      if ($i >= 16) {
        $output .= $itoa64[$c1];
        break;
      }

      $c2 = ord($input[$i++]);
      $c1 |= $c2 >> 4;
      $output .= $itoa64[$c1];
      $c1 = ($c2 & 0x0f) << 2;

      $c2 = ord($input[$i++]);
      $c1 |= $c2 >> 6;
      $output .= $itoa64[$c1];
      $output .= $itoa64[$c2 & 0x3f];
    } while (1);

    return $output;
  }

	// hřebíček -> h\xc5\x99eb\xc3\xad\xc4\x8dek
	private static function _EscapeNonAsciiChars($password){
		$chrs = array();
		for($i=0;$i<strlen($password);$i++){
			$chr = $password[$i];
			if(ord($chr)>127 || $chr=="\\"){
				$chrs[] = '\x'.strtolower(dechex(ord($chr)));
				continue;
			}
			$chrs[] = $chr;
		}
		return join("",$chrs);
	}


	private static function _CompareHashes($h1,$h2){
		if(
			!MyBlowfish::IsHash($h1) ||
			!MyBlowfish::IsHash($h2)
		){ return false; }

		if(strcmp($h1,$h2)==0){
			return true;
		}

		return false;
	}
}
