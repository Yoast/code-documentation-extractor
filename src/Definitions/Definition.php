<?php namespace YoastDocParser\Definitions;

use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\File;

/**
 * Interface Definition
 * @package YoastDocParser\Definitions
 */
interface Definition {
	/**
	 * Creates a specific definition based on the passed file.
	 *
	 * @param File|Element $object
	 *
	 * @return mixed
	 */
	public static function create( $object );

	public function toArray();
}
