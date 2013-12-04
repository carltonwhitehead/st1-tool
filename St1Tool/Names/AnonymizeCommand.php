<?php
namespace St1Tool\Names;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Peppercorn\St1\File;
use Symfony\Component\Console\Input\InputOption;
use Peppercorn\St1\Category;
use Peppercorn\St1\LineException;
class AnonymizeCommand extends Command
{
    const ARG_INPUT_FILE = 'input-file';
    const ARG_OUTPUT_FILE = 'output-file';
    
    protected function configure()
    {
        $this->setName('names:anonymize');
        $this->setDescription('Anonymize an st1 file. Replaces all driver names with randomly generated names.');
        $this->addArgument(
            self::ARG_INPUT_FILE, 
            InputArgument::REQUIRED, 
            'Input file path'
        );
        $this->addArgument(
            self::ARG_OUTPUT_FILE, 
            InputArgument::REQUIRED, 
            'Output file path'
        );
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputFilePath = $input->getArgument(self::ARG_INPUT_FILE);
        if (!is_readable($inputFilePath)) {
            $output->writeln("<error>Input file is not readable: {$inputFilePath}</error>");
            return 1;
        }
        $content = file_get_contents($inputFilePath);
        $rename = $this->buildRenameArray($content);
        foreach ($rename as $original => $replacement) {
            $output->writeln("Replacing {$original} with {$replacement}");
        }
        $anonymizedContent = $this->buildAnonymizedContent($content, $rename);
        $outputFilePath = $input->getArgument(self::ARG_OUTPUT_FILE);
        $output->writeln("Writing anonymized file to {$outputFilePath}");
        file_put_contents($outputFilePath, $anonymizedContent);
        return 0;
    }
    
    private function buildRenameArray($content)
    {
        $generator = new NameGenerator();
        $file = new File($content, array(new Category('')));
        $names = array();
        for ($i = 0; $i < $file->getLineCount(); $i++) {
            try {
                $name = $file->getLine($i)->getDriverName();
                if (!array_key_exists($name, $names)) {
                    $generated = $generator->generate();
                    $names[$name] = $generated;
                }
            } catch (LineException $le) {
                continue;
            }
        }
        return $names;
    }
    
    private function buildAnonymizedContent($content, $rename)
    {
        $anonymized = $content;
        foreach ($rename as $original => $replacement) {
            $anonymized = str_replace("_driver_{$original}", "_driver_{$replacement}", $anonymized);
        }
        return $anonymized;
    }
}