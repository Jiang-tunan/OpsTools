<?php
// Zabbix GUI configuration file.

$DB['TYPE']			= 'MYSQL';
$DB['SERVER']			= 'localhost';
$DB['PORT']			= '0';
$DB['DATABASE']			= 'zops';
$DB['USER']			= 'zops';
$DB['PASSWORD']			= 'Zbx@666888';

// Schema name. Used for PostgreSQL.
$DB['SCHEMA']                   = '';
// Used for TLS connection.
$DB['ENCRYPTION']		= false;
$DB['KEY_FILE']			= '';
$DB['CERT_FILE']		= '';
$DB['CA_FILE']			= '';
$DB['VERIFY_HOST']		= false;
$DB['CIPHER_LIST']		= '';

// Vault configuration. Used if database credentials are stored in Vault secrets manager.
$DB['VAULT']			= '';
$DB['VAULT_URL']		= '';
$DB['VAULT_DB_PATH']		= '';
$DB['VAULT_TOKEN']		= '';
$DB['VAULT_CERT_FILE']		= '';
$DB['VAULT_KEY_FILE']		= '';
// Uncomment to bypass local caching of credentials.
// $DB['VAULT_CACHE']		= true;

// Use IEEE754 compatible value range for 64-bit Numeric (float) history values.
// This option is enabled by default for new Zabbix installations.
// For upgraded installations, please read database upgrade notes before enabling this option.
$DB['DOUBLE_IEEE754']		= true;

$MEDIATYPE["mail_template"] = "<span>尊敬的用户，</span><br/><span>这是一封测试邮件，用于测试您设置的邮件通知功能是否可用。如果您能收到此邮件，则表示通知功能已成功配置。</span><br/><span>谢谢您使用我们的零点运维管理平台。</span><br/><span>此致，</span><br/><span>零点运维团队</span>";

$MEDIATYPE['script_wecom'] = "var wecom = {\r\n\turl: null,\r\n\tmessage: null,\r\n\tmsgtype: \"markdown\",\r\n\tproxy: null,\r\n\tsendMessage: function () {\r\n\t\tvar params = {\r\n\t\t\tmsgtype: wecom.msgtype,\r\n\t\t\tmarkdown: {\r\n\t\t\t\tcontent: wecom.message,\r\n\t\t\t},\r\n\t\t},\r\n\t\tdata,\r\n\t\tresponse,\r\n\t\trequest = new HttpRequest(),\r\n\t\turl = wecom.url;\r\n\t\tif (wecom.proxy) {\r\n\t\t\trequest.setProxy(wecom.proxy);\r\n\t\t}\r\n\t\trequest.addHeader(\"Content-Type: application/json\");\r\n\t\tdata = JSON.stringify(params);\r\n\t\t// Remove replace() function if you want to see the exposed key in the log file.\r\n\t\tZabbix.Log(4,\"[wecom Webhook] URL: \" + url);\r\n\t\tZabbix.Log(4, \"[wecom Webhook] params: \" + data);\r\n\t\tresponse = request.post(url, data);\r\n\t\tZabbix.Log(4, \"[wecom Webhook] HTTP code: \" + request.getStatus());\r\n\t\ttry {\r\n\t\t\tresponse = JSON.parse(response);\r\n\t\t}\r\n\t\tcatch (error) {\r\n\t\t\tthrow \"JSON.parse error. response: \" + response;\r\n\t\t}\r\n\t\t\r\n\t\tif (request.getStatus() !== 200 || response.errcode !== 0) {\r\n\t\t\tif (typeof response.errmsg === \"string\") {\r\n\t\t\t\tthrow response.errmsg;\r\n\t\t\t}\r\n\t\t\telse {\r\n\t\t\t\tthrow \"Unknown error. Check debug log for more information.\";\r\n\t\t\t}\r\n\t\t}\r\n\t},\r\n};\r\ntry {\r\n\tvar params = JSON.parse(value);\r\n\tif (typeof params.URL === \"undefined\") {\r\n\t\tthrow 'Incorrect value is given for parameter \"URL\": parameter is missing';\r\n\t}\r\n\twecom.url = params.URL;\r\n\tif (params.HTTPProxy) {\r\n\t\twecom.proxy = params.HTTPProxy;\r\n\t}\r\n\twecom.message = params.Subject + \"\\n\" + params.Message;\r\n\twecom.sendMessage();\r\n\treturn \"OK\";\r\n}\r\ncatch (error) {\r\n\tZabbix.Log(4, \"[wecom Webhook] notification failed: \" + error);\r\n\tthrow \"Sending failed: \" + error + \".\";\r\n}";
$MEDIATYPE["script_dingding"] = "var dingding = {\r\n url: null,\r\n subject: null,\r\n message: null,\r\n msgtype: \"markdown\",\r\n proxy: null,\r\n sendMessage: function () {\r\n var params = {\r\n \"msgtype\": dingding.msgtype,\r\n \"markdown\": {\r\n \"title\": dingding.subject,\r\n \"text\": dingding.message,\r\n }\r\n },\r\n data,\r\n response,\r\n request = new HttpRequest(),\r\n url = dingding.url;\r\n if (dingding.proxy) {\r\n request.setProxy(dingding.proxy);\r\n }\r\n request.addHeader(\"Content-Type: application/json\");\r\n data = JSON.stringify(params);\r\n data = data.replace(/\\\\\\\\n/g, \"\\\\n\");\r\n // Remove replace() function if you want to see the exposed key in the log file.\r\n Zabbix.Log(4,\"[dingding Webhook] URL: \" + url);\r\n Zabbix.Log(4, \"[dingding Webhook] params: \" + data);\r\n response = request.post(url, data);\r\n Zabbix.Log(4, \"[dingding Webhook] HTTP code: \" + request.getStatus());\r\n try {\r\n response = JSON.parse(response);\r\n }\r\n catch (error) {\r\n throw \"JSON.parse error. response: \" + response;\r\n }\r\n if (request.getStatus() !== 200 || response.errcode !== 0) {\r\n if (typeof response.errmsg === \"string\") {\r\n throw response.errmsg;\r\n }\r\n else {\r\n throw \"Unknown error. Check debug log for more information.\";\r\n }\r\n }\r\n },\r\n};\r\ntry {\r\n var params = JSON.parse(value);\r\n if (typeof params.URL === \"undefined\") {\r\n throw 'Incorrect value is given for parameter \"URL\": parameter is missing';\r\n }\r\n dingding.url = params.URL;\r\n if (params.HTTPProxy) {\r\n dingding.proxy = params.HTTPProxy;\r\n }\r\n dingding.subject = params.Subject;\r\n dingding.message = params.Subject + \"\\n\\n\" + params.Message;\r\n dingding.sendMessage();\r\n return \"OK\";\r\n}\r\ncatch (error) {\r\n Zabbix.Log(4, \"[dingding Webhook] notification failed: \" + error);\r\n throw \"Sending failed: \" + error + \".\";\r\n}";

