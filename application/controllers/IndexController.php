<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->headTitle('SHIORI FACTORY');
    }

    public function indexAction()
    {
        $this->render('index');
    }

}

