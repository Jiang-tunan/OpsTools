<?php
/*
** Zabbix
** Copyright (C) 2001-2023 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/


/**
 * Class containing methods for operations with graph items.
 */
class CHouse extends CApiService {

    public const ACCESS_RULES = [
        'get' => ['min_user_type' => USER_TYPE_ZABBIX_USER],
        'create' => ['min_user_type' => USER_TYPE_SUPER_ADMIN],
        'update' => ['min_user_type' => USER_TYPE_ZABBIX_USER],
        'delete' => ['min_user_type' => USER_TYPE_SUPER_ADMIN],
//        'createi' => ['min_user_type' => USER_TYPE_ZABBIX_USER],
//        'createb' => ['min_user_type' => USER_TYPE_ZABBIX_USER]
    ];

    protected $tableName = 'house';
    protected $tableAlias = 'i';
    protected $sortColumns = ['clock'];
    /**
     * Get GraphItems data
     *
     * @param array $options
     * @return array|boolean
     */
    public function get($options = []) {
        $result = [];

        $sqlParts = [
            'select'	=> ['house' => 'i.houseid'],
            'from'		=> ['house' => 'house i'],
            'where'		=> [],
            'order'		=> [],
            'limit'		=> null
        ];

        $defOptions = [
            'houseids'		=> null,
            'idcids'		=> null,
            'name'          => null,
            'output'		=> API_OUTPUT_EXTEND,
            'countOutput'	=> false,
            'preservekeys'	=> false,
            'selectUsers'	=> null,
            'filter'					=> null,
            'search'					=> null,
            'searchByAny'				=> null,
            'excludeSearch'				=> false,
            'enable'			     	=> null,
            'searchWildcardsEnabled'	=> null,
            'sortfield'		=> '',
            'sortorder'		=> '',
            'limit'			=> null
        ];
        $options = zbx_array_merge($defOptions, $options);

        // limit
        if (zbx_ctype_digit($options['limit']) && $options['limit']) {
            $sqlParts['limit'] = $options['limit'];
        }

        if ($options['houseids'] !== null) {
            zbx_value2array($options['houseids']);

            $sqlParts['where'][] = dbConditionInt('i.houseid', $options['houseids']);
        }

        if ($options['idcids'] !== null) {
            zbx_value2array($options['idcids']);

            $sqlParts['where'][] = dbConditionInt('i.idcid', $options['idcids']);
        }

        if ($options['userid'] !== null) {
            zbx_value2array($options['userid']);

            $sqlParts['where'][] = dbConditionInt('i.userid', $options['userid']);
        }

        if (is_array($options['search'])) {
            zbx_db_search('house i', $options, $sqlParts);
        }

        $sqlParts = $this->applyQueryOutputOptions($this->tableName(), $this->tableAlias(), $options, $sqlParts);
        $sqlParts = $this->applyQuerySortOptions($this->tableName(), $this->tableAlias(), $options, $sqlParts);
        $dbRes = DBselect(self::createSelectQueryFromParts($sqlParts), $sqlParts['limit'],$options['current'] ? ($options['current'] - 1) * $sqlParts['limit'] : 0);

        while ($item = DBfetch($dbRes)) {

            if ($options['countOutput']) {
                $result = $item['rowscount'];
            }
            else {
                $item['total'] = API::getTotal('house',$options);

                if ($options['preservekeys']) {
                    $result[$item['houseid']] = $item;
                }else{
                    $item['key'] = 'house'. $item['houseid'];
                    $result[] = $item;
                }
            }
        }

        if ($options['countOutput']) {
            return $result;
        }

        if ($result) {
            $result = $this->addRelatedObjects($options, $result);
        }

        return $result;
    }

    public function createI(){
        $arr = [10226=>25,10248=>26,10249=>27,10401=>28,10402=>29,10403=>30,10406=>31,10407=>32,10586=>33];
        $insert = [];
        $arr1 = [10,11,13];
        foreach($arr1 as $kk => $vv){
            $count = 0;
            foreach($arr as $k => $v){
                array_push($insert,['hostgroupid'=>1000+ $kk + $count,"checktype"=>$vv,'hostid'=>$k,'groupid'=>$v]);

                $count++;
            }
        }
        DB::insert('hosts_groups',$insert ,true);
    }

