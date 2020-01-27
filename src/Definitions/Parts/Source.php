<?php namespace YoastDocParser\Definitions\Parts;

/**
 * Class Source
 * @package YoastDocParser\Definitions\Parts
 */
class Source {
	/**
	 * @var string
	 */
	private $source;
	/**
	 * @var int
	 */
	private $startLine;
	/**
	 * @var int
	 */
	private $endLine;

	public function __construct( string $source, int $startLine = 0, int $endLine = 0 ) {
		$this->source    = $source;
		$this->startLine = $startLine;
		$this->endLine   = $endLine;
	}

	/**
	 * @return string
	 */
	public function getSource(): string {
		return $this->source;
	}

	/**
	 * @return int
	 */
	public function getStartLine(): int {
		return $this->startLine;
	}

	/**
	 * @return int
	 */
	public function getEndLine(): int {
		return $this->endLine;
	}
}
