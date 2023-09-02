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
class CInventoryType extends CApiService {

    public const ACCESS_RULES = [
        'get' => ['min_user_type' => USER_TYPE_ZABBIX_USER],
        'create' => ['min_user_type' => USER_TYPE_SUPER_ADMIN],
        'update' => ['min_user_type' => USER_TYPE_ZABBIX_USER],
        'delete' => ['min_user_type' => USER_TYPE_SUPER_ADMIN]
    ];

    protected $tableName = 'inventory_type';
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
            'select'	=> ['inventory_type' => 'i.inventory_typeid'],
            'from'		=> ['inventory_type' => 'inventory_type i'],
            'where'		=> [],
            'order'		=> [],
            'limit'		=> null
        ];

        $defOptions = [
            'name'          => null,
            'output'		=> API_OUTPUT_EXTEND,
            'countOutput'	=> false,
            'preservekeys'	=> false,
            'selectHosts'	=> null,
            'fid'           => null,
            'fids'          => null,
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

        if ($options['inventory_typeids'] !== null) {
            zbx_value2array($options['inventory_typeids']);

            $sqlParts['where'][] = dbConditionInt('i.inventory_typeid', $options['inventory_typeids']);
        }

        if ($options['fids'] !== null) {
            zbx_value2array($options['fids']);

            $sqlParts['where'][] = 'find_in_set("'.implode(',',$options['fids']).'", fids)';
        }

        if ($options['fid'] !== null) {
            zbx_value2array($options['fid']);

            $sqlParts['where'][] = dbConditionInt('i.fid', $options['fid']);
        }

        if (is_array($options['search'])) {
            zbx_db_search('inventory_type i', $options, $sqlParts);
        }

        $sqlParts = $this->applyQueryOutputOptions($this->tableName(), $this->tableAlias(), $options, $sqlParts);
        $sqlParts = $this->applyQuerySortOptions($this->tableName(), $this->tableAlias(), $options, $sqlParts);

        $dbRes = DBselect(self::createSelectQueryFromParts($sqlParts), $sqlParts['limit'],$options['current'] ? ($options['current'] - 1) * $sqlParts['limit'] : 0);

        while ($item = DBfetch($dbRes)) {
            if ($options['countOutput']) {
                $result = $item['rowscount'];
            }
            else {
                $item['total'] = API::getTotal('inventorytype',$options);

                $item['all'] = API::getTotal('inventorytype',[]);

                if(isset($item['fids'])){
                    $item['fathers'] = explode(',',$item['fids']);
                }

                if ($options['preservekeys']) {
                    $result[$item['inventory_typeid']] = $item;
                }else{
                    $result[] = $item;
                }
            }
        }

        if ($options['countOutput']) {
            return $result;
        }

        if ($result && !$options['isson']) {
            $result = $this->addRelatedObjects($options, $result);
        }

        return $result;
    }

    public function create(array $inventorys): array {
        $this->validateCreate($inventorys);
        $ins_inventory_type = [];

        foreach ($inventorys as $inventory) {
            $inventory['update_time'] = $inventory['create_time'] = time();
            $inventory['fids'] = 0;


            if($inventory['fid'] > 0){
                $farr = $this->checkFather($inventory['fid']);

                $fids = [];
                foreach($farr as $v){
                    if(isset($v[0])){
                        array_push($fids,$v[0]['inventory_typeid']);
                    }
                }

                $inventory['fids'] = implode(',',$fids);
                if($farr[4]) self::exception(ZBX_API_ERROR_PARAMETERS, '最多只能创建五层树结构');
            }

            $ins_inventory_type[] = $inventory;
        }

        $inventory_typeids = DB::insert('inventory_type', $ins_inventory_type,true);

        $this->addAuditBulk(CAudit::ACTION_ADD, CAudit::RESOURCE_INVENTORYTYPE, $inventorys);

        return ['inventory_typeids' =>  $inventory_typeids];
    }

    //查出往上四层的父id
    public function checkFather($id){
        $fid2 = $fid3 = $fid4 =[];
        $fid1 = $this->get(['inventory_typeids'=>[$id],'output'=>['inventory_typeid','fid','name']]);

        if($fid1){
            $fid2 = $this->get(['inventory_typeids'=>array_column($fid1,"fid"),'output'=>['inventory_typeid','fid','name']]);
        }

        if($fid1 && $fid2){
            $fid3 = $this->get(['inventory_typeids'=>array_column($fid2,"fid"),'output'=>['inventory_typeid','fid','name']]);
        }

        if($fid1 && $fid2 && $fid3){
            $fid4 = $this->get(['inventory_typeids'=>array_column($fid3,"fid"),'output'=>['inventory_typeid','fid','name']]);
        }

        if($fid1 && $fid2 && $fid3 && $fid4){
            $fid5 = $this->get(['inventory_typeids'=>array_column($fid4,"fid"),'output'=>['inventory_typeid','fid','name']]);
        }

        return [$fid1,$fid2,$fid3,$fid4,$fid5];
    }

    public function update(array $inventorys): array {
        if (self::$userData['type'] != USER_TYPE_SUPER_ADMIN) {
            self::exception(ZBX_API_ERROR_PERMISSIONS,
                _s('No permissions to call "%1$s.%2$s".', 'role', __FUNCTION__)
            );
        }

        $this->validateUpdate($inventorys, $db_manus);

        $$inventorys = [];

        foreach ($inventorys as $inventory) {
            $upd_inventory_type = DB::getUpdatedValues('inventory_type', $inventory, $db_manus[$inventory['inventory_typeid']]);
            $upd_inventory_type['fids'] = 0;
            if($inventory['fid'] > 0){
                $farr = $this->checkFather($inventory['fid']);

                $fids = [];
                foreach($farr as $v){
                    if(isset($v[0])){
                        array_push($fids,$v[0]['inventory_typeid']);
                    }
                }
                $upd_inventory_type['fids'] = implode(',',$fids);
                if($farr[4]) self::exception(ZBX_API_ERROR_PARAMETERS, '最多只能创建五层树结构');
            }

            if ($upd_inventory_type) {
                $upd_inventory_type['update_time'] = time();

                $$inventorys[] = [
                    'values' => $upd_inventory_type,
                    'where' => ['inventory_typeid' => $inventory['inventory_typeid']]
                ];
            }
        }

        if ($$inventorys) {
            DB::update('inventory_type', $$inventorys);
        }

        $this->addAuditBulk(CAudit::ACTION_UPDATE, CAudit::RESOURCE_INVENTORYTYPE, $inventorys, $db_manus);

        return ['inventory_typeids' => array_column($inventorys, 'inventory_typeid')];
    }

    public function delete(array $inventorys): array {
        if (self::$userData['type'] != USER_TYPE_SUPER_ADMIN) {
            self::exception(ZBX_API_ERROR_PERMISSIONS,
                _s('No permissions to call "%1$s.%2$s".', 'role', __FUNCTION__)
            );
        }

        $api_input_rules = ['type' => API_IDS, 'flags' => API_NOT_EMPTY, 'uniq' => true];

        if (!CApiInputValidator::validate($api_input_rules, $inventorys, '/', $error)) {
            self::exception(ZBX_API_ERROR_PARAMETERS, $error);
        }

        $db_inventory = $this->get([
            'output' => ['inventory_typeid', 'name'],
            'inventory_typeids' => $inventorys,
            'preservekeys' => true
        ]);

        if (count($db_inventory) != count($inventorys)) {
            self::exception(ZBX_API_ERROR_PERMISSIONS, _('No permissions to referred object or it does not exist!'));
        }


        //删除子元素
        foreach($inventorys as $v){
            $sons = $this->get([
                'output' => ['inventory_typeid', 'name'],
                'fids' => $v,
                'preservekeys' => true
            ]);

            DB::delete('inventory_type', ['inventory_typeid' => array_column($sons,'inventory_typeid')]);
        }


        DB::delete('inventory_type', ['inventory_typeid' => $inventorys]);

        $this->addAuditBulk(CAudit::ACTION_DELETE, CAudit::RESOURCE_INVENTORYTYPE, $db_inventory);

        return ['inventory_typeids' => $inventorys];
    }


    private function validateUpdate(array &$inventorys,?array &$db_manus): void {
        $api_input_rules = ['type' => API_OBJECTS, 'flags' => API_NOT_EMPTY | API_NORMALIZE, 'uniq' => [['name']], 'fields' => [
            'inventory_typeid' =>			['type' => API_ID, 'flags' => API_REQUIRED],
            'name' =>			['type' => API_STRING_UTF8],
            'description' =>			['type' => API_STRING_UTF8],
            'fid' =>			['type' => API_INT32]
        ]];

        $data = API::InventoryType()->get(['search'=>['name'=>$inventorys['name']],'searchWildcardsEnabled'=>true]);

        foreach($data as $v){
            if($v['name'] == $inventorys['name'] && $v['inventory_typeid'] != $inventorys['inventory_typeid']){
                self::exception(ZBX_API_ERROR_PARAMETERS, '该资产类型已存在');
            }
        }

        if (!CApiInputValidator::validate($api_input_rules, $inventorys, '/', $error)) {
            self::exception(ZBX_API_ERROR_PARAMETERS, $error);
        }

        $db_manus = $this->get([
            'output' => ['inventory_typeid', 'name','userid','usrgrpid'],
            'inventory_typeids' => array_column($inventorys, 'inventory_typeid'),
            'preservekeys' => true
        ]);

        if (count($db_manus) != count($inventorys)) {
            self::exception(ZBX_API_ERROR_PERMISSIONS, _('No permissions to referred object or it does not exist!'));
        }
    }

    private function validateCreate(array &$inventory): void {
        $api_input_rules = ['type' => API_OBJECTS, 'flags' => API_NOT_EMPTY | API_NORMALIZE, 'uniq' => [['name']], 'fields' => [
            'name' =>			['type' => API_STRING_UTF8, 'flags' => API_REQUIRED | API_NOT_EMPTY],
            'description' =>			['type' => API_STRING_UTF8],
            'fid' =>			['type' => API_INT32, 'flags' => API_REQUIRED | API_NOT_EMPTY]
        ]
        ];

        $data = API::InventoryType()->get(['search'=>['name'=>$inventory['name']],'searchWildcardsEnabled'=>true]);

        if($data){
            self::exception(ZBX_API_ERROR_PARAMETERS, '该资产类型已存在');
        }

        if (!CApiInputValidator::validate($api_input_rules, $inventory, '/', $error)) {
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

        $sons = $this->get(['fid'=>array_column($result,'inventory_typeid'),'output'=>['inventory_typeid','fid'],'isson'=>true]);


        foreach($result as $k => $inventory){
            $result[$k]['hasson'] = false;
            foreach ($sons as $son){

                if($inventory['inventory_typeid'] == $son['fid']){
                    $result[$k]['hasson'] = true;
                }
            }
        }

        if ($options['selectHosts']) {
            $hosts = API::Host()->get([
                'output' => ['username','userid'],
                'inventory_typeids' => array_column('inventory_typeid',$result),
            ]);

            foreach($hosts as $v){
                foreach($result as &$vv){
                    if($vv['inventory_typeid'] == $v['inventory_typeid']){
                        $vv['hosts'] = $v;
                    }
                }
            }
        }

        return $result;
    }
}