    public function createb(){
        $arr = [
            '网络设备'=>'67332e679035423f85090aa985947c36',
            'Linux服务器'=>'4d3a7adbb6964bd08f2b9d28e0da6496',
            'windows服务器'=>'f9a59315c8944853bb91c0a9ec3056d7',
            'x86服务器'=>'e8c0b2c40e884f1598d86f3edf020ea7',
            '超融合'=>'eafd78764fde4110b9e46ae184f327ba',
            '存储设备'=>'340ec6917c274ead8fab36925e57f30a',
            '安全设备'=>'b3caafda8c5345cc832ac3be3cefa615',
            '终端设备'=>'0b9a899ff8f1467c9fdf999d02b9fd77',
            '交换机'=>'c97b6148b73544ffb8f700db894c8f48'
        ];

        $insert = [];
        $count = 0;
        foreach($arr as $k=>$v){
            array_push($insert,['hstgrp'=>1000+$count,"name"=>$k,'flags'=>0,'uuid'=>$v,'type'=>0]);

            $count++;
        }
        DB::insert('hstgrp', $insert,true);
    }

    public function create(array $houses): array {
        $this->validateCreate($houses);

        $ins_house = [];

        foreach ($houses as $house) {
            $house['clock'] = time();
            $ins_house[] = $house;
        }

        $houseids = DB::insert('house', $ins_house,true);

        $this->addAuditBulk(CAudit::ACTION_ADD, CAudit::RESOURCE_HOUSE, $houses);

        return ['houseids' =>  $houseids];
    }

    public function update(array $houses): array {
        if (self::$userData['type'] != USER_TYPE_SUPER_ADMIN) {
            self::exception(ZBX_API_ERROR_PERMISSIONS,
                _s('No permissions to call "%1$s.%2$s".', 'role', __FUNCTION__)
            );
        }

        $this->validateUpdate($houses, $db_houses);

        $upd_houses = [];

        foreach ($houses as $house) {
            $upd_house = DB::getUpdatedValues('house', $house, $db_houses[$house['houseid']]);

            if ($upd_house) {
                $upd_houses[] = [
                    'values' => $upd_house,
                    'where' => ['houseid' => $house['houseid']]
                ];
            }
        }

        if ($upd_houses) {
            DB::update('house', $upd_houses);
        }

        $this->addAuditBulk(CAudit::ACTION_UPDATE, CAudit::RESOURCE_HOUSE, $houses, $db_houses);

        return ['houseids' => array_column($houses, 'houseid')];
    }

    public function delete(array $houses): array {
        if (self::$userData['type'] != USER_TYPE_SUPER_ADMIN) {
            self::exception(ZBX_API_ERROR_PERMISSIONS,
                _s('No permissions to call "%1$s.%2$s".', 'role', __FUNCTION__)
            );
        }

        $api_input_rules = ['type' => API_IDS, 'flags' => API_NOT_EMPTY, 'uniq' => true];

        if (!CApiInputValidator::validate($api_input_rules, $houses, '/', $error)) {
            self::exception(ZBX_API_ERROR_PARAMETERS, $error);
        }

        $db_houses = $this->get([
            'output' => ['houseid', 'name'],
            'selectUsers' => ['userid'],
            'houseids' => $houses,
            'preservekeys' => true
        ]);

        if (count($db_houses) != count($houses)) {
            self::exception(ZBX_API_ERROR_PERMISSIONS, _('No permissions to referred object or it does not exist!'));
        }

        DB::delete('house', ['houseid' => $houses]);

        $this->addAuditBulk(CAudit::ACTION_DELETE, CAudit::RESOURCE_HOUSE, $db_houses);

        return ['houses' => $houses];
    }


