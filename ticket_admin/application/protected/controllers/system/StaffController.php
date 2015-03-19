<?php

Yii::import('ext.phpPasswordHashingLib.passwordLib', true);

class StaffController extends Controller {

    public function actionIndex() {
        $criteria = new CDbCriteria();
        $criteria->order = 'status DESC,id DESC';
        $lists = Users::model()->findAll($criteria);
        $this->render('index', compact('lists'));
    }

    public function actionAccountexist() {
        $model = Users::model()->findByAttributes(array('account' => $_POST['account']));
        if ($model) {
            $this->_end(0, '该账号已经存在');
        }
        $this->_end(1, '该账号不存在');
    }

    //新增加员工
    public function actionAdd() {
        $this->render('add');
    }

    //编辑员工信息
    public function actionEdit($id) {
        header("Content-type: text/html; charset=utf-8");
        $user = Users::model()->findByPk($id);
        $this->render('edit', compact('user'));
    }

    /*
     * updated_by chencq
     * 保存用户信息
     */
    public function actionSaveStaff() {
        if (Yii::app()->request->isPostRequest) {
            if(isset($_POST['id']) && !empty($_POST['id'])){
                $id = $_POST['id'];
                $user = Users::model()->findByPk($id);
                $user->name = $_POST['name'];
                $user->mobile = $_POST['mobile'];
                $user->account = $_POST['account'];
                $user->status = isset($_POST['status']) ? $_POST['status'] : 1;
                $user->updated_at = date('Y-m-d H:i:s');
                if (!empty($_POST['password'])) {
                    $user->password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT, array('cost' => 8));
                }
                if ($user->update()) {
                    if ($id === Yii::app()->user->uid) {
                        Yii::app()->user->display_name = $_POST['name'];
                    }
                    if (isset($_POST['role_id'])) {
                        $roleUser = RoleUser::model()->findByAttributes(array('uid' => $user->id));
                        if ($roleUser) {
                            $roleUser->attributes = array('role_id' => $_POST['role_id']);
                            $result = $roleUser->save();
                        } else {
                            $roleUser = new RoleUser();
                            $roleUser->attributes = array('uid' => $user->id, 'role_id' => $_POST['role_id'], 'updated_at' => date('Y-m-d H:i:s'));
                            $result = $roleUser->save();
                        }
                    }else{
                        $this->_end(0,'编辑成功！');
                        exit;
                    }
                }
                if($result){
                    $this->_end(0,'编辑成功');
                }else{
                    $this->_end(1,'编辑失败，请刷新页面重试');
                }
            }else{
                try {
                    $_POST['password'] = password_hash(trim($_POST['password']), PASSWORD_BCRYPT, array('cost' => 8));
                    $_POST['organization_id'] = Yii::app()->user->org_id;
                    $_POST['created_by'] = Yii::app()->user->org_id;
                    $_POST['created_at'] = $_POST['updated_at'] = date('Y-m-d H:i:s');

                    $model = new Users();
                    $model->attributes = $_POST;

                    if ($model->save()) {
                        $roleUser = new RoleUser();
                        $roleUser->attributes = array('uid' => $model->id, 'role_id' => $_POST['role_id'], 'updated_at' => date('Y-m-d H:i:s'));
                        $roleUser->save();
                        $this->_end(0, '添加员工成功');
                    }
                    $this->_end(1, '该账号已存在');
                } catch (Exception $e) {
                    $this->_end(0, $e . getmessage());
                }
            }
        }
    }

    //删除员工
    public function actionDel() {
        if (Yii::app()->request->isPostRequest) {
            $id = $_POST['id'];
            $count = Users::model()->deleteByPk($id);
            if ($count > 0) {
                $this->_end(0, '删除员工成功');
            } else {
                $this->_end(1, '删除员工失败');
            }
        }
    }

}
