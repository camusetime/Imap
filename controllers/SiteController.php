<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Heads;
use \PhpImap;

/**
 * 
 * @package default
 */

class SiteController extends Controller
{

    public function actionTedivm()
    {

    $server = new \Fetch\Server('imap.gmail.com', 993);
    $server->setAuthentication('camilo@usetime.co', 'xxxx');

    // aqui van los parametros que para los correos el ultimo parametro es la cantidad de correos que descargada de imap
    //$messages = $server->getOrderedMessages(SORTDATE, 1, 100);
    $messages = $server->getMessages();
    //$this->register($messages);
    //exit();

        return $this->render("tedivm",["messages"=>$messages]);
    }


    public function register($messages)
    {
        /** @var $message \Fetch\Message */
        foreach ($messages as $message) {

        $result=$message->getOverview();    

        $model = new Heads;

        $model->subject    = $result->subject;
        $model->from       = $result->from;
        $model->to         = $result->to;
        $model->date       = $result->date;
        $model->message_id = $result->message_id;
        $model->size       = $result->size;
        $model->uid        = $result->uid;
        $model->msgno      = $result->msgno;
        $model->recent     = $result->recent;
        $model->flagged    = $result->flagged;
        $model->answered   = $result->answered;
        $model->deleted    = $result->deleted;
        $model->seen       = $result->seen;
        $model->draft      = $result->draft;

            if ($model->save()) {
            # code...
            //echo "true";
            }else{ 
            print_r($model->getErrors());       
            //echo "false";
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

    public function actionPhpimap()
    {
        //$imap = new PhpImap\Mailbox;
        $mailbox = new PhpImap\Mailbox('{imap.gmail.com:993/imap/ssl}INBOX', 'camilo@usetime.co', 'developerusetime', __DIR__);
        $mails = array();

        $mailsIds = $mailbox->searchMailBox('ALL');
        $mailsIds = array_reverse($mailsIds);
        if(!$mailsIds) {
            die('Mailbox is empty');
        }

        echo "<pre>";
        var_dump($mailbox->getImapStream());
        echo "</pre>";
        exit();

        //$mailId = end($mailsIds);
        //$mail = $mailbox->sortMails(SORTDATE);
        $mail = $mailbox->getMailsInfo($mailsIds);


        return $this->render('phpimap',["mail"=>$mail]);
    }
}
