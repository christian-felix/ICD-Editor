<?php
/**
 * Created by PhpStorm.
 * User: cfelix
 * Date: 10.08.2015
 * Time: 09:52
 */

/**
 * Class DB
 */
class DB {

    static $instance = null;
    private static $dbHost = 'sigma.actineo.test';
    private static $dbUser = 'locPhoenix01';
    private static $dbPass = 'mysql';
    private static $dbName = 'DB_system';
    protected static $dbLink = null;

    /**
     * single Ton Pattern
     */
    protected function __construct(){
        $db = mysql_connect(self::$dbHost, self::$dbUser, self::$dbPass);
        mysql_select_db(self::$dbName,$db);
        self::$dbLink = $db; //return $this
    }

    /**
     * liefert das aktuelle DB-Objekt
     *
     * @return  object $instance
     */
    public static function getInstance(){
        if(empty(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * @param string $sql
     * @param array $cols [tabelle- Spaltennamen]
     * @return array
     */
    public function getData($sql, array $cols = null){
        $data = [];
        $type = 'Array';
        $res  = $this->query($sql);
        while($row = $this->fetch($res,$type)){
            if(isset($cols)){ //
                if($type == 'Array' ||$type == 'Assoc'){
                    $data[]['icd'] = $row[$cols[0]]; //
                    //$data[]['icd_description_1'] = $row[$cols[1]];
                }else if($type == 'Object'){
                    $data[] = $row->$cols;
                }
            }
        }
        mysql_free_result($res);
        return $data;
    }

    /**
     *
     * @param string $sql
     * @return object $result
     */
    public function query($sql){  //prevent sql injections
        //$result = mysql_query(mysql_real_escape_string($sql), self::$dbLink); // \" \" werden nicht interpretiert
        $result = mysql_query($sql, self::$dbLink);
        return $result;
    }

    /**
     * Liefert das Query Ergebnis
     *
     * @param object $result
     * @param string $type
     * @return array|object
     */
    public function fetch($result, $type='Array'){
        switch($type){
            case 'Array':
                $data = mysql_fetch_array($result, MYSQL_BOTH);
                break;
            case 'Object':
                $data = mysql_fetch_object($result);
                break;
            case 'Assoc':
                $data = mysql_fetch_assoc($result);
                break;
        }
        return $data;
    }

}