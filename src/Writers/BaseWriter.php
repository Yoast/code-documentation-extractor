<?php namespace YoastDocParser\Writers;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Class BaseWriter
 * @package YoastDocParser\Writers
 */
abstract class BaseWriter {
	/**
	 * @var Filesystem
	 */
	public $filesystem;

	public function __construct() {
		$this->filesystem = new Filesystem();
	}
}
