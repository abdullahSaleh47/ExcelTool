<?php

namespace app\modules\api\controllers;

use app\models\EUser;
use yii\web\Controller;


class UserController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        echo "Test test";
        return $this->render('index');
    }
    public function actionCreateUser()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $user = new EUser();
        $cache = \Yii::$app->cache;
        $user->scenario = EUser::SCENARIO_CREATE;
        $user->attributes = \Yii::$app->request->post();
        if ($user->validate()) {
            $user->save();
            $id = $user->ID;
            $key = 'User' . $id;
            $cache->set($key, json_encode($user->attributes));
            return array('status' => true, 'data' => 'user Created Successfully!');
        } else {
            return array('status' => false, 'data' => $user->getErrors());
        }
    }
    public function actionFindUser($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $cache = \Yii::$app->cache;
        $key = 'User' . $id;
        $cashedUser = $cache->get($key);
        if ($cashedUser != '{}' && $cashedUser != false) {
            $user = json_decode($cashedUser, true);
        } else {
            $user = EUser::findOne($id);
            if ($user != null) {
                $cache->set($key, json_encode($user->attributes));
            }
        }
        return $user;
        if ($user != null) {
            return array('status' => true, 'data' => $user);
        } else {
            return array('status' => false, 'data' => 'No Users Found!');
        }

    }
    public function actionUpdateUser($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $user = EUser::findOne($id);
        if ($user === null) {
            $cache = \Yii::$app->cache;
            $key = 'User' . $id;
            $cache->delete($key);
            return [
                'status' => false,
                'data' => 'User not found!',
            ];
        }
        $user->scenario = EUser::SCENARIO_UPDATE;
        $user->updateAttributes(\Yii::$app->request->post());
        if ($user->validate()) {

            if ($user->save()) {
                $cache = \Yii::$app->cache;
                $key = 'User' . $id;
                $cache->set($key, json_encode($user->attributes));
                return ['status' => true, 'data' => 'User Updated successfully!'];
            } else {
                return ['status' => false, 'data' => 'Failed to update user!'];
            }
        } else {
            return ['status' => false, 'data' => $user->getErrors()];
        }

    }
    public function actionDeleteUser($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $user = EUser::findOne($id);
        $cache = \Yii::$app->cache;
        $key = 'User' . $id;
        if ($user === null) {
            return ['status' => false,'data' => 'User not found!'];
        }
        $user->attributes = \Yii::$app->request->post();
        if ($user->validate()) {
            $user->delete();
            $cache->delete($key);
            return ['status' => true, 'data' => 'User deleted successfully!'];
        } else {
            return ['status' => false, 'data' => 'Failed to delete user!'];
        }

    }
    public function actionSearchUser($firstName)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $users = EUser::find()->where(['First_Name' => $firstName])->all();
        if ($users != null) {
            return array('status' => true, 'data' => $users);
        } else {
            return array('status' => false, 'data' => 'No Users Found!');
        }

    }
    public function actionRedisTest()
    {
        $redis=\Yii::$app->redis;
        $reData=$redis->get("User15021");
        $cache = \Yii::$app->cache;
        $key = 'new';
        $data = $cache->get($key);
        if ($data === false) {
            $key = 'new';
            $data = 'A newly cache added';
            $cache->set($key, $data);
        }
        var_dump($reData);
    }
}
