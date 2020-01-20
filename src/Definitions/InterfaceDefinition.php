<?php namespace YoastDocParser\Definitions;

use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\Interface_;
use Webmozart\Assert\Assert;
use YoastDocParser\Definitions\Parts\Description;

/**
 * Class InterfaceDefinition
 * @package YoastDocParser\Definitions
 */
class InterfaceDefinition implements Definition {

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
	 * @var array
	 */
	private $methods;
	/**
	 * @var array
	 */
	private $extends;
	/**
	 * @var array
	 */
	private $constants;

	/**
	 * InterfaceDefinition constructor.
	 *
	 * @param string               $name
	 * @param string               $namespace
	 * @param Description          $description
	 * @param DefinitionCollection $methods
	 * @param array                $extends
	 * @param DefinitionCollection $constants
	 */
	public function __construct( string $name, string $namespace, Description $description, DefinitionCollection $methods, array $extends, DefinitionCollection $constants ) {

		$this->name        = $name;
		$this->namespace   = $namespace;
		$this->description = $description;
		$this->methods     = $methods;
		$this->extends     = $extends;
		$this->constants   = $constants;
	}

	/**
	 * @param File $file
	 *
	 * @return array
	 */
	public static function create( $file ) {
		Assert::isInstanceOfAny( $file, [ File::class, Element::class ] );

		// As there are potentially more than one interfaces in a file, we must accompany this.
		$interfaces = new DefinitionCollection();

		/** @var Interface_ $interface */
		foreach ( $file->getInterfaces() as $interface ) {
			$interfaces->add(
				new static(
					$interface->getName(),
					(string) $interface->getFqsen(),
					Description::fromDocBlock( $interface->getDocBlock() ),
					MethodDefinition::create( $interface ),
					$interface->getParents(),
					ConstantDefinition::create( $interface )
				)
			);
		}

		return $interfaces;
	}

	public function toArray() {
		return [
			'name'            => $this->name,
			'namespace'       => $this->namespace,
			'summary'         => $this->description->getSummary(),
			'longDescription' => (string) $this->description,
			'extends'         => array_map( function ( $parent ) { return (string) $parent; }, $this->extends ),
			'methods'         => $this->methods->toArray(),
			'constants'       => $this->constants->toArray(),
		];
	}
}
