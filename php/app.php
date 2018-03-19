<?php

/**
*
*/
class App
{
	private $name;
	private $directory;
	private $ipaName;
	private $bundleID;
	private $linkPicutre;

	function __construct($name, $directory, $ipaName, $bundleID, $linkPicutre = '') {
		$this->name = $name;
		$this->directory = $directory;
		$this->ipaName = $ipaName;
		$this->bundleID = $bundleID;
		$this->linkPicutre = $linkPicutre;
	}

	function getName() {
		return $this->name;
	}

	function getHref() {
    $directory = $this->directory == '.' ? $this->currentDirectory() : $this->directory;
		return "itms-services://?action=download-manifest&amp;url=https://test.website.com/path" . "$directory/manifest-$this->ipaName.plist";
	}

	function getLinkPicture() {
		return $this->linkPicutre;
	}

	function generate_manifest() {
		$manifest = $this->manifest_string();
    	$directory = $this->directory == '.' ? '' : $this->directory . '/';
		$filename = $directory . "manifest-$this->ipaName.plist";
		if (file_exists($filename)) {
			// echo '<h1>File already exists</h1>';

			/* was used to see if the file was being tampered with
				but beacuse of 8.1 need to tamper with some files to
				install app onto phones
			$old_content = file_get_contents($filename);
			if ($old_content == $manifest) {
			// echo '<h1>Old file is up to date no reason to create again</h1>';
				return;
			}
			*/

			return;

		}
		$file = fopen($filename, 'w');
		if ($file) {
			fwrite($file, $manifest);
			fclose($file);
			// echo "<a href=\"itms-services://?action=download-manifest&amp;url=https://test.website.com/path" . $filename . "\"><h1>Download</h1></a>";
		} else {
			echo '<h1>This file could not be created</h1>';
			return;
		}
	 }

  private function currentDirectory() {
    $result = str_replace(dirname(getcwd()), '', getcwd());
    return substr($result, 1);
  }

	private function manifest_string() {
    $directory = $this->directory == '.' ? $this->currentDirectory() : $this->directory .'/';
		$manifest = <<<XML
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
   <!-- array of downloads. -->
   <key>items</key>
   <array>
       <dict>
           <!-- an array of assets to download -->
           <key>assets</key>
           <array>
               <!-- software-package: the ipa to install. -->
               <dict>
                   <!-- required. the asset kind. -->
                   <key>kind</key>
                   <string>software-package</string>
                   <!-- optional. md5 every n bytes. will restart a chunk if md5 fails.
                   <key>md5-size</key>
                   <integer>10485760</integer>
                   -->
                   <!-- optional. array of md5 hashes for each "md5-size" sized chunk.
                   <key>md5s</key>
                   <array>
                   </array>
                   -->
                   <!-- required. the URL of the file to download. -->
                   <key>url</key>
                   <string>https://test.website.com/path$directory$this->ipaName</string>
               </dict>
               <!-- display-image: the icon to display during download .-->
               <dict>
                   <key>kind</key>
                   <string>display-image</string>
                   <!-- optional. indicates if icon needs shine effect applied.
                   <key>needs-shine</key>
                   <true/>
                   -->
                   <key>url</key>
                   <string>https://test.website.com/pathimg/lionhead57x57.png</string>
               </dict>
               <!-- full-size-image: the large 512x512 icon used by iTunes. -->
               <dict>
                   <key>kind</key>
                   <string>full-size-image</string>
                   <!-- optional. one md5 hash for the entire file.
                   <key>md5</key>
                   <string>61fa64bb7a7cae5a46bfb45821ac8bba</string>
                   -->
                   <key>needs-shine</key>
                   <true/>
                   <key>url</key>
                   <string>https://test.website.com/pathimg/lionhead512x512.png</string>
               </dict>
           </array>
           <key>metadata</key>
           <dict>
               <!-- required -->
               <key>bundle-identifier</key>
               <string>$this->bundleID</string>
               <!-- optional (software only)
               <key>bundle-version</key>
               <string>1.0</string>
               -->
               <!-- required. the download kind. -->
               <key>kind</key>
               <string>software</string>
               <!-- optional. displayed during download; typically company name
               <key>subtitle</key>
               <string>Optional Name</string>
               -->
               <!-- required. the title to display during the download. -->
               <key>title</key>
               <string>$this->name</string>
           </dict>
       </dict>
   </array>
</dict>
</plist>
XML;
	return $manifest;
	}

}
