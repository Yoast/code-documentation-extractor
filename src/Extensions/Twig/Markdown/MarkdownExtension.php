<?php namespace YoastDocParser\Extensions\Twig\Markdown;

use League\HTMLToMarkdown\Converter\BlockquoteConverter;
use League\HTMLToMarkdown\Converter\CommentConverter;
use League\HTMLToMarkdown\Converter\DivConverter;
use League\HTMLToMarkdown\Converter\EmphasisConverter;
use League\HTMLToMarkdown\Converter\HardBreakConverter;
use League\HTMLToMarkdown\Converter\HeaderConverter;
use League\HTMLToMarkdown\Converter\HorizontalRuleConverter;
use League\HTMLToMarkdown\Converter\ImageConverter;
use League\HTMLToMarkdown\Converter\LinkConverter;
use League\HTMLToMarkdown\Converter\ListBlockConverter;
use League\HTMLToMarkdown\Converter\ListItemConverter;
use League\HTMLToMarkdown\Converter\ParagraphConverter;
use League\HTMLToMarkdown\Converter\PreformattedConverter;
use League\HTMLToMarkdown\Converter\TextConverter;
use League\HTMLToMarkdown\Environment;
use League\HTMLToMarkdown\HtmlConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use YoastDocParser\Extensions\League\HtmlToMarkdown\CodeConverter;

/**
 * Class MarkdownExtension
 * @package YoastDocParser\Extensions\Twig\Markdown
 */
class MarkdownExtension extends AbstractExtension {

	/**
	 * Gets the filters.
	 *
	 * @return TwigFilter[] The filters.
	 */
	public function getFilters() {
		return [
			new TwigFilter( 'markdown_to_html', [
				'Twig\\Extra\\Markdown\\MarkdownRuntime',
				'convert',
			], [ 'is_safe' => [ 'all' ] ] ),
			new TwigFilter( 'html_to_markdown', [ $this, 'twig_html_to_markdown' ], [ 'is_safe' => [ 'all' ] ] ),
		];
	}

	/**
	 * Converts HTML to Markdown.
	 *
	 * @param string $body    The HTML body to process.
	 * @param array  $options The options to apply.
	 *
	 * @return string The converted markdown.
	 */
	public function twig_html_to_markdown( string $body, array $options = [] ): string {
		static $converters;

		if ( ! class_exists( HtmlConverter::class ) ) {
			throw new \LogicException( 'You cannot use the "html_to_markdown" filter as league/html-to-markdown is not installed; try running "composer require league/html-to-markdown".' );
		}

		$options = $options + [
				'header_style'      => 'setext',
				'suppress_errors'   => true,
				'strip_tags'        => true,
				'bold_style'        => '**',
				'italic_style'      => '*',
				'remove_nodes'      => 'head style',
				'hard_break'        => true,
				'list_item_style'   => '-',
				'preserve_comments' => false,
			];

		$environment = new Environment( $options );
		$environment->addConverter( new BlockquoteConverter() );
		$environment->addConverter( new CodeConverter() );
		$environment->addConverter( new CommentConverter() );
		$environment->addConverter( new DivConverter() );
		$environment->addConverter( new EmphasisConverter() );
		$environment->addConverter( new HardBreakConverter() );
		$environment->addConverter( new HeaderConverter() );
		$environment->addConverter( new HorizontalRuleConverter() );
		$environment->addConverter( new ImageConverter() );
		$environment->addConverter( new LinkConverter() );
		$environment->addConverter( new ListBlockConverter() );
		$environment->addConverter( new ListItemConverter() );
		$environment->addConverter( new ParagraphConverter() );
		$environment->addConverter( new PreformattedConverter() );
		$environment->addConverter( new TextConverter() );

		if ( ! isset( $converters[ $key = serialize( $options ) ] ) ) {
			$converters[ $key ] = new HtmlConverter( $environment );
		}

		return $converters[ $key ]->convert( $body );
	}
}
