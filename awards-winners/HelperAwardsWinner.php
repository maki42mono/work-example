<?php

class HelperAwardsWinner
{
    /**
     * @var TaglineAwards
     */
    private $Awards;
    /**
     * @var OldWinnersInterface
     */
    private $OldHelper = false;
    private $winners   = [];
    const CACHE_TIME   = 60 * 60 * 24 * 7; /* Неделя */
    const HELPERS_LIST = [
        'HelperAwards20202021Winners',
        'HelperAwards2019Winners',
        'HelperAwards2018Winners',
        'HelperAwards2017Winners',
    ];

    public function __construct(TaglineAwards $Awards)
    {
        $helpersWinners = [];
        foreach (self::HELPERS_LIST as $helperStr) {
            $helper = new $helperStr();
            $helpersWinners[$helper::getAwardsId()] = $helperStr;
        }

        $this->Awards = $Awards;
        if (key_exists($Awards->id, $helpersWinners)) {
            $this->OldHelper = new $helpersWinners[$Awards->id]();
        }

        $this->setWinners();
    }

    public function setWinners(array $options = []): self
    {
        $cached_name = "HelperAwardsWinner_getWinners_{$this->Awards->id}";

        $winners = Yii::app()->cache->get($cached_name);
        if ($winners) {
            $this->winners = $winners;
        }

        if (!$winners || (isset($options['fresh']) && $options['fresh'] === true)) {
            if ($this->OldHelper) {
                $winners = $this->OldHelper->getAwardsWinnersAnySort('winners_page');
                Yii::app()->cache->set($cached_name, $winners, self::CACHE_TIME);

                $this->winners = $winners;
            }
        }

        return $this;
    }

    public function getAwardsByWorkId(int $workId): array
    {
        return array_filter($this->winners, function ($var) use ($workId) {
            return isset($var['work_id']) && $var['work_id'] == $workId;
        });
    }

    public function getTaglineAwards(): TaglineAwards
    {
        return $this->Awards;
    }

    public function calcPreSortList(array $options = [])
    {

        if (isset($options['cached'])) {
            $PreShortService = new PreShortService($this, $options['cached']);
        } else {
            $PreShortService = new PreShortService($this);
        }

        $noms_for_sort = $PreShortService->getNomsForSort();
        $metal_flags = [
            'bronze' => ['bronze_1', 'bronze_2', 'bronze_3', 'bronze_4', 'bronze_5', 'bronze_6'],
            'silver' => ['silver_1', 'silver_2', 'silver_3', 'silver_4'],
            'gold' => ['gold', 'gold_2'],
        ];
        $min_sec_vote = 10;
        $vote_flags = [
            'first' => ['sum_1', 'avg_1', 'count_1', 'tag_tagline_awards_voting_first'],
            'second' => ['sum_2', 'avg_2', 'count_2', 'tag_tagline_awards_voting_second'],
        ];
        $data_metal = $data_vote = [];

        foreach ($metal_flags as $k => $v) {
            $winner_flags = 'td.' . implode(" like concat(tw.id, '%') or td.", $v) . " like concat(tw.id, '%')";

            $sql = sprintf(<<< SQL
SELECT tw.id as work_id, td.nomination_id, count(1) AS %s FROM tag_m_work tw
INNER JOIN tag_tagline_awards_voting_second_dropdown td
ON (%s)
WHERE tw.%s = 1
AND td.tagline_awards_id = %d
GROUP BY tw.id, td.nomination_id
SQL
            , $k, $winner_flags, $this->Awards->name_flag_tagline_awards, $this->Awards->id);
            $data_metal[$k] = Yii::app()->db->createCommand($sql)->queryAll();
        }

        foreach ($vote_flags as $k => $v) {
            $sql = sprintf(<<< SQL
SELECT tf.work_id, tf.nomination_id, sum(tf.star) AS %s, avg(tf.star) AS %s, count(1) AS %s FROM %s tf
WHERE tf.sec_on_page >= %d AND tf.tagline_awards_id = %d
GROUP BY tf.work_id, tf.nomination_id
SQL
            , $v[0], $v[1], $v[2], $v[3], $min_sec_vote, $this->Awards->id);
            $data_vote[$k] = Yii::app()->db->createCommand($sql)->queryAll();
        }

        $res = [];
        foreach ($data_vote['first'] as $item) {
            if (!key_exists($item["nomination_id"], $noms_for_sort)) {
                continue;
            }
            $res[$item["work_id"]][$item["nomination_id"]] = $item;
        }

        foreach ($data_vote['second'] as $item) {
            if (!key_exists($item["nomination_id"], $noms_for_sort)) {
                continue;
            }
            if (!isset($res[$item["work_id"]][$item["nomination_id"]])) {
                $res[$item["work_id"]][$item["nomination_id"]] = $item;
            }
            else {
                $res[$item["work_id"]][$item["nomination_id"]]["sum_2"] = $item["sum_2"];
                $res[$item["work_id"]][$item["nomination_id"]]["avg_2"] = $item["avg_2"];
                $res[$item["work_id"]][$item["nomination_id"]]["count_2"] = $item["count_2"];
            }
        }

        foreach ($data_metal as $metal => $data) {
            foreach ($data as $item) {
                if (!key_exists($item["nomination_id"], $noms_for_sort)) {
                    continue;
                }
                if (!isset($res[$item["work_id"]][$item["nomination_id"]])) {
                    $res[$item["work_id"]][$item["nomination_id"]] = $item;
                }
                else {
                    $res[$item["work_id"]][$item["nomination_id"]][$metal] = $item[$metal];
                }
            }
        }

        $PreShortService
            ->parseDataFromArray($res)
            ->sortForPrint()
            ->downloadCsv();
    }

