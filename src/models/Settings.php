<?php
/**
 * Created by PhpStorm.
 * User: lucasweijers
 * Date: 11-10-17
 * Time: 08:44
 */


namespace plugins\dolphiq\form\models;

use Craft;
use craft\base\Model;

class Settings extends Model
{
    /**
     * The path where the forms wil be. Each form has his own directory.
     * In this path there can also be a general thank you view file named thanks.php
     */
    public $form_path = '@root/forms/';

    /**
     * The namespace that each form model must have
     */
    public $form_namespace = 'app\forms\\';

    /**
     * The default name for the general thank you view file.
     * The general thank you file, also named thanks.php, will reside in de main forms directory (The FORM_PATH)
     */
    public $form_thanks_view = 'thanks.php';

    /**
     * This is the ending part of the form model. Each model must end with this filename.
     * For example: contactForm.php
     */
    public $append_form_part = 'Form.php';

    /**
     * The form settings that are saved.
     * These settings will be loaded in the FormSettings.php model
     * @var array
     */
    public $forms = [];

    /**
     * The validation rules
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Returns the general thank you file, if it exists
     * @return null|string
     */
    public function getThanxView(){
        // The thanx path
        $thanx = $this->form_path . $this->form_thanks_view;

        // Return the path if it exists
        return file_exists(Craft::getAlias($thanx)) ? $thanx : null;
    }


}