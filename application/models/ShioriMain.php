<?php

class ShioriMainTable extends Zend_Db_Table_Abstract
{
    protected $_name = 'shiori_main';
}

class ShioriMain
{
    private $_shioriMain;

    const SPAN_YEAR = 'year';
    const SPAN_MONTH = 'month';
    const SPAN_DAY = 'day';

    public static $spanList = array(
        self::SPAN_YEAR  => '年',
        self::SPAN_MONTH => '月',
        self::SPAN_DAY   => '日',
    );

    const COLUMN_ID = 'id';
    const COLUMN_TITLE = 'title';
    const COLUMN_THEMA = 'thema';
    const COLUMN_START_DATE = 'start_date';
    const COLUMN_END_DATE = 'end_date';
    const COLUMN_MEMBER = 'member';
    const COLUMN_DETAIL = 'detail';
    const COLUMN_CREATE_AT = 'create_at';
    const COLUMN_UPDATE_AT = 'update_at';

    function __construct()
    {
        $this->_shioriMain = new ShioriMainTable();
    }

    /**
     * データを登録する
     * 
     * @param string $title     タイトル
     * @param string $thema     テーマ
     * @param string $startDate 出発日
     * @param string $endDate   最終日
     * @param string $member    メンバー
     * @param string $detail    詳細
     */
    public function insertShiori($title, $thema, $startDate, $endDate, $member, $detail)
    {
        $data = array(
            self::COLUMN_TITLE      => $title,
            self::COLUMN_THEMA      => $thema,
            self::COLUMN_START_DATE => $startDate,
            self::COLUMN_END_DATE   => $endDate,
            self::COLUMN_MEMBER     => $member,
            self::COLUMN_DETAIL     => $detail,
            self::COLUMN_CREATE_AT  => new Zend_Db_Expr('NOW()'),
            self::COLUMN_UPDATE_AT  => new Zend_Db_Expr('NOW()')
        );
        $this->_shioriMain->insert($data);
    }

    /**
     * データを更新する
     * 
     * @param string $title     タイトル
     * @param string $thema     テーマ
     * @param string $startDate 出発日
     * @param string $endDate   最終日
     * @param string $member    メンバー
     * @param string $detail    詳細
     */
    public function updateShiori($id, $title, $thema, $startDate, $endDate, $member, $detail)
    {
        $where = $this->_shioriMain->getAdapter()->quoteInto(self::COLUMN_ID . ' = ?', $id);
        $data = array(
            self::COLUMN_TITLE      => $title,
            self::COLUMN_THEMA      => $thema,
            self::COLUMN_START_DATE => $startDate,
            self::COLUMN_END_DATE   => $endDate,
            self::COLUMN_MEMBER     => $member,
            self::COLUMN_DETAIL     => $detail,
            self::COLUMN_UPDATE_AT  => new Zend_Db_Expr('NOW()')
        );
        $this->_shioriMain->update($data, $where);
    }

    /**
     * データを削除する
     * @param int $id しおりのID
     */
    public function deleteShiori($id)
    {
        $where = $this->_shioriMain->getAdapter()->quoteInto(self::COLUMN_ID . ' = ?', $id);
        $this->_shioriMain->delete($where);
    }

    /**
     * IDを指定してデータを取得する
     * 
     * @param  int   $id しおりのID
     * @return array     
     */
    public function getShioriData($id)
    {
        if (self::isValidateNumber($id) === false) {
            return false;
        }
        
        $select = $this->_shioriMain->select()
                ->where('`' . self::COLUMN_ID . '` = ?', $id);
        $result = $this->_shioriMain->fetchRow($select);

        return (empty($result) === false) ? $result->toArray() : false;
    }

    /**
     * 最新のしおりデータを取得する
     * 
     * @return array
     */
    public function getNewestShioriData()
    {
        $select = $this->_shioriMain->select()
                ->order(self::COLUMN_UPDATE_AT . ' DESC');
        $result = $this->_shioriMain->fetchRow($select);

        return (empty($result) === true) ? $result : $result->toArray();
    }

    /**
     * しおり一覧を取得する
     */
    public function getAllShioriData()
    {
        $result = $this->_shioriMain->fetchAll();

        return (empty($result) === true) ? $result : $result->toArray();
    }

    /**
     * 日付の文字列を年月日の配列に変換する
     * 
     * @param  string $date 年月日の配列
     * @return array
     */
    public static function convertDateToArray($date)
    {
        $time = strtotime($date);
        $dateList = array();

        $dateList[self::SPAN_YEAR] = date('Y', $time);
        $dateList[self::SPAN_MONTH] = date('m', $time);
        $dateList[self::SPAN_DAY] = date('d', $time);

        return $dateList;
    }

