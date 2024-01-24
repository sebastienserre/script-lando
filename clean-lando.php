<?php
if ( ! file_exists( __DIR__ . '/conf.php' ) ){
	echo 'conf file does not exist' . PHP_EOL;
	exit;
}
require __DIR__ . '/conf.php';

create_debug();
clean_lando();

function clean_lando() {
	$listdir = scandir( DIR );

	error_log( PHP_EOL . date( 'd-m-Y-H-i-s' ) . PHP_EOL, 3, DIR . '/debug.log' );
	remove_old_images( );
	if ( ! empty( $listdir ) ) {

		foreach ( $listdir as $key => $folder_name ) {
			$path           = DIR . '/' . $folder_name;
			if ( in_array( $folder_name, KEEP_ALIVE ) ) {
				continue;
			}
			$fileinfo    = stat( $path );
			$last_access = $fileinfo['atime'];
			$now         = time();
			$day         = ( $now - $last_access );

			if ( $day >= DAYS ) {
				chdir( $path );
				if ( file_exists( $path . '/.lando.yml' ) ) {
					exec( 'lando destroy -y' );
					rrmdir( $path );
					echo "destroyed";
					error_log( $folder_name . ' has been destroyed' . PHP_EOL, 3, DIR . '/debug.log' );

				} else {
					echo $folder_name . ' is not a lando' . PHP_EOL;
					error_log( $folder_name . ' is not a lando' . PHP_EOL, 3, DIR . '/debug.log' );
				}
			} else {
				error_log( $folder_name . ' is too young to die' . PHP_EOL, 3, DIR . '/debug.log' );
				echo $folder_name . ' is too young to die' . PHP_EOL;
			}
		}
	}
}

//https://stackoverflow.com/questions/3338123/how-do-i-recursively-delete-a-directory-and-its-entire-contents-files-sub-dir
function rrmdir( $dir ) {
	if (is_dir(DIR)) {
		$objects = scandir(DIR);
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (is_dir(DIR. DIRECTORY_SEPARATOR .$object) && !is_link(DIR."/".$object))
					rrmdir(DIR. DIRECTORY_SEPARATOR .$object);
				else
					unlink(DIR. DIRECTORY_SEPARATOR .$object);
			}
		}
		rmdir(DIR);
	}
}

function remove_old_images( ){
	$timestamp = time() - DAYS * 86400;
	$reclaimed = exec( 'docker image prune -af --filter until=' . $timestamp);
	error_log( $reclaimed . ' by docker image prune'. PHP_EOL, 3,  DIR . '/debug.log');
	echo $reclaimed . ' by docker image prune'. PHP_EOL;
}

function create_debug(){
	if ( file_exists( DIR . 'debug.log' ) ){
		error_log( 'debug.log already exists'. PHP_EOL, 3,  DIR . '/debug.log');
		echo 'debug.log already exists'. PHP_EOL;
		return;
	}
	echo 'debug.log dont exists'. PHP_EOL;

	file_put_contents( DIR . '/debug.log', 'start', FILE_APPEND );
}