<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OtaAccount
 *
 * @author wfdx1_000
 */
class OtaController extends Base_Controller_Abstract {
    
    public function createAction() {
        if (!defined('SET_CREATE_OTA') || !SET_CREATE_OTA) {
            exit;
        }
        
        $this->checkParams($_POST, array('name', 'distributor_id'));
        
        $name = trim($_POST['name']);
        $id = strtoupper(uniqid());
        $salt = substr(str_shuffle(uniqid()),0, 8);
        $secret = strtoupper(md5(uniqid()));
        $pwd = md5($salt . md5($salt . $secret));
        $distributor_id = (int)$_POST['distributor_id'];
       
        /**
        $notify_url = $_POST['notify_url'];
        $tmp = parse_url($notify_url);
        if (!$tmp || !isset($tmp['host']) || !isset($tmp['scheme'])) {
            Lang_Msg::error('notify_url 格式错误');
        }
         * 
         */
        
        $record = OtaAccountModel::model()->get('name=\'' . addslashes($name) . '\'');
        if ($record) {
            Lang_Msg::error("$name 已经存在了");
        }
        
        $r = OtaAccountModel::model()->add(array(
            'id' => $id,
            'name' => $name,
            'salt' => $salt,
            'secret' => $secret,
            'pwd' => $pwd,
            'distributor_id' => $distributor_id,
            'notify_url' => $notify_url
        ));
        
        if ($id) {
            Lang_Msg::output(array(
                'client_id' => $id,
                'client_secret' => $secret
            ));
        } else {
            Lang_Msg::error('创建失败');
        }
    }
    
    private function checkParams($params, $must) {
        foreach ($must as $val) {
            if (!isset($params[$val])) {
                Lang_Msg::error("缺少参数$val");
            }
        }
    }
}
