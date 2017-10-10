<?php
/**
 * Created by PhpStorm.
 * User: lucasweijers
 * Date: 19-05-17
 * Time: 17:04
 */

namespace plugins\dolphiq\form\controllers;

use Craft;
use craft\web\View;
use IntlCalendar;
use Locale;
use plugins\dolphiq\form\models\contactForm;
use plugins\dolphiq\form\models\logModel;
use ResourceBundle;
use Yii;
use yii\helpers\FileHelper;
use yii\mail\MessageInterface;

class MainController extends \craft\web\Controller
{

    /**
     * The path where the forms wil be. Each form has his own directory.
     * In this path there can also be a general thank you view file named thanks.php
     */
    CONST FORM_PATH = '@root/forms/';

    /**
     * The namespace that each form model must have
     */
    CONST FORM_NAMESPACE = 'app\forms\\';

    /**
     * The default name for the general thank you view file.
     * The general thank you file, also named thanks.php, will reside in de main forms directory (The FORM_PATH)
     */
    CONST FORM_THANX_VIEW = 'thanks.php';

    /**
     * This is the ending part of the form model. Each model must end with this filename.
     * For example: contactForm.php
     */
    CONST APPEND_FORM_PART = 'Form.php';

    /**
     * This is the ending part of the form view. Each form view must and with this filename.
     * For example: contactView.php
     */
    CONST APPEND_VIEW_PART = 'View.php';

    /**
     * The default name for the form specific thank you view file.
     * Form specific thank you files are named thanks.php and reside in the form directory that it applies to
     */
    CONST APPEND_THANX_PART = 'thanks.php';

    /**
     * This is the ending part of the form owner mail. Each owner mail must end with this filename. You can only have one owner mail per form.
     * For example: contactMailOwner.php
     */
    CONST APPEND_MAIL_OWNER_PART = 'MailOwner.php';

    /**
     * This is the ending part of the form customer mail. Each customer mail must end with this filename. You can only have one customer mail per form.
     * For example: contactMailCustomer.php
     */
    CONST APPEND_MAIL_CUSTOMER_PART = 'MailCustomer.php';


    /**
     * @inheritdoc
     */
    protected $allowAnonymous = true;


    /**
     * Each time this controller is called a few assets will be loaded.
     * Other assets that will be loaded like jquery are removed because we expect you to load them yourself on the site level instead from the plugin.
     * This way jQuery only gets loaded once instead of multiple times if you have multiple forms on your page.
     *
     * @inheritdoc
     */
    public function init()
    {
        Yii::$app->view->on(View::EVENT_AFTER_RENDER, function ($e) {
            $allowedAssets = [
                'yii\validators\ValidationAsset',
                'yii\widgets\ActiveFormAsset',
            ];

            foreach (array_keys($e->sender->assetBundles) as $bundle){
                if(!in_array($bundle,$allowedAssets)){
                    unset($e->sender->assetBundles[$bundle]);
                }
            }
        });
    }

