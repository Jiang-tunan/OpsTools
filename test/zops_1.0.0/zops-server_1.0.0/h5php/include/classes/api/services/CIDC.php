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
class CIDC extends CApiService {

    public const ACCESS_RULES = [
        'get' => ['min_user_type' => USER_TYPE_ZABBIX_USER],
        'create' => ['min_user_type' => USER_TYPE_SUPER_ADMIN],
        'update' => ['min_user_type' => USER_TYPE_ZABBIX_USER],
        'delete' => ['min_user_type' => USER_TYPE_SUPER_ADMIN]
    ];

    protected $tableName = 'idc';
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
            'select'	=> ['idc' => 'i.idcid'],
            'from'		=> ['idc' => 'idc i'],
            'where'		=> [],
            'order'		=> [],
            'limit'		=> null
        ];

        $defOptions = [
            'usrgrpid'		=> null,
            'userid'		=> null,
            'name'          => null,
            'output'		=> API_OUTPUT_EXTEND,
            'countOutput'	=> false,
            'preservekeys'	=> false,
            'selectUsers'	=> null,
            'selectUsrgrps'	=> null,
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

        if ($options['idcids'] !== null) {
            zbx_value2array($options['idcids']);

            $sqlParts['where'][] = dbConditionInt('i.idcid', $options['idcids']);
        }

        if ($options['usrgrpid'] !== null) {
            zbx_value2array($options['gsrgrpid']);

            $sqlParts['where'][] = dbConditionInt('i.usrgrpid', $options['usrgrpid']);
        }

        if (is_array($options['search'])) {
            zbx_db_search('idc i', $options, $sqlParts);
        }

        if ($options['userid'] !== null) {
            zbx_value2array($options['userid']);

            $sqlParts['where'][] = dbConditionInt('i.userid', $options['userid']);
        }

        $sqlParts = $this->applyQueryOutputOptions($this->tableName(), $this->tableAlias(), $options, $sqlParts);
        $sqlParts = $this->applyQuerySortOptions($this->tableName(), $this->tableAlias(), $options, $sqlParts);

        $dbRes = DBselect(self::createSelectQueryFromParts($sqlParts), $sqlParts['limit'],$options['current'] ? ($options['current'] - 1) * $sqlParts['limit'] : 0);

        while ($item = DBfetch($dbRes)) {
            if ($options['countOutput']) {
                $result = $item['rowscount'];
            }
            else {
                $item['total'] = API::getTotal('idc',$options);
                if ($options['preservekeys']) {
                    $result[$item['idcid']] = $item;
                }else{
                    $item['key'] = 'idc' .  $item['idcid'];
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

    public function create(array $idcs): array {
        $this->validateCreate($idcs);

        $ins_idc = [];

        foreach ($idcs as $idc) {
            $idc['clock'] = time();
            $ins_idc[] = $idc;
        }

        $idcids = DB::insert('idc', $ins_idc,true);

        $this->addAuditBulk(CAudit::ACTION_ADD, CAudit::RESOURCE_IDC, $idcs);

        return ['idcids' =>  $idcids];
    }

    public function update(array $idcs): array {
        if (self::$userData['type'] != USER_TYPE_SUPER_ADMIN) {
            self::exception(ZBX_API_ERROR_PERMISSIONS,
                _s('No permissions to call "%1$s.%2$s".', 'role', __FUNCTION__)
            );
        }

        $this->validateUpdate($idcs, $db_idcs);

        $upd_idcs = [];

        foreach($idcs as $k => $v){
            if ($v['usrgrpid'] == 9) {
                self::exception(ZBX_API_ERROR_PERMISSIONS,
                    _s('Invalid usrgrpid 9"', 'idc', __FUNCTION__)
                );
            }
        }

        foreach ($idcs as $idc) {
            $upd_idc = DB::getUpdatedValues('idc', $idc, $db_idcs[$idc['idcid']]);

            if ($upd_idc) {
                $upd_idcs[] = [
                    'values' => $upd_idc,
                    'where' => ['idcid' => $idc['idcid']]
                ];
            }
        }

        if ($upd_idcs) {
            DB::update('idc', $upd_idcs);
        }

        $this->addAuditBulk(CAudit::ACTION_UPDATE, CAudit::RESOURCE_IDC, $idcs, $db_idcs);

        return ['idcids' => array_column($idcs, 'idcid')];
    }

    public function delete(array $idcs): array {
        if (self::$userData['type'] != USER_TYPE_SUPER_ADMIN) {
            self::exception(ZBX_API_ERROR_PERMISSIONS,
                _s('No permissions to call "%1$s.%2$s".', 'role', __FUNCTION__)
            );
        }

        $api_input_rules = ['type' => API_IDS, 'flags' => API_NOT_EMPTY, 'uniq' => true];

        if (!CApiInputValidator::validate($api_input_rules, $idcs, '/', $error)) {
            self::exception(ZBX_API_ERROR_PARAMETERS, $error);
        }

        $db_idcs = $this->get([
            'output' => ['idcid', 'name'],
            'selectUsers' => ['userid'],
            'idcids' => $idcs,
            'preservekeys' => true
        ]);

        if (count($db_idcs) != count($idcs)) {
            self::exception(ZBX_API_ERROR_PERMISSIONS, _('No permissions to referred object or it does not exist!'));
        }

        DB::delete('idc', ['idcid' => $idcs]);
        DB::delete('house', ['idcid' => $idcs]);

        $this->addAuditBulk(CAudit::ACTION_DELETE, CAudit::RESOURCE_IDC, $db_idcs);

        return ['idcs' => $idcs];
    }

    
    private function validateUpdate(array &$idcs,?array &$db_idcs): void {
        $api_input_rules = ['type' => API_OBJECTS, 'flags' => API_NOT_EMPTY | API_NORMALIZE, 'uniq' => [['name']], 'fields' => [
            'idcid' =>			['type' => API_ID, 'flags' => API_REQUIRED],
            'userid'=>          ['type' => API_INT32],
            'usrgrpid'=>          ['type' => API_INT32],
            'address' =>			['type' => API_STRING_UTF8],
            'name' =>			['type' => API_STRING_UTF8],
        ]];

        if(isset($idcs['name'])){
            $data = API::IDC()->get(['search'=>['name'=>$idcs['name']],'searchWildcardsEnabled'=>true]);

            foreach($data as $k => $v){
                if($v['idcid'] != $idcs['idcid'] && $idcs['name'] == $v['name']){
                    self::exception(ZBX_API_ERROR_PARAMETERS, '该数据中心名称已存在');
                }
            }
        }

        if (!CApiInputValidator::validate($api_input_rules, $idcs, '/', $error)) {
            self::exception(ZBX_API_ERROR_PARAMETERS, $error);
        }

        $db_idcs = $this->get([
            'output' => ['idcid', 'name','userid','usrgrpid'],
            'idcids' => array_column($idcs, 'idcid'),
            'preservekeys' => true
        ]);

        if (count($db_idcs) != count($idcs)) {
            self::exception(ZBX_API_ERROR_PERMISSIONS, _('No permissions to referred object or it does not exist!'));
        }
    }

    private function validateCreate(array &$idc): void {
        $api_input_rules = ['type' => API_OBJECTS, 'flags' => API_NOT_EMPTY | API_NORMALIZE, 'uniq' => [['name']], 'fields' => [
            'name' =>			['type' => API_STRING_UTF8, 'flags' => API_REQUIRED | API_NOT_EMPTY],
            'address' =>			['type' => API_STRING_UTF8],
            'userid' =>			['type' => API_INT32],
            'usrgrpid' =>			['type' => API_INT32]
        ]
        ];

        $data = API::IDC()->get(['search'=>['name'=>$idc['name']],'searchWildcardsEnabled'=>true]);

        if($data){
            self::exception(ZBX_API_ERROR_PARAMETERS, '该数据中心名称已存在');
        }

        if (!CApiInputValidator::validate($api_input_rules, $idc, '/', $error)) {
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
        $gsrgrpids = array_column($result,'usrgrpid');

        if ($options['selectUsers']) {
            $users = API::User()->get([
                'output' => ['name','userid'],
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

        if ($options['selectUsrgrps']) {
            $groups = API::UserGroup()->get([
                'output' => ['name','usrgrpid'],
                'usrgrpids' => $gsrgrpids,
            ]);

            foreach($groups as $v){
                foreach($result as &$vv){
                    if($vv['usrgrpid'] == $v['usrgrpid']){
                        $vv['usrgrp'] = $v;
                    }
                }
            }
        }

        if ($options['selectHosts']) {
            $idcids = array_column($result,'idcid');
            $hosts = API::Host()->get([
                'idcids' => $idcids,
                'output' => $options['selectHosts'] == "count" ? "extend" : $options['selectHosts'],
            ]);

            if($hosts){
                foreach($result as $k => $v){
                    $result[$k]['hosts'] = [];
                    foreach($hosts as  $host){
                        if($host['idcid'] == $v['idcid']){
                            $host['key'] = 'idc' . $v['idcid'] . $host['hostid'];
                            array_push($result[$k]['hosts'],$host);
                        }
                    }

                    //只返回有设备的信息
                    if(count($result[$k]['hosts']) == 0 && $options['hashosts'] && $options['selectHosts'] != "count") {
                        unset($result[$k]);
                    }else if($options['selectHosts'] == "count"){
                        $result[$k]['hosts'] = count($result[$k]['hosts']);
                    }
                }
                sort($result);
            }
        }

        return $result;
    }
}
