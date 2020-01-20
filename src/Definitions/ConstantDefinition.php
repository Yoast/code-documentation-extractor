<?php namespace YoastDocParser\Definitions;


use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\Constant;
use phpDocumentor\Reflection\Php\File;
use Webmozart\Assert\Assert;
use YoastDocParser\Definitions\Parts\Description;

/**
 * Class ConstantDefinition
 * @package YoastDocParser\Definitions
 */
class ConstantDefinition implements Definition {

	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var Description
	 */
	private $description;
	/**
	 * @var string
	 */
	private $value;

	/**
	 * ConstantDefinition constructor.
	 *
	 * @param string      $name
	 * @param Description $description
	 * @param string      $value
	 */
	public function __construct( string $name, Description $description, string $value ) {

		$this->name        = $name;
		$this->description = $description;
		$this->value       = $value;
	}

	/**
	 * @param Element $element
	 *
	 * @return mixed
	 */
	public static function create( $element ) {
		Assert::isInstanceOfAny( $element, [ File::class, Element::class ] );
		// As there are potentially more than one classes in a file, we must accompany this.

		$constants = new DefinitionCollection();

		/** @var Constant $constant */
		foreach ( $element->getConstants() as $constant ) {
			$constants->add(
				new static(
					$constant->getName(),
					Description::fromDocBlock( $constant->getDocBlock() ),
					$constant->getValue()
				)
			);
		}

		return $constants;
	}

	public function toArray() {
		return [
			'name'            => $this->name,
			'summary'         => $this->description->getSummary(),
			'longDescription' => (string) $this->description,
			'value'           => $this->value,
		];
	}
}
