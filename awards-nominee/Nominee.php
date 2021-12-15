<?php

abstract class Nominee
{
    protected $name;
    protected $master_id;
    /**
     * @var string
     */
    private $link;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Nominee
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getMasterId(): int
    {
        return $this->master_id;
    }

    /**
     * @param int $master_id
     *
     * @return Nominee
     */
    public function setMasterId(int $master_id): self
    {
        $this->master_id = $master_id;
        return $this;
    }

    public static function getNomineesMasterIds(): array
    {
        $SQL['has_cases']['master_id_column'] = 'company_master_id';
        $SQL['has_cases']['sql'] = <<< SQL
SELECT DISTINCT(company_master_id) AS master_id  FROM tag_m_work tw
WHERE 1
SQL;
        $SQL['paid']['master_id_column'] = 'master_id';
        $SQL['paid']['sql'] = <<< SQL
SELECT DISTINCT(tm.master_id) AS master_id FROM tag_merchant_order tm
WHERE tm.nomenclature LIKE 'ПТ%'
SQL;

        $SQL['backups']['master_id_column'] = 'company_master_id';
        $SQL['backups']['sql'] = <<< SQL
SELECT DISTINCT(company_master_id) AS master_id  FROM tag_m_work_backup tw
WHERE 1
SQL;
        $nominees = [];

        foreach ($SQL as $key => $value) {

            $value['sql'] .= sprintf(<<< SQL

AND %s NOT IN (
    SELECT DISTINCT(tmas.master_id) FROM tag_m_company tcom
    INNER JOIN tag_master_id tmas
    ON tmas.target_id = tcom.id
    WHERE tmas.target_table = 'tag_m_company'
    AND (
        tcom.flag_ban = 1
        OR tcom.flag_blacklist = 1
    )
)
AND %s NOT IN (
    SELECT DISTINCT(tmas.master_id) FROM tag_user_awards tu
    INNER JOIN tag_master_id tmas
    ON tmas.target_id = tu.id
    WHERE tmas.target_table = 'tag_user_awards'
    AND tu.flag_archive = 1
)
SQL
            , $value['master_id_column'], $value['master_id_column']);
            $nominees = array_merge($nominees,
                Yii::app()->db->createCommand($value['sql'])
                ->queryColumn()
            );
        }

        return array_unique($nominees);
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     *
     * @return Nominee
     */
    public function setLink(string $link): self
    {
        $this->link = $link;
        return $this;
    }
}