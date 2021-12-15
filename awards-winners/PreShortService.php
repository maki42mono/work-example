<?php

use League\Csv\Writer;

class PreShortService
{
    /**
     * @var ShortNominee[]
     */
    private $ShortCollection = [];
    private $ShortCollection4Print = [];

    /**
     * @var HelperAwardsWinner
     */
    private $HelperAwardsWinner;
    private $nominations;
    private $winners_in_nom;
    private $noms_for_sort;
    private $is_cached;

    public function __construct(HelperAwardsWinner $HelperAwardsWinner, bool $is_cached = true)
    {
        $this->HelperAwardsWinner = $HelperAwardsWinner;
        $this->nominations = $this->HelperAwardsWinner->getNominations();
        $this->noms_for_sort = array_flip(array_keys($this->nominations));
        $this->is_cached = $is_cached;

        $tmp_cached_name = 'shortParseDataFromArray_' . $this->HelperAwardsWinner->getAwards()->id;
        $this->winners_in_nom = Yii::app()->cache->get($tmp_cached_name);
        if (!($this->winners_in_nom && $this->is_cached)) {
            $sql = sprintf(<<< SQL
SELECT COUNT(1) as _count, tw.nomination_id AS nomination_id FROM tag_tagline_awards_winners tw
WHERE tw.tagline_awards_id = %d
AND tw.flag_is_archive = 0
GROUP BY nomination_id  
ORDER BY COUNT(1)  DESC
SQL
                , $this->HelperAwardsWinner->getAwards()->id
            );

            $winners_in_nom = Yii::app()->db->createCommand($sql)->queryAll();
            $noms_id = array_column($winners_in_nom, 'nomination_id');
            $count = array_column($winners_in_nom, '_count');
            $this->winners_in_nom = array_combine($noms_id, $count);
            Yii::app()->cache->set($tmp_cached_name, $this->winners_in_nom, 60 * 60 * 24);
        }
    }

    public function addShort(ShortNominee $Short)
    {
        $this->ShortCollection[] = $Short;
    }

    /**
     * @return ShortNominee[]
     */
    public function getShortCollection(): array
    {
        return $this->ShortCollection;
    }

    public function parseDataFromArray(array $res): self
    {
        $nominations = $this->nominations;
        $tmp_cached_name = 'shortParseDataFromArray_' . $this->HelperAwardsWinner->getAwards()->id;
        $res_short_obj = Yii::app()->cache->get($tmp_cached_name);
        $tagline_awards_id = $this->HelperAwardsWinner->getAwards()->id;
        if (!($res_short_obj && $this->is_cached)) {
            $res_short_obj = [];

            foreach ($res as $res_key => $res_value) {
                foreach ($res_value as $value_k => $value_v) {
                    $WorkBackup = MWorkBackup::model()->findByAttributes([
                        'work_id'           => $value_v["work_id"],
                        'tagline_awards_id' => $tagline_awards_id,
                        'backup_type'       => 3,
                        'flag_backup_is_archive' => null,
                    ]);
                    if (empty($WorkBackup)) {
                        continue;
                    }
                    $Winner = TaglineAwardsWinners::model()->findByAttributes([
                        'case_id' => $res_key,
                        'tagline_awards_id' => $tagline_awards_id,
                        'nomination_id' => $value_k,
                        'flag_is_archive' => 0,
                    ]);
                    $tmp["work_id"] = $res_key;
                    $tmp["nomination_id"] = $value_k;
                    $tmp["nomination"] = $nominations[$value_k];
                    $tmp["company"] = $WorkBackup->performer_name;
                    $tmp['master_id'] = $WorkBackup->company_master_id;
                    $tmp['work_name'] = $WorkBackup->work_name_moderated ?? $WorkBackup->work_name;
                    $tmp['client_name'] = $WorkBackup->client_moderated ?? $WorkBackup->client;
                    $tmp['sum_1'] = $value_v['sum_1'] ?? 0;
                    $tmp['avg_1'] = round($value_v['avg_1'], 1) ?? 0;
                    $tmp['count_1'] = $value_v['count_1'] ?? 0;
                    $tmp['sum_2'] = $value_v['sum_2'] ?? 0;
                    $tmp['avg_2'] = round($value_v['avg_2'], 1) ?? 0;
                    $tmp['count_2'] = $value_v['count_2'] ?? 0;
                    $tmp['gold'] = $value_v['gold'] ?? 0;
                    $tmp['silver'] = $value_v['silver'] ?? 0;
                    $tmp['bronze'] = $value_v['bronze'] ?? 0;
                    $tmp['nom_winners_count'] = $this->winners_in_nom[$value_k] ?? 0;
                    $tmp['is_winner'] = (isset($Winner) && $Winner != [] && $Winner != null) ? 1 : 0;
                    $work_url = null;
                    if (isset($WorkBackup) && $WorkBackup != null) {
                        $work_url =  'tagline.ru/' . $WorkBackup->getWorkUrl();
                    }
                    $tmp['work_url'] = $work_url;

                    $Short = new ShortNominee($tmp);
                    $Short->calcRezBall();

                    $res_short_obj[] = $Short;
                }
            }
            Yii::app()->cache->set($tmp_cached_name, $res_short_obj, 24 * 60 * 60);
        }

        $this->ShortCollection = $res_short_obj;
        return $this;
    }

    public function sortForPrint(): self
    {
        $that = $this;

        $sort_noms = function ($a, $b) use ($that) {
            if ($that->noms_for_sort[$a->getNominationId()] == $that->noms_for_sort[$b->getNominationId()]) {
                return ($a->getRezBall() > $b->getRezBall()) ? -1 : 1;
            }

            return ($that->noms_for_sort[$a->getNominationId()] < $that->noms_for_sort[$b->getNominationId()]) ? -1 : 1;
        };

        usort($this->ShortCollection, $sort_noms);


        $res_short_obj = $this->ShortCollection;
        $res_csv[] = $res_short_obj[0]->toArray();
        $short_empty = ShortNominee::getNullObj();
        for ($i = 1; $i < count($res_short_obj); $i++) {
            if ($res_short_obj[$i]->getNominationId() !== $res_short_obj[$i - 1]->getNominationId()) {
                $res_csv[] = $short_empty->toArray();
            }

            $res_csv[] = $res_short_obj[$i]->toArray();
        }

        $this->ShortCollection4Print = $res_csv;

        return $this;

    }

    /**
     * @return array
     */
    public function getNomsForSort()
    {
        return $this->noms_for_sort;
    }

    /**
     * @return array
     */
    public function getShortCollection4Print(): array
    {
        return $this->ShortCollection4Print;
    }

    public function downloadCsv()
    {
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $Awards = $this->HelperAwardsWinner->getAwards();
        $csv->insertOne(ShortNominee::getCsvHeader($Awards));
        $csv->insertAll($this->ShortCollection4Print);

        $csv->output("{$Awards->name_merchant_direction} shortlist ".date('Y-m-d').'.csv');
    }
}