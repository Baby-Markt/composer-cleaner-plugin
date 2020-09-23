# composer-cleaner-plugin
[![Build Status](https://travis-ci.org/Baby-Markt/composer-cleaner-plugin.svg?branch=master)](https://travis-ci.org/Baby-Markt/composer-cleaner-plugin) ![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/babymarkt/composer-cleaner-plugin) [![version](https://img.shields.io/packagist/v/babymarkt/composer-cleaner-plugin?style=flat)](https://packagist.org/packages/babymarkt/composer-cleaner-plugin) ![GitHub](https://img.shields.io/github/license/Baby-Markt/composer-cleaner-plugin) ![GitHub All Releases](https://img.shields.io/github/downloads/Baby-Markt/composer-cleaner-plugin/total) 

* [Install](#install)
* [Configure](#configure)
* [Run](#run)

## Install
To install the plugin use Composer:
```bash
composer require --dev babymarkt/composer-plugin-cleaner
```

## Configure
The configuration must be placed in the `extra` section in `composer.json`. An example is:
```json
{
  "name": "company/your-project",
  "type": "project",
  "extra": {
    "babymarkt:cleaner": {
      "context-name": {
        "pattern": [
          "README*",
          ".git*"
        ],
        "paths": [
          "test",
          "artifacts"
        ],
        "exclude": [
          "test/important"
        ]
      }
    }
  },
  "require-dev": {
    "babymarkt/composer-plugin-cleaner": "*"
  }
}
```
The program contains a default configuration under the context `default` (Surprise surprise!). 
More detailed information can be found in the class 
[`AbstractCommand`](./src/Cleaner/AbstractCommand.php). Run 
```bash
composer babymarkt:cleaner:clean default
```
to use it.

### Configuration options

#### `context`
Each configuration set is wrapped in a cleaning context. A context is a simple string
which can be used on terminal to select the configuration.
```json
{
  "extra": {
    "babymarkt:cleaner": {
      "context-name": {
        "your cleaner options": "..."
      }
    }
  }
}
```
#### `pattern`
`pattern` contains a list of `glob` pattern to select files in the project 
directory tree. Consult the [PHP documentation](https://www.php.net/manual/de/function.glob.php) 
or [Wikipedia](https://en.wikipedia.org/wiki/Glob_(programming)) for more information about the `glob` function and patterns.  
```json
{
  "extra": {
    "babymarkt:cleaner": {
      "context-name": {
        "pattern": [
          "README*",
          ".git*"
        ]
      }
    }
  }
}
```
This example searches for files starting with `README` or `.git` in the complete 
project tree. 

#### `paths`
By default the cleaner command use the project root directory to search files 
(the directory that contains the `composer.json` file). With the option `paths`
you can set multiple paths to search for files in instead of the default root 
directory. All paths are relative to the root directory, also such starting 
with an `/`. Paths outside the root are being ommited.
```json
{
  "extra": {
    "babymarkt:cleaner": {
      "context-name": {
        "pattern": [
          ".git*"
        ],
        "paths": [
          "src",
          "vendor"
        ]
      }
    }
  }
}
```
This example searches for files starting with `.git` in `./src` and `./vendor`, nowhere else.

#### `exclude`
This option allows you to exclude files from deletion. The list consists of 
[RegEx](https://www.php.net/manual/de/reference.pcre.pattern.syntax.php) patterns, without specifying the delimiter characters. The pattern is 
applied to the entire path and not only to the file name. The used delimiter 
character is the `#`. If you need to use it in you pattern, you must escape it.
```json
{
  "extra": {
    "babymarkt:cleaner": {
      "context-name": {
        "pattern": [
          "data/*"
        ],
        "exclude": [
          ".*\\.important"
        ]
      }
    }
  }
}
```
This example searches for paths starting with `data/` and NOT ends with 
`.important`. 

## Run
To run the command use composer:
```bash    
composer babymarkt:cleaner:clean your-context-name
```
or add a custom script to your `composer.json`:
```json
{
  "scripts": {
    "cleanup:dev": [
      "@composer babymarkt:cleaner:clean your-context-name"
    ]
  }
}
```