<?php
require __DIR__ . '/conf.php';
$listdir = scandir( DIR );

error_log(PHP_EOL . date( 'd-m-Y-H-i-s'). PHP_EOL, 3,  DIR . '/debug.log');
remove_old_images( 30, DIR );
if (! empty($listdir) ){

	foreach ( $listdir as $key => $folder_name ){
		$path = DIR . '/' . $folder_name;
		$do_not_destroy = array(

		);
		if ( in_array( $folder_name, KEEP_ALIVE ) ){
			continue;
		}
		$fileinfo = stat( $path );
		$last_access = $fileinfo['atime'];
		$now = time();
		$day = ($now - $last_access)/86400;

		if ( $day >= 30 ){
			chdir( $path );
			if ( file_exists( $path . '/.lando.yml' ) ) {

				exec( 'lando destroy -y' );
				rrmdir( $path );
				echo "destroyed";
				error_log($folder_name . ' has been destroyed'. PHP_EOL, 3,  DIR . '/debug.log');

			} else {
				echo $folder_name . ' is not a lando' . PHP_EOL;
				error_log($folder_name . ' is not a lando'. PHP_EOL, 3,  DIR . '/debug.log');
			}
		} else {
			error_log( $folder_name . ' is too young to die' . PHP_EOL, 3,  DIR . '/debug.log');
			echo $folder_name . ' is too young to die' . PHP_EOL;
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

function remove_old_images( $days = 30 ){
	$timestamp = time() - $days * 86400;
	$reclaimed = exec( 'docker image prune -af --filter until=' . $timestamp);
	error_log( $reclaimed . ' by docker image prune'. PHP_EOL, 3,  DIR . '/debug.log');
}
