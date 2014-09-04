<?php
namespace St1Tool\Query;

use Peppercorn\St1\File;
use Peppercorn\St1\GroupByDriver;
use Peppercorn\St1\Query;

class ConeKillerQuery extends Query {
    public function __construct(File $file) {
        parent::__construct($file);
        $this->orderBy(ConeKillerSort::getSort())
            ->breakTiesWith(new ConeKillerSortTieBreaker())
            ->distinct(new GroupByDriver());
    }
}
