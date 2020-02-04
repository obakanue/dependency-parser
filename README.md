# Simple dependency parser find-deps
This simple dependency parser CLI-tool is used to find dependencies for .json- and .lock-files.

![Screenshot of generated tables](https://i.imgur.com/UQTrY0s.png=50x50)

## Installation
```
git clone git://github.com/obakanue/dependency-parser.git
```

## Usage
```
php bin/console.php app:find-deps [REQARGUMENT: SOME DIRECTORY]
```
This command will, if valid directory, find all .json- and .lock-files in "SOME DIRECTORY PATH" and find all dependencies, render a table with product and version number.
