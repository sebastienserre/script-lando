<?php
$dir = '/media/projets/polylang/customers/sites';
$listdir = scandir( $dir );

error_log(PHP_EOL . date( 'd-m-Y-H-i-s'). PHP_EOL, 3,  $dir . '/debug.log');
remove_old_images( 30, $dir );
if (! empty($listdir) ){

	foreach ( $listdir as $key => $folder_name ){
		$path = $dir . '/' . $folder_name;
		$do_not_destroy = array(
			'.',
			'..',
			'7bioch',
			'debug.log',
		);
		if ( in_array( $folder_name, $do_not_destroy ) ){
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
				error_log($folder_name . ' has been destroyed'. PHP_EOL, 3,  $dir . '/debug.log');

			} else {
				echo $folder_name . ' is not a lando' . PHP_EOL;
				error_log($folder_name . ' is not a lando'. PHP_EOL, 3,  $dir . '/debug.log');
			}
		} else {
			error_log( $folder_name . ' is too young to die' . PHP_EOL, 3,  $dir . '/debug.log');
			echo $folder_name . ' is too young to die' . PHP_EOL;
		}
	}
}

//https://stackoverflow.com/questions/3338123/how-do-i-recursively-delete-a-directory-and-its-entire-contents-files-sub-dir
function rrmdir($dir) {
	if (is_dir($dir)) {
		$objects = scandir($dir);
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
					rrmdir($dir. DIRECTORY_SEPARATOR .$object);
				else
					unlink($dir. DIRECTORY_SEPARATOR .$object);
			}
		}
		rmdir($dir);
	}
}

function remove_old_images( $days = 30, $dir ){
	$timestamp = time() - $days * 86400;
	var_dump( $timestamp );
	exec( 'docker image prune -af --filter until=' . $timestamp);
	error_log( 'Docker image prune performed' . PHP_EOL, 3,  $dir . '/debug.log');

}