$MEDIATYPE['subject_wecom'] = '<font color="warning">{HOST.NAME} {EVENT.NAME}</font>';
$MEDIATYPE['subject_dingding'] = '<font color="Red">{HOST.NAME} {EVENT.NAME}</font>';

$MEDIATYPE['message_wecom'] = '>主机名称：<font color="comment">{HOST.NAME}</font>\n\n>主机IP：<font color="comment">{HOST.IP}</font>\n\n>告警时间：<font color="comment">{EVENT.DATE} {EVENT.TIME}</font>\n\n>严重程度：<font color="warning">{EVENT.SEVERITY}</font>\n\n>问题名称：<font color="warning">{EVENT.NAME}</font>\n\n>问题详情：<font color="comment">{ITEM.NAME}:{ITEM.VALUE}</font>\n\n>目前状态：<font color="comment">{TRIGGER.STATUS}</font>\n\n>事件ID：<font color="comment">{EVENT.ID}</font>\n\n';
$MEDIATYPE['message_dingding'] = '>主机名称：<font color="Blue">{HOST.NAME}</font>\n\n>主机IP：<font color="Blue">{HOST.IP}</font>\n\n>告警时间：<font color="Blue">{EVENT.DATE} {EVENT.TIME}</font>\n\n>严重程度：<font color="Red">{EVENT.SEVERITY}</font>\n\n>问题名称：<font color="Red">{EVENT.NAME}</font>\n\n>问题详情：<font color="Blue">{ITEM.NAME}:{ITEM.VALUE}</font>\n\n>目前状态：<font color="Blue">{TRIGGER.STATUS}</font>\n\n>事件ID：<font color="Blue">{EVENT.ID}</font>\n\n';

// Uncomment and set to desired values to override Zabbix hostname/IP and port.
// $ZBX_SERVER			= '';
// $ZBX_SERVER_PORT		= '';

$ZBX_SERVER_NAME		= 'zops';

$IMAGE_FORMAT_DEFAULT	= IMAGE_FORMAT_PNG;

// Uncomment this block only if you are using Elasticsearch.
// Elasticsearch url (can be string if same url is used for all types).
//$HISTORY['url'] = [
//	'uint' => 'http://localhost:9200',
//	'text' => 'http://localhost:9200'
//];
// Value types stored in Elasticsearch.
//$HISTORY['types'] = ['uint', 'text'];

// Used for SAML authentication.
// Uncomment to override the default paths to SP private key, SP and IdP X.509 certificates, and to set extra settings.
//$SSO['SP_KEY']			= 'conf/certs/sp.key';
//$SSO['SP_CERT']			= 'conf/certs/sp.crt';
//$SSO['IDP_CERT']		= 'conf/certs/idp.crt';
//$SSO['SETTINGS']		= [];
