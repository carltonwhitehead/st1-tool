<?php
namespace St1Tool\Results;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableHelper;
use Peppercorn\St1\Category;
use Peppercorn\St1\File;
use Peppercorn\St1\Line;
use Peppercorn\St1\Query;
use Peppercorn\St1\GroupByDriver;
use Peppercorn\St1\SortTimeRawAscending;
use Peppercorn\St1\ResultSetSimple;
use Peppercorn\St1\Result;

class PaxCommand extends Command
{

    const ARG_FILE = 'file';

    protected function configure()
    {
        $this->setName('results:pax')
            ->setDescription('Print the pax results of an st1 file')
            ->addArgument(self::ARG_FILE, InputArgument::REQUIRED, 'The st1 file to query');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePath = $input->getArgument(self::ARG_FILE);
        $result = $this->query($filePath);
        $this->outputTable($output, $result);
    }
    
    private function query($filePath)
    {
        $content = file_get_contents($filePath);
        $file = new File($content, array(new Category('')));
        return Query::paxResults($file);
    }
    
    private function outputTable(OutputInterface $output, ResultSetSimple $results)
    {
        $tableHelper = $this->getHelper('table'); /* @var $tableHelper TableHelper */
        $tableHelper->setHeaders(array('Pos.', 'Class', 'Number', 'Name', 'PAX Time'));
        $rows = array();
        for ($i = 1; $i <= $results->getCount(); $i++) {
            $result = $results->getPlace($i);
            $line = $result->getLine();
            $rows[] = array(
            	$result->getPlace(),
                $line->getDriverClassRaw(),
                $line->getDriverNumber(),
                $line->getDriverName(),
                $line->getTimePax()
            );
        }
        $tableHelper->setRows($rows);
        $tableHelper->render($output);
    }
}