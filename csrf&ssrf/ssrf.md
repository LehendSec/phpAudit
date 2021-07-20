# SSRF

#### 背景

```
SSRF(Server-Side Request Forgery:服务器端请求伪造) 是一种由攻击者构造形成由服务端发起请求的一个安全漏洞。有的大型网站在web应用上提供了从其他服务器获取数据的功能。使用户指定的URL web应用获取图片，下载文件，读取文件内容。攻击者利用有缺陷的web应用作为代理攻击远程和内网的服务器（跳板）。
```

#### 常用函数

```
file_get_contents
fsockopen
curl
```

##### file_get_contents

```
示例:
<?php
	if (isset($_POST['url'])) {
        //服务器通过使用file_get_contents这个函数获得了
        //某个ip地址对应的资源文件
        //读取数据流信息
        $content = file_get_contents($_POST['url']);
        $filename = './image/' . rand() . '.jpg';
        //写入数据路信息
        file_put_contents($filename, $content);
        echo $_POST['url'];
        $img = "<img src=\"" . $filename . "\"/>";
        echo $img;
}
```

##### fsockopen

```
fsockopen
$fp = fsockopen($host, intval($port), $errno, $errstr, 30);
fsockopen()将返回一个文件句柄，之后可以被其他文件类函数调用（例如：fgets()，fgetss()，fwrite()，fclose()还有feof()）。如果调用失败，将返回FALSE。

示例:
	<?php
        function GetFile($host, $port, $link)
        {
            //连接一个地址的某个端口号，先发送ｈｔｔｐ请求报，
            //然后再将接收到的内容进行返回
            //fsockopen()将返回一个文件句柄，之后可以被其他文件类函数调用（例如：fgets()，fgetss()，fwrite()，fclose()还有feof()）。如果调用失败，将返回FALSE。
            $fp = fsockopen($host, intval($port), $errno, $errstr, 30);
            //当$fp为false的时候，是不是此时!$fp就为true


            //建立连接是否成功，返回的是一个文件句柄 ，可以有对应的文件操作
            if (!$fp) {
                echo "$errstr (error number $errno) \n";
            } else {
                //模拟请求包
                $out = "GET $link HTTP/1.1\r\n";
                $out .= "Host: $host\r\n";
                $out .= "Connection: Close\r\n\r\n";
                $out .= "\r\n";
                /*
                 *
                 * $fwrite = fopen('1.txt', 'w+');
                 * fwrite($fwrite, 'hello world');
                 * */

                fwrite($fp, $out);
                $contents = '';
                //通过while循环逐行读取文件内容
                while (!feof($fp)) {
                    //fgets — 从文件指针中读取一行
                    $contents .= fgets($fp, 1024);
                }
                fclose($fp);
                return $contents;
            }
        }

        $host = $_GET['host'];
        $port = $_GET['port'];
        $link = $_GET['link'];
        echo GetFile($host, $port, $link);
```

##### curl

```
curl_init():初始化新的会话，返回curl句柄给curl对象
curl_setopt($curlobj, CURLOPT_POST, 0):如果CURLOPT_POST为0表示发送get请求，为1表示发送post请求
curl_exec():发送cur请求，并且将结果返回
注意curl函数不支持重定向功能,但是file_get_contents是支持重定向功能的,所以在进行绕过的时候curl函数是不支持短网址的方式绕过的
示例:
	<?php
        if (isset($_GET['url'])) {
            $link = $_GET['url'];
            //http://www.localhost.com/diy/ssrf/ssrfCurl.php?url=127.0.0.1:3306
            //可以访问内网服务以及对应端口
            //初始化新的会话，返回 cURL 句柄给$curlobj
            $curlobj = curl_init();

            //下面设置的CURLOPT_POST为0，代表发送get请求,为1表示发送post请求
            curl_setopt($curlobj, CURLOPT_POST, 0);

            //CURLOPT_url 表示设置需要进行ssrf获取的内网的URL 地址
            curl_setopt($curlobj, CURLOPT_URL, $link);
            //CURLOPT_RETURNTRANSFER 代表的是当调用curl_exec()函数时，将以字符串的形式返回结果
            curl_setopt($curlobj, CURLOPT_RETURNTRANSFER, 1);
            //使用curl发送请求，并且将返回包的结果赋值给$result，输出到页面
            $result = curl_exec($curlobj);
            curl_close($curlobj);
            echo $result;
        }
```

#### 常见防御

```
1.限制为http://www.xxx.com 域名
采用http基本身份认证的方式绕过。即@
http://www.xxx.com@www.xxc.com
2.限制请求IP不为内网地址
当不允许ip为内网地址时
（1）采取短网址绕过
（2）采取特殊域名
（3）采取进制转换
3.限制请求只为http协议
（1）采取302跳转
（2）采取短地址
```

#### SSRF绕过方式

