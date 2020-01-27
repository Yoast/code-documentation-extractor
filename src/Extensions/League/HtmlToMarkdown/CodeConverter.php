<?php namespace YoastDocParser\Extensions\League\HtmlToMarkdown;

use League\HTMLToMarkdown\Converter\ConverterInterface;
use League\HTMLToMarkdown\ElementInterface;

/**
 * Class CodeConverter
 * @package YoastDocParser\Extensions\League\HtmlToMarkdown
 */
class CodeConverter implements ConverterInterface {
	/**
	 * @param ElementInterface $element
	 *
	 * @return string
	 */
	public function convert( ElementInterface $element ) {
		$language = '';

		// Checking for language class on the code block
		$classes = $element->getAttribute( 'class' );

		if ( $classes ) {
			// Since tags can have more than one class, we need to find the one that starts with 'language-'
			$classes = explode( ' ', $classes );
			foreach ( $classes as $class ) {
				if ( strpos( $class, 'language-' ) !== false ) {
					// Found one, save it as the selected language and stop looping over the classes.
					$language = str_replace( 'language-', '', $class );
					break;
				}
			}
		}

		return $this->toMarkdown(
			$element,
			$language,
			$element->getAttribute( 'data-start-line' ),
			$element->getAttribute( 'data-end-line' )
		);
	}

	protected function toMarkdown( ElementInterface $element, string $language, string $startLine, string $endLine ) {
		$code = html_entity_decode( $element->getChildrenAsString() );

		// In order to remove the code tags we need to search for them and, in the case of the opening tag
		// use a regular expression to find the tag and the other attributes it might have
		$code = preg_replace( '/<code\b[^>]*>/', '', $code );
		$code = str_replace( '</code>', '', $code );

		// Checking if it's a code block or span
		if ( $this->shouldBeBlock( $element, $code ) ) {
			// Code block detected, newlines will be added in parent
			return '```' . $language . ' ' . $this->getLines( $startLine, $endLine ) . "\n" . $code . "\n" . '```';
		}

		// One line of code, wrapping it on one backtick, removing new lines
		return '`' . preg_replace( '/\r\n|\r|\n/', '', $code ) . '`';
	}

	/**
	 * @param ElementInterface $element
	 * @param string           $code
	 *
	 * @return bool
	 */
	private function shouldBeBlock( ElementInterface $element, $code ) {
		if ( $element->getParent()->getTagName() == 'pre' ) {
			return true;
		}

		if ( preg_match( '/[^\s]` `/', $code ) ) {
			return true;
		}

		return false;
	}

	protected function getLines( string $start, string $end ) {
		if ( $start === '' && $end === '' ) {
			return '';
		}

		if ( $start === '' && $end !== '' ) {
			$start = $end;
			$end   = '';
		}

		if ( ( $start !== '' && $end === '' ) ) {
			return '{' . $start . '}';
		}

		return sprintf( '{%s-%s}', $start, $end );
	}

	/**
	 * @return string[]
	 */
	public function getSupportedTags() {
		return array( 'code' );
	}
}
