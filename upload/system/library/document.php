<?php
/**
 * @package		VentoCart
 * @author		Daniel Kerr
 * @copyright	Copyright (c) 2005 - 2017, VentoCart, Ltd. (https://www.ventocart.com/)
 * @license		https://opensource.org/licenses/GPL-3.0
 * @link		https://www.ventocart.com
 */
namespace Ventocart\System\Library;
/**
 * Class Document
 */
class Document
{
	/**
	 * @var string
	 */
	private string $title = '';
	/**
	 * @var string
	 */
	private string $description = '';
	/**
	 * @var string
	 */
	private string $keywords = '';
	/**
	 * @var array
	 */
	private array $links = [];
	/**
	 * @var array
	 */
	private array $styles = [];
	/**
	 * @var array
	 */
	private array $scripts = [];

	/**
	 * setTitle
	 *
	 * @param string $title
	 * 
	 * @return void
	 */
	public function setTitle(string $title): void
	{
		$this->title = $title;
	}

	/**
	 * getTitle
	 *
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * setDescription
	 *
	 * @param string $description
	 *
	 * @return void
	 */
	public function setDescription(string $description): void
	{
		$this->description = $description;
	}

	/**
	 * getDescription
	 *
	 * @return string
	 */
	public function getDescription(): string
	{
		return $this->description;
	}

	/**
	 * setKeywords
	 *
	 * @param string $keywords
	 */
	public function setKeywords(string $keywords): void
	{
		$this->keywords = $keywords;
	}

	/**
	 * getKeywords
	 *
	 * @return string
	 */
	public function getKeywords(): string
	{
		return $this->keywords;
	}

	/**
	 * addLink
	 *
	 * @param string $href
	 * @param string $rel
	 *
	 * @return void
	 */
	public function addLink(string $href, string $rel): void
	{
		$this->links[$href] = [
			'href' => $href,
			'rel' => $rel
		];
	}

	/**
	 * getLinks
	 *
	 * @return array
	 */
	public function getLinks(): array
	{
		return $this->links;
	}

	/**
	 * addStyle
	 *
	 * @param string $href
	 * @param string $rel
	 * @param string $media
	 *
	 * @return void
	 */
	public function addStyle(string $href, string $rel = 'stylesheet', string $media = 'screen'): void
	{
		$this->styles[$href] = [
			'href' => $href,
			'rel' => $rel,
			'media' => $media
		];
	}

	/**
	 * getStyles
	 *
	 * @return array
	 */
	public function getStyles(): array
	{
		return $this->styles;
	}

	/**
	 * addScript
	 *
	 * @param string $href
	 * @param string $position
	 *
	 * @return void
	 */
	public function addScript(string $href, string $position = 'header'): void
	{
		$this->scripts[$position][$href] = ['href' => $href];
	}

	/**
	 * getScripts
	 *
	 * @param string $position
	 *
	 * @return array
	 */
	public function getScripts(string $position = 'header'): array
	{
		if (isset($this->scripts[$position])) {
			return $this->scripts[$position];
		} else {
			return [];
		}
	}
}
