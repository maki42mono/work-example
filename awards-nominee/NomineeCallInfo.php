<?php

final class NomineeCallInfo extends HelperUser
{
    private $all_years_paid_sum = 0;
    private $proceeds;
    private $contacts;
    private $flags              = [];

    const FLAGS_LIST = [
        'flag_a2a_client',
        'flag_blacklist',
        'flag_blacklist_events',
        'blacklist_events_history',
        'blacklist_history',
        'flag_archive',
    ];

    const COLUMNS = [
        'name'                     => 'Номинант',
        'masterId'                 => 'MasterId',
        'proceeds'                 => 'Выручка, тыр',
        'all_years_paid_sum'       => 'Заплатил за все премии, Р',
        'flag_a2a_client'          => 'А2А-клиент?',
        'flag_archive'             => 'Неактивный аккаунт',
        'flag_blacklist'           => 'Блеклист?',
        'blacklist_history'        => 'История занесения в блеклист',
        'flag_blacklist_events'    => 'Блеклист ивентов?',
        'blacklist_events_history' => 'История занесения в блеклист ивентов',
        'contacts'                 => 'Контакты',
    ];

    public function __construct($master_id)
    {
        parent::__construct($master_id);

        $this->setAwardsStats();
        foreach ($this->getAwardsStats() as $stat) {
            $this->all_years_paid_sum += $stat['sum'];
        }
        $model = $this->getModel();
        $this->proceeds = $model->getProceeds();
        $this->contacts = $model->getContacts();
        foreach (self::FLAGS_LIST as $flag) {
            $val = $model->$flag ?? '—';
            $this->flags[$flag] = $val;
        }
    }

    /**
     * @return int
     */
    public function getAllYearsPaidSum(): int
    {
        return $this->all_years_paid_sum;
    }

    public static function getCachedModel(int $master_id, bool $cached = true): self
    {
        $cached_name = sprintf('cached_NomineeCallInfo_%s', $master_id);
        $cached_model = Yii::app()->cache->get($cached_name);
        if ($cached && $cached_model) {
            return $cached_model;
        }

        $cached_model = new self($master_id);
        Yii::app()->cache->set($cached_name, $cached_model, 24 * 60 * 60);

        return $cached_model;
    }

    public function toArray(): array
    {
        return array_merge(
            [
                'name'               => $this->getName(),
                'masterId'           => $this->getMasterId(),
                'proceeds'           => $this->proceeds,
                'all_years_paid_sum' => $this->all_years_paid_sum,
                'contacts'           => $this->contacts,
            ],
            call_user_func(function () {
                $res = [];
                $stats = $this->getAwardsStats();
                $awards_ids = array_reverse(array_keys($stats));
                foreach ($awards_ids as $award_id) {
                    $stat = $stats[$award_id];
                    foreach ($stat as $k => $v) {
                        if ('awards_name' === $k) {
                            continue;
                        }
                        $res[$award_id . '_' . $k] = $v;
                    }
                }
                return $res;
            }),
            call_user_func(function () {
                $res = [];
                foreach ($this->flags as $k => $v) {
                    $res[$k] = $v;
                }
                return $res;
            })
        );
    }

    public static function getColumns(): array
    {
        $Awards = TaglineAwards::model()->findAll(['order' => 'id desc']);

        $stats_columns = [];

        /** @var TaglineAwards $Award */
        foreach ($Awards as $Award) {
            //            $stats_columns[$Award->id.'_awards_name'] = $Award->name_merchant_direction;
            $stats_columns[$Award->id . '_null'] = $Award->name_merchant_direction;
            $stats_columns[$Award->id . '_sum'] = 'Оплачено Р';
            $stats_columns[$Award->id . '_debt'] = 'Дебиторка';
            $stats_columns[$Award->id . '_items_applied'] = 'Подано работ';
            $stats_columns[$Award->id . '_orders_paid'] = 'Счетов оплачено';
            $stats_columns[$Award->id . '_items_paid'] = 'Подач оплачено';
            $stats_columns[$Award->id . '_izhd'] = 'Иждивенец?';
            $stats_columns[$Award->id . '_works_changed'] = 'Менял работ';
        }

        return array_merge(self::COLUMNS, $stats_columns);
    }
}