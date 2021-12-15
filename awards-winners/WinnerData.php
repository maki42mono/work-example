<?php

class WinnerData
{
    private $work_name;
    private $uploaded_cover;
    private $performer_name;
    private $client;
    private $year;
    private $work_url;
    private $coauthor;
    private $project_full_url;
    private $project_pretty_url;
    private $master_id;
    private $nomination_id;
    private $metal;
    private $nom_translit;

    /**
     * @return mixed
     */
    public function getWorkName()
    {
        return $this->work_name;
    }

    /**
     * @param mixed $work_name
     */
    public function setWorkName($work_name): WinnerData
    {
        $this->work_name = $work_name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUploadedCover()
    {
        return $this->uploaded_cover;
    }

    /**
     * @param mixed $uploaded_cover
     */
    public function setUploadedCover($uploaded_cover): WinnerData
    {
        $this->uploaded_cover = $uploaded_cover;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPerformerName()
    {
        return $this->performer_name;
    }

    /**
     * @param mixed $performer_name
     */
    public function setPerformerName($performer_name): WinnerData
    {
        $this->performer_name = $performer_name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $client
     */
    public function setClient($client): WinnerData
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     */
    public function setYear($year): WinnerData
    {
        $this->year = $year;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWorkUrl()
    {
        return $this->work_url;
    }

    /**
     * @param mixed $work_url
     */
    public function setWorkUrl($work_url): WinnerData
    {
        $this->work_url = $work_url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCoauthor()
    {
        return $this->coauthor;
    }

    /**
     * @param mixed $coauthor
     */
    public function setCoauthor($coauthor): WinnerData
    {
        $this->coauthor = $coauthor;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProjectFullUrl()
    {
        return $this->project_full_url;
    }

    /**
     * @param mixed $project_full_url
     */
    public function setProjectFullUrl($project_full_url): WinnerData
    {
        $this->project_full_url = $project_full_url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProjectPrettyUrl()
    {
        return $this->project_pretty_url;
    }

    /**
     * @param mixed $project_pretty_url
     */
    public function setProjectPrettyUrl($project_pretty_url): WinnerData
    {
        $this->project_pretty_url = $project_pretty_url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMasterId()
    {
        return $this->master_id;
    }

    /**
     * @param mixed $master_id
     */
    public function setMasterId($master_id): WinnerData
    {
        $this->master_id = $master_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNominationId()
    {
        return $this->nomination_id;
    }

    /**
     * @param mixed $nomination_id
     */
    public function setNominationId($nomination_id): WinnerData
    {
        $this->nomination_id = $nomination_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMetal()
    {
        return $this->metal;
    }

    /**
     * @param mixed $metal
     */
    public function setMetal($metal): WinnerData
    {
        $this->metal = $metal;
        return $this;
    }

    public function serialize(): array
    {
        return get_object_vars($this);
    }

    /**
     * @return mixed
     */
    public function getNomTranslit()
    {
        return $this->nom_translit;
    }

    /**
     * @param mixed $nom_translit
     */
    public function setNomTranslit($nom_translit): WinnerData
    {
        $this->nom_translit = $nom_translit;
        return $this;
    }

    public function setNomById(): self
    {
        $HS = HelperSingleton::getInstance();
        $key = 'awards_nominations';
        $nom_translits = $HS->getValue($key);
        if ($nom_translits == null) {
            $sql = <<< SQL
select tn.id as id, tn.translit as translit from tag_m_work_nomination tn
where tn.flag_archive = 0
SQL;
            $noms = Yii::app()->db->createCommand($sql)->queryAll();
            $nom_ids = array_column($noms, 'id');
            $nom_trans = array_column($noms, 'translit');
            $nom_translits = array_combine($nom_ids, $nom_trans);
            $HS->setValue($key, $nom_translits);
        }

        $this->nom_translit = $nom_translits[$this->nomination_id];


        return $this;
    }
}