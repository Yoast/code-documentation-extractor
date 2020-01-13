<?php namespace YoastDocParser\Definitions;

use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\File;
use Webmozart\Assert\Assert;

/**
 * Class ClassDefinition
 * @package YoastDocParser\Definitions
 */
class ClassDefinition implements Definition {

	/**
	 * @param File $file
	 *
	 * @return array|mixed
	 */
	public function create( $file ) {
		Assert::isInstanceOfAny( $file, [ File::class, Element::class ] );

		// As there are potentially more than one classes in a file, we must accompany this.
		$classes = [];

		/** @var Class_ $class */
		foreach ( $file->getClasses() as $class ) {
			$classes[ $class->getName() ] = [
				'name'            => $class->getName(),
				'namespace'       => (string) $class->getFqsen(),
				'summary'         => $class->getDocBlock()->getSummary(),
				'longDescription' => (string) $class->getDocBlock()->getDescription(),
				'implements'      => $this->getInterfaces( $class ),
				'extends'         => $this->getExtends( $class ),
				'isAbstract'      => $class->isAbstract(),
				'isFinal'         => $class->isFinal(),
				'isDeprecated'    => $this->isDeprecated( $class ),
				'methods'         => $this->getMethods( $class ),
				'properties'      => $this->getProperties( $class ),
				'constants'       => $this->getConstants( $class ),
			];
		}

		return $classes;
	}

	protected function getInterfaces( Class_ $class ) {
		return array_values(
			array_map(
				function ( $interface ) { return $interface->getName(); },
				$class->getInterfaces()
			)
		);
	}

	protected function getExtends( Class_ $class ) {
		$parent = $class->getParent();

		if ( ! $parent ) {
			return '';
		}

		return $parent->getName();
	}

	protected function isDeprecated( Class_ $class ) {
		return $class->getDocBlock()->hasTag( 'deprecated' );
	}

	protected function getMethods( Class_ $class ) {
		return ( new MethodDefinition() )->create( $class );
	}

	protected function getProperties( Class_ $class ) {
		return ( new PropertyDefinition() )->create( $class );
	}

	protected function getConstants( Class_ $class ) {
		return ( new ConstantDefinition() )->create( $class );
	}
}
