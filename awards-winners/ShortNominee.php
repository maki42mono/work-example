<?php

class ShortNominee
{
    private $nomination        = null;
    private $work_name         = null;
    private $rez_ball          = null;
    private $nom_winners_count = null;
    private $work_id           = null;
    private $nomination_id     = null;
    private $company           = null;
    private $master_id         = null;
    private $client_name       = null;
    private $sum_1             = null;
    private $avg_1             = null;
    private $count_1           = null;
    private $sum_2             = null;
    private $avg_2             = null;
    private $count_2           = null;
    private $gold              = null;
    private $silver            = null;
    private $bronze            = null;
    private $is_winner         = null;
    private $work_url          = null;


    public function __construct(array $params = [])
    {
        if (empty($params)) {
            return;
        }

        $class_name = self::class;
        foreach ($params as $k => $v) {
            if (!property_exists($class_name, $k)) {
                throw new \Exception('Нет такого свойства у шорта');
            }

            $this->$k = $v;
        }
    }

    public static function getNullObj(): self
    {
        $class_name = self::class;
        $empty = new $class_name();
        $attrs = get_object_vars($empty);

        foreach ($attrs as $k => $v) {
            $empty->$k = null;
        }

        return $empty;
    }

    public function toArray(): array
    {
        $attrs = get_object_vars($this);
        $res = [];
        foreach ($attrs as $k => $v) {
            $res[$k] = $v;
        }

        return $res;
    }

    /**
     * @return null|int
     */
    public function getRezBall()
    {
        return $this->rez_ball;
    }

    /**
     * @return null|int
     */
    public function getNominationId()
    {
        return $this->nomination_id;
    }

    public function calcRezBall()
    {
        $this->rez_ball = round(
            $this->gold * 50 + $this->silver * 20 + $this->bronze * 10 + $this->sum_2 + $this->sum_1 / 2,
            1);
    }

    public static function getCsvHeader(TaglineAwards $Awards): array
    {
        $class_name = self::class;
        $empty = new $class_name();
        $attrs = get_object_vars($empty);
        $labels = self::getLabels($Awards);

        $res = [];
        foreach ($attrs as $k => $v) {
            $res[$k] = $labels[$k];
        }

        return $res;
    }

    private static function getLabels(TaglineAwards $Awards): array
    {
        return [
            'work_id'           => 'workId',
            'nomination_id'     => 'nomId',
            'nomination'        => 'Номинация',
            'company'           => 'Компания',
            'master_id'         => 'masterId',
            'work_name'         => 'Название работы',
            'client_name'       => 'Название клиента',
            'sum_1'             => 'Балл I',
            'avg_1'             => 'Среднее I',
            'count_1'           => 'Голосов I',
            'sum_2'             => 'Балл II',
            'avg_2'             => 'Среднее II',
            'count_2'           => 'Голосов II',
            'gold'              => 'Кол-во золота',
            'silver'            => 'Кол-во серебра',
            'bronze'            => 'Кол-во бронзы',
            'is_winner'         => 'Победитель ' . $Awards->year,
            'work_url'          => 'url',
            'rez_ball'          => 'ИТОГ балл',
            'nom_winners_count' => 'Победителей в номинации',
        ];
    }
}