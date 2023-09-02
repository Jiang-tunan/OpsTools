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
class CModelType extends CApiService {

    public const ACCESS_RULES = [
        'get' => ['min_user_type' => USER_TYPE_ZABBIX_USER],
        'create' => ['min_user_type' => USER_TYPE_SUPER_ADMIN],
        'update' => ['min_user_type' => USER_TYPE_ZABBIX_USER],
        'delete' => ['min_user_type' => USER_TYPE_SUPER_ADMIN]
    ];

    protected $tableName = 'model_type';
    protected $tableAlias = 'i';
    protected $sortColumns = ['create_time','modeltypeid'];
    /**
     * Get GraphItems data
     *
     * @param array $options
     * @return array|boolean
     */
    public function get($options = []) {
        $result = [];

        $sqlParts = [
            'select'	=> ['model_type' => 'i.modeltypeid'],
            'from'		=> ['model_type' => 'model_type i'],
            'where'		=> [],
            'order'		=> [],
            'limit'		=> null
        ];

        $defOptions = [
            'name'          => null,
            'output'		=> API_OUTPUT_EXTEND,
            'countOutput'	=> false,
            'preservekeys'	=> false,
            'selectHostGroup'	=> null,
            'selectManufacturer'	=> null,
            'manufacturerids' => null,
            'groupids'       => null,
            'models'  => null,
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

        if ($options['modeltypeids'] !== null) {
            zbx_value2array($options['modeltypes']);

            $sqlParts['where'][] = dbConditionInt('i.modeltypeid', $options['modeltypeids']);
        }

        if ($options['manufacturerids'] !== null) {
            zbx_value2array($options['manufacturerids']);

            $sqlParts['where'][] = dbConditionInt('i.manufacturerid', $options['manufacturerids']);
        }

        if ($options['models'] !== null) {
            zbx_value2array($options['models']);

            $str = '';
            foreach($options['models'] as $k => $v){
                if($v != '' && $v != null){
                    $str .= "'".$v."'";
                    if($k != count($options['models']) - 1) $str .= ',';
                }
            }
            $sqlParts['where'][] = 'i.physical_model in ('.$str.')';
        }


        if ($options['groupids'] !== null) {
            zbx_value2array($options['groupids']);

            $sqlParts['where'][] = dbConditionInt('i.groupid', $options['groupids']);
        }

        if (is_array($options['search'])) {
            zbx_db_search('model_type i', $options, $sqlParts);
        }

        $sqlParts = $this->applyQueryOutputOptions($this->tableName(), $this->tableAlias(), $options, $sqlParts);
        $sqlParts = $this->applyQuerySortOptions($this->tableName(), $this->tableAlias(), $options, $sqlParts);

        $dbRes = DBselect(self::createSelectQueryFromParts($sqlParts), $sqlParts['limit'],$options['current'] ? ($options['current'] - 1) * $sqlParts['limit'] : 0);

        while ($item = DBfetch($dbRes)) {
            if ($options['countOutput']) {
                $result = $item['rowscount'];
            }
            else {
                $item['total'] = API::getTotal('modeltype',$options);
                if ($options['preservekeys']) {
                    $result[$item['modeltype']] = $item;
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

    public function create(array $modeltypes): array {
        $this->validateCreate($modeltypes);
        $ins_modeltype = [];

        foreach ($modeltypes as $modeltype) {
            $modeltype['create_time'] = $modeltype['update_time'] = time();

            $ins_modeltype[] = $modeltype;
        }

        $modeltypes = DB::insert('model_type', $ins_modeltype,true);

        $this->addAuditBulk(CAudit::ACTION_ADD, CAudit::RESOURCE_MODELTYPE, $modeltypes);

        return ['modeltypes' =>  $modeltypes];
    }

    public function update(array $modeltypes): array {
        if (self::$userData['type'] != USER_TYPE_SUPER_ADMIN) {
            self::exception(ZBX_API_ERROR_PERMISSIONS,
                _s('No permissions to call "%1$s.%2$s".', 'role', __FUNCTION__)
            );
        }

        $this->validateUpdate($modeltypes, $db_modeltypes);

        $upd_modeltypes = [];

        foreach ($modeltypes as $modeltype) {
            $upd_modeltype = DB::getUpdatedValues('model_type', $modeltype, $db_modeltypes[$modeltypes['modeltypeid']]);

            if ($upd_modeltype) {
                $upd_modeltype['update_time'] = time();
                $upd_modeltypes[] = [
                    'values' => $upd_modeltype,
                    'where' => ['modeltypeid' => $modeltype['modeltypeid']]
                ];
            }
        }

        if ($upd_modeltypes) {
            DB::update('model_type', $upd_modeltypes);
        }

        $this->addAuditBulk(CAudit::ACTION_UPDATE, CAudit::RESOURCE_MODELTYPE, $modeltypes, $db_modeltypes);

        return ['modeltypes' => array_column($modeltypes, 'modeltypeid')];
    }

    public function delete(array $modeltypes): array {
        if (self::$userData['type'] != USER_TYPE_SUPER_ADMIN) {
            self::exception(ZBX_API_ERROR_PERMISSIONS,
                _s('No permissions to call "%1$s.%2$s".', 'role', __FUNCTION__)
            );
        }

        $api_input_rules = ['type' => API_IDS, 'flags' => API_NOT_EMPTY, 'uniq' => true];

        if (!CApiInputValidator::validate($api_input_rules, $modeltypes, '/', $error)) {
            self::exception(ZBX_API_ERROR_PARAMETERS, $error);
        }

        $db_modeltypes = $this->get([
            'output' => ['modeltype', 'name'],
            'selectUsers' => ['userid'],
            'modeltypes' => $modeltypes,
            'preservekeys' => true
        ]);

        if (count($db_modeltypes) != count($modeltypes)) {
            self::exception(ZBX_API_ERROR_PERMISSIONS, _('No permissions to referred object or it does not exist!'));
        }

        DB::delete('model_type', ['modeltypeid' => $modeltypes]);

        $this->addAuditBulk(CAudit::ACTION_DELETE, CAudit::RESOURCE_MODELTYPE, $db_modeltypes);

        return ['modeltypes' => $modeltypes];
    }


    private function validateUpdate(array &$modeltypes,?array &$db_modeltypes): void {
        $api_input_rules = ['type' => API_OBJECTS, 'flags' => API_NOT_EMPTY | API_NORMALIZE, 'uniq' => [['name']], 'fields' => [
            'modeltypeid' =>			['type' => API_ID, 'flags' => API_REQUIRED],
            'physical_model' =>			['type' => API_STRING_UTF8],
            'company' =>			['type' => API_STRING_UTF8],
            'series' =>			['type' => API_STRING_UTF8],
            'spec' =>			['type' => API_STRING_UTF8],
            'max_memory_count' =>			['type' => API_INT32],
            'description' =>			['type' => API_STRING_UTF8],
            'manufacturerid' =>			['type' => API_INT32],
            'groupid' =>			['type' => API_INT32],
            'unumber' =>			['type' => API_INT32],
            'max_power_consumption' =>			['type' => API_INT32],
            'rated_power_consumption' =>			['type' => API_INT32],
            'peak_power' =>			['type' => API_INT32],
            'weight' =>			['type' => API_INT32],
            'service_period' =>			['type' => API_INT32],
            'update_time' =>			['type' => API_INT32],
            'create_time' =>			['type' => API_INT32],
            'structure' =>			['type' => API_INT32],
        ]];


        $group = API::HostGroup()->get(['groupids'=>$modeltypes['groupid']]);
        if(!$group){
            self::exception(ZBX_API_ERROR_PARAMETERS, '该设备类型不存在');
        }

        $manufacturer = API::Manufacturer()->get(['manufacturerids'=>$modeltypes['manufacturerid']]);
        if(!$manufacturer){
            self::exception(ZBX_API_ERROR_PARAMETERS, '该厂商不存在');
        }

        if(isset($modeltypes['name'])){
            $data = API::ModelType()->get(['search'=>['physical_model'=>$modeltypes['physical_model']],'searchWildcardsEnabled'=>true]);

            foreach($data as $v){
                if($v['modeltypeid'] != $modeltypes['modeltypeid'] && $v['groupid'] == $modeltypes['groupid'] && $v['physical_model'] == $modeltypes['physical_model']){
                    self::exception(ZBX_API_ERROR_PARAMETERS, '该设备型号名称已存在');
                }
            }
        }


        if (!CApiInputValidator::validate($api_input_rules, $modeltypes, '/', $error)) {
            self::exception(ZBX_API_ERROR_PARAMETERS, $error);
        }

        $db_modeltypes = $this->get([
            'output' => ['modeltype', 'name','userid','usrgrpid'],
            'modeltypes' => array_column($modeltypes, 'modeltype'),
            'preservekeys' => true
        ]);

        if (count($db_modeltypes) != count($modeltypes)) {
            self::exception(ZBX_API_ERROR_PERMISSIONS, _('No permissions to referred object or it does not exist!'));
        }
    }

    private function validateCreate(array &$modeltype): void {
        $api_input_rules = ['type' => API_OBJECTS, 'flags' => API_NOT_EMPTY | API_NORMALIZE, 'uniq' => [['name']], 'fields' => [
            'physical_model' =>			['type' => API_STRING_UTF8, 'flags' => API_REQUIRED | API_NOT_EMPTY],
            'company' =>			['type' => API_STRING_UTF8],
            'series' =>			['type' => API_STRING_UTF8],
            'spec' =>			['type' => API_STRING_UTF8],
            'max_memory_count' =>			['type' => API_INT32],
            'description' =>			['type' => API_STRING_UTF8],
            'manufacturerid' =>			['type' => API_INT32, 'flags' => API_REQUIRED | API_NOT_EMPTY],
            'groupid' =>			['type' => API_INT32, 'flags' => API_REQUIRED | API_NOT_EMPTY],
            'unumber' =>			['type' => API_INT32, 'flags' => API_REQUIRED | API_NOT_EMPTY],
            'max_power_consumption' =>			['type' => API_INT32],
            'rated_power_consumption' =>			['type' => API_INT32],
            'peak_power' =>			['type' => API_INT32],
            'weight' =>			['type' => API_INT32],
            'service_period' =>			['type' => API_INT32],
            'update_time' =>			['type' => API_INT32],
            'create_time' =>			['type' => API_INT32],
            'structure' =>			['type' => API_INT32]
        ]
        ];

        $group = API::HostGroup()->get(['groupids'=>$modeltype['groupid']]);

        if(!$group){
            self::exception(ZBX_API_ERROR_PARAMETERS, '该设备类型不存在');
        }

        $manufacturer = API::Manufacturer()->get(['manufacturerids'=>$modeltype['manufacturerid']]);

        if(!$manufacturer){
            self::exception(ZBX_API_ERROR_PARAMETERS, '该厂商不存在');
        }

        $data = API::ModelType()->get(['search'=>['physical_model'=>$modeltype['physical_model']],'searchWildcardsEnabled'=>true]);

        if($data){
            self::exception(ZBX_API_ERROR_PARAMETERS, '该设备型号名称已存在');
        }

        if (!CApiInputValidator::validate($api_input_rules, $modeltype, '/', $error)) {
            self::exception(ZBX_API_ERROR_PARAMETERS, $error);
        }
    }

    protected function applyQueryOutputOptions($tableName, $tableAlias, array $options, array $sqlParts) {
        $sqlParts = parent::applyQueryOutputOptions($tableName, $tableAlias, $options, $sqlParts);

        return $sqlParts;
    }

    protected function addRelatedObjects(array $options, array $result) {
        $result = parent::addRelatedObjects($options, $result);

        if ($options['selectManufacturer'] !== null) {
            $manufacturerids = array_column($result,'manufacturerid');
            $manufacturerArr = API::Manufacturer()->get([
                'manufacturerids' => $manufacturerids,
                'output' => $options['selectManufacturer'] == "count" ? "extend" : $options['selectManufacturer'],
            ]);

            foreach($result as $k => $v){
                $result[$k]['manufacturer'] = "";
                foreach($manufacturerArr as  $manu){

                    if($manu['manufacturerid'] == $v['manufacturerid']){
                        $result[$k]['manufacturer'] = $manu;
                    }
                }

               if($options['selectManufacturer'] == "count"){
                    $result[$k]['manufacturer'] = count($result[$k]['manufacturer']);
                }
            }
        }

        if ($options['selectHostGroup'] !== null) {
            $groupids = array_column($result,'groupid');
            $hostGroupArr = API::HostGroup()->get([
                'groupids' => $groupids,
                'output' => $options['selectHostGroup'] == "count" ? "extend" : $options['selectHostGroup'],
            ]);


            foreach($result as $k => $v){
                $result[$k]['hostgroup'] = "";
                foreach($hostGroupArr as  $group){

                    if($group['groupid'] == $v['groupid']){
                        $result[$k]['hostgroup'] = $group;
                    }
                }

                if($options['selectHostGroup'] == "count"){
                    $result[$k]['hostgroup'] = count($result[$k]['hostgroup']);
                }
            }
        }

        return $result;
    }
}
