# 同源策略[#](https://www.cnblogs.com/mysticbinary/p/12620152.html#546539048)

浏览器中有两个安全机制，一个浏览器沙盒（Sandbox），另一个就是同源策略(Same Origin Policy，简称SOP) ，下面介绍同源策略。同源是指`同协议`、`同域名`、`同端口`，必须三同，缺一不可。下面列举了一些例子，为方便读者了解哪些是属于同源，下面列举一些案例：
 [![img](https://img2020.cnblogs.com/blog/1552062/202004/1552062-20200402142625044-1964473081.png)](https://img2020.cnblogs.com/blog/1552062/202004/1552062-20200402142625044-1964473081.png)

根据这个策略，a.com域名下的JavaScript无法跨域操作b.com域名下的对象。跨域的安全限制都是对浏览器端来说的，服务器端是不存在跨域安全限制的。如下流程图：
 [![img](https://img2020.cnblogs.com/blog/1552062/202004/1552062-20200402164703382-2122365194.png)](https://img2020.cnblogs.com/blog/1552062/202004/1552062-20200402164703382-2122365194.png)
 流程图1.

[![img](https://img2020.cnblogs.com/blog/1552062/202004/1552062-20200402171602270-159799704.png)](https://img2020.cnblogs.com/blog/1552062/202004/1552062-20200402171602270-159799704.png)
 流程图2.

不同源也意味着不能通信，因为同源策略认为其他任何站点的资源内容都是不安全的。这个限制有一定的道理，我们来想象一个场景，假设攻击者利用`Iframe`标签，把真正的银行登陆页面嵌套在他的页面上，那么当用户在这个嵌套的页面上登陆时，该页面就可以通过JavaScript读取到用户表单中的内容，意味着用户就泄露了登陆信息。

浏览器使用了同源策略之后，`好处`是能确保用户正在查看的页面确实是来自于正在浏览的域，然而有好就会有坏，`坏处`是一些业务就是需要进行跨域操作，同源策略显然就阻挡了业务需求。比如现在IT公司都发展得大，（假设的案例）阿里公司有好几个事业部，淘宝、天猫、支付宝等独立的事业部，你在登陆支付宝页面的时候，你跳转到支付宝的个人中心页面时，支付宝就会跨域去请求你登陆过的淘宝站的接口来回传你的个人信息。

在这种情景下，你可以思考一下开发者怎么做到跨域的？其实解决方法还是有很多的，比如JSONP就是其中一种。下面我们就介绍JSONP跨域请求。



# JSONP原理[#](https://www.cnblogs.com/mysticbinary/p/12620152.html#3950632767)

为了便于客户端使用跨站的数据，开发的过程中逐渐形成了一种非正式传输协议。人们把它称作JSONP，该协议的一个要点就是允许用户传递一个callback参数给服务端，然后服务端返回数据时会将这个callback参数作为函数名来包裹住JSON数据，这样客户端就可以随意定制自己的函数来自动处理返回数据了。

JSONP 跨域请求的原理，可以参考下面的文章，这位大佬从前端开发的角度把开发流程都讲清楚了， 我就不在叙述了。
 https://www.cnblogs.com/chiangchou/p/jsonp.html

随着跨域技术带来了便利，同样的，也带来了安全风险。



# 观察B站的JSONP跨域请求流程[#](https://www.cnblogs.com/mysticbinary/p/12620152.html#565100528)

- 1. 登陆B站之后，进入B站的个人中心页面：`https://space.bilibili.com/9996xxx1`
- 1. 打开F12调试工具，查看是否有跨站请求，通过查看url 是否有`callback=`
      [![img](https://img2020.cnblogs.com/blog/1552062/202004/1552062-20200402212305468-907820306.png)](https://img2020.cnblogs.com/blog/1552062/202004/1552062-20200402212305468-907820306.png)

[![img](https://img2020.cnblogs.com/blog/1552062/202004/1552062-20200402215415694-1302090603.png)](https://img2020.cnblogs.com/blog/1552062/202004/1552062-20200402215415694-1302090603.png)

很明显了，现在所在域名是`space.bilibili.com`，但是却跨域请求了`api.bilibili.com`的数据

- 1. 查看前端源码，发现确实是做了jsonp
      [![img](https://img2020.cnblogs.com/blog/1552062/202004/1552062-20200402214609685-1821553832.png)](https://img2020.cnblogs.com/blog/1552062/202004/1552062-20200402214609685-1821553832.png)



# 测试是否存在JSONP劫持[#](https://www.cnblogs.com/mysticbinary/p/12620152.html#1480022315)

`https://api.bilibili.com/x/space/myinfo?jsonp=jsonp&callback=__jp0` ，看到URL的GET参数里面并没有携带token，那么有以下两种方式来测试是否存在JOSONP劫持。

## 方式一[#](https://www.cnblogs.com/mysticbinary/p/12620152.html#1228214267)

正常重放以下数据包，看到个人信息正常返回。
 [![img](https://img2020.cnblogs.com/blog/1552062/202004/1552062-20200402215920481-1306394879.png)](https://img2020.cnblogs.com/blog/1552062/202004/1552062-20200402215920481-1306394879.png)

修改`Referer` Referer: https://space.abilibili.com/9996xxx1 ，将space.bilibili.com改成`space.abilibili.com`，发现返回信息没有个人信息，意味着不存在JSONP劫持。

[![img](https://img2020.cnblogs.com/blog/1552062/202004/1552062-20200402220041306-1499133238.png)](https://img2020.cnblogs.com/blog/1552062/202004/1552062-20200402220041306-1499133238.png)

## 方式二[#](https://www.cnblogs.com/mysticbinary/p/12620152.html#4064066279)

制作一个playload：

```html
Copy<html>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" /> 
    <script type="text/javascript"> 
        function __jp0(result) { 
            console.log(result);
        }
    </script> 
    <script type="text/javascript" src="https://api.bilibili.com/x/space/myinfo?jsonp=jsonp&callback=__jp0"></script>
</html>
```

放在一个web站点，使用已经登陆B站的浏览器打开这个链接。如果控制台（Console）没有输出个人信息，也意味着不存在JSONP劫持。



# JSONP劫持与CSRF的相同与不同[#](https://www.cnblogs.com/mysticbinary/p/12620152.html#3872065580)

利用上相同：

- 需要用户点击恶意链接
- 用户必须登陆该站点，在本地存储了Cookie

两个不同：

- 必须找到跨站请求资源的接口来实施攻击
- CSRF只管发送http请求，但是Jsonp Hijacking的目的是获取敏感数据



# JSONP劫持的防御方法[#](https://www.cnblogs.com/mysticbinary/p/12620152.html#789935856)

JSONP劫持属于CSRF（ Cross-site request forgery 跨站请求伪造）的攻击范畴，所以解决的方法和解决CSRF的方法一样。
 1、验证 HTTP Referer 头信息；
 2、在请求中添加Token，并在后端进行验证；