<?php

class NomineeEditService
{
    private $work_count = [];
    private $edit_count = [];

    /**
     * @var NomineeEditStats[]
     */
    private $Nominees = [];

    public static function fromSqlArray(array $data): self
    {
        $NomineeEditService = new NomineeEditService();
        foreach ($data as $row) {
            $master_id = $row['master_id'];
            $work_id = $row['work_id'];
            $changed = $row['row'];
            if (!key_exists($master_id, $NomineeEditService->work_count)
                || !key_exists($work_id, $NomineeEditService->work_count[$master_id])) {
                $NomineeEditService->work_count[$master_id][$work_id] = 1;
            }

            if (isset($changed) && null != $changed) {
                $NomineeEditService->edit_count[$master_id][$changed]++;
            }
        }

        return $NomineeEditService;
    }

    /**
     * @return array
     */
    public function getWorkCount(): array
    {
        return $this->work_count;
    }

    /**
     * @return array
     */
    public function getEditCount(): array
    {
        return $this->edit_count;
    }

    /**
     * @param NomineeEditStats[] $Nominees
     *
     * @return $this
     */
    public function addNominees(array $Nominees): self
    {
        $this->Nominees = array_merge($this->Nominees, $Nominees);
        return $this;
    }

    /**
     * @return NomineeEditStats[]
     */
    public function getNominees(): array
    {
        return $this->Nominees;
    }

    public function getNomineesArray(): array
    {
        $res = [];
        foreach ($this->Nominees as $nominee) {
            $res[] = $nominee->toArray();
        }
        return $res;
    }

    public static function getSqlRawData(AwardsNominee $Nominee, array $and_where = []): array
    {
        $fields_observe = "'" .  implode("','", MWork::ATTRIBUTES_OBSERVE) . "'";
        $nominee_table_name = $Nominee->getTableName();
        $sql = sprintf(<<< SQL
SELECT tmas.master_id AS
master_id, tw.id AS work_id, tl.row
FROM %s tnom
INNER JOIN tag_master_id tmas ON tmas.target_id = tnom.id
INNER JOIN tag_m_work tw ON tw.company_master_id = tmas.master_id
LEFT JOIN tag_log_change tl on tw.id = tl.row AND
tl.field IN (%s)
WHERE tmas.target_table = '%s' AND
(tw.flag_tagline_awards = %d OR tw.created >= '%s')
SQL
            ,$nominee_table_name, $fields_observe, $nominee_table_name, 1, TaglineAwards::getCurAwards()->getPrevCeremonyDate());
        foreach ($and_where as $condition) {
            $sql .= sprintf(' AND %s', $condition);
        }
        $data = Yii::app()->db->createCommand($sql)
            ->queryAll();
        //        этот говяный фреймфорк преобразует {{m_work}} в tag_m_work в запросе — из-за чего результат запроса — неверный!!!
        return array_filter($data, function ($v) {
            return in_array($v['table'], [null, '{{m_work}}']);
        });
    }
}