    public function actionIndex($type=null, $params = [])
    {
        $this->layout = false;

        $this->getForms();

        // Get form classname
        $form = $this->getForm($type);

        // Create new form model
        $model = $form ? new $form['class']() : null;

        if($form && $model) {

            $view = $form['view'];
            $mail_owner = $form['mail_owner'];
            $mail_customer = $form['mail_customer'];

            $params = is_array($params) ? $params : json_decode($params, true);

            if ($model->load(Craft::$app->request->post()) && $model->validate()) {

                /** Validated succesfull. **/

                // Send mail and return thank you page
                if(!is_null($mail_owner) || !is_null($mail_customer)) {
                    $mailer = Yii::$app->mailer;
                    $mailer->htmlLayout = '@vendor/dolphiq/form/src/mail/layouts/html';

                    // Create owner mail
                    if(!is_null($mail_owner)) {
                        $ownerMail = $mailer->compose($mail_owner, ['model' => $model, 'params' => $params])
                            ->setSubject('New request')
                            ->setTo('lucas@dolphiq.nl')
                            ->setBcc('submit@dolphiq.nl');

                        // Save owner mail
                        $this->saveInDb($model, $ownerMail);

                        // Send owner mail
                        $ownerMail->send();
                    }else{
                        $this->saveInDb($model);
                    }

                    // Send customer mail
                    if(!is_null($mail_customer) && isset($model->mail) && !empty($model->mail)){
                        $mailer->compose($mail_customer, ['model' => $model, 'params' => $params])
                            ->setSubject('Thank you')
                            ->setTo($model->mail)
                            ->setBcc('submit@dolphiq.nl')
                            ->send();
                    }
                }else{
                    //No mail, just save in db
                    $this->saveInDb($model);
                }

                // Render thank you part
                if(!is_null($form['thanx'])){
                    return $this->renderAjax($form['thanx']);
                }else{
                    return $this->renderAjax(self::FORM_THANX_VIEW);
                }
            }

            return $this->renderAjax($view, ['model' => $model, 'params' => $params, 'type' => $type]);
        }

        return null;
    }

    /**
     * Log the form model in the database
     * @param $model
     * @param null|MessageInterface $mail
     */
    private function saveInDb($model, $mail = null){
        $log = new logModel();
        $log->form_data = json_encode($model->attributes);
        $log->server_data = json_encode($_SERVER);
        $log->html_mail = $mail;
        $log->save(false);
    }

    /**
     * Get the forms that reside in the defined form directory.
     * For each form it is also determined if there is a specific thank you file or if the default thank you file is to be used.
     * @return array
     */
    private function getForms(){
        $forms = [];

        // Get all files in the directory where users forms are
        $files = FileHelper::findFiles(Craft::getAlias(self::FORM_PATH), ['only' => ['*'.self::APPEND_FORM_PART]]);

        // Loop trough files and find form model files
        foreach ($files as $file){
            $pathInfo = pathinfo($file);
            $type = str_replace(self::APPEND_FORM_PART, "", $pathInfo['basename']);
            $class = self::FORM_NAMESPACE.$pathInfo['filename'];
            $formDir = self::FORM_PATH . str_replace(Craft::getAlias(self::FORM_PATH), "", $pathInfo['dirname']);

            $view           = $formDir . DIRECTORY_SEPARATOR . $type . self::APPEND_VIEW_PART;
            $mailOwner      = $formDir . DIRECTORY_SEPARATOR . $type . self::APPEND_MAIL_OWNER_PART;
            $mailCustomer   = $formDir . DIRECTORY_SEPARATOR . $type . self::APPEND_MAIL_CUSTOMER_PART;
            $thanx_custom   = $formDir . DIRECTORY_SEPARATOR . $type . self::APPEND_THANX_PART;
            $thanx_default  = self::FORM_PATH . self::FORM_THANX_VIEW;
            $thanx          = file_exists(Craft::getAlias($thanx_custom)) ? $thanx_custom : (file_exists(Craft::getAlias($thanx_default)) ? $thanx_default : null);

            // Check if class is autoloaded, if not then include class
            if(class_exists($class) === false){
                include_once $file;
            }

            if(class_exists($class) && file_exists(Craft::getAlias($view))) {
                $forms[$type] = [
                    'class' => $class,
                    'view' => $view,
                    'mail_owner' => file_exists(Craft::getAlias($mailOwner)) ? $mailOwner : null,
                    'mail_customer' => file_exists(Craft::getAlias($mailCustomer)) ? $mailCustomer : null,
                    'thanx' => $thanx,
                    'thanx_custom' => $thanx_custom,
                    'thanx_default' => $thanx_default,
                ];
            }
        }

        return $forms;
    }

    /**
     * Get one form from the many forms that reside in the form directory
     * @param $type
     * @return mixed|null
     */
    private function getForm($type){
        return $this->getForms()[$type] ?? null;
    }
}