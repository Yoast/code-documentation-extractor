<?php namespace YoastDocParser\Definitions;

use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\Method;
use phpDocumentor\Reflection\Php\Visibility;
use phpDocumentor\Reflection\Type;
use Webmozart\Assert\Assert;
use YoastDocParser\Definitions\Parts\Description;
use YoastDocParser\Definitions\Parts\Meta;
use YoastDocParser\Definitions\Parts\Parameter;

/**
 * Class MethodDefinition
 * @package YoastDocParser\Definitions
 */
class MethodDefinition implements Definition {

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
	private $parameters;
	/**
	 * @var Meta
	 */
	private $meta;
	/**
	 * @var Type
	 */
	private $returns;
	/**
	 * @var string
	 */
	private $visibility;

	private $filters;
	private $actions;

	/**
	 * MethodDefinition constructor.
	 *
	 * @param string      $name
	 * @param string      $namespace
	 * @param Description $description
	 * @param array       $parameters
	 * @param Visibility  $visibility
	 * @param Type        $returns
	 * @param Meta        $meta
	 * @param array       $hooks
	 */
	public function __construct( string $name, string $namespace, Description $description, array $parameters, Visibility $visibility, Type $returns, Meta $meta, array $hooks ) {
		$this->name        = $name;
		$this->namespace   = $namespace;
		$this->description = $description;
		$this->parameters  = $parameters;
		$this->visibility  = $visibility;
		$this->returns     = $returns;
		$this->meta        = $meta;
		$this->filters     = $hooks['filters'];
		$this->actions     = $hooks['actions'];
	}

	/**
	 * @param Element $element
	 *
	 * @return DefinitionCollection
	 */
	public static function create( $element ) {
		Assert::isInstanceOfAny( $element, [ File::class, Element::class ] );

		$methods = new DefinitionCollection();

		/** @var Method $method */
		foreach ( $element->getMethods() as $method ) {
			$methods->add(
				new static(
					$method->getName(),
					(string) $method->getFqsen(),
					Description::fromDocBlock( $method->getDocBlock() ),
					Parameter::fromDocBlock( $method->getDocBlock() ),
					$method->getVisibility(),
					$method->getReturnType(),
					new Meta(
						$method->isAbstract(),
						$method->isFinal(),
						$method->getDocBlock()->hasTag( 'deprecated' ),
						$method->isStatic()
					),
					HookDefinition::create( $method )
				)
			);
		}

		return $methods;
	}

	public function toArray() {
		return [
			'name'         => $this->name,
			'namespace'    => $this->namespace,
			'summary'      => $this->description->getSummary(),
			'description'  => (string) $this->description,
			'isAbstract'   => $this->meta->isAbstract(),
			'isFinal'      => $this->meta->isFinal(),
			'isStatic'     => $this->meta->isStatic(),
			'isDeprecated' => $this->meta->isDeprecated(),
			'parameters'   => $this->parameters,
			'visibility'   => (string) $this->visibility,
			'returns'      => (string) $this->returns,
		];
	}

	public function getFilters() {
		return $this->filters;
	}

	public function getActions() {
		return $this->actions;
	}
}
