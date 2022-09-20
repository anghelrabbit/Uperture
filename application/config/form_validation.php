<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$config = array(
    'LandingPage/LoginAccount' => array(
        array('field' => 'username',
            'label' => 'Username',
            'rules' => 'required',
        ),
        array('field' => 'password',
            'label' => 'Password',
            'rules' => 'required',
        ),
    )
);
// $this->form_validation->set_rules($config);