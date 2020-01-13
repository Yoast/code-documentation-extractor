<?php namespace YoastDocParser\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use YoastDocParser\Generator;
use YoastDocParser\Parser;

/**
 * Class ParseCommand
 * @package YoastDocParser
 */
class ParseCommand extends Command {
	/**
	 * @var string
	 */
	protected static $defaultName = 'parse';

	/**
	 * Configures the command.
	 */
	protected function configure() {
		$this->setDescription( 'Runs the parser.' )
			 ->setHelp( 'This command runs the parser.' );

		// Arguments
		$this->addOption( 'directory', '-d', InputOption::VALUE_OPTIONAL, 'The plugin directory', '' );
		$this->addOption( 'output-directory', '-o', InputOption::VALUE_OPTIONAL, 'The output directory', '' );
	}

	/**
	 * Executes the command.
	 *
	 * @param InputInterface  $input The input handler to use.
	 * @param OutputInterface $output The output handler to use.
	 *
	 * @return int The exit code.
	 */
	protected function execute( InputInterface $input, OutputInterface $output ) {
		$directory 		 = $input->getOption( 'directory' );
		$outputDirectory = $input->getOption( 'output-directory' );

		$helper = $this->getHelper( 'question' );

		// Check for empty directory.
		if ( empty( $directory ) ) {
			$question = new Question( 'Please enter the directory of the plugin you want to parse: ', '' );
			$question->setValidator( function( $answer ) {
				if ( ! is_string( $answer ) || empty( $answer ) ) {
					throw new \RuntimeException( 'The directory cannot be empty' );
				}

				return $answer;
			} );

			$directory = $helper->ask( $input, $output, $question );
		}

		if ( empty( $outputDirectory ) ) {
			$outputDirectory = YOAST_PARSER_DIR . '/out/';
		}

		$output->writeln( 'Parsing plugin in directory: ' . $directory );

		$parserResults 	= ( new Parser( $directory, $output ) )->parse();
		$generator 		= ( Generator::createDefaultInstance( $outputDirectory ) )->run( $parserResults );

		return 0;
	}
}
