<?php namespace YoastDocParser\Definitions;


use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\Constant;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\Property;
use Webmozart\Assert\Assert;

/**
 * Class ConstantDefinition
 * @package YoastDocParser\Definitions
 */
class ConstantDefinition implements Definition {

	/**
	 * @param Class_ $class
	 *
	 * @return mixed
	 */
	public function create( $class ) {
		Assert::isInstanceOfAny( $class, [ File::class, Element::class ] );
		// As there are potentially more than one classes in a file, we must accompany this.

		$properties = [];

		/** @var Constant $constant */
		foreach ( $class->getConstants() as $constant ) {
			$properties[$constant->getName()] = [
				'name' => $constant->getName(),
				'summary' => $constant->getDocBlock()->getSummary(),
				'longDescription' => (string) $constant->getDocBlock()->getDescription(),
				'value' => $constant->getValue(),
			];
		}

		return $properties;
	}
}