    public function getNominations()
    {
        if ($this->OldHelper) {
            $noms = $this->OldHelper->getNominationsInOrder();
            return self::cleanNoms($noms);
        }

        return false;
    }

//    todo: перенести в более релевантный класс
    private static function cleanNoms($noms): array
    {
        $remove = [
            "&laquo;", "&raquo;",
        ];

        $spaces = [
           "&nbsp;",
        ];
        $names = array_values($noms);
        for ($i = 0; $i < count($names); $i++) {
            $names[$i] = str_replace($remove, '', $names[$i]);
            $names[$i] = str_replace($spaces, ' ', $names[$i]);
        }
        return array_combine(array_keys($noms), $names);
    }

    /**
     * @return TaglineAwards
     */
    public function getAwards(): TaglineAwards
    {
        return $this->Awards;
    }

    /**
     * @throws Exception
     */
    public function getAgencyMedalStatusCleared(): array
    {
        if (isset($this->OldHelper)) {
            return $this->OldHelper::getAgencyMedalStatusCleared($this->winners);
        }

        throw new \Exception(sprintf('Нет хелпера для Tagline Awards с id = %d', $this->Awards->id));
    }

//    todo: ++переделать в более общий
    public function getSpecialWinners(): array
    {
        $data_tmp = $winners_arr = [];
        if (isset($this->OldHelper)) {
            $data_tmp = $this->OldHelper::getSpecialWinners();
        }

        foreach ($data_tmp as $winner) {
            $Winner = new WinnerData();
            $Winner->setMasterId($winner['winner_master_id'])
                ->setNominationId($winner['nomination_id'])
                ->setMetal($winner['metal'])
                ->setYear($this->Awards->year)
                ->setWorkUrl($winner['url'])
                ->setNomById();


            if (isset($winner['company_name'])) {
                if (!isset($winner['work_name'])) {
                    $Winner->setWorkName($winner['company_name']);
                } else {
                    $Winner->setPerformerName($winner['company_name']);
                }
            }

            $winners_arr[] = $Winner->serialize();
        }

        return $winners_arr;
    }
}