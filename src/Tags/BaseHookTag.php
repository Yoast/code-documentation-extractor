<?php namespace YoastDocParser\Tags;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod;
use phpDocumentor\Reflection\PrettyPrinter;
use phpDocumentor\Reflection\Types\Void_;
use PhpParser\Node;
use Webmozart\Assert\Assert;

/**
 * Class BaseHookTag
 * @package YoastDocParser\Tags
 */
abstract class BaseHookTag extends BaseTag implements StaticMethod {

	/**
	 * @var string
	 */
	protected $name = '';

	/** @var string[][] */
	private $arguments = [];

	/**
	 * @var string
	 */
	private $tagName;

	/**
	 * @var bool
	 */
	private $isDeprecated;

	/**
	 * @var DocBlock\Tags\Return_
	 */
	private $returns;

	/**
	 * Constructs the BaseHookTag.
	 *
	 * @param string        $name         The name of the tag.
	 * @param string        $tagName      The tag's name.
	 * @param mixed[][]     $arguments    The tag's arguments.
	 * @param DocBlock|null $docBlock     The tag's docblock.
	 * @param bool          $isDeprecated Whether or not the tag is deprecated.
	 */
	public function __construct(
		string $name,
		string $tagName,
		array $arguments = [],
		?DocBlock $docBlock = null,
		bool $isDeprecated = false
	) {
		Assert::stringNotEmpty( $name );
		Assert::stringNotEmpty( $tagName );

		$this->name         = $name;
		$this->tagName      = $tagName;
		$this->arguments    = $arguments;
		$this->returns      = $this->detectReturns( $docBlock );
		$this->description  = $this->setDescription( $docBlock );
		$this->isDeprecated = $isDeprecated;
	}

	/**
	 * Detects the return object based on the passed DocBlock.
	 *
	 * @param DocBlock|null $docBlock The DocBlock.
	 *
	 * @return DocBlock\Tags\Return_ The return object.
	 */
	protected function detectReturns( $docBlock ) {
		if ( $docBlock === null || ! $docBlock->hasTag( 'api' ) ) {
			return new DocBlock\Tags\Return_( new Void_() );
		}

		return $docBlock->getTagsByName( 'api' )[0];
	}

	protected function setDescription( $docBlock ) {
		if ( $docBlock === null ) {
			return null;
		}

		if ( (string) $docBlock->getDescription() === '' ) {
			return new DocBlock\Description( $docBlock->getSummary(), $docBlock->getTags() );
		}

		return $docBlock->getDescription();
	}

	/**
	 * @inheritDoc
	 */
	public static function create( string $body ) {
		// TODO: Implement create() method.
	}

	/**
	 * @param DocBlock|null $docBlock
	 *
	 * @return array The detected return type or Void_ if none could be detected.
	 */

	/**
	 * Creates a new instance of the tag.
	 *
	 * @param Node          $node         The node to base the new tag on.
	 * @param DocBlock|null $description  The documentation associated with the node.
	 * @param bool          $isDeprecated Whether or not the node is deprecated.
	 *
	 * @return static The new instance.
	 */
	public static function fromNode( Node $node, DocBlock $description = null, bool $isDeprecated = false ) {
		$printer = new PrettyPrinter();

		$args = array_map( function ( $arg ) { return $arg; }, $node->args );

		array_shift( $args );

		return new static(
			$printer->prettyPrintExpr( $node->args[0]->value ),
			$args,
			$description,
			$isDeprecated
		);
	}

	/**
	 * Gets the tag's name.
	 *
	 * @return string The tag's name.
	 */
	public function __toString(): string {
		return $this->tagName;
	}

	public function getTagName() {
		return $this->tagName;
	}

	public function isDeprecated() {
		return $this->isDeprecated;
	}

	public function getArguments() {
		return $this->arguments;
	}

	public function getSummary() {
		return $this->description->getSummary();
	}

	public function getReturnType() {
		return $this->returns;
	}
}
