# Simple dependency parser find-deps
This simple dependency parser CLI-tool is used to find dependencies for .json- and .lock-files.

![Screenshot of generated tables](https://i.imgur.com/UQTrY0s.png=50x50)

## Installation
```
git clone git://github.com/obakanue/dependency-parser.git
```

## Usage
```
php bin/console.php app:find-deps [OPTIONAL: SOME-DIRECTORY-PATH]
```
This command will, if valid directory, find all .json- and .lock-files in "SOME-DIRECTORY-PATH" recursively and render a table with product and version number of found dependencies. If no argument is given the parser will look in the current directory for files recursively to parse.

## Testing
Testfiles folder gives a few different variations of .lock and .json file to test with the command tool.
