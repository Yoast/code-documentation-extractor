<?php namespace YoastDocParser\Definitions;


use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\Property;
use phpDocumentor\Reflection\Php\Visibility;
use Webmozart\Assert\Assert;
use YoastDocParser\Definitions\Parts\Description;
use YoastDocParser\Definitions\Parts\Meta;

/**
 * Class PropertyDefinition
 * @package YoastDocParser\Definitions
 */
class PropertyDefinition implements Definition {

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
	 * @var string
	 */
	private $default;
	/**
	 * @var array
	 */
	private $types;
	/**
	 * @var Visibility
	 */
	private $visibility;
	/**
	 * @var Meta
	 */
	private $meta;

	public function __construct( string $name, string $namespace, Description $description, $default, array $types, Visibility $visibility, Meta $meta ) {

		$this->name        = $name;
		$this->namespace   = $namespace;
		$this->description = $description;
		$this->default     = $default;
		$this->types       = $types;
		$this->visibility  = $visibility;
		$this->meta        = $meta;
	}

	/**
	 * @param Class_ $element
	 *
	 * @return DefinitionCollection
	 */
	public static function create( $element ) {
		Assert::isInstanceOfAny( $element, [ File::class, Element::class ] );
		// As there are potentially more than one classes in a file, we must accompany this.

		$properties = new DefinitionCollection();

		/** @var Property $property */
		foreach ( $element->getProperties() as $property ) {
			$properties->add(
				new static(
					$property->getName(),
					(string) $property->getFqsen(),
					Description::fromDocBlock( $property->getDocBlock() ),
					$property->getDefault(),
					$property->getTypes(),
					$property->getVisibility(),
					new Meta(
						false,
						false,
						false,
						$property->isStatic()
					)
				)
			);
		}

		return $properties;
	}

	public function toArray() {
		return [
			'name'            => $this->name,
			'namespace'       => $this->namespace,
			'summary'         => $this->description->getSummary(),
			'longDescription' => (string) $this->description,
			'default'         => $this->default,
			'types'           => $this->types,
			'isStatic'        => $this->meta->isStatic(),
			'visibility'      => $this->visibility,
		];
	}
}
