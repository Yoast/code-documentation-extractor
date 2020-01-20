<?php namespace YoastDocParser\Tags;

use phpDocumentor\Reflection\DocBlock;
use Webmozart\Assert\Assert;

/**
 * Class Action
 * @package YoastDocParser\Tags
 */
class Action extends BaseHookTag {

	/**
	 * Action constructor.
	 *
	 * @param string        $actionName   The action name.
	 * @param mixed[][]     $arguments    The action's arguments.
	 *
	 * @param DocBlock|null $docBlock     The action's docblock.
	 *
	 * @param bool          $isDeprecated Whether or not the action is flagged as deprecated.
	 */
	public function __construct(
		string $actionName,
		array $arguments = [],
		?DocBlock $docBlock = null,
		bool $isDeprecated = false
	) {
		Assert::stringNotEmpty( $actionName );

		parent::__construct( 'action', $actionName, $arguments, $docBlock, $isDeprecated );
	}
}
