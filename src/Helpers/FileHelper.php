<?php namespace YoastDocParser\Helpers;

use Error;
use Symfony\Component\Finder\Finder;

/**
 * Class FileHelper
 * @package YoastDocParser
 */
class FileHelper {
	/**
	 * Gets files from the passed directory.
	 *
	 * @param string $directory     The directory to get the files from.
	 * @param array  $exclude_files The files and directories to exclude.
	 *
	 * @return Finder|Error The found files. Throws an error if the directory can't be accessed.
	 */
	public static function getFiles( $directory, $exclude_files = [] ) {
		$finder = new Finder();

		try {
			return $finder
				->ignoreDotFiles( true )
				->ignoreVCS( true )
				->exclude( $exclude_files )
				->files()
				->name( '*.php' )
				->in( $directory );
		} catch ( Exception $e ) {
			return new Error(
				'unexpected_value_exception',
				sprintf( 'Directory [%s] contained a directory we can not recurse into', $directory )
			);
		}
	}
}
