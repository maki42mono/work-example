<?php

final class NomineePay extends Nominee
{
    /**
     * @var int
     */
    private $sum;
    /**
     * @var int
     */
    private $sum_yandex = 0;
    /**
     * @var int
     */
    private $sum_beznal = 0;

    /**
     * @var int
     */
    private $items             = 0;
    /**
     * @var int
     */
    private $works             = 0;
    /**
     * @var int
     */
    private $distinct_nominations = 0;


    /**
     * @return int
     */
    public function getSum(): int
    {
        return $this->sum;
    }

    /**
     * @param int $sum
     *
     * @return NomineePay
     */
    public function setSum(int $sum): self
    {
        $this->sum = $sum;
        return $this;
    }

    public function getStatsStr(): string
    {
        $payed = [];
        if ($this->sum_yandex > 0) {
            $payed['yandex'] = sprintf(
                '%s Р ' . MerchantOrder::TYPE_YANDEX,
                number_format($this->sum_yandex, 0, '', ' ')
            );
        }
        if ($this->sum_beznal > 0) {
            $payed['beznal'] = sprintf(
                '%s Р ' . mb_strtolower(MerchantOrder::TYPE_BEZNAL),
                number_format($this->sum_beznal, 0, '', ' ')
            );
        }

        $payed_str = (empty($payed)) ? '' : implode($payed, ', ');
        $sum = number_format($this->sum, 0, '', ' ') . ' Р';
        if (count($payed) > 1) {
            $payed_str = " ($payed_str)";
        } else {
            $sum = $payed_str;
            $payed_str = '';
        }
        return sprintf(
            "— <a href='%s'>%s</a> → %d | %d | %d | <b>%s</b>%s<br>",
            'https://reg.tagline.ru/ctrl/company/view?master_id=' . $this->master_id,
            $this->name,
            $this->works,
            $this->distinct_nominations,
            $this->items,
            $sum,
            $payed_str
        );
    }

    /**
     * @return int
     */
    public function getSumYandex(): int
    {
        return $this->sum_yandex;
    }

    /**
     * @param int $sum_yandex
     *
     * @return NomineePay
     */
    public function setSumYandex(int $sum_yandex): self
    {
        $this->sum_yandex = $sum_yandex;
        return $this;
    }

    /**
     * @return int
     */
    public function getSumBeznal(): int
    {
        return $this->sum_beznal;
    }

    /**
     * @param int $sum_beznal
     *
     * @return NomineePay
     */
    public function setSumBeznal(int $sum_beznal): self
    {
        $this->sum_beznal = $sum_beznal;
        return $this;
    }

    /**
     * @param int $items
     *
     * @return NomineePay
     */
    public function setItems(int $items): self
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @param int $works
     *
     * @return NomineePay
     */
    public function setWorks(int $works): self
    {
        $this->works = $works;
        return $this;
    }

    /**
     * @param int $distinct_nominations
     *
     * @return NomineePay
     */
    public function setDistinctNominations(int $distinct_nominations): self
    {
        $this->distinct_nominations = $distinct_nominations;
        return $this;
    }
}