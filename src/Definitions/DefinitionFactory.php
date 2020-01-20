<?php namespace YoastDocParser\Definitions;

use phpDocumentor\Reflection\Php\File;
use Webmozart\Assert\Assert;

/**
 * Class DefinitionFactory
 * @package YoastDocParser\Definitions
 */
class DefinitionFactory {
	/**
	 * @var array|Definition[]
	 */
	private $definitions;

	/**
	 * DefinitionFactory constructor.
	 *
	 * @param Definition[] $definitions
	 */
	public function __construct( array $definitions ) {
		Assert::isMap( $definitions );

		$this->definitions = $definitions;
	}

	public static function createDefaultInstance() {
		return new self(
			[
				'classes'    => ClassDefinition::class,
				'interfaces' => InterfaceDefinition::class,
				'functions'  => FunctionDefinition::class,
			]
		);
	}

	/**
	 * @param File[] $files
	 *
	 * @return array
	 */
	public function create( array $files ) {
		$out = [];

		/** @var File $file */
		foreach ( $files as $file ) {
			$out[ $file->getPath() ] = [];

			foreach ( $this->definitions as $type => $defintionClass ) {
				$data = $defintionClass::create( $file );

				if ( $data instanceof DefinitionCollection ) {
					$data = $data->toArray();
				}

				$out[ $file->getPath() ][ $type ] = $data;
			}
		}

		return $out;
	}
}
