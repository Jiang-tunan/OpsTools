import unittest
from app.modify_conf import modify_conf
class TestModifyConf(unittest.TestCase):

    def test_parse_query_string(self):
        test_string = "key1=new_value1&key2=new_value2"
        result = modify_conf.parse_query_string(test_string)
        expected_result = {'key1': ['new_value1'], 'key2': ['new_value2']}
        self.assertEqual(result, expected_result)

    def test_modify_conf_file(self):
        test_file = "E:/project\python\Version-upgrade/tests/data/test_configure.conf"
        params = {'key1': ['new_value1'], 'key2': ['new_value2']}
        modify_conf.modify_conf_file(test_file, params)

        # 重新读取文件，查看修改是否成功
        with open(test_file, 'r') as file:
            lines = file.readlines()
        self.assertEqual(lines[0], "key1 = new_value1\n")
        self.assertEqual(lines[1], "key2 = new_value2\n")

if __name__ == '__main__':
    unittest.main()