```
(1)利用@（[http://example.com@127.0.0.1](http://example.com@127.0.0.1/)）；
@：http基本身份认证的方式绕过

(2)xip.io 绕过 

(3)利用短地址（http://dwz.cn/11SMa）；

(4)利用[::1]（http://[::1]:80/）；

(6) localhost
 
(5)利用进制转换

(6)利用DNS解析

(7)协议（Dict://、SFTP://、TFTP://、LDAP://、Gopher://）
```

##### @ 过滤绕过

```
@：http基本身份认证的方式绕过

http://www.baidu.com@10.10.10.10与http://10.10.10.10 请求是相同的


示例:
<?php
if (isset($_GET['url'])) {
    $link = $_GET['url'];
    //进行url检测,可以使用@绕过

    //eg:http://192.168.1.40/diy/ssrf/ssrfRaoGuo.php?url=www.localhost.com@www.baidu.com
    //注意curl函数不支持重定向功能 ,但是file_get_contents是支持重定向功能的
    preg_match('/www\.localhost\.com/',$link,$matches);
    if(!$matches){
        echo 'your url is wrong';
        die();
    }
    //初始化新的会话，返回 cURL 句柄给$curlobj
    $curlobj = curl_init();

    //下面设置的CURLOPT_POST为0，代表发送get请求,为1表示发送post请求
    curl_setopt($curlobj, CURLOPT_POST, 0);

    //CURLOPT_url 表示设置需要进行ssrf获取的内网的URL 地址
    curl_setopt($curlobj, CURLOPT_URL, $link);
    //CURLOPT_RETURNTRANSFER 代表的是当调用curl_exec()函数时，将以字符串的形式返回结果
    curl_setopt($curlobj, CURLOPT_RETURNTRANSFER, 1);
    //使用curl发送请求，并且将返回包的结果赋值给$result，输出到页面
    $result = curl_exec($curlobj);
    curl_close($curlobj);
    echo $result;
}
```

##### 短网址绕过

```
限制指定域名或者指定的内网ip
	可以使用 短网址绕过
	限制具体ip地址访问(一般为内网ip地址)，可以使用短链接
注意:调用短网址会有重定向，curl函数不支持，可以使用file_get_contents

示例:

<?php if (isset($_GET['url'])) {
    $link = $_GET['url'];
//    preg_match('/www\.localhost\.com/', $link, $matches);
    //限制具体ip地址访问(一般为内网ip地址)，可以使用短链接
    //使用短网址绕过  但是调用短网址会有重定向，curl函数不支持，可以使用file_get_contents
    preg_match('/\d+\.\d+\.\d+\.\d+/', $link, $matches);
    if ($matches){
        echo 'your url is wrong';
        die();
    }
    //初始化新的会话，返回 cURL 句柄给$curlobj
    $content = file_get_contents($link);


    echo $content;
} ?>

```

##### xip.io 绕过 

```
请求参数 在不允许只有数字的情况下可以使用xip.io或者短链接

示例:
	127.0.0.1.xip.io 				 --127.0.0.1

    www.127.0.0.1.xip.io 		 --127.0.0.1

    Haha.127.0.0.1.xip.io 		 --127.0.0.1

    Haha.xixi.127.0.0.1.xip.io --127.0.0.1
```

#### SSRF漏洞挖掘

```
1、通过URL地址分享网页内容
	早期应用中 ，为了更好的用户体验，Web应用在分享功能中，通常会获取目标URL地址网页内容中<title></title>标签或者<meta name="description”content=""/>标签中content的文本内容提供更好的用户体验。
2、转码服务 
	通过URL地址把原地址的网页内容调优使其适合手机屏幕浏览
3、在线翻译
	通过 URL地址翻译对应文本的内容。提供此功能的百度、有道等。
4、图片加载与下载
	开发者为了有更好的用户体验通常对图片做些微小调整例如加水印、压缩等，就必须要把图片下载到服务器的本地，所以就可能造成SSRF问题）。
5、从URL关键字中寻找
	Share、wap、url、link、src、source、target、u、3g、display、sourceURL、imageURL、domain
	归根到底,其实都是跟链接有关联的 
```

#### 危害：

```
1、可对内网，服务器所在内网，受控服务器进行端口扫描，获取一些banner

2、对内网web应用进行指纹识别，通过访问默认文件实现。

3、攻击内外网web应用，主要是使用get参数就可以实现分攻击。

4、利用file协议读取本地文件。
```

#### SSRF漏洞的验证

```
由于SSRF是服务端发起的请求，因此在加载对应的资源信息的时候本地浏览器network参数中不应该存在对应的请求。
```

#### CSRF与SSRF的区别?

```
CSRF是服务器端没有对用户进行数据编辑时进行严格验证(是否是当前用户进行修改)，（一般可以在用户进行数据修改时进行验证码、token、referer校验），导致攻击者可以在用户未知情的情况下进行用户信息的篡改.

SSRF是服务器没有对前端用户传递的url地址严格校验，导致攻击者可以以当前服务器为跳板攻击内网或者其他服务器
 @绕过
 短网址绕过
 xip.io绕过
 [::1]:端口
 进制转换
 DNS
```

