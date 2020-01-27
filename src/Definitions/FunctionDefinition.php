<?php namespace YoastDocParser\Definitions;

use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\Function_;
use phpDocumentor\Reflection\Type;
use Webmozart\Assert\Assert;
use YoastDocParser\Definitions\Parts\Description;
use YoastDocParser\Definitions\Parts\Meta;
use YoastDocParser\Definitions\Parts\Parameter;
use YoastDocParser\Definitions\Parts\Source;

/**
 * Class FunctionDefinition
 * @package YoastDocParser\Definitions
 */
class FunctionDefinition implements Definition {

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
	 * @var Type
	 */
	private $returns;
	/**
	 * @var Meta
	 */
	private $meta;
	/**
	 * @var array
	 */

	/**
	 * @var mixed
	 */
	private $filters;

	/**
	 * @var mixed
	 */
	private $actions;
	/**
	 * @var Source
	 */
	private $source;

	/**
	 * FunctionDefinition constructor.
	 *
	 * @param string      $name
	 * @param string      $namespace
	 * @param Description $description
	 * @param array       $parameters
	 * @param Type        $returns
	 * @param Meta        $meta
	 * @param array       $hooks
	 * @param Source      $source
	 */
	public function __construct( string $name, string $namespace, Description $description, array $parameters, Type $returns, Meta $meta, array $hooks, Source $source ) {
		$this->name        = $name;
		$this->namespace   = $namespace;
		$this->description = $description;
		$this->parameters  = $parameters;
		$this->returns     = $returns;
		$this->meta        = $meta;
		$this->filters     = $hooks['filters'];
		$this->actions     = $hooks['actions'];
		$this->source      = $source;
	}

	/**
	 * @param File $file
	 *
	 * @return DefinitionCollection
	 */
	public static function create( $file ) {
		Assert::isInstanceOfAny( $file, [ File::class, Element::class ] );

		// As there are potentially more than one functions in a file, we must accompany this.
		$functions = new DefinitionCollection();

		/** @var Function_ $function */
		foreach ( $file->getFunctions() as $function ) {
			$functions->add(
				new static(
					$function->getName(),
					(string) $function->getFqsen(),
					Description::fromDocBlock( $function->getDocBlock() ),
					Parameter::fromDocBlock( $function->getDocBlock() ),
					$function->getReturnType(),
					new Meta(
						false,
						false,
						$function->getDocBlock()->hasTag( 'deprecated' )
					),
					HookDefinition::create( $function ),
					new Source( $file->getSource(), $function->getLocation()->getLineNumber() )
				)
			);
		}

		return $functions;
	}

	public function toArray() {
		return [
			'name'         => $this->name,
			'namespace'    => $this->namespace,
			'summary'      => $this->description->getSummary(),
			'description'  => (string) $this->description,
			'isDeprecated' => $this->meta->isDeprecated(),
			'parameters'   => $this->parameters,
			'returns'      => (string) $this->returns,
			'source'       => $this->source->getSource(),
			'startLine'    => $this->source->getStartLine(),

		];
	}

	public function getFilters() {
		return $this->filters;
	}

	public function getActions() {
		return $this->actions;
	}
}
