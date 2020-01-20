<?php namespace YoastDocParser\Definitions\Parts;

/**
 * Class Meta
 * @package YoastDocParser\Definitions\Parts
 */
class Meta {
	/**
	 * @var bool
	 */
	private $isAbstract;
	/**
	 * @var bool
	 */
	private $isFinal;
	/**
	 * @var bool
	 */
	private $isDeprecated;
	/**
	 * @var bool
	 */
	private $isStatic;

	/**
	 * Meta constructor.
	 *
	 * @param bool $isAbstract
	 * @param bool $isFinal
	 * @param bool $isDeprecated
	 * @param bool $isStatic
	 */
	public function __construct( bool $isAbstract = false, bool $isFinal = false, bool $isDeprecated = false, bool $isStatic = false ) {

		$this->isAbstract = $isAbstract;
		$this->isFinal = $isFinal;
		$this->isDeprecated = $isDeprecated;
		$this->isStatic = $isStatic;
	}

	/**
	 * @return bool
	 */
	public function isAbstract(): bool {
		return $this->isAbstract;
	}

	/**
	 * @return bool
	 */
	public function isFinal(): bool {
		return $this->isFinal;
	}

	/**
	 * @return bool
	 */
	public function isDeprecated(): bool {
		return $this->isDeprecated;
	}

	/**
	 * @return bool
	 */
	public function isStatic(): bool {
		return $this->isStatic;
	}

}
