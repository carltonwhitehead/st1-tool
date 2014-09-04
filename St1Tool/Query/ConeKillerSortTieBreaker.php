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
    
    public function __construct()
    {
        $this->paxTimeTieBreaker = new SortTieBreakerByNextFastestTimePax();
    }

    protected function getRuns(Line $line)
    {
        $query = new Query($line->getFile());
        return $query->where(new WhereDriverIs($line->getDriverCategory(), $line->getDriverClass(), $line->getDriverNumber()))
                ->orderBy(ConeKillerSort::getSort())
                ->executeSimple();
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

    protected function getReasonCodeForTieBreakByTimeDifference(Line $winner, Line $loser) {
        // no-op
    }

                    
}
