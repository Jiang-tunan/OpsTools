import os
import unittest
from app.modify_profile.modify_conf import modify_conf_file
from app.modify_profile.modify_conf import parse_query_string
from app.modify_profile.modify_conf import modify_php_conf_file

# 设定测试数据路径和输出路径
DATA_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', 'data')
OUTPUTS_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', 'outputs')
class TestQueryStringParsing(unittest.TestCase):

    def test_parse_query_string(self):
        test_query = "?DBName=zabbix_v2&DBUser=zabbix_v2&DBPassword=zbx12345;"
        expected_result = {
            "DBName": "zabbix_v2",
            "DBUser": "zabbix_v2",
            "DBPassword": "zbx12345"
        }
        # print(parse_query_string(test_query))
        self.assertEqual(parse_query_string(test_query, file_type='.conf'), expected_result, msg="测试 用例1 失败")

    def test_empty_query_string(self):
        test_query = "?;"
        expected_result = {}
        # print(parse_query_string(test_query))
        self.assertEqual(parse_query_string(test_query, file_type='.conf'), expected_result, msg="Query string parsing failed.")

    def test_single_parameter(self):
        test_query = "?DBName=zabbix_v2;"
        expected_result = {"DBName": "zabbix_v2"}
        # print(parse_query_string(test_query))
        self.assertEqual(parse_query_string(test_query, file_type='.conf'), expected_result)

class TestModifyConf(unittest.TestCase):
    def test_modify_conf(self):
        test_file = os.path.join(DATA_DIR, 'zops_server.conf')
        params = {
            "DBName": "ZOOOOOPPSS",
            "DBUser": "ZOOOOPSDSSD",
            "DBPassword": "J999999999999"
        }
        modify_conf_file(test_file, params, OUTPUTS_DIR)

class TestModifyPHPConfFile(unittest.TestCase):

    def setUp(self):
        self.test_file_name = os.path.join(DATA_DIR, 'zabbix.conf.php')

    def test_valid_modifications(self):
        modify_string = "?DB.TYPE='NEW_MYSQL'&DB.SERVER='NEW_SERVER'&DB.PORT='NEW_PORT'&DB.DATABASE='NEW_DATABASE';"
        modify_php_conf_file(self.test_file_name, modify_string, OUTPUTS_DIR)

        # 检查修改后的输出
        modified_file_name = os.path.join(OUTPUTS_DIR, "test_zabbix.conf.php")
        with open(modified_file_name, 'r', encoding='utf-8') as f:
            modified_content = f.read()

        self.assertIn("$DB['TYPE'] = 'NEW_MYSQL';", modified_content)
        self.assertIn("$DB['SERVER'] = 'NEW_SERVER';", modified_content)
        self.assertIn("$DB['PORT'] = 'NEW_PORT';", modified_content)
        self.assertIn("$DB['DATABASE'] = 'NEW_DATABASE';", modified_content)
if __name__ == '__main__':
    unittest.main()
