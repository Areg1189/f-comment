<?php

namespace Quick;

class File
{
	public static function read()
	{
		$contents = file_get_contents(getenv('USERS_FILE_PATH'));
		if ($contents !== false) {
			return explode(PHP_EOL, $contents);
		}

		Logger::log('users.txt not found');
		return [];
	}

	public static function write($data)
	{
		$fh = fopen(getenv('USERS_FILE_PATH'), 'a');
		flock($fh, LOCK_EX);
		fwrite($fh, $data . PHP_EOL);
		flock($fh, LOCK_UN);
	}
}