<?php namespace YoastDocParser\Writers;

/**
 * Class Markdown
 * @package YoastDocParser\Writers
 */
class Markdown extends BaseWriter implements Writer {

	public function write( string $destination, string $filename, string $content ) {
		$this->filesystem->mkdir( $destination, 0775 );
		$this->filesystem->dumpFile( $destination . '/' . strtolower( $filename ) . '.md', $content );
	}
}
