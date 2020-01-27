<?php namespace YoastDocParser;

use Cocur\Slugify\Bridge\Twig\SlugifyExtension;
use Cocur\Slugify\Slugify;
use phpDocumentor\Reflection\Project;
use Symfony\Component\Filesystem\Filesystem;
use Twig;
use Twig\Extension\DebugExtension;
use YoastDocParser\Definitions\Definition;
use YoastDocParser\Definitions\DefinitionFactory;
use YoastDocParser\Extensions\Twig\Markdown\MarkdownExtension;
use YoastDocParser\WordPress\PluginInterface;
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
	 * @var DefinitionFactory
	 */
	private $factory;

	/**
	 * @var Filesystem
	 */
	private $filesystem;
	/**
	 * @var PluginInterface
	 */
	private $plugin;
	/**
	 * @var Project
	 */
	private $project;
	/**
	 * @var Parser
	 */
	private $parser;
	/**
	 * @var WordPress\NullPlugin|WordPress\Plugin
	 */
	private $pluginData;

	/**
	 * Generator constructor.
	 *
	 * @param Parser            $parser
	 * @param DefinitionFactory $factory
	 * @param string            $outputDir
	 * @param string            $templateDir
	 * @param Writer            $writer
	 */
	public function __construct( Parser $parser, DefinitionFactory $factory, string $outputDir, string $templateDir, Writer $writer ) {
		$this->project    = $parser->getProject();
		$this->pluginData = $parser->getPluginData();

		$this->parser      = $parser;
		$this->factory     = $factory;
		$this->outputDir   = $this->prepareOutputDirectory( $outputDir );
		$this->templateDir = $templateDir;
		$this->indexFile   = 'index.md';
		$this->writer      = $writer;

		$this->filesystem = new Filesystem();
		$loader           = new Twig\Loader\FilesystemLoader( $this->templateDir );
		$this->twig       = new Twig\Environment( $loader, [ 'debug' => true ] );

		$this->twig->addExtension( new DebugExtension() );
		$this->twig->addExtension( new MarkdownExtension() );
		$this->twig->addExtension( new SlugifyExtension( Slugify::create() ) );

	}

	protected function prepareOutputDirectory( string $outputDir ) {
		return $outputDir . $this->pluginData->getSlug() . '/';
	}

	public static function createDefaultInstance( Parser $project, string $outputDirectory ) {
		return new static(
			$project,
			DefinitionFactory::createDefaultInstance(),
			$outputDirectory,
			YOAST_PARSER_DIR . '/src/Templates/',
			new Markdown()
		);
	}

	public function generate() {
		$files = $this->factory->create( $this->project->getFiles() );

		foreach ( $files as $originalFileName => $file ) {
			foreach ( array_keys( $file ) as $definition ) {
				$this->render( $file, $definition );
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

			$this->writer->write( $this->outputDir . $definition, $item['name'], $rendered );
		}
	}

	protected function definitionHasTemplate( $definition ) {
		return array_key_exists( $definition, $this->templates );
	}

	protected function templateFileExists( $template ) {
		return $this->filesystem->exists( $this->templateDir . $this->templates[ $template ] );
	}

	protected function getTemplateName( $definition ) {
		return $this->templates[ $definition ];
	}
}
