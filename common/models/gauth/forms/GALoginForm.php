<?php
/**
 * Created by PhpStorm.
 * User: noelwalsh
 * Date: 04/12/2014
 * Time: 09:13
 */

namespace common\models\gauth\forms ;


//namespace amnah\yii2\user\models\forms;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class GALoginForm extends \amnah\yii2\user\models\forms\LoginForm {


    /**
     * CHECK LOGIN DETAILS
     * ===================
     * This validates the username and password without actually logging the
     * in. If the details are valid it grabs the user and returns the user id,
     * which allows it to be used for a call to Google Authentication.
     *
     * @param $loginDuration
     *
     * @return bool|string
     */
    public function checkLoginDetails($loginDuration)
    {
        \yii::info(__METHOD__);

        if ($this->validate()) {
            $user = $this->getUser();
            return $user->uuid ;
        }
        return false ;
    }



    /**
     * Get user based on email and/or username
     * First checks Active Directory using LDAP to see if they are an Exertis Employee
     *  - if so, check if we have a local user
     *      - if not, create one
     *  - if not, check our current users, they're probably a customer
     *      - if not a known user/customer then login fail
     *
     * @return \amnah\yii2\user\models\User|null
     */
    public function getUser()
    {
        \yii::info(__METHOD__);

        $LDAPLookupSuccess = false;

        // check if we need to get user
        if ($this->_user === false) {
            $this->username = trim($this->username);
            $this->password = trim($this->password);

            // RCH 20150818
            // First try to lookup these credentials using the Exertis Active Directory server
            // but only if the user is connecting from within Exertis and LDAP lookup is enabled
            /*
            if (\Yii::$app->params['GALoginForm.useLDAP'] && preg_match('/^(172\.)|(127\.)/', Yii::$app->getRequest()->getUserIP())==1) {
                \yii::info(__METHOD__.': attempting LDAP Authentication');
                $LDAPLookupSuccess = \Yii::$app->ldap->authenticate($this->username,$this->password);
            }
             * 
             */
            //var_dump ($authUser);


            // build query based on email and/or username login properties
            $user = Yii::$app->getModule("user")->model("User");
            $user = $user::find();
            if (Yii::$app->getModule("user")->loginEmail && !$LDAPLookupSuccess) {
                \yii::info(__METHOD__.' Check against email');
                $user->orWhere(["email" => $this->username]);
            }
            if (Yii::$app->getModule("user")->loginUsername || ($LDAPLookupSuccess && Yii::$app->getModule("user")->loginEmail)) {
                \yii::info(__METHOD__.' Check against username');
                $user->orWhere(["username" => $this->username]);
            }

            // get and store user
            $this->_user = $user->one();

            // RCH 20150817
            // If LDAP lookup was successful, then store this password locally too
            if (!empty($this->_user) && $LDAPLookupSuccess) {

                \yii::info(__METHOD__.' Store password locally');

                $this->_user->setScenario('ldap'); // relax password strength
                $this->_user->newPassword = $this->password;
                $this->_user->newPasswordConfirm = $this->password;
                if (!$this->_user->save()) {
                    Yii::error(__METHOD__.': Failed to save password from LDAP: '. print_r($this->_user->getErrors(),true));
                }
            }
        }

        //\yii::info(__METHOD__.': '.print_r($this->_user->attributes,true));

        // return stored user
        return $this->_user;
    }

}
