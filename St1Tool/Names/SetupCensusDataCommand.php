<?php
namespace St1Tool\Names;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
class SetupCensusDataCommand extends Command
{
    
    protected function configure()
    {
        $this
            ->setName('names:setup-census-data')
            ->setDescription('Download the 1990 US Census names lists needed for other names:* commands');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Creating folder...');
        $this->createDataFolder();
        
        $output->writeln('Downloading male first names...');
        $this->setupFirstNamesMale();
        
        $output->writeln('Downloading female first names...');
        $this->setupFirstNamesFemale();
        
        $output->writeln('Downloading all last names...');
        $this->setupLastNamesAll();
        
        return 0;
    }
    
    private function createDataFolder()
    {
        $folder = NamesHelper::getDataFolderPath();
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }
    }
    
    private function download($url)
    {
        $output = NamesHelper::getDataFolderPath(basename($url));
        file_put_contents($output, file_get_contents($url));
    }
    
    private function setupFirstNamesMale()
    {
        $this->download(NamesHelper::URL_FIRSTNAMES_MALE);
    }
    
    private function setupFirstNamesFemale() 
    {
        $this->download(NamesHelper::URL_FIRSTNAMES_FEMALE);
    }
    
    private function setupLastNamesAll() 
    {
        $this->download(NamesHelper::URL_LASTNAMES);
    }
    
}