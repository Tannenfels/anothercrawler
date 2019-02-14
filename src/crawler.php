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
            $this->save(end($hash), end($position),end($salary),end($age),end($profilePicUrl),end($exp));
        }
    }

    public function save($hash, $position, $salary, $age, $profilePicUrl, $exp)
    {
        $dsn = ""; //эмуляция
        $pdo = new \PDO($dsn);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO HASHES (hashid) VALUES (:hash)");
            $stmt->bindParam(':hashid', $hash);
            $stmt->execute();

            $stmt = $pdo->prepare("INSERT INTO USERDATA (hashid, position, salary, age, profilePicUrl, exp) VALUES (:hashid, :position, :salary, :age, :profilePicUrl, :exp)");
            $stmt->bindParam(':position', $position);
            $stmt->bindParam(':salary', $salary);
            $stmt->bindParam(':age', $age);
            $stmt->bindParam(':profilePicUrl', $profilePicUrl);
            $stmt->bindParam(':exp', $exp);
            $stmt->execute();

            $pdo->commit();

        }catch (\Exception $e){
            $pdo->rollBack();
        }
    }
}







