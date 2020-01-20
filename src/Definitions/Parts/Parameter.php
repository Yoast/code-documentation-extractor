<?php namespace YoastDocParser\Definitions\Parts;

/**
 * Class Parameter
 * @package YoastDocParser\Definitions\Parts
 */
class Parameter {

	public static function fromDocBlock( $docBlock ) {
		if ( ! $docBlock || ! $docBlock->hasTag( 'param' ) ) {
			return [ '' ];
		}

		return array_map(
			function ( $param ) { return (string) $param; },
			$docBlock->getTagsByName( 'param' )
		);
	}
}
