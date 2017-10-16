# Form plugin for Craft CMS 3.x

Craft CMS 3 is build on top of the Yii 2 Framework.
This plugin makes it possible to use forms the way the Yii 2 Framework offers. This includes:
- Easy out of the box client and server side validation with use of rules in a model.
- Assign field labels in your model to use them in multiple areas.

Next to this Yii 2 Framework logic, we added:
- Easily enable/disable the form in the settings.
- Easily enable/disable logging form entries into the database in the settings.
- Control the recipient and subject of the contact requests e-mails in the plugin settings per form.
- Twig extensions, form examples and E-mail examples.

**Note**: This plugin may become a paid add-on when the Craft Plugin store becomes available.

## Requirements
* Craft 3.0 (beta 28)+
* PHP 7.0+

## Installation
1. Install with Composer
    
       composer require dolphiq/craf3-forms
       
2. Install plugin in the Craft Control Panel under Settings > Plugins

3. Add a new directory `forms` to the root directory of your craft project (next to your config and templates directory) or copy the forms directory from the examples folder in the plugin directory

## Directory structure
Below you will find an example directory structure for a contact and a vacancy form

As you can see each form has its own directory. The minimum files are the files in the contact directory. 
When using every functionallity of this plugin you have as much files as you see in the vacancy directory.

```html
forms/
    contact/
        contactForm.php
        contactView.php
    vacancy/
        vacancyForm.php
        vacancyView.php
        vacancyOwnerMail.php
        vacancyCustomerMail.php
        vacancyThanks.php
    thanks.php       
```

## Creating a new form
Lets say that we are going to create a contact form.

1. Create a directory with the name of your form inside the `forms` directory. This name will be your form handle later on.
In this case it will be named `contact`
2. Inside this directory create two files:
    * `contactForm.php` This file is a model that defines the fields and rules for the form. The name consists of the handle appended with `Form.php`.
    
       This file is a model as descriped on: <http://www.yiiframework.com/doc-2.0/guide-start-forms.html#creating-model>
       Make sure the class extends the Form model from the plugin and uses the `app\forms` namespace, if you don't then the plugin won't work.
      
      ##### Example contactForm.php
        ```php
        namespace app\forms;
                 
        use Craft;
        use plugins\dolphiq\form\models\Form;
        
        class contactForm extends Form {
        
         public $firstname = "";
         public $lastname = "";
         public $phone = "";
         public $email = "";
         public $message = "";
        
         public function rules()
         {
             return [
                 [['firstname', 'lastname', 'email', 'message'], 'required'],
                 ['email', 'email'],
                 ['phone', 'safe']
             ];
         }
        
         public function attributeLabels()
         {
             return [
                 'firstname' => Craft::t('site', 'Firstname'),
                 'lastname' => Craft::t('site', 'Lastname'),
                 'phone' => Craft::t('site', 'Phone'),
                 'email' => Craft::t('site', 'Email'),
                 'message' => Craft::t('site', 'Message'),
             ];
         }
        }
        ```
                         
    * `contactView.php` This file is a view with which you can display your form. The name consists of the handle appended with `View.php`.
    
       The file is a view file as descriped on: <http://www.yiiframework.com/doc-2.0/guide-start-forms.html#creating-views>
       It is important to leave the action of the activeform and the id of the pjax widget id unchanged, else your form won't work.
    
       ##### Example contactView.php
        ```php
        <?php
  
        /**
          * @var $model app\forms\contactForm
          * @var $handle string
          */
    
        use yii\widgets\ActiveForm;
        use yii\widgets\Pjax;
        
        // Start ajax handling of the form
        Pjax::begin(['enablePushState' => false, 'id' => 'pjax-'.$handle]);
  
        // Start active form
        $form = ActiveForm::begin([
            'action' => \craft\helpers\UrlHelper::actionUrl('dolphiq-craft3-forms/main/index', ['handle' => $handle]),
            'method' => 'POST',
            'options' => [
                'data-pjax' => true,
            ],
        ]);
  
        ?>
        
        <?= $form->field($model, 'firstname')->textInput(); ?>
        <?= $form->field($model, 'lastname')->textInput(); ?>
        <?= $form->field($model, 'phone')->textInput(); ?>
        <?= $form->field($model, 'email')->textInput(); ?>
        
        <?= $form->field($model, 'message')->textarea(); ?>
        
        <div>
          <button type="submit">
              <?= Craft::t('site', 'Send request'); ?>
          </button>
        </div>
        
        
        <?php 
          
          // End active form
          ActiveForm::end();
          
          // End ajax handling
          Pjax::end(); 
    
        ?>
        ```
        
