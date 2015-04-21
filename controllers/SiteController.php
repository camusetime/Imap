<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{


    public function actionPrecorreos()
    {
        $this->Imap();
        return $this->render("precorreos");
    }

    public function Imap()
    {
       $hostname = '{imap.gmail.com:993/ssl}';

        $username = 'camilo@usetime.co';
        $password = 'developerapp';
         
        $inbox = imap_open($hostname,$username,$password) or die('Ha fallado la conexión: ' . imap_last_error());

        $emails = imap_search($inbox,'ALL');

        /* Si obtenemos los emails, accedemos uno a uno... */


        if($emails) {



            /* variable de salida */

            $output = '';



            /* Colocamos los nuevos emails arriba */

            rsort($emails);


            /* por cada email... */

            $i=0;

            foreach($emails as $email_number) {

                //var_dump($email_number);exit();

                /* Obtenemos la información específica para este email */

                $overview = imap_fetch_overview($inbox,$email_number,0);

                /*echo "<pre>";
                echo imap_utf8($overview[0]->subject);
                echo "</pre>";
                exit();*/

                $message = imap_fetchbody($inbox,$email_number,2);

                //print_r($overview[0]->subject);

                //exit();
                
                if(empty($overview[0]->subject)){

                    $overview[0]->subject = "(sin asunto)";
                }

                /* Mostramos la información de la cabecera del email */

                

                //$output.= $overview[0]->seen.'<br>';

                $output.= "subject --> ".imap_utf8($overview[0]->subject).'<br>';

                $output.= "from --> ".$overview[0]->from.'<br><br>';

                //$output.= $overview[0]->date.'<br>';



                /* Mostramos el mensaje del email */

                //$output.= $i.$message.'<br>';

                

                

                $i++;

                //exit();

            }



            echo $output.'<br>';

        } 



        /* Cerramos la connexión */

        imap_close($inbox);

        //exit();
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
