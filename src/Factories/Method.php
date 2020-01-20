<?php namespace YoastDocParser\Factories;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Php\Factory\AbstractFactory;
use phpDocumentor\Reflection\Php\Method as MethodReflector;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node\Stmt\ClassMethod;
use YoastDocParser\Helpers\HooksHelper;

/**
 * Class Method
 * @package YoastDocParser\Factories
 */
class Method extends AbstractFactory implements ProjectFactoryStrategy {

	/**
	 * @var ProjectFactoryStrategy
	 */
	private $defaultStrategy;

	/**
	 * Method constructor.
	 *
	 * @param ProjectFactoryStrategy $strategy The project strategy to use.
	 */
	public function __construct( ProjectFactoryStrategy $strategy )	{
		$this->defaultStrategy = $strategy;
	}

	/**
	 * @inheritDoc
	 */
	public function matches( $object ): bool {
		return $this->defaultStrategy->matches( $object );
	}

	/**
	 * Creates a new Method instance.
	 *
	 * @param                   $object The object to base the Method on.
	 * @param StrategyContainer $strategies The strategies to apply.
	 * @param Context|null      $context The context.
	 *
	 * @return MethodReflector The Method instance.
	 */
	protected function doCreate( $object, StrategyContainer $strategies, ?Context $context = null ): MethodReflector {
		/** @var MethodReflector $method */
		$method = $this->defaultStrategy->doCreate( $object, $strategies, $context );

		return new MethodReflector(
			$method->getFqsen(),
			$method->getVisibility(),
			$this->getDocBlock( $method, $object ),
			$method->isAbstract(),
			$method->isStatic(),
			$method->isFinal(),
			$method->getLocation(),
			$method->getReturnType()
		);
	}

	/**
	 * Gets the DocBlock based on the passed parent method and ClassMethod object.
	 *
	 * @param MethodReflector $method The parent method.
	 * @param ClassMethod     $object The ClassMethod to extract the hook data from.
	 *
	 * @return DocBlock The DocBlock instance.
	 */
	protected function getDocBlock( MethodReflector $method, ClassMethod $object ): DocBlock {
		$filters = HooksHelper::extractHooks( $object );

		$docBlock = $method->getDocBlock();
		if ( $docBlock === null ) {
			return new DocBlock(
				'',
				null,
				$filters
			);
		}

		return new DocBlock(
			$docBlock->getSummary(),
			$docBlock->getDescription(),
			array_merge( $docBlock->getTags(), $filters ),
			$docBlock->getContext(),
			$docBlock->getLocation(),
			$docBlock->isTemplateStart(),
			$docBlock->isTemplateEnd()
		);
	}
}
