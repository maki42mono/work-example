<?php

final class NomineeEditStats extends Nominee
{
    protected $case_edits;
    protected $case_count;
    protected $case_avg;
    protected $case_median = 0;
    /**
     * @var NomineeEditService
     */
    protected $nomineeEditService;

    public function __construct(NomineeEditService $nomineeEditService, int $master_id)
    {
        $this->nomineeEditService = $nomineeEditService;
        $this->master_id = $master_id;
        $this->calculate();
    }


    /**
     * @return int
     */
    public function getCaseEdits(): int
    {
        return $this->case_edits;
    }

    /**
     * @param int $case_edits
     *
     * @return NomineeEditStats
     */
    public function setCaseEdits(int $case_edits): NomineeEditStats
    {
        $this->case_edits = $case_edits;
        return $this;
    }

    /**
     * @return int
     */
    public function getCaseCount(): int
    {
        return $this->case_count;
    }

    /**
     * @param int $case_count
     *
     * @return NomineeEditStats
     */
    public function setCaseCount(int $case_count): NomineeEditStats
    {
        $this->case_count = $case_count;
        return $this;
    }

    protected function calculate(): self
    {
        $edits = $this->nomineeEditService->getEditCount()[$this->master_id];
        $this->case_count = count($this->nomineeEditService->getWorkCount()[$this->master_id]);
        $this->case_edits = count($edits) > 0 ? array_sum($edits) : 0;
        $this->case_avg = round($this->case_edits / $this->case_count, 1);
        $edits[] = 2;
        sort($edits);
        $middle = (count($edits) % 2 == 0) ? [count($edits) / 2, count($edits) / 2 - 1] : [(count($edits) - 1) / 2];
        foreach ($middle as $v) {
            $this->case_median += $edits[$v];
        }

        $this->case_median = round($this->case_median / count($middle));

        return $this;
    }

    public function toArray(): array
    {
        return [
            'Номинант'            => $this->name,
            'MasterId'            => $this->master_id,
            'Кейсов вообще'       => $this->case_count,
            'Изменений кейсов'    => $this->case_edits,
            'Редактируют сред.'   => $this->case_avg,
            'Редактируют медиана' => $this->case_median,
        ];
    }
}