    private function validateUpdate(array &$houses,?array &$db_houses): void {
        $api_input_rules = ['type' => API_OBJECTS, 'flags' => API_NOT_EMPTY | API_NORMALIZE, 'uniq' => [['name']], 'fields' => [
            'houseid' =>			['type' => API_ID, 'flags' => API_REQUIRED],
            'userid'=>          ['type' => API_INT32],
            'idcid'=>          ['type' => API_INT32],
            'address' =>			['type' => API_STRING_UTF8],
            'usrgrpid'=>          ['type' => API_INT32],
            'name' =>			['type' => API_STRING_UTF8, 'flags' => API_NOT_EMPTY, 'length' => DB::getFieldLength('role', 'name')],
        ]];

        $data = API::HOUSE()->get(['search'=>['name'=>$houses['name']],'searchWildcardsEnabled'=>true]);

        $housedata = API::HOUSE()->get(['houseids'=>[$houses['houseid']]]);

        if(isset($houses['name'])){
            foreach($data as $v){
                foreach($housedata as $vv){
                    if($v['idcid'] == $vv['idcid'] && $v['houseid'] != $vv['houseid'] && $v['name'] == $houses['name']){
                        self::exception(ZBX_API_ERROR_PARAMETERS, '该机房名称已存在');
                    }
                }
            }
        }

        if (!CApiInputValidator::validate($api_input_rules, $houses, '/', $error)) {
            self::exception(ZBX_API_ERROR_PARAMETERS, $error);
        }

        $db_houses = $this->get([
            'output' => ['houseid', 'name','userid','usrgrpid'],
            'houseids' => array_column($houses, 'houseid'),
            'preservekeys' => true
        ]);

        if (count($db_houses) != count($houses)) {
            self::exception(ZBX_API_ERROR_PERMISSIONS, _('No permissions to referred object or it does not exist!'));
        }
    }

    private function validateCreate(array &$house): void {
        $api_input_rules = ['type' => API_OBJECTS, 'flags' => API_NOT_EMPTY | API_NORMALIZE, 'uniq' => [['name']], 'fields' => [
            'name' =>			['type' => API_STRING_UTF8],
            'address' =>			['type' => API_STRING_UTF8],
            'userid' =>			['type' => API_INT32],
            'idcid' =>			['type' => API_INT32]
        ]
        ];

        $data = API::HOUSE()->get(['search'=>['name'=>$house['name']],'searchWildcardsEnabled'=>true]);

        if($data){
            foreach($data as $v){
                if($v['idcid'] == $house['idcid']){
                    self::exception(ZBX_API_ERROR_PARAMETERS, '该机房名称已存在');
                }
            }
        }

        if (!CApiInputValidator::validate($api_input_rules, $house, '/', $error)) {
            self::exception(ZBX_API_ERROR_PARAMETERS, $error);
        }
    }

    protected function applyQueryOutputOptions($tableName, $tableAlias, array $options, array $sqlParts) {
        $sqlParts = parent::applyQueryOutputOptions($tableName, $tableAlias, $options, $sqlParts);

        if ($options['selectGraphs'] !== null) {
            $sqlParts = $this->addQuerySelect('graphid', $sqlParts);
        }

        return $sqlParts;
    }

    protected function addRelatedObjects(array $options, array $result) {
        $result = parent::addRelatedObjects($options, $result);

        $userids = array_column($result,'userid');

        if ($options['selectUsers']) {
            $users = API::User()->get([
                'output' => ['username','userid'],
                'userids' => $userids,
            ]);

            foreach($users as $v){
                foreach($result as &$vv){
                    if($vv['userid'] == $v['userid']){
                        $vv['user'] = $v;
                    }
                }
            }
        }

        if ($options['selectIDC'] !== null) {
            $idcids = array_column($result,'idcid');
            $idcidArr = API::IDC()->get([
                'idcids' => $idcids
            ]);

            foreach ($result as $hostid => $host) {
                foreach($idcidArr as $idc){
                    if($host['idcid'] == $idc['idcid']){
                        $result[$hostid]['idc'] = $idc;
                    }
                }
            }
        }

        if ($options['selectHosts']) {
            $houseids = array_column($result,'houseid');
            $hosts = API::Host()->get([
                'houseids' => $houseids,
                'monitored_hosts' => true,
                'output' => $options['selectHosts'] == "count" ? "extend" : $options['selectHosts'],
            ]);

            foreach($result as $k => $v){
                $result[$k]['hosts'] = [];
                foreach($hosts as  $host){

                    if($host['houseid'] == $v['houseid']){
                        $host['key'] = 'house'.$v['houseid'] . $host['hostid'];
                        array_push($result[$k]['hosts'],$host);
                    }
                }

                //如果是只需要设备数，返回设备数
                if(count($result[$k]['hosts']) == 0 && $options['hashosts'] && $options['selectHosts'] != "count") {
                    unset($result[$k]);
                }else if($options['selectHosts'] == "count"){
                    $result[$k]['hosts'] = count($result[$k]['hosts']);
                }
            }
            sort($result);
        }

        return $result;
    }
}
