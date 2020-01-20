<?php namespace YoastDocParser\Tags;

use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod;
use phpDocumentor\Reflection\DocBlock\Tags\TagWithType;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;
use Webmozart\Assert\Assert;

/**
 * Class API
 * @package YoastDocParser\Tags
 */
class API extends TagWithType implements StaticMethod {

	protected $name = 'api';

	public function __construct( Type $type, ?Description $description = null ) {
		$this->type        = $type;
		$this->description = $description;
	}

	/**
	 * @inheritDoc
	 */
	public static function create(
		string $body,
		?TypeResolver $typeResolver = null,
		?DescriptionFactory $descriptionFactory = null,
		Context $context = null
	): API {
		Assert::notNull( $typeResolver );
		Assert::notNull( $descriptionFactory );

		[ $type, $description ] = self::extractTypeFromBody( $body );

		$type        = $typeResolver->resolve( $type, $context );
		$description = $descriptionFactory->create( $description, $context );

		return new static( $type, $description );
	}

	public function __toString(): string {
		return ( $this->type ?: 'mixed' ) . ' ' . (string) $this->description;
	}
}
