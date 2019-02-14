<?php
namespace crawler;

require_once __DIR__.'/../phpquery/phpQuery/phpQuery.php';

class crawler {
    private $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function crawl()
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);

        $res = curl_exec($curl);

        $pq = \phpQuery::newDocument($res);

        $resumeCollection = $pq->find('div[hh-resume-hash]');
        $resumeCollection = pq($resumeCollection);

        foreach($resumeCollection as $resume){
            $hash[] = $resume->attr('[hh-resume-hash]');
            $position[] = $resume->text('a .resume-search-item__name HH-VisitedResume-Href HH-LinkModifier');
            $salary[] = (int)$resume->text('div .resume-search-item__compensation');
            $age[] = (int)$resume->text('div[itemprop]=name > span');
            $profilePicUrl[] = $resume->attr('img[src]');
            $exp[] = (int)$resume->text('div[data-qa]=resume-serp__resume-excpirience-sum');
        }
    }

    public function save()
    {

    }
}
//$url = "https://hh.ru/search/resume";







