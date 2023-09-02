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
class CManufacturer extends CApiService {

    public const ACCESS_RULES = [
        'get' => ['min_user_type' => USER_TYPE_ZABBIX_USER],
        'create' => ['min_user_type' => USER_TYPE_SUPER_ADMIN],
        'update' => ['min_user_type' => USER_TYPE_ZABBIX_USER],
        'delete' => ['min_user_type' => USER_TYPE_SUPER_ADMIN]
    ];

    protected $tableName = 'manufacturer';
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
            'select'	=> ['manufacturer' => 'i.manufacturerid'],
            'from'		=> ['manufacturer' => 'manufacturer i'],
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

        if ($options['manufacturerids'] !== null) {
            zbx_value2array($options['manufacturerids']);

            $sqlParts['where'][] = dbConditionInt('i.manufacturerid', $options['manufacturerids']);
        }

        if (is_array($options['search'])) {
            zbx_db_search('manufacturer i', $options, $sqlParts);
        }

        $sqlParts = $this->applyQueryOutputOptions($this->tableName(), $this->tableAlias(), $options, $sqlParts);
        $sqlParts = $this->applyQuerySortOptions($this->tableName(), $this->tableAlias(), $options, $sqlParts);

        $dbRes = DBselect(self::createSelectQueryFromParts($sqlParts), $sqlParts['limit'],$options['current'] ? ($options['current'] - 1) * $sqlParts['limit'] : 0);

