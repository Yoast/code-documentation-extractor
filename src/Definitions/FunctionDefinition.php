<?php namespace YoastDocParser\Definitions;

use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\Function_;
use Webmozart\Assert\Assert;

/**
 * Class FunctionDefinition
 * @package YoastDocParser\Definitions
 */
class FunctionDefinition implements Definition {

	/**
	 * @param File $file
	 *
	 * @return array
	 */
	public function create( $file ) {
		Assert::isInstanceOfAny( $file, [ File::class, Element::class ] );

		// As there are potentially more than one functions in a file, we must accompany this.
		$functions = [];

		/** @var Function_ $function */
		foreach ( $file->getFunctions() as $function ) {
			$functions[$function->getName()] = [
				'name' => $function->getName(),
				'namespace' => (string) $function->getFqsen(),
				'summary' => $function->getDocBlock()->getSummary(),
				'longDescription' => (string) $function->getDocBlock()->getDescription(),
				'isDeprecated' => $this->isDeprecated( $function ),
				'parameters' => $this->getParameters( $function ),
				'returns' => (string) $function->getReturnType(),
			];
		}


		return $functions;
	}

	protected function getParameters( Function_ $function ) {
		return array_map(
			function( $param ) { return (string) $param; },
			$function->getDocBlock()->getTagsByName( 'param' )
		);
	}

	protected function isDeprecated( Function_ $function ) {
		return $function->getDocBlock()->hasTag( 'deprecated' );
	}
}
