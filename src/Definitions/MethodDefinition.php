<?php namespace YoastDocParser\Definitions;

use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\Method;
use Webmozart\Assert\Assert;

/**
 * Class MethodDefinition
 * @package YoastDocParser\Definitions
 */
class MethodDefinition {

	/**
	 * @param Class_ $class
	 *
	 * @return array
	 */
	public function create( $class ) {
		Assert::isInstanceOfAny( $class, [ File::class, Element::class ] );

		// As there are potentially more than one methods in a file, we must accompany this.
		$methods = [];

		/** @var Method $method */
		foreach ( $class->getMethods() as $method ) {


			$methods[$method->getName()] = [
				'name' => $method->getName(),
				'namespace' => (string) $method->getFqsen(),
				'summary' => $method->getDocBlock()->getSummary(),
				'longDescription' => (string) $method->getDocBlock()->getDescription(),
				'isAbstract' => $method->isAbstract(),
				'isFinal' => $method->isFinal(),
				'isStatic' => $method->isStatic(),
				'isDeprecated' => $this->isDeprecated( $method ),
				'parameters' => $this->getParameters( $method ),
				'visibility' => (string) $method->getVisibility(),
				'returns' => (string) $method->getReturnType(),
			];
		}

		return $methods;
	}

	protected function getParameters( Method $method ) {
		return array_map(
			function( $param ) { return (string) $param; },
			$method->getDocBlock()->getTagsByName( 'param' )
		);
	}

	protected function isDeprecated( Method $method ) {
		return $method->getDocBlock()->hasTag( 'deprecated' );
	}
}
