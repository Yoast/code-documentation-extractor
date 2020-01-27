<?php namespace YoastDocParser\WordPress;

use Cocur\Slugify\Slugify;

/**
 * Class Plugin
 * @package YoastDocParser\WordPress
 */
class Plugin implements PluginInterface {

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $uri;

	/**
	 * @var string
	 */
	private $version;

	/**
	 * @var string
	 */
	private $description;

	/**
	 * @var Author
	 */
	private $author;

	/**
	 * @var Textdomain
	 */
	private $textdomain;

	/**
	 * @var bool
	 */
	private $network;

	/**
	 * @var string
	 */
	private $slug;

	/**
	 * Plugin constructor.
	 *
	 * @param string     $name        The name of the plugin.
	 * @param string     $uri         The URI of the plugin.
	 * @param string     $version     The version of the plugin.
	 * @param string     $description The description of the plugin.
	 * @param Author     $author      The author of the plugin.
	 * @param Textdomain $textdomain  The textdomain used by the plugin.
	 * @param bool       $network     Whether or not the plugin is network activated.
	 */
	public function __construct( string $name, string $uri, string $version, string $description, Author $author, Textdomain $textdomain, bool $network = false ) {
		$this->name        = $name;
		$this->uri         = $uri;
		$this->version     = $version;
		$this->description = $description;
		$this->author      = $author;
		$this->textdomain  = $textdomain;
		$this->network     = $network;
		$this->slug        = ( new Slugify() )->slugify( $name );
	}

	/**
	 * Gets the name.
	 *
	 * @return string The name of the plugin.
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Gets the URI.
	 *
	 * @return string The URI of the plugin.
	 */
	public function getUri() {
		return $this->uri;
	}

	/**
	 * Gets the version.
	 *
	 * @return string The version of the plugin.
	 */
	public function getVersion() {
		return $this->version;
	}

	/**
	 * Gets the description.
	 *
	 * @return string The description of the plugin.
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Gets the author's name.
	 *
	 * @return string The author's name.
	 */
	public function getAuthor() {
		return $this->author->getName();
	}

	/**
	 * Gets the author's URI.
	 *
	 * @return string The author's URI.
	 */
	public function getAuthorURI() {
		return $this->author->getUri();
	}

	/**
	 * Gets the textdomain.
	 *
	 * @return string The textdomain of the plugin.
	 */
	public function getTextdomain() {
		return $this->textdomain->getDomain();
	}

	/**
	 * Gets the textdomain's path.
	 *
	 * @return string The path for the textdomain of the plugin.
	 */
	public function getTextdomainPath() {
		return $this->textdomain->getPath();
	}

	/**
	 * Determines whether or not the plugin is network activated.
	 *
	 * @return bool Whether or not the plugin is network activated.
	 */
	public function isNetwork() {
		return $this->network;
	}

	/**
	 * Gets the slug of the plugin.
	 *
	 * @return string The slug.
	 */
	public function getSlug() {
		return $this->slug;
	}
}