    /**
     * 日付の配列を取得する
     * 
     * @param  int   $time UNIXTIME
     * @return array       年月日 ['year']['month']['day']
     */
    public static function makeDateList($time)
    {
        $todayList[self::SPAN_YEAR] = (int) date('Y', $time);
        $todayList[self::SPAN_MONTH] = (int) date('m', $time);
        $todayList[self::SPAN_DAY] = (int) date('d', $time);

        return $todayList;
    }

    /**
     * 表示用にプルダウンの配列を作成する
     * 
     * @param  array $todayList 年月日の配列 self::getTodayで作成したもの
     * @return array            年月日それぞれの配列
     */
    public static function makeSelectDateListForForm($todayList)
    {
        $dateList = array();
        for ($i = $todayList[self::SPAN_YEAR]; $i <= $todayList[self::SPAN_YEAR] + 5; $i++) {
            $dateList[self::SPAN_YEAR][] = $i;
        }
        for ($i = 1; $i <= 12; $i++) {
            $dateList[self::SPAN_MONTH][] = $i;
        }
        for ($i = 1; $i <= 31; $i++) {
            $dateList[self::SPAN_DAY][] = $i;
        }

        return $dateList;
    }

    /**
     * DBに保存するデータが正常かチェックする
     * 
     * @param string $title     タイトル
     * @param string $thema     テーマ
     * @param string $startDate 出発日
     * @param string $endDate   最終日
     * @param string $member    メンバー
     * @param string $detail    詳細
     * @return array            エラーの配列
     */
    public static function checkShioriData($title, $thema, $startDate, $endDate, $member, $detail)
    {
        $errorList = array();

        // タイトルチェック
        if (self::isNotEmptyText($title) === false) {
            $errorList[self::COLUMN_TITLE] = 'タイトルを入力してください';
        }

        // 目的をチェック
        if (self::isNotEmptyText($thema) === false) {
            $errorList[self::COLUMN_THEMA] = '目的を入力してください';
        }

        // 出発日チェック
        if (self::isValidateDate($startDate) === false) {
            $errorList[self::COLUMN_START_DATE] = '出発日が日付が正しくありません';
        }

        // 終了日チェック
        if (self::isValidateDate($endDate) === false) {
            $errorList[self::COLUMN_END_DATE] = '帰着日が日付が正しくありません';
        }

        // 前後時間チェック
        if (self::isValidateStartAndEndDate($startDate, $endDate) === false) {
            $errorList[self::COLUMN_START_DATE] = '帰着日が出発日より前です';
        }

        // 詳細チェック
        if (self::isNotEmptyText($detail) === false) {
            $errorList[self::COLUMN_DETAIL] = '詳細を入力してください';
        }

        return $errorList;
    }

    /**
     * 数字かどうか確認する
     * @param  int     $number 数字
     * @return boolean         正しい場合 true
     */
    public static function isValidateNumber($number)
    {
        if (is_numeric($number) === false) {
            return false;
        }
        return true;
    }
    
    /**
     * テキストが空かどうかをチェックする
     * 
     * @param  string $text 対象テキスト 
     * @return boolean      空じゃない場合true
     */
    public static function isNotEmptyText($text)
    {
        if ($text === '' || mb_strlen($text) === 0) {
            return false;
        }
        return true;
    }

    /**
     * 日付が正しいか確認する
     * 
     * @param  array   $targetDate 年月日の配列
     * @return boolean             正しい場合 true
     */
    public static function isValidateDate($targetDate)
    {
        if (checkdate($targetDate[self::SPAN_MONTH], $targetDate[self::SPAN_DAY], $targetDate[self::SPAN_YEAR]) === false) {
            return false;
        }
        return true;
    }

    /**
     * 開始終了の日付が正しいか確認する
     * 
     * @param  array   $startDate 開始の年月日の配列
     * @param  array   $endDate   終了の年月日の配列
     * @return boolean            正しい場合 true
     */
    public static function isValidateStartAndEndDate($startDate, $endDate)
    {
        $startTime = mktime(0, 0, 0, $startDate[self::SPAN_MONTH], $startDate[self::SPAN_DAY], $startDate[self::SPAN_YEAR]);
        $endTime = mktime(0, 0, 0, $endDate[self::SPAN_MONTH], $endDate[self::SPAN_DAY], $endDate[self::SPAN_YEAR]);

        if ($startTime > $endTime) {
            return false;
        }
        return true;
    }
}
