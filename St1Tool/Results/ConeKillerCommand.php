<?php
namespace St1Tool\Results;

use Peppercorn\St1\Category;
use Peppercorn\St1\File;
use Peppercorn\St1\Line;
use Peppercorn\St1\ResultSetSimple;
use St1Tool\Query\ConeKillerQuery;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConeKillerCommand extends Command
{

    const ARG_FILE = 'file';

    protected function configure()
    {
        $this->setName('results:cone-killer')
            ->setDescription('Print the Cone Killer results of an st1 file')
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
        $query = new ConeKillerQuery($file);
        return $query->executeSimple();
    }
    
    private function outputTable(OutputInterface $output, ResultSetSimple $results)
    {
        $tableHelper = $this->getHelper('table'); /* @var $tableHelper TableHelper */
        $tableHelper->setHeaders(array('Pos.', 'Class', 'Number', 'Name', 'Cones'));
        $rows = array();
        for ($i = 1; $i <= $results->getCount(); $i++) {
            $result = $results->getPlace($i);
            $line = $result->getLine(); /* @var $line Line */
            $rows[] = array(
            	$result->getPlace(),
                $line->getDriverClassRaw(),
                $line->getDriverNumber(),
                $line->getDriverName(),
                $line->hasConePenalty() ? $line->getPenalty() : '0'
            );
        }
        $tableHelper->setRows($rows);
        $tableHelper->render($output);
    }
}