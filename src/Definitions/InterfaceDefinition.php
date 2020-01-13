<?php namespace YoastDocParser\Definitions;

use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\Interface_;
use phpDocumentor\Reflection\Php\File;
use Webmozart\Assert\Assert;

/**
 * Class InterfaceDefinition
 * @package YoastDocParser\Definitions
 */
class InterfaceDefinition implements Definition {

	/**
	 * @param File $file
	 *
	 * @return array
	 */
	public function create( $file ) {
		Assert::isInstanceOfAny( $file, [ File::class, Element::class ] );

		// As there are potentially more than one interfaces in a file, we must accompany this.
		$interfaces = [];

		/** @var Interface_ $interface */
		foreach ( $file->getInterfaces() as $interface ) {
			$interfaces[$interface->getName()] = [
				'name' => $interface->getName(),
				'namespace' => (string) $interface->getFqsen(),
				'summary' => $interface->getDocBlock()->getSummary(),
				'longDescription' => (string) $interface->getDocBlock()->getDescription(),
				'extends' => $this->getExtends( $interface ),
				'methods' => $this->getMethods( $interface ),
				'constants' => $this->getConstants( $interface ),
			];
		}

		return $interfaces;
	}

	protected function getExtends( Interface_ $interface ) {
		return array_map( function( $parent ) {
			return (string) $parent;
			},
			$interface->getParents()
		);
	}

	protected function getMethods( Interface_ $interface ) {
		return ( new MethodDefinition() )->create( $interface );
	}

	protected function getConstants( Interface_ $interface_ ) {

	}
}
