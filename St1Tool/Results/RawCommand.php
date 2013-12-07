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

class RawCommand extends Command
{

    const ARG_FILE = 'file';

    protected function configure()
    {
        $this->setName('results:raw')
            ->setDescription('Print the raw results of an st1 file')
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
        $query = new Query($file);
        $query->distinct(new GroupByDriver());
        $query->orderBy(SortTimeRawAscending::getSort());
        return $query->execute();
    }
    
    private function outputTable(OutputInterface $output, $result)
    {
        $tableHelper = $this->getHelper('table'); /* @var $tableHelper TableHelper */
        $tableHelper->setHeaders(array('Pos.', 'Class', 'Number', 'Name', 'Raw Time'));
        $rows = array();
        $i = 0;
        foreach ($result as $line /* @var $line Line */) {
            $rows[] = array(
            	++$i,
                $line->getDriverClassRaw(),
                $line->getDriverNumber(),
                $line->getDriverName(),
                $line->getTimeRawWithPenalty()
            );
        }
        $tableHelper->setRows($rows);
        $tableHelper->render($output);
    }
}