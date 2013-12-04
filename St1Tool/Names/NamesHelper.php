<?php
namespace St1Tool\Names;

class NamesHelper
{

    const DATA_FOLDER = '/data/Names';
    const URL_LASTNAMES = 'http://www.census.gov/genealogy/www/data/1990surnames/dist.all.last';
    const URL_FIRSTNAMES_MALE = 'http://www.census.gov/genealogy/www/data/1990surnames/dist.male.first';
    const URL_FIRSTNAMES_FEMALE = 'http://www.census.gov/genealogy/www/data/1990surnames/dist.female.first';
    
    public static function getDataFolderPath($file = null)
    {
        $path = ST1_TOOL_PATH . self::DATA_FOLDER;
        if (is_string($file)) {
            $path .= "/{$file}";
        }
        return $path;
    }
    
    public static function getLastNamesPath()
    {
        return self::getDataFolderPath(basename(self::URL_LASTNAMES));
    }
    
    public static function getFirstNamesMalePath()
    {
        return self::getDataFolderPath(basename(self::URL_FIRSTNAMES_MALE));
    }
    
    public static function getFirstNamesFemalePath()
    {
        return self::getDataFolderPath(basename(self::URL_FIRSTNAMES_FEMALE));
    }
}