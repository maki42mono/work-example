<?php

class VoteCandidate
{
    /**
     * @var int
     */
    private $work_id;
    /**
     * @var int
     */
    private $nom_id;

    /**
     * @var int
     */
    private $weight;

    /**
     * @var int
     */
    private $view_count;

    /**
     * @var string
     */
    private $type;

    const MAX_WEIGHT    = 10000;
    const MIDDLE_WEIGHT = 3000;
    const MIN_WEIGHT    = 1;

    const TYPE_MIN     = 'TYPE_MIN';
    const TYPE_CHANGED = 'TYPE_CHANGED';
    const TYPE_ALL     = 'TYPE_ALL';


    public function __construct(int $work_id, int $nom_id, int $view_count = 0, int $weight = self::MAX_WEIGHT)
    {
        $this->work_id = $work_id;
        $this->nom_id = $nom_id;
        $this->weight = $weight;
        $this->view_count = $view_count;
    }

    /**
     * @return int
     */
    public function getWorkId(): int
    {
        return $this->work_id;
    }

    /**
     * @param int $work_id
     */
    public function setWorkId(int $work_id)
    {
        $this->work_id = $work_id;
    }

    /**
     * @return int
     */
    public function getNomId(): int
    {
        return $this->nom_id;
    }

    /**
     * @param int $nom_id
     */
    public function setNomId(int $nom_id)
    {
        $this->nom_id = $nom_id;
    }

    public function __toString(): string
    {
        return sprintf('work_id: %d, nom_id: %d', $this->work_id, $this->nom_id);
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight(int $weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return int
     */
    public function getViewCount(): int
    {
        return $this->view_count;
    }

    /**
     * @param int $view_count
     */
    public function setViewCount(int $view_count)
    {
        $this->view_count = $view_count;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function getAsArray(): array
    {
        return [
            'work_id'    => $this->work_id,
            'nom_id'     => $this->nom_id,
            'view_count' => $this->view_count,
        ];
    }
}