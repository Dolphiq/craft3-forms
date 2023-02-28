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
use plugins\dolphiq\form\models\Form;
use plugins\dolphiq\form\models\Log;
use plugins\dolphiq\form\models\Settings;
use plugins\dolphiq\form\Plugin;
use Yii;
use yii\helpers\FileHelper;
use yii\mail\MessageInterface;

class MainController extends \craft\web\Controller
{

    /**
     * The default name for the general thank you view file.
     * The general thank you file, also named thanks.php, will reside in de main forms directory (The FORM_PATH)
     */
    CONST FORM_THANX_VIEW = 'thanks';


    /**
     * The default name for the general thank you view file.
     * The general thank you file, also named thanks.php, will reside in de main forms directory (The FORM_PATH)
     */
    CONST FORM_MAIL_LAYOUT = '@vendor/dolphiq/craft3-forms/src/mail/layouts/html';


    /**
     * @inheritdoc
     */
    protected $allowAnonymous = self::ALLOW_ANONYMOUS_LIVE;

    /**
     * The loaded forms
     * @var
     */
    protected $forms;


    /**
     * Remove the assets the controller wants to load, because we will load the assets trought the twigextention.
     * This way assets like jQuery only gets loaded once instead of multiple times if you have multiple forms on your page.
     *
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        $this->allowAnonymous = (is_bool($this->allowAnonymous) ? (int) $this->allowAnonymous : $this->allowAnonymous);
        Yii::$app->view->on(View::EVENT_AFTER_RENDER, function ($e) {
            $e->sender->assetBundles = [];
        });
    }

    public function actionIndex($handle=null, $params = [])
    {
        $this->layout = false;

        // Get form classname
        $form = $this->getForm($handle);

        if($form && $form->getSettings()->enabled) {

            $view = $form->getView();
            $mail_owner = $form->getMailOwner();
            $mail_customer = $form->getMailCustomer();

            $params = is_array($params) ? $params : json_decode($params, true);

            if ($form->load(Craft::$app->request->post()) && $form->validate()) {

                /** Validated succesfull. **/

                // Send mail and return thank you page
                if(!is_null($mail_owner) || !is_null($mail_customer)) {
                    $mailer = Yii::$app->mailer;
                    $mailer->htmlLayout = self::FORM_MAIL_LAYOUT;

                    // Create owner mail
                    if(!is_null($mail_owner)) {
                        $ownerMail = $mailer->compose($mail_owner, ['model' => $form, 'params' => $params])
                            ->setSubject($form->getSettings()->mail_subject_owner)
                            ->setTo($form->getSettings()->mail_to);

                        // Save owner mail
                        $this->saveInDb($form, $ownerMail);

                        // Send owner mail
                        $ownerMail->send();
                    }else{
                        $this->saveInDb($form);
                    }

                    // Send customer mail
                    if(!is_null($mail_customer) && isset($form->email) && !empty($form->email)){
                        $mailer->compose($mail_customer, ['model' => $form, 'params' => $params])
                            ->setSubject($form->getSettings()->mail_subject_customer)
                            ->setTo($form->email)
                            ->send();
                    }

                }else{
                    //No mail, just save in db
                    $this->saveInDb($form);
                }

                // Render thank you part
                if(!is_null($form['thanx'])){
                    return $this->renderAjax($form['thanx'], ['model' => $form, 'params' => $params, 'handle' => $handle]);
                }else{
                    return $this->renderAjax(self::FORM_THANX_VIEW, ['model' => $form, 'params' => $params, 'handle' => $handle] );
                }
            }

            return $this->renderAjax($view, ['model' => $form, 'params' => $params, 'handle' => $handle]);
        }

        return null;
    }

    /**
     * Log the form model in the database
     * @param Form $form
     * @param null|MessageInterface $mail
     */
    private function saveInDb($form, $mail = null){

        if($form->getSettings()->enabled_logging == true) {
            $log = new Log();
            $log->form_data = json_encode($form->attributes);
            $log->server_data = json_encode($_SERVER);
            $log->html_mail = $mail;
            $log->save(false);
        }
    }

    /**
     * Get the forms that are loaded
     * @return array
     */
    public function getForms(){
        if(empty($this->forms)){
            $this->forms = $this->loadForms();
        }

        return $this->forms;
    }

    /**
     * Load the forms that reside in the defined form directory.
     * For each form it is also determined if there is a specific thank you file or if the default thank you file is to be used.
     * @return array|Form[]
     */
    private function loadForms(){
        $forms = [];
        $settings = $this->getSettings();


        if (!file_exists(Craft::getAlias($settings->form_path))) {
          return $forms;
        }

        // Get all files in the directory where users forms are
        $files = FileHelper::findFiles(Craft::getAlias($settings->form_path), ['only' => ['*'.$settings->append_form_part]]);

        // Loop trough files and find form model files
        foreach ($files as $file){
            $pathInfo = pathinfo($file);
            $type = str_replace($settings->append_form_part, "", $pathInfo['basename']);
            $class = $settings->form_namespace.$pathInfo['filename'];

            // Check if class is autoloaded, if not then include class
            if(class_exists($class) === false){
                include_once $file;
            }

            if(class_exists($class)) {

                $model = new $class();

                if($model->getView()) {
                    $forms[$type] = $model;
                }
            }
        }

        return $forms;
    }

    /**
     * Get one form from the many forms that reside in the form directory
     * @param $handle
     * @return Form|null
     */
    private function getForm($handle){
        return $this->getForms()[$handle] ?? null;
    }

    /**
     * @return Settings
     */
    private function getSettings(){
        return Plugin::getInstance()->getSettings();
    }
}
