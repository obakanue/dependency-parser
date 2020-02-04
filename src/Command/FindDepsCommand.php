<?php
// src/Command/FindDepsCommand.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\String\UnicodeString;
use function Symfony\Component\String\u;

/**
 * This class represents a CLI tool dependency parser
 *
 * Run tool 'FindDepsCommand.php' with php bin/console app:find-deps [SOME_DIRECTORY_PATH];
 *
 * @author Sofi Flink <sofi.flicnk @ gmail.com>
 */
class FindDepsCommand extends Command
{
    /**
    * Command for tool
    */
    protected static $defaultName = 'app:find-deps';

    /**
    * Arguments for tool
    */
    protected function configure()
    {
        $this->addArgument('filespath', InputArgument::OPTIONAL, 'Path to directory of files to parse for dependencies.');
    }

    /**
    * Logics for execution of tool
    *
    * Accepts only .lock and .json files
    */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder();
        $filespath = $input->getArgument('filespath');
        if(is_dir($filespath)){
            $finder->files()->in($filespath)->name('/^.*\.(lock|json)$/');
        } elseif(!isset($filepath)){
            $finder->files()->in(__DIR__)->name('/^.*\.(lock|json)$/');
        } else{
            $output->writeln("${filespath} is not a valid directory/file-path. Please verify correct path before trying command again.");
            return 0;
        }
        foreach($finder as $file){
            $tables = array();
            $devDependencyArray = array();
            $dependencyArray = array();
            $output->writeln($file->getRelativePathName());
            $contents = $file->getContents();
            if($file->getExtension() == 'json'){
                $tables = $this->_getJsonDependencies($contents);
                $devDependencyArray = $tables[0];
                $dependencyArray = $tables[1];
                $this->_createTable($devDependencyArray, $output, 1);
                $this->_createTable($dependencyArray, $output, 0);
            } else{
                $tables = $this->_getLockDependencies($contents);
                $dependencyArray = $tables;
                $this->_createTable($dependencyArray, $output, 0);
            }
        }
        return 0;
    }

    /**
    * Returns an array with arrays devDependencies and dependencies mapped product to version
    *
    * @param string format of JSON-file to filter out devDependencies and dependencies
    */
    private function _getJsonDependencies($fileContent)
    {
        $devRows = array();
        $rows = array();
        $tables = array();
        $contentAsJson = json_decode($fileContent, true);
        foreach ($contentAsJson["dependencies"] as $product => $version) {
            array_push($rows, [$product, $version]);
        }
        foreach ($contentAsJson["devDependencies"] as $product => $version) {
            array_push($devRows, [$product, $version]);
        }
        array_push($tables, $devRows);
        array_push($tables, $rows);
        return $tables;
    }

    /**
    * Returns an array with dependencies mapped product to version
    *
    * @param string format of LOCK-file to filter out dependencies
    */
    private function _getLockDependencies($fileContent)
    {
        $rows = array();
        $fileContent = strstr($fileContent, "DEPENDENCIES");
        $fileContent = strstr($fileContent, "\n\n", true);
        $fileContent = strstr($fileContent, "\n");
        $fileContent = explode("\n", $fileContent);
        foreach($fileContent as $row){
            array_push($rows, explode(" ", ltrim($row), 2));
        }

        return $rows;
    }

    /**
    * Renders a table with dependencies (Product and Version) for a file
    *
    * @param Array an array to add to the table
    * @param int to determine wether devDependencies or dependencies are to be rendered
    */
    private function _createTable(Array $array, OutputInterface $output, int $devDep)
    {
        if(!empty($array)){
            $table = new Table($output);
            $table
                ->setHeaders(['Product', 'Version'])
                ->setRows($array)
                ->setColumnWidths([10, 10, 10]);

            if($devDep){
                $table->setHeaderTitle('Devdependencies found');
            } else{
                $table->setHeaderTitle('Dependencies found');
            }
            $table->render();
        } else{
            $table = new Table($output);
            if($devDep){
                $table->setHeaders(['No devdependencies found']);
            } else{
                $table->setHeaders(['No dependencies found']);
            }
            $table->render();
        }
    }

}
