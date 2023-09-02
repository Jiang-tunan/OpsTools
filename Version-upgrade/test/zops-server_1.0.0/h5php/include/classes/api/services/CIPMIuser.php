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
class CIPMIuser extends CApiService {

    public const ACCESS_RULES = [
        'get' => ['min_user_type' => USER_TYPE_ZABBIX_USER],
        'create' => ['min_user_type' => USER_TYPE_SUPER_ADMIN],
        'update' => ['min_user_type' => USER_TYPE_ZABBIX_USER],
        'delete' => ['min_user_type' => USER_TYPE_SUPER_ADMIN]
    ];

    protected $tableName = 'ipmiuser';
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
            'select'	=> ['ipmiuser' => 'i.ipmiuserid'],
            'from'		=> ['ipmiuser' => 'ipmiuser i'],
            'where'		=> [],
            'order'		=> [],
            'limit'		=> null
        ];

        $defOptions = [
            'name'          => null,
            'output'		=> API_OUTPUT_EXTEND,
            'countOutput'	=> false,
            'preservekeys'	=> false,
            'selectUsers'	=> null,
            'selectManufacturer'	=> null,
            'filter'					=> null,
            'search'					=> null,
            'searchByAny'				=> null,
            'excludeSearch'				=> false,
            'enable'			     	=> null,
            'searchWildcardsEnabled'	=> null,
            'selectHosts'   =>null,
            'sortfield'		=> '',
            'sortorder'		=> '',
            'limit'			=> null
        ];
        $options = zbx_array_merge($defOptions, $options);

        // limit
        if (zbx_ctype_digit($options['limit']) && $options['limit']) {
            $sqlParts['limit'] = $options['limit'];
        }

        if ($options['ipmiuserids'] !== null) {
            zbx_value2array($options['ipmiuserids']);

            $sqlParts['where'][] = dbConditionInt('i.ipmiuserid', $options['ipmiuserids']);
        }

        if (is_array($options['search'])) {
            zbx_db_search('ipmiuser i', $options, $sqlParts);
        }

        $sqlParts = $this->applyQueryOutputOptions($this->tableName(), $this->tableAlias(), $options, $sqlParts);
        $sqlParts = $this->applyQuerySortOptions($this->tableName(), $this->tableAlias(), $options, $sqlParts);

        $dbRes = DBselect(self::createSelectQueryFromParts($sqlParts), $sqlParts['limit'],$options['current'] ? ($options['current'] - 1) * $sqlParts['limit'] : 0);

        while ($item = DBfetch($dbRes)) {
            if ($options['countOutput']) {
                $result = $item['rowscount'];
            }
            else {
                $item['total'] = API::ipmiuser()->get(array_merge($options,['countOutput'=>true]));
                $item['updatetime_str'] = date('Y-m-d',$item['update_time']);
                unset($item['password']);
                if ($options['preservekeys']) {
                    $result[$item['ipmiuserid']] = $item;
                }else{
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

    public function create(array $ipmiuser): array {
        $ipmiuser['password'] = md5($ipmiuser['password']);
        $this->validateCreate($ipmiuser);
        $ins_ipmiuser = [];

        foreach ($ipmiuser as $user) {
            $user['clock'] = $user['update_time'] = time();
            $user['create_user'] = self::$userData['userid'];

            $ins_ipmiuser[] = $user;
        }

        $ipmiuserids = DB::insert('ipmiuser', $ins_ipmiuser,true);

        $this->addAuditBulk(CAudit::ACTION_ADD, CAudit::RESOURCE_IPMIUSER, $ipmiuser);

        return ['ipmiuserids' =>  $ipmiuserids];
    }

    public function update(array $ipmiuser): array {
        if (self::$userData['type'] != USER_TYPE_SUPER_ADMIN) {
            self::exception(ZBX_API_ERROR_PERMISSIONS,
                _s('No permissions to call "%1$s.%2$s".', 'role', __FUNCTION__)
            );
        }

        $ipmiuser['password'] = md5($ipmiuser['password']);

        $this->validateUpdate($ipmiuser, $db_ipmiuser);

        $upd_ipmiusers = [];

        foreach ($ipmiuser as $user) {
            $upd_ipmiuser = DB::getUpdatedValues('ipmiuser', $user, $db_ipmiuser[$user['ipmiuserid']]);

            if ($upd_ipmiuser) {
                $upd_ipmiuser['update_time'] = time();
                $upd_ipmiusers[] = [
                    'values' => $upd_ipmiuser,
                    'where' => ['ipmiuserid' => $user['ipmiuserid']]
                ];
            }
        }

        if ($upd_ipmiusers) {
            DB::update('ipmiuser', $upd_ipmiusers);
        }

        $this->addAuditBulk(CAudit::ACTION_UPDATE, CAudit::RESOURCE_IPMIUSER, $ipmiuser, $db_ipmiuser);

        return ['ipmiuserids' => array_column($ipmiuser, 'ipmiuserid')];
    }

    public function delete(array $ipmiuser): array {
        if (self::$userData['type'] != USER_TYPE_SUPER_ADMIN) {
            self::exception(ZBX_API_ERROR_PERMISSIONS,
                _s('No permissions to call "%1$s.%2$s".', 'role', __FUNCTION__)
            );
        }

        $api_input_rules = ['type' => API_IDS, 'flags' => API_NOT_EMPTY, 'uniq' => true];

        if (!CApiInputValidator::validate($api_input_rules, $ipmiuser, '/', $error)) {
            self::exception(ZBX_API_ERROR_PARAMETERS, $error);
        }

        $db_ipmiuser = $this->get([
            'output' => ['ipmiuserid', 'name'],
            'selectUsers' => ['userid'],
            'ipmiuserids' => $ipmiuser,
            'preservekeys' => true
        ]);

        if (count($db_ipmiuser) != count($ipmiuser)) {
            self::exception(ZBX_API_ERROR_PERMISSIONS, _('No permissions to referred object or it does not exist!'));
        }

        DB::delete('ipmiuser', ['ipmiuserid' => $ipmiuser]);

        $this->addAuditBulk(CAudit::ACTION_DELETE, CAudit::RESOURCE_IPMIUSER, $db_ipmiuser);

        return ['ipmiuserids' => $ipmiuser];
    }


    private function validateUpdate(array &$ipmiuser,?array &$db_ipmiuser): void {
        $api_input_rules = ['type' => API_OBJECTS, 'flags' => API_NOT_EMPTY | API_NORMALIZE, 'uniq' => [['name']], 'fields' => [
            'ipmiuserid' =>			['type' => API_ID, 'flags' => API_REQUIRED],
            'username' =>			['type' => API_STRING_UTF8],
            'memo' =>			['type' => API_STRING_UTF8],
            'password' =>			['type' => API_STRING_UTF8],
            'manufacturerid'=>  ['type' => API_INT32]
        ]];

        $data = API::ipmiuser()->get(['search'=>['username'=>$ipmiuser['username']],'searchWildcardsEnabled'=>true]);

        foreach($data as $v){
            if($v['username'] == $ipmiuser['username'] && $v['ipmiuserid'] != $ipmiuser['ipmiuserid']){
                self::exception(ZBX_API_ERROR_PARAMETERS, '该厂商带外用户名称已存在');
            }
        }

        if (!CApiInputValidator::validate($api_input_rules, $ipmiuser, '/', $error)) {
            self::exception(ZBX_API_ERROR_PARAMETERS, $error);
        }

        $db_ipmiuser = $this->get([
            'output' => ['ipmiuserid', 'name','userid','usrgrpid'],
            'ipmiuserids' => array_column($ipmiuser, 'ipmiuserid'),
            'preservekeys' => true
        ]);

        if (count($db_ipmiuser) != count($ipmiuser)) {
            self::exception(ZBX_API_ERROR_PERMISSIONS, _('No permissions to referred object or it does not exist!'));
        }
    }

    private function validateCreate(array &$user): void {
        $api_input_rules = ['type' => API_OBJECTS, 'flags' => API_NOT_EMPTY | API_NORMALIZE, 'uniq' => [['name']], 'fields' => [
            'username' =>			['type' => API_STRING_UTF8, 'flags' => API_REQUIRED | API_NOT_EMPTY],
            'password' =>			['type' => API_STRING_UTF8, 'flags' => API_REQUIRED | API_NOT_EMPTY],
            'memo' =>			['type' => API_STRING_UTF8],
            'manufacturerid'=>  ['type' => API_INT32, 'flags' => API_NOT_EMPTY | API_NORMALIZE]
        ]
        ];

        $data = API::ipmiuser()->get(['search'=>['username'=>$user['username']],'searchWildcardsEnabled'=>true]);

        if($data){
            self::exception(ZBX_API_ERROR_PARAMETERS, '该厂商带外用户名称已存在');
        }

        if (!CApiInputValidator::validate($api_input_rules, $user, '/', $error)) {
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


        if ($options['selectUsers']) {
            $userids = array_column($result,'create_user');
            $users = API::User()->get([
                'output' => ['username','userid'],
                'userids' => $userids,
            ]);

            foreach($users as $v){
                foreach($result as &$vv){
                    if($vv['create_user'] == $v['userid']){
                        $vv['user'] = $v;
                    }
                }
            }
        }

        if($options['selectManufacturer']){
            $manufacturerids = array_column($result,'manufacturerid');

            $manus = API::Manufacturer()->get([
                'output' => ['name','manufacturerid'],
                'manufacturerids' => $manufacturerids,
            ]);

            foreach($manus as $v){
                foreach($result as &$vv){
                    if($vv['manufacturerid'] == $v['manufacturerid']){
                        $vv['manufacturer'] = $v;
                    }
                }
            }
        }

        return $result;
    }
}
