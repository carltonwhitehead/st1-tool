<?php
namespace St1Tool\Query;

use Peppercorn\St1\SortProvider;
use Peppercorn\St1\Line;

class ConeKillerSort implements SortProvider {
    public static function getSort() {
        return function(Line $a, Line $b) {
            $aConed = $a->hasConePenalty();
            $bConed = $b->hasConePenalty();
            if (!$aConed and !$bConed) {
                return 0;
            }
            $aConeCount = $aConed ? $a->getPenalty() : 0;
            $bConeCount = $bConed ? $b->getPenalty() : 0;
            if ($aConeCount == $bConeCount) {
                return 0;
            }
            return $aConeCount < $bConeCount
                    ? 1 : -1;
        };
    }

}