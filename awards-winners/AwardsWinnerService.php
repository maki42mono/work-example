<?php

class AwardsWinnerService
{
    private $AwardsWinnersCollection = [];
    private $worksAwards             = [];

    public function __construct()
    {
        $this->setAllAwards();
    }


    public function addAwardsWinner(HelperAwardsWinner $Winner): self
    {
        array_push($this->AwardsWinnersCollection, $Winner);

        return $this;
    }

    private function setAllAwards()
    {
        $this->AwardsWinnersCollection = [];
        $Awards = TaglineAwards::model()->findAll();
        foreach ($Awards as $Award) {
            $this->AwardsWinnersCollection[] = new HelperAwardsWinner($Award);
        }
    }

    public function getWinnersCollection(): array
    {
        return $this->AwardsWinnersCollection;
    }

    private function setAwardsByWorkId(int $workId)
    {
        $winners = [];
        foreach ($this->AwardsWinnersCollection as $WinnerHelper) {
            $winnersArr = $WinnerHelper->getAwardsByWorkId($workId);
            if (empty($winnersArr)) {
                continue;
            }

            $winners[$WinnerHelper->getTaglineAwards()->id] = $winnersArr;
        }

        $this->worksAwards[$workId] = $winners;
    }

    public function getCaseCardByWorkId(int $workId): array
    {
        if (empty($this->worksAwards[$workId])) {
            $this->setAwardsByWorkId($workId);
        }

        $res = [];

        $winners = $this->worksAwards[$workId];

        foreach ($winners as $k => $v) {
            $TAwards = TaglineAwards::getById($k);
            $casePosition = "case_position_{$TAwards->getYear4CaseCard()}";

            $metals = TaglineAwardsWinners::getMetalsEng();
            foreach ($metals as $metal) {

                $metalWinners = array_filter($v, function ($var) use ($metal) {
                    return $var['metal'] == $metal;
                });

                if (!empty($metalWinners)) {
                    foreach ($metalWinners as $w) {
                        $res[$casePosition][$metal][] = $w['nomination_name'];
                    }
                }
            }
        }

        return $res;
    }
}