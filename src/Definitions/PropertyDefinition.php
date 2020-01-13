<?php namespace YoastDocParser\Definitions;


use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\Property;
use Webmozart\Assert\Assert;

/**
 * Class PropertyDefinition
 * @package YoastDocParser\Definitions
 */
class PropertyDefinition implements Definition {

	/**
	 * @param Class_ $class
	 *
	 * @return mixed
	 */
	public function create( $class ) {
		Assert::isInstanceOfAny( $class, [ File::class, Element::class ] );
		// As there are potentially more than one classes in a file, we must accompany this.

		$properties = [];

		/** @var Property $property */
		foreach ( $class->getProperties() as $property ) {
			$properties[$property->getName()] = [
				'name' => $property->getName(),
				'namespace' => (string) $property->getFqsen(),
				'summary' => $this->getSummary( $property ),
				'longDescription' => $this->getDescription( $property ),
				'default' => $property->getDefault(),
				'types' => $property->getTypes(),
				'isStatic' => $property->isStatic(),
				'visibility' => $property->getVisibility(),
			];
		}

		return $properties;
	}

	/**
	 * @param Property $property
	 *
	 * @return string
	 */
	protected function getSummary( Property $property ): string {
		if ( ! $property->getDocBlock() ) {
			return '';
		}

		return $property->getDocBlock()->getSummary();
	}

	/**
	 * @param Property $property
	 *
	 * @return string
	 */
	protected function getDescription( Property $property ): string {
		if ( ! $property->getDocBlock() ) {
			return '';
		}

		return (string) $property->getDocBlock()->getDescription();
	}
}
