# 读取型CSRF 

##### 前言：

在大多数的人眼里CSRF可能仅仅是**写入型**的比如：修改个人资料、授权登陆等等功能场景的CSRF问题。

读取型CSRF：这些漏洞的利用手法都跟CSRF是一样的：

- JSONP劫持
- Flash跨域劫持
- CORS跨域资源读取

## 1、JSONP劫持

JSONP:为了便于客户端使用跨站的数据，开发的过程中逐渐形成了一种非正式传输协议

### 漏洞案例

这里来看一条请求：

[![jsonp hijacking](https://chen-blog-oss.oss-cn-beijing.aliyuncs.com/2018-03-22/0x00.png)](https://chen-blog-oss.oss-cn-beijing.aliyuncs.com/2018-03-22/0x00.png)

这条请求返回的结果中有手机号（这里我测试的账号没绑定手机），如果我们想要以CSRF交互式攻击的方式获取这个手机号该怎么办？

来看看这条请求有callback，而返回结果是不是类似Javascript中的函数？

Javascript原函数定义如下：

```
function funName(){}
```

这里是缺少了函数定义的关键词`function`和花括号的函数主体部分，只有函数名和函数传参，聪明人已经想到了，这不就相当于是自定义函数被引用了么，而中间那段传参就相当于是一个数组，所以我们可以先用JS自定义好这个函数，然后再引用这个请求，自然就可以获取到数据了。

这时候我们可以来构建一下PoC：

```
<!-- 引用一段如上请求为JS -->
<script>function jsonp2(data){alert(JSON.stringify(data));}</script>
<script src="http://gh0st.cn/user/center?callback=jsonp2"></script>
```

使用正常的账号(绑定过手机号)来测试下：

[![jsonp hijacking](https://chen-blog-oss.oss-cn-beijing.aliyuncs.com/2018-03-22/0x01.png)](https://chen-blog-oss.oss-cn-beijing.aliyuncs.com/2018-03-22/0x01.png)

### 案例总结

其实通过这个例子，我们可以知道HTML标签`<script>`在一定的情况下是可以跨域读取的。

对此漏洞的修复有很多：

1.打乱响应主体内容

2.Referer等进行限制

…..等等

## 2、Flash跨域劫持

Flash跨域比较经典了，在做web目录资产整理的时候有时候会发现这样的文件 **crossdomain.xml** ，文件内容如果是如下的，那么就存在Flash跨域问题，如下内容的意思是支持所有域：

```
<?xml version="1.0"?>
<cross-domain-policy>
  <allow-access-from domain="*" />
</cross-domain-policy>
```

为什么会如此？具体流程是这样的：

gh0st.cn 有一个SWF文件，这个文件是想要获取 vulkey.cn 的 userinfo 的返回响应主体，SWF首先会看在 vulkey.cn 的服务器目录下有没有 **crossdomain.xml** 文件，如果没有就会访问不成功，如果有 **crossdomain.xml** ，则会看**crossdomain.xml** 文件的内容里面是否设置了允许 gh0st.cn 域访问，如果设置允许了，那么 gh0st.cn 的SWF文件就可以成功获取到内容。所以要使Flash可以跨域传输数据，其关键就是**crossdomain.xml** 文件。

当你发现 **crossdomain.xml** 文件的内容为我如上所示的内容，那么就是存在Flash跨域劫持的。

### 漏洞案例

在对一个厂商进行测试的时候正好发现了这样的文件：

[![flash hijacking](https://chen-blog-oss.oss-cn-beijing.aliyuncs.com/2018-03-22/0x02.png)](https://chen-blog-oss.oss-cn-beijing.aliyuncs.com/2018-03-22/0x02.png)

在这里我需要做两件事：

1.找到一个能获取敏感信息的接口

2.构建PoC

在这里敏感的信息接口以个人中心为例子，PoC使用的是 https://github.com/nccgroup/CrossSiteContentHijacking/raw/master/ContentHijacking/objects/ContentHijacking.swf

[![flash hijacking](https://chen-blog-oss.oss-cn-beijing.aliyuncs.com/2018-03-22/0x03.png)](https://chen-blog-oss.oss-cn-beijing.aliyuncs.com/2018-03-22/0x03.png)

### 案例总结

很简单的一个东西，但是用处却很大，其利用方法跟CSRF也是一样的，只需要修改下PoC就行。

修复方案同样也很简单，针对`<allow-access-from domain="*" />`的domain进行调整即可。

## 3、CORS跨域资源读取

### 漏洞案例

[![CORS](https://chen-blog-oss.oss-cn-beijing.aliyuncs.com/2018-03-22/0x04.png)](https://chen-blog-oss.oss-cn-beijing.aliyuncs.com/2018-03-22/0x04.png)

如上图中我在请求的时候加上了请求头 `Origin: http://gh0st.cn`，而对应的响应包中出现了`Access-Control-Allow-Origin: http://gh0st.cn`这个响应头其实就是访问控制允许，在这里是允许http://gh0st.cn的请求的，所以http://gh0st.cn是可以跨域读取此网址的内容的~在这里我介绍下`Origin`：

`Origin`和`Referer`很相似，就是将当前的请求参数删除，仅剩下**三元组（协议 主机 端口）**，标准的浏览器，会在每次请求中都带上`Origin`，至少在跨域操作时肯定携带（例如ajax的操作）。

所以要测试是否存在CORS这个问题就可以参考我上面的操作手法了。

怎么利用呢？在这里我使用了github上的开源项目:https://github.com/nccgroup/CrossSiteContentHijacking，readme.md中有具体的说明，这里我就不一一讲解了，那么已经确认问题了，那就需要进一步的验证。

在这里我找到了一处接口，其响应主体内容是获取用户的真实姓名、身份证、手机号等内容：

/daren/author/query （要注意的是这个请求在抓取的时候是POST请求方式，但并没有请求正文，经过测试请求正文为任意内容即可）

响应报文正文内容：

[![CORS](https://chen-blog-oss.oss-cn-beijing.aliyuncs.com/2018-03-22/0x05.png)](https://chen-blog-oss.oss-cn-beijing.aliyuncs.com/2018-03-22/0x05.png)

这里CrossSiteContentHijacking项目我搭建在了本地(127.0.0.1) http://127.0.0.1/CrossSiteContentHijacking/ContentHijackingLoader.html

根据项目所说的操作去进行参数的配置，然后点击 Retrieve Contents 按钮：

[![CORS](https://chen-blog-oss.oss-cn-beijing.aliyuncs.com/2018-03-22/0x06.png)](https://chen-blog-oss.oss-cn-beijing.aliyuncs.com/2018-03-22/0x06.png)

测试如下，测试结果是可以跨域读取的：

[![CORS](https://chen-blog-oss.oss-cn-beijing.aliyuncs.com/2018-03-22/0x07.png)](https://chen-blog-oss.oss-cn-beijing.aliyuncs.com/2018-03-22/0x07.png)

### 案例总结

这个问题其实就是对Origin的验证没有控制好，对其进行加强即可。