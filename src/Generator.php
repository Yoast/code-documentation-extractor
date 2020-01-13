<?php namespace YoastDocParser;

use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Project;
use Twig;
use Webmozart\Assert\Assert;
use YoastDocParser\Definitions\ClassDefinition;
use YoastDocParser\Definitions\Definition;
use YoastDocParser\Definitions\FunctionDefinition;
use YoastDocParser\Definitions\InterfaceDefinition;
use YoastDocParser\Writers\Markdown;
use YoastDocParser\Writers\Writer;

/**
 * Class Generator
 * @package YoastDocParser
 */
class Generator {
	protected $outputDir;

	protected $templateDir;

	protected $indexFile;
	/**
	 * @var array|Definition[]
	 */
	private $definitions;

	/**
	 * @var Markdown
	 */
	private $writer;

	/**
	 * @var Twig\Environment
	 */
	private $twig;

	private $templates = [
		'classes'    => 'class.twig',
		'interfaces' => 'interface.twig',
		'functions'  => 'function.twig',
	];

	/**
	 * Generator constructor.
	 *
	 * @param Definition[] $definitions
	 * @param string       $outputDir
	 * @param string       $templateDir
	 * @param string       $indexFile
	 * @param Writer       $writer
	 */
	public function __construct( array $definitions, string $outputDir, string $templateDir, string $indexFile = 'index.md', Writer $writer = null ) {
		Assert::isMap( $definitions );

		$this->definitions = $definitions;
		$this->outputDir   = $outputDir;
		$this->templateDir = $templateDir;
		$this->indexFile   = $indexFile;
		$this->writer      = ( $writer ) ?: new Markdown();

		$loader     = new Twig\Loader\FilesystemLoader( $this->templateDir );
		$this->twig = new Twig\Environment( $loader );

	}

	public static function createDefaultInstance( string $outputDirectory ) {
		return new static(
			[
				'classes'    => ClassDefinition::class,
				'interfaces' => InterfaceDefinition::class,
				'functions'  => FunctionDefinition::class,
			],
			$outputDirectory,
			YOAST_PARSER_DIR . '/src/Templates/'
		);
	}

	public function run( Project $project ) {
		$this->prepare();

		$this->generate( $this->buildFromDefinitions( $project ) );
	}

	protected function prepare() {
		if ( ! is_dir( $this->outputDir ) ) {
			mkdir( $this->outputDir, 0775, true );
		}
	}

	protected function generate( $files ) {
		foreach ( $files as $originalFileName => $file ) {
			foreach ( array_keys( $file ) as $definition ) {
				$this->render($file, $definition );
			}
		}
	}

	protected function render( array $file, string $definition ) {
		$definedContent = $file[ $definition ];

		if ( empty( $definedContent ) || ! $this->definitionHasTemplate( $definition ) ) {
			return;
		}

		if ( ! $this->templateFileExists( $definition ) ) {
			throw new \RuntimeException( 'Template with name of `' . $definition . '` does not exist.' );
		}

		foreach ( $definedContent as $name => $item ) {
			$rendered = $this->twig->render(
				$this->getTemplateName( $definition ),
				$item
			);

			$this->writer->write( $this->outputDir . $definition, $name, $rendered );
		}
	}

	protected function prepareFileName( $fileName ) {
		return str_replace( YOAST_PARSER_DIR, '', $fileName );
	}

	protected function definitionHasTemplate( $definition ) {
		return array_key_exists( $definition, $this->templates );
	}

	protected function getTemplateName( $definition ) {
		return $this->templates[ $definition ];
	}

	protected function templateFileExists( $template ) {
		return file_exists( $this->templateDir . $this->templates[ $template ] );
	}

	protected function buildFromDefinitions( Project $project ) {
		$fileDefinitions = [];

		/** @var File $file */
		foreach ( $project->getFiles() as $file ) {
			$fileDefinitions[ $file->getPath() ] = $this->createDefinitions( $file );
		}

		return $fileDefinitions;
	}

	protected function createDefinitions( File $file ) {
		$definitions = [];

		foreach ( $this->definitions as $definition => $definitionClass ) {
			$definitionClass = new $definitionClass;

			Assert::isInstanceOf(
				$definitionClass,
				Definition::class,
				'Only instances of `Definition` are allowed. Got: ' . get_class( $definitionClass )
			);

			$definitions[ $definition ] = $definitionClass->create( $file );
		}

		return $definitions;
	}

	protected function writeClass( string $fileName, array $classDefinitions ) {
		if ( empty( $classDefinitions ) ) {
			return;
		}

		$output = implode( '', array_map(
				function ( $definition ) {
					return $this->twig->render( 'class.twig', $definition );
				}, $classDefinitions )
		);


		$this->writer->write( $fileName, $output );
	}
}
