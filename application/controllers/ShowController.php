<?php

require_once '../application/models/ShioriMain.php';

class ShowController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->headTitle('SHIORI FACTORY');
    }

    public function indexAction()
    {
        $shioriMain = new ShioriMain();
        $result = $shioriMain->getAllShioriData();
        $this->view->assign('shioriList', $result);
        $this->render('index');
    }

    public function detailAction()
    {
        $id = $this->getRequest()->getParam('id');

        $shioriMain = new ShioriMain();
        $shiori = $shioriMain->getShioriData($id);

        if (empty($shiori) === true) {
            $this->_redirect('/show/');
        }

        $shiori[ShioriMain::COLUMN_DETAIL] = nl2br($shiori[ShioriMain::COLUMN_DETAIL]);

        $this->view->assign('shiori', $shiori);
        $this->render('detail');
    }

    public function downloadAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $id = $this->getRequest()->getParam('id');

        $shioriMain = new ShioriMain();
        $shiori = $shioriMain->getShioriData($id);

        if (empty($shiori) === true) {
            $this->_redirect('/show/');
        }

        $fileName = "shiori_{$shiori[ShioriMain::COLUMN_START_DATE]}_{$shiori[ShioriMain::COLUMN_END_DATE]}.html";
        $this->getResponse()
                ->setHeader('Content-disposition', 'attachment; filename="' . $fileName . '"', true)
                ->setHeader('Content-type', 'text/octet-stream')
                ->sendHeaders();

        $shiori[ShioriMain::COLUMN_DETAIL] = nl2br($shiori[ShioriMain::COLUMN_DETAIL]);
        $this->view->assign('shiori', $shiori);
        $cssFile = file_get_contents('css/download.css');
        $this->view->assign('css', $cssFile);
        $this->render('download');
    }

}
