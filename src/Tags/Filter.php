<?php namespace YoastDocParser\Tags;

use phpDocumentor\Reflection\DocBlock;
use PhpParser\Comment\Doc;
use Webmozart\Assert\Assert;

/**
 * Class Filter
 * @package YoastDocParser\Tags
 */
class Filter extends BaseHookTag {

	/**
	 * Filter constructor.
	 *
	 * @param string        $filterName   The filter's name.
	 * @param mixed[][]     $arguments    The filter's arguments.
	 *
	 * @param DocBlock|null $docBlock     The filter's docblock.
	 *
	 * @param bool          $isDeprecated Whether or not the filter is flagged as deprecated.
	 */
	public function __construct(
		string $filterName,
		array $arguments = [],
		?DocBlock $docBlock = null,
		bool $isDeprecated = false
	) {
		Assert::stringNotEmpty($filterName);

		parent::__construct( 'filter', $filterName, $arguments, $docBlock, $isDeprecated );
	}
}
