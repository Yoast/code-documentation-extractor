<?php namespace YoastDocParser\Definitions;

use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\Interface_;
use Webmozart\Assert\Assert;
use YoastDocParser\Definitions\Parts\Description;
use YoastDocParser\Definitions\Parts\Source;

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
	 * @var Source
	 */
	private $source;

	/**
	 * InterfaceDefinition constructor.
	 *
	 * @param string               $name
	 * @param string               $namespace
	 * @param Description          $description
	 * @param DefinitionCollection $methods
	 * @param array                $extends
	 * @param DefinitionCollection $constants
	 * @param Source               $source
	 */
	public function __construct( string $name, string $namespace, Description $description, DefinitionCollection $methods, array $extends, DefinitionCollection $constants, Source $source ) {

		$this->name        = $name;
		$this->namespace   = $namespace;
		$this->description = $description;
		$this->methods     = $methods;
		$this->extends     = $extends;
		$this->constants   = $constants;
		$this->source      = $source;
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
					ConstantDefinition::create( $interface ),
					new Source( $file->getSource(), $interface->getLocation()->getLineNumber() )
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
			'source'          => $this->source->getSource(),
			'startLine'       => $this->source->getStartLine(),
		];
	}
}