3. In your template you can now use the form by using the tag the following tag. Instead of `contact` you fill in the handle of your form.

   ```twig
   {{ dolphiqForm('contact')|raw }}
   ```
   
## Thank you message
When the form is submitted and validated correctly it will be replaced with a thank you message.
There are two types of thank you messages:

1. **Default thank you message.**

    The plugin comes with a default thank you message. You can overwrite this message by creating a `thanks.php` file in the `forms/` directory.
    This will then be your default thank you message that is used for all your forms. You can use the form model here to personalize the thank you message.
    ##### Example of a default thank you message
    
    ```html
    <p>Thank you! We will contact you soon.</p>
    ```
2. **A custom, per form, thank you message**

    You can create a thank you message per form so it won't use the default thank you message. 
    This is extra usefull if you want to personalize the thank you message by using the form model.
    
    ##### Example of a customer, per form, thank you message
    ```php
   <?php
   /**
     * @var $model \app\forms\contactForm
     */
   ?>
   
   <p>Thank you <?= $model->firstname ?>, we will contact you soon</p>
    ```
           
## Mails
You can enable the plugin to send an email to the form owner and to the person who filled in the form.
To do this you can create two files:

1. `contactMailOwner.php` This file contains the email that will be send to the owner of the form.
    
    You can use the `$model` variable to get attributes from the filled in form.
    
    ##### Example
    
    ```php
    <?php
    
    use yii\widgets\DetailView;
    
    /* @var $this \yii\web\View view component instance */
    /* @var $message \yii\mail\BaseMessage instance of newly created mail message */
    /* @var $model \app\forms\contactForm */
    
    ?>
    <h2>A contact request has been filled in</h2>
    <p>
      We received the following details:<br>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'firstname',
                'lastname',
                'email',
                'phone',
                'message:ntext'
            ]
        ]); ?>
    </p>
    ```
    
2. `contactMailCustomer.php` This file contains the email that will be send to the person who filled in the form (if there is an `email` attribute available en it is filled in).

    You can use the `$model` variable to get attributes from the filled in form. This way you can personalize the email.
    
    ```php
    <?php
        
    /* @var $this \yii\web\View view component instance */
    /* @var $message \yii\mail\BaseMessage instance of newly created mail message */
    /* @var $model app\forms\contactForm */
    
    ?>
    <h2>Thank you for your message <?= $model->lastname; ?></h2>
    <p>
        We will contact you as soon as possible
    </p>
    ```

The two mails will be wrapped with a mail layout that is defined in the plugin folder. You can not change this.
           
## CP Settings
In the Controlpanel you can set the settings per form.
You can set the following options per form:
* **Enabled** _When disabled the form will not be shown_
* **Logging** _When enabled the results from the form will be logged into the database_
* **Owner Mail** _This is the email that will be send when the form has been filled in. You can fill in the following options:_
    * Mail to adress _The emailadress to which the email will be send_
    * Mail subject _The subject of the email that will be send to above emailaddress_
* **Customer Mail** _This is the email that will be send to the person who filled in the form. 
  This only works when the form contains the `email` attribute so the person that fills in the form can fill in his emailadress.
  You can fill in the following option:_
    * Mail subject _The subject of the email that will be send to the customers emailaddress._


### Contributors & Developers
Lucas Weijers - info@dolphiq.nl
Brought to you by [Dolphiq](https://dolphiq.nl)