<?php

class AwardsSectionController extends Controller
{
    public $fileRoot;
    public $year;
    public $nominees;
    public $metals_count = [
        'gold' => 0,
        'silver' => 0,
        'bronze' => 0,
    ];
    public $nomination;
    public $nomination_1st_short;
    public $nomination_1st_url;
    public $parent_nomination;
    public $nominations_1st;
    public $nominations_2nd;
    public $subnominations;
    public $nomination_short;
    public $nom_prev_url;
    public $nom_prev_short;
    public $nom_next_url;
    public $nom_next_short;
    public $nom_rand_url;
    public $nom_title_short;
//    public $nom_main_short = null;

    const WORK_COVER_IMG_ROOT = 'https://reg.tagline.ru/file/upload/awards-cover/';
    const YEARS = [
//      '2016', '2017', '2018', '2019', '2020-2021',
//            пока не выложили предыдущие страницы — нужно так
        '2020-2021',
    ];

//    кэширование страниц
    public function filters()
    {
        $year = Yii::app()->request->getQuery('year');
        $nomination = Yii::app()->request->getQuery('nomination');
        $rand = Yii::app()->request->getQuery('rand');
        return [
            [
                'COutputCache + view',
                'duration'=>YII_DEBUG ? 0 : 60*60*24 ,
                'varyByParam'=>[
                    $year,
                    $nomination,
                    $rand,
                ],
            ],
        ];
    }


    public function getViewPath()
    {
        return Yii::getPathOfAlias('application') . '/views/awards/section';
    }

    public function beforeAction($action)
    {
        $this->layout = 'awards';
        $this->fileRoot = Yii::app()->baseUrl . '/file/awards/section';

        return parent::beforeAction($action);
    }

    public function actionViewNoYear(string $nomination) {
        $years = self::YEARS;
        $year = array_pop($years);
        $url = $this->createUrl('view', [
            'year' => $year,
            'nomination' => $nomination,
        ]);
        $this->redirect($url);
    }

//  todo: добавить кеширование страниц
//  todo: сделать комманду, которая по крону ночью будет открывать все страницы, а значит кешировать
    public function actionView(string $year, string $nomination)
    {
        $Api = new API(API::AWARDS_SECTION_CACHE_NAME);
        $data = $Api->getAwardsSectionsData();
        $redirect = $data['nominations']['translit_redirect'][$nomination];
        if ($redirect) {
            $this->redirect('view', [
                'year' => $year,
                'nomination' => $redirect,
            ]);
        }

        $nominees = $data['nominees'][$year];
        $nominations = $data['nominations'][$year];
        $nomination_names = $nominations['names'];
        $nominations_1st = $nominations['first'];
        $nominations_2nd = $nominations['second'];
        $nominations_kv = $nominations['key_value'];
        $noms_list = $data['nominations_list'][$year]['kv'];
        $noms_list_reverse = $data['nominations_list'][$year]['reverse'];
        if (key_exists($nomination, $nominees) && key_exists($nominations_kv[$nomination], $nominations_2nd)) {
            $this->subnominations = $nominations_2nd[$nominations_kv[$nomination]];
            $nomination_short = $nomination_names[$nomination];

            $nom_order = $noms_list_reverse[$nomination];
            $nom_prev_url = $nom_next_url = false;

            $getSubNomShort = function (string $translit) use ($nominations_kv, $nomination_names): string {
                $nom = $nomination_names[$translit];
                if ($nominations_kv[$translit] !== $translit) {
                    $nom = sprintf('%s → %s', $nomination_names[$nominations_kv[$translit]], $nom);
                }

                return $nom;
            };

            if (isset($noms_list[$nom_order - 1])) {
                $nom_prev_url = $noms_list[$nom_order - 1];
                $this->nom_prev_short = $getSubNomShort($nom_prev_url);
            }

            if (isset($noms_list[$nom_order + 1])) {
                $nom_next_url = $noms_list[$nom_order + 1];
                $this->nom_next_short = $getSubNomShort($nom_next_url);
            }

//            берем рандомную номинацию из вообще всех 140+
            $other_noms = $noms_list_reverse;
            unset($other_noms[$nomination]);
            unset($other_noms[$nom_prev_url]);
            unset($other_noms[$nom_next_url]);
            $tmp_keys = array_keys($other_noms);
            $session = new CHttpSession;
            $session->open();
            $isFromRand = Yii::app()->request->getQuery('rand');
            if (!isset($_SESSION['noms_rand_index_sections'])) {
                $_SESSION['noms_rand_index_sections'] = [];
            }
            do {
                $rand_nom_key = rand(0, count($tmp_keys) - 1);
            } while ($isFromRand && in_array($rand_nom_key, $_SESSION['noms_rand_index_sections']));
            if ($isFromRand) {
                if (count($_SESSION['noms_rand_index_sections']) == ($nominations['count'] - 10)) {
                    $_SESSION['noms_rand_index_sections'] = [];
                } else {
                    $_SESSION['noms_rand_index_sections'][] = $rand_nom_key;
                }
            }


            $rand_nom = $tmp_keys[$rand_nom_key];

            $this->year = $year;
            $this->nominees = $nominees[$nomination];
            $this->nomination = $nomination;
            $this->parent_nomination = $nominations_kv[$nomination];

            foreach ($this->nominees as $nominee) {
                $this->metals_count[$nominee['metal']]++;
            }

            $this->nominations_1st = $nominations_1st;
            foreach ($nominations_1st as $key => $value) {
                if ($value['translit'] == $this->parent_nomination) {
                    $this->nomination_1st_short = $value['short_name'];
                    $this->nomination_1st_url = $this->createUrl('view', [
                        'year' => $year,
                        'nomination' => $value['translit'],
                    ]);
                    unset($this->nominations_1st[$key]);
                    break;
                }
            }
            $this->nominations_2nd = $nominations_2nd;
            $this->nomination_short = $nomination_short;
            $this->nom_prev_url = ($nom_prev_url) ?
                $this->createUrl('view', [
                'year' => $year,
                'nomination' => $nom_prev_url,
            ]) : null;

            $this->nom_next_url = ($nom_next_url) ?
                $this->createUrl('view', [
                    'year' => $year,
                    'nomination' => $nom_next_url,
                ]): null;

            $this->nom_rand_url = $this->createUrl('view', [
                'year' => $year,
                'nomination' => $rand_nom,
            ]) . '?rand=1';


            $this->nom_title_short = $this->nomination_short;
            if ($this->nomination_1st_short !== $this->nom_title_short) {
                $this->nom_title_short = sprintf('%s → %s', $this->nomination_1st_short, $this->nom_title_short);
            }

            $this->pageTitle = "Победители Tagline Awards 2020–2021 в номинации {$this->nom_title_short} — крупнейшием digital-конкурсе в Европе";

            $this->render('view');

        } else {
            throw new CHttpException(404,'В такой номинации нет победителей');
        }
    }

    public function getWorkCoverImgRoot(): string
    {
        return self::WORK_COVER_IMG_ROOT;
    }

	// Uncomment the following methods and override them if needed
	/*
	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}