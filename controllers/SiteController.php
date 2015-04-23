<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Heads;

/**
 * 
 * @package default
 */

class SiteController extends Controller
{

    public function actionTedivm()
    {

    $server = new \Fetch\Server('imap.gmail.com', 993);
    $server->setAuthentication('camilo@usetime.co', 'xxxxx');

    $messages = $server->getOrderedMessages(SORTDATE, 1, 100);
    $this->register($messages);
    exit();

        return $this->render("tedivm",["messages"=>$messages]);
    }


    public function register($messages)
    {

        /** @var $message \Fetch\Message */
        foreach ($messages as $message) {
        echo "Subject: {$message->getSubject()}"."<br>";

        $model = new Heads;

        $model->subject    = $message->getSubject();
        $model->from       = $message->from;
        $model->to         = $message->to;
        $model->date       = $message->getDate();
        $model->message_id = null;
        $model->size       = $message->size;
        $model->uid        = $message->getUid();
        $model->msgno      = $message->msgno;
        $model->recent     = $message->recent;
        $model->flagged    = $message->flagged;
        $model->answered   = $message->answered;
        $model->deleted    = $message->deleted;
        $model->seen       = $message->seen;
        $model->draft      = $message->draft;

        if ($model->insert()) {
        # code...
        echo "true";
        }else{
        
        echo "false";
        }

        }
        

    }


    public function actionPrecorreos()
    {
        $this->Imap();
        return $this->render("precorreos");
    }


    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }
}
