<?php

class VoteCandidateService
{
    const TO_VOTE                    = 'VoteCandidateService_TO_VOTE';
    const MINIMUM_VOTE_CACHE_NAME    = 'VoteCandidateService_MINIMUM_VOTE_CACHE_NAME';
    const ALL_CANDIDATES_CACHED_NAME = 'VoteCandidateService_ALL_CANDIDATES_CACHED_NAME';
    const CACHED_TIME                = 60 * 30; /* 30 мин */
    const ALL_CANDIDATES_CACHED_TIME = 60 * 50 * 24; /* 1 день */

    /**
     * @var VoteCandidate[]
     */
    private $VoteCandidates;
    private $candidates_hashes = [];
    /**
     * @var int
     */
    private $voter_master_id;

    public function __construct(int $voter_master_id = null)
    {
        $this->voter_master_id = $voter_master_id;
    }

    /**
     * @return int
     */
    public function getVoterMasterId(): int
    {
        return $this->voter_master_id;
    }

    /**
     * @param int $voter_master_id
     */
    public function setVoterMasterId(int $voter_master_id)
    {
        $this->voter_master_id = $voter_master_id;
    }

    /**
     * @return VoteCandidate[]
     */
    public function getVoteCandidates(): array
    {
        return $this->VoteCandidates;
    }

    /**
     * @param VoteCandidate[] $VoteCandidates
     */
    public function setVoteCandidates(array $VoteCandidates)
    {
        $this->VoteCandidates = $VoteCandidates;
    }


    /**
     * @param VoteCandidate[] $Candidates
     *
     * @return $this
     */
    public function addCandidatesArray(array $Candidates): self
    {
        $res = array_merge($this->VoteCandidates, $Candidates);
        $this->VoteCandidates = array_unique($res);
        return $this;
    }

    public function addCandidate(VoteCandidate $Candidate): self
    {
        $hash = (string)$Candidate;
        if (!key_exists($hash, $this->candidates_hashes)) {
            $this->candidates_hashes[$hash] = count($this->candidates_hashes);
            $this->VoteCandidates[] = $Candidate;
        }

        return $this;
    }

    public function buildVoteCandidates(): self
    {
        $to_vote_name = self::TO_VOTE . $this->voter_master_id;
        $works_2_vote = Yii::app()->cache->get($to_vote_name);
        if ($works_2_vote) {
            $this->VoteCandidates = $works_2_vote;
            return $this;
        }
        $this->VoteCandidates = [];
        $has_minimum_voted = Yii::app()->cache->get(self::MINIMUM_VOTE_CACHE_NAME);
        if (!$has_minimum_voted) {
            $raw_data = TaglineAwardsVotingFirst::getMinVotedWorks();
            foreach ($raw_data as $row) {
                $Candidate = new VoteCandidate($row['work_id'], $row['nomination_id'], $row['view_count']);
                $Candidate->setType(VoteCandidate::TYPE_MIN);
                $this->addCandidate($Candidate);
            }
            Yii::app()->cache->set(self::MINIMUM_VOTE_CACHE_NAME, $this->VoteCandidates, self::CACHED_TIME);
        }

        $changed_works = TaglineAwardsVotingFirst::getCorrectedWorks($this->voter_master_id);
        foreach ($changed_works as $row) {
            $Candidate = new VoteCandidate(
                $row['work_id'], $row['nomination_id'], $row['view_count'],
                VoteCandidate::MIDDLE_WEIGHT
            );
            $Candidate->setType(VoteCandidate::TYPE_CHANGED);
            $this->addCandidate($Candidate);
        }

        $all_candidates = Yii::app()->cache->get(self::ALL_CANDIDATES_CACHED_NAME);
        if (!$all_candidates) {
            $raw_data = TaglineAwardsVotingFirst::getAllCandidates();
            foreach ($raw_data as $row) {
                $Candidate = new VoteCandidate(
                    $row['work_id'], $row['nomination_id'], $row['view_count'],
                    VoteCandidate::MIN_WEIGHT
                );
                $Candidate->setType(VoteCandidate::TYPE_ALL);
                $this->addCandidate($Candidate);
            }
            Yii::app()->cache->set(
                self::ALL_CANDIDATES_CACHED_NAME, $this->VoteCandidates, self::ALL_CANDIDATES_CACHED_TIME
            );
        }

        Yii::app()->cache->set($to_vote_name, $this->VoteCandidates, self::CACHED_TIME);
        return $this;
    }

    public function getRandCandidate(): VoteCandidate
    {
        $weights = $this->getWightsArr();
        $count = count($this->VoteCandidates);
        $i = 0;
        $n = 0;
        $num = mt_rand(1, array_sum($weights));
        while ($i < $count) {
            $n += $weights[$i];
            if ($n >= $num) {
                break;
            }
            $i++;
        }
        return $this->VoteCandidates[$i];
    }

    public function removeCandidate(VoteCandidate $VoteCandidate): self
    {
        $Candidates = $this->VoteCandidates;
        /*for ($i = 0; $i < count($Candidates); $i++) {
            if ($VoteCandidate === $Candidates[$i]) {
                break;
            }
        }*/

        $hash = (string)$VoteCandidate;

        if (!key_exists($hash, $this->candidates_hashes)) {
            throw new \Exception(sprintf('Нет номинанта для голосования %s', $VoteCandidate));
        }
        unset($Candidates[$this->candidates_hashes[$hash]]);
        $this->VoteCandidates = array_values($Candidates);
        return $this;
    }

    private function getWightsArr(): array
    {
        $res = [];
        foreach ($this->VoteCandidates as $Candidate) {
            $res[] = $Candidate->getWeight();
        }

        return $res;
    }

    /**
     * @return array
     */
    public function getCandidatesHashes(): array
    {
        return $this->candidates_hashes;
    }

    /**
     * @param array $candidates_hashes
     */
    public function setCandidatesHashes(array $candidates_hashes)
    {
        $this->candidates_hashes = $candidates_hashes;
    }
}