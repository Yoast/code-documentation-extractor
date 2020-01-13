<?php namespace YoastDocParser;

use Error;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\Factory\Argument;
use phpDocumentor\Reflection\Php\Factory\Class_;
use phpDocumentor\Reflection\Php\Factory\ClassConstant;
use phpDocumentor\Reflection\Php\Factory\DocBlock;
use phpDocumentor\Reflection\Php\Factory\File;
use phpDocumentor\Reflection\Php\Factory\GlobalConstant;
use phpDocumentor\Reflection\Php\Factory\Interface_;
use phpDocumentor\Reflection\Php\Factory\Property;
use phpDocumentor\Reflection\Php\Factory\Trait_;
use phpDocumentor\Reflection\Php\NodesFactory;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\PrettyPrinter;
use PhpParser\ParserFactory;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use YoastDocParser\Factories\Function_;
use YoastDocParser\Factories\Method;
use YoastDocParser\Helpers\FileHelper;
use YoastDocParser\WordPress\NullPlugin;
use YoastDocParser\WordPress\PluginFinder;

/**
 * Class Parser
 * @package YoastDocParser
 */
class Parser {
	/**
	 * @var array
	 */
	private $ignore = [
		'vendor',
		'vendor_prefixed',
		'node_modules',
		'integration-tests',
		'tests',
		'build',
		'config',
		'grunt',
		'deploy_keys',
		'js',
		'languages',
		'webpack',
		'images',
		'css',
	];

	/**
	 * @var Finder|Error
	 */
	private $files;

	/**
	 * @var WordPress\NullPlugin|WordPress\Plugin
	 */
	private $pluginData;

	/**
	 * @var \PhpParser\Parser
	 */
	private $parserFactory;

	/**
	 * @var string
	 */
	private $directory;

	/**
	 * @var OutputInterface
	 */
	private $output;

	/**
	 * @var ProjectFactory
	 */
	private $projectFactory;

	/**
	 * Parser constructor.
	 *
	 * @param string          $directory The directory to parse.
	 *
	 * @param OutputInterface $output 	 The OutputInterface to send user-facing messages to.
	 */
	public function __construct( string $directory, OutputInterface $output ) {
		if ( ! is_dir( $directory ) ) {
			throw new Error( 'The passed directory is invalid.' );
		}

		$this->output = $output;
		$this->directory = $directory;

		$this->files = FileHelper::get_files(
			$this->directory,
			$this->ignore
		);

		$this->setupFactories();
	}

	public function getFileCount() {
		return $this->files->count();
	}

	public function getPluginData() {
		return $this->pluginData;
	}

	/**
	 * Parses the collected files.
	 */
	public function parse() {
		// First attempt to collect plugin data.
		$this->pluginData = $this->findPluginData();

		$files = array_map( function( $file ) {
			return new LocalFile( $file );
		}, array_keys( iterator_to_array( $this->files ) ) );

		$start   = microtime( true );
		$project = $this->projectFactory->create( 'YoastSEO', $files );
		$end     = microtime( true );

		$this->output->writeln( sprintf( 'Parsed %d files in %s seconds',
			count( $files ),
			( $end - $start )
		) );

		return $project;
	}

	protected function setupFactories() {
		$this->parserFactory = ( new ParserFactory() )
			->create( ParserFactory::PREFER_PHP5 );

		$this->projectFactory = new ProjectFactory( [
			new Argument(new PrettyPrinter()),
			new Class_(),
			new GlobalConstant(new PrettyPrinter()),
			new ClassConstant(new PrettyPrinter()),
			new DocBlock( DocBlockFactory::createInstance() ),
			new File( NodesFactory::createInstance() ),
			new Function_( new \phpDocumentor\Reflection\Php\Factory\Function_() ),
			new Interface_(),
			new Method( new \phpDocumentor\Reflection\Php\Factory\Method() ),
			new Property(new PrettyPrinter()),
			new Trait_(),
		] );
	}

	/**
	 * Finds WordPress plugin data.
	 *
	 * @return NullPlugin|WordPress\Plugin The found plugin.
	 *
	 * @throws RuntimeException
	 */
	protected function findPluginData() {
		if ( ! empty( $this->pluginData ) ) {
			throw new RuntimeException( 'Plugin data already set. You cannot set this data twice.' );
		}

		$pluginData = ( new PluginFinder( $this->files ) )->find();

		if ( $pluginData instanceof NullPlugin ) {
			throw new RuntimeException( 'No plugin data could be found. Are you parsing the correct directory?' );
		}

		$this->output->writeln( 'Successfully found plugin data' );

		return $pluginData;
	}
}
