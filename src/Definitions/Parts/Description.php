<?php namespace YoastDocParser\Definitions\Parts;

use phpDocumentor\Reflection\DocBlock;

/**
 * Class Description
 * @package YoastDocParser\Definitions\Parts
 */
class Description {
	/**
	 * @var string
	 */
	private $summary;
	/**
	 * @var string
	 */
	private $description;

	/**
	 * Description constructor.
	 *
	 * @param string $summary
	 * @param string $description
	 */
	public function __construct( string $summary = '', string $description = '' ) {
		$this->summary     = $summary;
		$this->description = $description;
	}

	/**
	 * @param DocBlock|null $docBlock
	 *
	 * @return Description
	 */
	public static function fromDocBlock( $docBlock ) {
		if ( ! $docBlock ) {
			return new self();
		}

		return new self(
			$docBlock->getSummary(),
			(string) $docBlock->getDescription()
		);
	}

	public function getSummary() {
		return $this->summary;
	}

	public function __toString() {
		return $this->description;
	}
}
