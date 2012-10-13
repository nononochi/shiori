<?php

require_once '../application/models/ShioriMain.php';

class EditController extends Zend_Controller_Action
{

    private $_shioriMain;

    public function init()
    {
        
    }

    public function preDispatch()
    {
        $this->_shioriMain = new ShioriMain();
    }

    public function indexAction()
    {
        $today = time();
        $todayList = ShioriMain::makeDateList($today);
        $this->view->assign('startDate', $todayList);
        $this->view->assign('endDate', $todayList);
        $this->view->assign('dateList', ShioriMain::makeSelectDateListForForm($todayList));

        $id = $this->getRequest()->getParam('id');
        $data = $this->_shioriMain->getShioriData($id);

        if (empty($data) === false) {
            // idが指定されていた場合、値を入れる
            $startDate = ShioriMain::convertDateToArray($data[ShioriMain::COLUMN_START_DATE]);
            $endDate = ShioriMain::convertDateToArray($data[ShioriMain::COLUMN_END_DATE]);
            // データアサイン
            $this->_assignFormData($data[ShioriMain::COLUMN_ID], $data[ShioriMain::COLUMN_TITLE], $data[ShioriMain::COLUMN_THEMA], 
                    $startDate, $endDate, $data[ShioriMain::COLUMN_MEMBER], $data[ShioriMain::COLUMN_DETAIL]);
        }

        $this->render('index');
    }

    public function checkAction()
    {
        // 取得
        $id = $this->getRequest()->getParam('id');
        $title = $this->getRequest()->getParam('title');
        $thema = $this->getRequest()->getParam('thema');
        $startDate[ShioriMain::SPAN_YEAR] = (int) $this->getRequest()->getParam('startyear');
        $startDate[ShioriMain::SPAN_MONTH] = (int) $this->getRequest()->getParam('startmonth');
        $startDate[ShioriMain::SPAN_DAY] = (int) $this->getRequest()->getParam('startday');
        $endDate[ShioriMain::SPAN_YEAR] = (int) $this->getRequest()->getParam('endyear');
        $endDate[ShioriMain::SPAN_MONTH] = (int) $this->getRequest()->getParam('endmonth');
        $endDate[ShioriMain::SPAN_DAY] = (int) $this->getRequest()->getParam('endday');
        $member = $this->getRequest()->getParam('member');
        $detail = $this->getRequest()->getParam('detail');

        // エラーチェック
        $errorList = ShioriMain::checkShioriData($title, $thema, $startDate, $endDate, $member, $detail);

        // データアサイン
        $this->_assignFormData($id, $title, $thema, $startDate, $endDate, $member, $detail);

        // 表示
        $back = $this->getRequest()->getparam('back');
        $todayList = ShioriMain::makeDateList(time());
        $this->view->assign('dateList', ShioriMain::makeSelectDateListForForm($todayList));

        if ($back !== '' && $back !== null) {
            // 戻るボタンを押した場合
            $this->render('index');
        } elseif (empty($errorList) === false) {
            // エラーがあった場合
            $this->view->assign('errorList', $errorList);
            $this->render('index');
        } else {
            $this->render('check');
        }
    }

    public function updateAction()
    {
        $id = $this->getRequest()->getParam('id');
        $title = $this->getRequest()->getParam('title');
        $thema = $this->getRequest()->getParam('thema');
        $startDateList = $this->getRequest()->getParam('startDate');
        $endDateList = $this->getRequest()->getParam('endDate');
        $member = $this->getRequest()->getParam('member');
        $detail = $this->getRequest()->getParam('detail');
        $startDate = implode('-', $startDateList);
        $endDate = implode('-', $endDateList);

        // エラーチェック
        $errorList = ShioriMain::checkShioriData($title, $thema, $startDate, $endDate, $member, $detail);
        if (empty($errorList) === false) {
            $this->_assignFormData($id, $title, $thema, $startDate, $endDate, $member, $detail);
            $this->view->assign('errorList', $errorList);
            $this->render('index');
        }
        
        if ($id !== '') {
            $this->_shioriMain->updateShiori($id, $title, $thema, $startDate, $endDate, $member, $detail);
        } else {
            $this->_shioriMain->insertShiori($title, $thema, $startDate, $endDate, $member, $detail);
        }
        $this->_redirect('/show/');
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        if (ShioriMain::isValidateNumber($id) === false) {
            $this->_redirect('/');
        }
        $this->_shioriMain->deleteShiori($id);
        $this->_redirect('/show/');
    }

    // 各パラメータをアサインする
    private function _assignFormData($id, $title, $thema, $startDate, $endDate, $member, $detail)
    {
        $this->view->assign('id', $id);
        $this->view->assign('title', $title);
        $this->view->assign('thema', $thema);
        $this->view->assign('startDate', $startDate);
        $this->view->assign('endDate', $endDate);
        $this->view->assign('member', $member);
        $this->view->assign('detail', $detail);
    }

}

