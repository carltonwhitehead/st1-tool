<?php

namespace St1Tool\Query;

use Peppercorn\St1\Line;
use Peppercorn\St1\Query;
use Peppercorn\St1\SortTieBreaker;
use Peppercorn\St1\SortTieBreakerByNextFastestTimePax;
use Peppercorn\St1\TieBreak;
use Peppercorn\St1\WhereDriverIs;

class ConeKillerSortTieBreaker extends SortTieBreaker
{
    
    private $paxTimeTieBreaker;
    private $runCache;
    
    public function __construct()
    {
        $this->paxTimeTieBreaker = new SortTieBreakerByNextFastestTimePax();
        $this->runCache = array();
    }

    protected function getRuns(Line $line)
    {
        $key = $line->getDriverClassRaw() . "_" . $line->getDriverNumber();
        $runs;
        if (isset($this->runCache[$key])) {
            $runs = $this->runCache[$key];
        } else {
            $query = new Query($line->getFile());
            $runs = $query->where(new WhereDriverIs($line->getDriverCategory(), $line->getDriverClass(), $line->getDriverNumber()))
                    ->orderBy(ConeKillerSort::getSort())
                    ->executeSimple();
            $this->runCache[$key] = $runs;
        }
        return $runs;
        
    }

    protected function getTimeForTieBreak(Line $line)
    {
        return $line->hasConePenalty() ? 0 - $line->getPenalty() : 0;
    }

    public function breakTie(Line $a, Line $b) 
    {
        $aRuns = $this->getRuns($a);
        $bRuns = $this->getRuns($b);
        $aCount = $aRuns->getCount();
        $bCount = $bRuns->getCount();
        for ($i = 0; $i < $aCount && $i < $bCount; $i++) {
            $aLineForTieBreak = $aRuns->getLine($i);
            $aHasCones = $aLineForTieBreak->hasConePenalty();
            $aCones = $aHasCones ? $aLineForTieBreak->getPenalty() : 0;
            $bLineForTieBreak = $bRuns->getLine($i);
            $bHasCones = $bLineForTieBreak->hasConePenalty();
            $bCones = $bHasCones ? $bLineForTieBreak->getPenalty() : 0;
            if ($aCones > $bCones) {
                return TieBreak::goesToA($a, $b, TieBreak::REASON_CODE_CONE);
            } else if ($bCones > $aCones) {
                return TieBreak::goesToB($b, $a, TieBreak::REASON_CODE_CONE);
            }
        }
        
        return $this->paxTimeTieBreaker->breakTie($a, $b);
    }

}
