import os
import unittest
from app.modify_profile.modify_conf import modify_conf_file
from app.modify_profile.modify_conf import parse_query_string

# 设定测试数据路径和输出路径
DATA_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', 'data')
OUTPUTS_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', 'outputs')
class TestQueryStringParsing(unittest.TestCase):

    def test_parse_query_string(self):
        test_query = "?DBName=zabbix_v2&DBUser=zabbix_v2&DBPassword=zbx12345."
        expected_result = {
            "DBName": "zabbix_v2",
            "DBUser": "zabbix_v2",
            "DBPassword": "zbx12345"
        }
        print(parse_query_string(test_query))
        self.assertEqual(parse_query_string(test_query), expected_result)

    # def test_empty_query_string(self):
    #     test_query = ""
    #     expected_result = {}
    #
    #     self.assertEqual(parse_and_validate_query_string(test_query), expected_result)
    #
    # def test_single_parameter(self):
    #     test_query = "DBName=zabbix_v2"
    #     expected_result = {"DBName": "zabbix_v2"}
    #
    #     self.assertEqual(parse_and_validate_query_string(test_query), expected_result)

class TestModifyConf(unittest.TestCase):
    def test_modify_conf(self):
        test_file = os.path.join(DATA_DIR, 'zops_server.conf')
        params = {
            "DBName": "ZOOOOOPPSS",
            "DBUser": "ZOOOOPSDSSD",
            "DBPassword": "J999999999999"
        }
        modify_conf_file(test_file, params, OUTPUTS_DIR)

        # 以下可以添加更多的断言，验证outputs目录下的文件内容是否如您所期望

if __name__ == '__main__':
    unittest.main()
