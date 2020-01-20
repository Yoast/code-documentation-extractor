<?php namespace YoastDocParser\Factories;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Php\Factory\AbstractFactory;
use phpDocumentor\Reflection\Php\Function_ as FunctionReflector;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node\Stmt\Function_ as FunctionStmt;
use YoastDocParser\Helpers\HooksHelper;

/**
 * Class Function_
 * @package YoastDocParser\Factories
 */
class Function_ extends AbstractFactory implements ProjectFactoryStrategy {
	/**
	 * @var ProjectFactoryStrategy
	 */
	private $defaultStrategy;

	/**
	 * Function_ constructor.
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
	 * Creates a new Function_ instance.
	 *
	 * @param                   $object The object to base the Funtion_ on.
	 * @param StrategyContainer $strategies The strategies to apply.
	 * @param Context|null      $context The context.
	 *
	 * @return FunctionReflector The Function_ instance.
	 */
	protected function doCreate( $object, StrategyContainer $strategies, ?Context $context = null ): FunctionReflector {
		/** @var FunctionReflector $function */
		$function = $this->defaultStrategy->doCreate( $object, $strategies, $context );

		return new FunctionReflector(
			$function->getFqsen(),
			$this->getDocBlock( $function, $object ),
			$function->getLocation(),
			$function->getReturnType()
		);
	}

	/**
	 * Gets the DocBlock based on the passed parent method and ClassMethod object.
	 *
	 * @param FunctionReflector $function The parent function.
	 * @param FunctionStmt      $object   The Function_ to extract the hook data from.
	 *
	 * @return DocBlock The DocBlock instance.
	 */
	protected function getDocBlock( FunctionReflector $function, FunctionStmt $object ): DocBlock {
		$filters = HooksHelper::extractHooks( $object );

		$docBlock = $function->getDocBlock();
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
