<?php namespace YoastDocParser\Definitions;

use Tightenco\Collect\Support\Collection;

/**
 * Class DefinitionCollection
 * @package YoastDocParser\Definitions
 */
class DefinitionCollection extends Collection {

	public function __construct( array $items = [] ) {
		parent::__construct( $items );
	}

	public function add( Definition $item ) {
		return parent::push( $item );
	}

	public function toArray() {
		return parent::map( function ( $item ) {
			if ( is_array( $item ) ) {
				return $item;
			}

			return $item->toArray();
		} )->all();
	}

}
