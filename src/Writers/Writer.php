<?php namespace YoastDocParser\Writers;

/**
 * Interface Writer
 * @package YoastDocParser\Writers
 */
interface Writer {
	public function write(string $destination, string $filename, string $content );
}
