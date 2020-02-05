<?php


namespace App\Services;

class TokenGenerator
{
	/**
	 * @param int $length
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function getToken(int $length = 50): string
	{
		return rtrim(strtr(base64_encode(random_bytes($length)), '+/', '-_'), '=');
	}

}
