<?php
namespace St1Tool\Names;

class NameGenerator
{
    private $firstsMale;
    private $firstsFemale;
    private $lasts;
    
    private $generated = array();
    
    public function __construct()
    {
        $this->firstsMale = file(NamesHelper::getFirstNamesMalePath());
        $this->firstsFemale = file(NamesHelper::getFirstNamesFemalePath());
        $this->lasts = file(NamesHelper::getLastNamesPath());
    }
    
    public function generate()
    {
        $generated = false;
        while (!$generated) {
            $first = null;
            switch (rand(0, 1))
            {
            	case 0:
            	    $first = $this->pickFrom($this->firstsMale);
            	    break;
            	case 1:
            	    $first = $this->pickFrom($this->firstsFemale);
            	    break;
            }
            $last = $this->pickFrom($this->lasts);
            $name = "{$first} {$last}";
            if (!in_array($name, $this->generated)) {
                $this->generated[] = $name;
                $generated = true;
            }
        }
        return $name;
    }
    
    private function pickFrom(array $lines)
    {
        $key = array_rand($lines);
        $name = $lines[$key];
        $name = rtrim(substr($name, 0, 15));
        $name = strtolower($name);
        $name = ucfirst($name);
        return $name;
    }
    
}