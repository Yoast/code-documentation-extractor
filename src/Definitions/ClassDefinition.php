<?php namespace YoastDocParser\Definitions;

use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\File;
use Webmozart\Assert\Assert;
use YoastDocParser\Definitions\Parts\Description;
use YoastDocParser\Definitions\Parts\Meta;

/**
 * Class ClassDefinition
 * @package YoastDocParser\Definitions
 */
class ClassDefinition implements Definition {

	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var string
	 */
	private $namespace;
	/**
	 * @var Description
	 */
	private $description;

	/**
	 * @var DefinitionCollection
	 */
	private $methods;
	/**
	 * @var array
	 */
	private $implements;
	/**
	 * @var array
	 */
	private $extends;
	/**
	 * @var array
	 */
	private $properties;
	/**
	 * @var array
	 */
	private $constants;
	/**
	 * @var Meta
	 */
	private $meta;

	/**
	 * ClassDefinition constructor.
	 *
	 * @param string               $name
	 * @param string               $namespace
	 * @param Description          $description
	 * @param DefinitionCollection $methods
	 * @param array                $properties
	 * @param array                $constants
	 * @param array                $implements
	 * @param array                $extends
	 * @param Meta                 $meta
	 */
	public function __construct( string $name, string $namespace, Description $description, DefinitionCollection $methods, DefinitionCollection $properties, DefinitionCollection $constants, array $implements, array $extends, Meta $meta ) {

		$this->name        = $name;
		$this->namespace   = $namespace;
		$this->description = $description;
		$this->methods     = $methods;
		$this->properties  = $properties;
		$this->constants   = $constants;
		$this->implements  = $implements;
		$this->extends     = $extends;
		$this->meta        = $meta;
	}

	/**
	 * @param File $file
	 *
	 * @return DefinitionCollection
	 */
	public static function create( $file ) {
		Assert::isInstanceOfAny( $file, [ File::class, Element::class ] );

		$classes = new DefinitionCollection();

		/** @var Class_ $class */
		foreach ( $file->getClasses() as $class ) {
			$classes->add(
				new static(
					$class->getName(),
					(string) $class->getFqsen(),
					Description::fromDocBlock( $class->getDocBlock() ),
					MethodDefinition::create( $class ),
					PropertyDefinition::create( $class ),
					ConstantDefinition::create( $class ),
					$class->getInterfaces(),
					// To be consistent with interfaces, which have an array of parents.
					[ $class->getParent() ],
					new Meta(
						$class->isAbstract(),
						$class->isFinal(),
						$class->getDocBlock()->hasTag( 'deprecated' )
					)
				)
			);
		}

		return $classes;
	}

	public function toArray() {
		return [
			'name'         => $this->name,
			'namespace'    => $this->namespace,
			'summary'      => $this->description->getSummary(),
			'description'  => (string) $this->description,
			'methods'      => $this->methods->toArray(),
			'properties'   => $this->properties->toArray(),
			'constants'    => $this->constants->toArray(),
			'implements'   => $this->getInterfaces(),
			'extends'      => $this->getExtends(),
			'isAbstract'   => $this->meta->isAbstract(),
			'isFinal'      => $this->meta->isFinal(),
			'isDeprecated' => $this->meta->isDeprecated(),
			'hooks'        => [
				'filters' => $this->getFilters()->toArray(),
				'actions' => $this->getActions()->toArray(),
			],
		];
	}

	protected function getInterfaces() {
		return array_values(
			array_map(
				function ( $interface ) { return $interface->getName(); },
				$this->implements
			)
		);
	}

	protected function getExtends() {
		if ( ! $this->extends || empty( $this->extends[0] ) ) {
			return '';
		}

		return $this->extends[0]->getName();
	}



	protected function getFilters() {
		return $this->methods->flatMap( function ( $method ) {
			return $method->getFilters()->toArray();
		} )->reject( function ( $item ) {
			return empty( $item );
		} );
	}

	protected function getActions() {
		return $this->methods->flatMap( function ( $method ) {
			return $method->getActions()->toArray();
		} )->reject( function ( $item ) {
			return empty( $item );
		} );
	}
}
