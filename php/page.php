<?php

/**
* This is a class that creates an HTML page.
* There are few core parts of the class
* First, is the defaults() method. Use this method to load any default
* stylesheets, scripts that will show up on all pages. This method will be
* called when the object gets created.
* Second, the generate() method is what returns the html page generated by
* the object.
*/
class Page
{

	private $title;
	private $description;
	private $body;
	private $stylesheets;
	private $scriptsTop;
	private $scriptsBottom;
	private $favicon;

	function __construct($body = '', $title = '', $description = '') {
		$this->title = $title;
		$this->body = $body;
		$this->description = $description;
		$this->stylesheets = array();
		$this->scriptsTop = array();
		$this->scriptsBottom = array();
		$this->favicon = '';

		// loads defaults
		$this->defaults();
	}

	/**
	* Use this function to add all the default scripts and stylesheets file
	* paths that need to be in all pages
	*/
	private function defaults() {
		$rootFolder = '//example.com/';

		$this->favicon = $this->htmlLink($rootFolder . 'img/favicon.ico', 'shortcut icon', 'image/x-icon');

		$this->stylesheets[] = $this->htmlStyleSheet($rootFolder . 'css/example.css');

		$this->scriptsTop[] = $this->htmlScript($rootFolder . 'js/exmaple.js');

		$this->scriptsBottom[] = $this->htmlScript($rootFolder . 'js/example.js');
	}

	/**
	* This the core method that will create the page.
	* If there is an element that needs to show up on every page (Ex. Header, Footer, Navbar)
	* edit the $page variable.
	*/
	function generate() {
		if (strlen($this->title) == 0) {
			$this->title = 'Title | Goes Here ...';
		}

		if (strlen($this->description) == 0) {
			$this->description = 'Description Goes Here ...';
		}

		$this->stylesheetString = implode("\n\t", $this->stylesheets);

		$this->scriptTopString = implode("\n\t", $this->scriptsTop);
		$this->scriptBottomString = implode("\n\t", $this->scriptsBottom);

		$page = <<<HTML
<!DOCTYPE html>

<html class="no-js" lang="en">

<head>
	<meta charset="UTF-8">
	<title>$this->title</title>

	<meta name="description" content="$this->description">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	$this->favicon

	$this->stylesheetString

	$this->scriptTopString
</head>

<body>
	<header>
	</header>

	$this->body

	$this->scriptBottomString

	<footer>
	</footer>

	<!-- To initialize foundation-->
	<script>
		$(document).foundation();
	</script>
</body>

</html>
HTML;
		return $page;
	}

	public function addScriptToTop($src) {
		$this->scriptsTop[] = $this->htmlScript($src);
	}

	public function addScriptToBottom($src) {
		$this->scriptsBottom[] = $this->htmlScript($src);
	}

	public function addStyleSheet($href) {
		$this->stylesheets[] = $this->htmlStyleSheet($href);
	}

	/**
	* private helper functions
	*/

	private function htmlLink($href, $rel, $type) {
		$rel = strlen($rel) > 0 ? "rel=\"$rel\"" : '';
		$type = strlen($type) > 0 ? "type=\"$type\"" : '';
		$href = strlen($href) > 0 ? "href=\"$href\"" : '';

		return "<link $rel $type $href>";
	}

	private function htmlStyleSheet($href) {
		return $this->htmlLink($href, 'stylesheet', 'text/css');
	}

	private function htmlScript($src, $type = '') {
		$src = strlen($src) > 0 ? "src=\"$src\"" : '';
		$type = strlen($type) > 0 ? "type=\"$type\"" : '';

		return "<script $type $src></script>";
	}

}