        while ($item = DBfetch($dbRes)) {
            if ($options['countOutput']) {
                $result = $item['rowscount'];
            }
            else {
                $item['showChinese'] = $item['showChinese'] == 1 ? true : false;
                $item['isDomestic'] = $item['isDomestic'] == 1 ? true : false;

                $item['total'] = API::getTotal('manufacturer',$options);
                if ($options['preservekeys']) {
                    $result[$item['manufacturerid']] = $item;
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

    public function create(array $manus): array {
        $manus['showChinese'] = $manus['showChinese'] ? 1 : 0;
        $manus['isDomestic'] = $manus['isDomestic'] ? 1 : 0;


        $this->validateCreate($manus);
        $ins_manufacturer = [];

        foreach ($manus as $manu) {
            $manu['clock'] = time();
            $ins_manufacturer[] = $manu;
        }

        $manufacturerids = DB::insert('manufacturer', $ins_manufacturer,true);

        $this->addAuditBulk(CAudit::ACTION_ADD, CAudit::RESOURCE_MANUFACTURER, $manus);

        return ['manufacturerids' =>  $manufacturerids];
    }

    public function update(array $manus): array {
        if (self::$userData['type'] != USER_TYPE_SUPER_ADMIN) {
            self::exception(ZBX_API_ERROR_PERMISSIONS,
                _s('No permissions to call "%1$s.%2$s".', 'role', __FUNCTION__)
            );
        }

        $manus['showChinese'] = $manus['showChinese'] ? 1 : 0;
        $manus['isDomestic'] = $manus['isDomestic'] ? 1 : 0;
        $this->validateUpdate($manus, $db_manus);

        $upd_manus = [];

        foreach ($manus as $manu) {
            $upd_manufacturer = DB::getUpdatedValues('manufacturer', $manu, $db_manus[$manu['manufacturerid']]);

            if ($upd_manufacturer) {
                $upd_manus[] = [
                    'values' => $upd_manufacturer,
                    'where' => ['manufacturerid' => $manu['manufacturerid']]
                ];
            }
        }

        if ($upd_manus) {
            DB::update('manufacturer', $upd_manus);
        }

        $this->addAuditBulk(CAudit::ACTION_UPDATE, CAudit::RESOURCE_MANUFACTURER, $manus, $db_manus);

        return ['manufacturerids' => array_column($manus, 'manufacturerid')];
    }

    public function delete(array $manus): array {
        if (self::$userData['type'] != USER_TYPE_SUPER_ADMIN) {
            self::exception(ZBX_API_ERROR_PERMISSIONS,
                _s('No permissions to call "%1$s.%2$s".', 'role', __FUNCTION__)
            );
        }

        $api_input_rules = ['type' => API_IDS, 'flags' => API_NOT_EMPTY, 'uniq' => true];

        if (!CApiInputValidator::validate($api_input_rules, $manus, '/', $error)) {
            self::exception(ZBX_API_ERROR_PARAMETERS, $error);
        }

        $db_manus = $this->get([
            'output' => ['manufacturerid', 'name'],
            'selectUsers' => ['userid'],
            'manufacturerids' => $manus,
            'preservekeys' => true
        ]);

        if (count($db_manus) != count($manus)) {
            self::exception(ZBX_API_ERROR_PERMISSIONS, _('No permissions to referred object or it does not exist!'));
        }

        DB::delete('manufacturer', ['manufacturerid' => $manus]);

        $this->addAuditBulk(CAudit::ACTION_DELETE, CAudit::RESOURCE_MANUFACTURER, $db_manus);

        return ['manufacturerids' => $manus];
    }


    private function validateUpdate(array &$manus,?array &$db_manus): void {
        $api_input_rules = ['type' => API_OBJECTS, 'flags' => API_NOT_EMPTY | API_NORMALIZE, 'uniq' => [['name']], 'fields' => [
            'manufacturerid' =>			['type' => API_ID, 'flags' => API_REQUIRED],
            'name' =>			['type' => API_STRING_UTF8],
            'company' =>			['type' => API_STRING_UTF8],
            'chinesename' =>			['type' => API_STRING_UTF8],
            'showChinese' =>			['type' => API_INT32],
            'isDomestic' =>			['type' => API_INT32],
            'website' => ['type' => API_STRING_UTF8]
        ]];

        $data = API::Manufacturer()->get(['search'=>['name'=>$manus['name']],'searchWildcardsEnabled'=>true]);

        if(isset($manus['name'])){
            foreach($data as $k => $v){
                if($v['manufacturerid'] != $manus['manufacturerid'] && $manus['name'] == $v['name']){
                    self::exception(ZBX_API_ERROR_PARAMETERS, '该厂商名称已存在');
                }
            }
        }



        if (!CApiInputValidator::validate($api_input_rules, $manus, '/', $error)) {
            self::exception(ZBX_API_ERROR_PARAMETERS, $error);
        }

        $db_manus = $this->get([
            'output' => ['manufacturerid', 'name','userid','usrgrpid'],
            'manufacturerids' => array_column($manus, 'manufacturerid'),
            'preservekeys' => true
        ]);

        if (count($db_manus) != count($manus)) {
            self::exception(ZBX_API_ERROR_PERMISSIONS, _('No permissions to referred object or it does not exist!'));
        }
    }

    private function validateCreate(array &$manu): void {
        $api_input_rules = ['type' => API_OBJECTS, 'flags' => API_NOT_EMPTY | API_NORMALIZE, 'uniq' => [['name']], 'fields' => [
            'name' =>			['type' => API_STRING_UTF8, 'flags' => API_REQUIRED | API_NOT_EMPTY],
            'company' =>			['type' => API_STRING_UTF8, 'flags' => API_REQUIRED | API_NOT_EMPTY],
            'showChinese' =>			['type' => API_INT32],
            'isDomestic' =>			['type' => API_INT32],
            'website' => ['type' => API_STRING_UTF8],
            'chinesename' =>			['type' => API_STRING_UTF8]
        ]
        ];

        $data = API::Manufacturer()->get(['search'=>['name'=>$manu['name']],'searchWildcardsEnabled'=>true]);

        if($data){
            self::exception(ZBX_API_ERROR_PARAMETERS, '该厂商名称已存在');
        }

        if (!CApiInputValidator::validate($api_input_rules, $manu, '/', $error)) {
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

        return $result;
    }
}
