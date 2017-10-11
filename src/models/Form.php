<?php
/**
 * Created by PhpStorm.
 * User: lucasweijers
 * Date: 11-10-17
 * Time: 10:04
 */

namespace plugins\dolphiq\form\models;

use Craft;
use craft\base\Model;
use plugins\dolphiq\form\Plugin;

/**
 * Class Form
 * This class defines a form. All forms that you create have to extend this class.
 * @package plugins\dolphiq\form\models
 */
class Form extends Model{

    /**
     * This is the ending part of the form view. Each form view must and with this filename.
     * For example: contactView.php
     */
    CONST APPEND_VIEW_PART = 'View.php';

    /**
     * The default name for the form specific thank you view file.
     * Form specific thank you files are named thanks.php and reside in the form directory that it applies to
     */
    CONST APPEND_THANKS_PART = 'thanks.php';

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
     * The name of this form
     * @return string
     */
    public function getName(){
        return ucfirst($this->getHandle());
    }

    /**
     * The view path, if it exists
     * @return null|string
     */
    public function getView(){
        // The view path
        $path = $this->getFormDir() . DIRECTORY_SEPARATOR . $this->getHandle() . self::APPEND_VIEW_PART;

        // Return the path if it exists. Important, the form only works if you have a view file
        return file_exists(Craft::getAlias($path)) ? $path : null;
    }

    /**
     * The path to the owner mail, if it exists
     * @return null|string
     */
    public function getMailOwner(){
        // The Owner Mail path
        $path = $this->getFormDir() . DIRECTORY_SEPARATOR . $this->getHandle() . self::APPEND_MAIL_OWNER_PART;

        // Return the path if it exists.
        return file_exists(Craft::getAlias($path)) ? $path : null;
    }

    /**
     * The path to the customer mail, if it exists
     * @return null|string
     */
    public function getMailCustomer(){
        // The Customer Mail path
        $path = $this->getFormDir() . DIRECTORY_SEPARATOR . $this->getHandle() . self::APPEND_MAIL_CUSTOMER_PART;

        // Return the path if it exists.
        return file_exists(Craft::getAlias($path)) ? $path : null;
    }

    /**
     * The path to a custom thank you message, if it exists
     * @return null|string
     */
    public function getThanxCustom(){
        // The path to the thanx view of this form
        $path = $this->getFormDir() . DIRECTORY_SEPARATOR . $this->getHandle() . self::APPEND_THANKS_PART;

        // Return the path if it exists.
        return file_exists(Craft::getAlias($path)) ? $path : null;
    }

    /**
     * The path to the thank you message that is going to be used
     * This will return the custom thank you message if it exists or the general thank you message if it exists.
     * If neither of those exists than this will return null
     * @return null|string
     */
    public function getThanx(){
        return $this->getThanxCustom() ?? $this->getPluginSettings()->getThanxView() ?? null;
    }

    /**
     * The aliased directory where the form lives
     * @return string
     */
    private function getFormDir(){
        return $this->getPluginSettings()->form_path . $this->getHandle();
    }

    /**
     * The handle of the form
     * It is generated based on the directory name
     * @return mixed
     */
    public function getHandle(){

        $rc = new \ReflectionClass($this);
        $filename = dirname($rc->getFileName());

        return str_replace(Craft::getAlias($this->getPluginSettings()->form_path), "", $filename);
    }

    /**
     * The form settings model, loaded with the settings set in the CP if those exist
     * @return null|FormSettings
     */
    public function getSettings(){
        $pluginSettings = $this->getPluginSettings();
        $formSettings = new FormSettings();


        if(!empty($pluginSettings) && isset($pluginSettings['forms'][$this->getHandle()])){
            $formSettings->load($pluginSettings['forms'], $this->getHandle());
            return $formSettings;
        }

        return $formSettings;
    }

    /**
     * The settings of the plugin, which can be set from the CP
     * @return Settings
     */
    private function getPluginSettings(){
        return Plugin::getInstance()->getSettings();
    }
}