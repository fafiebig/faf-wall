# faf-wall

Wordpress Plugin for Image Walls.
Show image galleries as responsive wall.

[VNJS Freewall](http://vnjs.net/www/project/freewall/)

# Installation

* Unzip and upload the plugin to the **/wp-content/plugins/** directory
* Activate the plugin in WordPress
* Use the editor button or **[wall images="ids of images"]** to show as wall.

# Installation with composer

* Add the repo to your composer.json

```json

"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/fafiebig/faf-wall.git"
    }
],

```

* require the package with composer

```shell

composer require fafiebig/faf-wall 1.*

```