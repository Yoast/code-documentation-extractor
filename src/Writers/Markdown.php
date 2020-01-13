<?php namespace YoastDocParser\Writers;

/**
 * Class Markdown
 * @package YoastDocParser\Writers
 */
class Markdown implements Writer {

	public function write( string $destination, string $filename, string $content ) {
		if ( ! is_dir( $destination ) ) {
			mkdir( $destination, 0775, true );
		}

		file_put_contents( $destination . '/' . strtolower( $filename ) . '.md', $content );
	}
}
