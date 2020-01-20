<?php namespace YoastDocParser\Definitions;

use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\Php\Function_;
use phpDocumentor\Reflection\Php\Method;
use phpDocumentor\Reflection\Type;
use Webmozart\Assert\Assert;
use YoastDocParser\Tags\BaseHookTag;

/**
 * Class HookDefinition
 * @package YoastDocParser\Definitions
 */
class HookDefinition implements Definition {

	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var Description
	 */
	private $description;
	/**
	 * @var Tyoe
	 */
	private $returns;
	/**
	 * @var Tyoe
	 */
	private $returnType;
	/**
	 * @var string
	 */
	private $returnDescription;

	/**
	 * HookDefinition constructor.
	 *
	 * @param string      $name
	 * @param Description $description
	 * @param Type        $returnType
	 * @param string      $returnDescription
	 */
	public function __construct( string $name, $description, Type $returnType, $returnDescription ) {

		$this->name              = $name;
		$this->description       = $description;
		$this->returnType        = $returnType;
		$this->returnDescription = $returnDescription;
	}

	/**
	 * @param Function_|Method $callable
	 *
	 * @return mixed
	 */
	public static function create( $callable ) {
		Assert::isInstanceOfAny( $callable, [ Function_::class, Method::class ] );

		return [
			'filters' => self::hookToDefinition( $callable, 'filter' ),
			'actions' => self::hookToDefinition( $callable, 'action' ),
		];
	}


	/**
	 * @param        $callable
	 * @param string $hookTag
	 *
	 * @return DefinitionCollection
	 */
	protected static function hookToDefinition( $callable, string $hookTag ) {
		Assert::oneOf( $hookTag, [ 'filter', 'action' ] );

		$hooks = new DefinitionCollection();

		/** @var BaseHookTag $hook */
		foreach ( $callable->getDocBlock()->getTagsByName( $hookTag ) as $hook ) {
			$hooks->add( new static(
				$hook->getTagName(),
				$hook->getDescription(),
				$hook->getReturnType()->getType(),
				$hook->getReturnType()->getDescription()
			) );
		}

		return $hooks;
	}

	public function toArray() {
		return [
			'name'              => $this->name,
			'description'       => (string) $this->description,
			'return'            => $this->returnType,
			'returnDescription' => (string) $this->returnDescription,
		];
	}
}
