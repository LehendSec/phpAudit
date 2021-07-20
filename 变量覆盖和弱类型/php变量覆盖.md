# php变量覆盖

### 变量覆盖函数

```
register_globals 全局变量覆盖

$$ 变量赋值方式有可能覆盖已有的变量，从而导致一些不可控制的结果。一般出现foreach中

extract()变量覆盖

import_request_variables变量覆盖

parse_str()变量覆盖
```

#### register_globals 

**全局变量覆盖**

​		**功能:**

```
	register_globals的意思就是注册为全局变量,当register_globals为on时，页面的get、post、cookie传递的参数会被注册为全局变量。而Off的时候，我们需要到特定的数组里去得到它。                                 
```

​		**示例:**

```
	<?php  
        //?id=1
        echo "Register_globals: ".(int)ini_get("register_globals")."<br/>"; 
        echo '$_GET["id"] :'.$_GET['id']."<br/>";
        echo '$id :'.$id;
    ?>
```

#### $$ 

​		**功能:**

**变量赋值方式有可能覆盖已有的变量，从而导致一些不可控制的结果。一般出现foreach中**

```
$$ 导致的变量覆盖问题通常伴随着foreach一起出现
```

​	**示例:**

```
	<?php
        include'flag.php';
        $yds = "dog";
        $is = "cat";
        $handsome = 'yds';

        foreach($_GET as $x => $y){ //get传值
            //$flag = $handsome='yds;'
            //$yds = $flag;
        //    $handsome = $flag
            $$x = $$y;
        }

        //flag = $handsome;
        foreach($_GET as $x => $y){
            //这里需要传递两个参数 ?handsome=flag&flag=handsome
            //$_GET['flag'] === handsome && handsome != 'flag'
            if($_GET['flag'] === $x && $x !== 'flag'){ //判断get传进来的值等不等于flag 如果等于flag则跳过
                //退出当前并输出
        //        $handsome = $flag
                //这里即为echo $handsome;
                //die();
                exit($handsome);

            }
        }

        //检测get是否为flag 或者post是否为flag  必须两方都为假  否则输出$yds
        //通过这里我们就可以结合前面的来构造 既然要输出$yds所以我们想办法让$flag的值赋值给$yds
        //构造yds=flag GET传输 在经过第一个foreach的时候进行了赋值 等于进行了这样的一个操作$yds=$flag
        //所以这个条件为真就可以输出flag了。
        if(!isset($_GET['flag']) && !isset($_POST['flag'])){
            //$yds = $flag 从下往上推断
            //echo $yds;
            //die();
            exit($yds);

        }
        //

        //检测POST flag是否为flag  或者get 是否为flag   //至少有一个为真则为真
        if($_POST['flag'] === 'flag'  || $_GET['flag'] === 'flag'){
            //$is = $flag 从下往上推断 is=flag&flag=flag

        //    $is = $flag;
        //    echo $is;
        //    die();
            exit($is);
        }
        //handsome=flag&flag=handsome
        //is=flag&flag=flag
        //$yds = $flag
        //这里只需要post方式传第一个值即可，只要flag!=flag
        echo "the flag is: ".$flag;

       ?>
```

#### extract(array $array,$flags)

​		**功能:**

​		**本函数用来将数组中的变量注册到到全局变量中,如果是关联数组，那么注册的是k v 键值对 extract(array $array,$flags)**  

```
extract 将数组中的变量注册到全局变量中，如果是关联数组，那么注册的是k、v键值对
$array
    需要传入的数组
$flags
    对待非法／数字和冲突的键名的方法将根据取出标记 flags 参数决定。可以是以下值之一：
    EXTR_OVERWRITE
        如果有冲突，覆盖已有的变量。 
    EXTR_SKIP
        如果有冲突，不覆盖已有的变量。 
    EXTR_PREFIX_SAME
        如果有冲突，在变量名前加上前缀 prefix。 
    EXTR_PREFIX_ALL
        给所有变量名加上前缀 prefix。 
    需要注意的是:                                             
         添加前缀后只会注册一个全局变量，默认的全局变量不会保留，使用下划线进行拼接
```


​		**示例:**
```
	<?php
        $flag = 'flag.php';
        //会将$_GET接受的参数注册为全局变量
        //?ceshi=xx

        //将超全局数组中的变量注册为全局变量
        extract($_GET);
        //var_dump($GLOBALS);
        //echo $ceshi;
        //1.需要能够传递一个$ceshi的值，确保该变量有值
        if (isset($ceshi)) {
            //http://www.localhost.com/diy/variable/extractDemo2.php?ceshi=123&flag=http://www.localhost.com/diy/variable/1.txt 需要远程文件包含
            //http://www.localhost.com/diy/variable/1.txt
            //方式二:http://www.localhost.com/diy/variable/extractDemo2.php?ceshi=&flag=
        //    ?ceshi=congratulations for you get the flag!&flag=xx
            //file_get_contents — 将整个文件读入一个字符串也可以读取远程文件的内容
            $content = trim(file_get_contents($flag));
            //有两种解答方式
            //1、远程读取文件内容 需要文件中的内容和ceshi的值一样
            //extractDemo2.php?ceshi=123&flag=http://www.localhost.com/diy/variable/1.txt
            //2、必须知道flag.php文件的具体内容，用ceshi值等于该文件中的内容 格式也有要求，可以调试得到$content的内容再进行赋值 这种方式貌似有问题

            if ($ceshi == $content) {

                echo 'flag{congratulations for you get the flag!}';

            } else {

                echo 'Oh.no';

            }
        }
```

####  import_request_variables

​		**功能:**

```
import_request_variables 函数可以在 register_globals(全局注册变量) = off 时，
把 GET/POST/Cookie 变量导入全局作用域中。
有效版本为(PHP 4 >= 4.1.0, PHP 5 < 5.4.0) 
import_request_variables("gpc", "hehe");
这里的gpc是指 把 GET/POST/Cookie 变量导入全局作用域中。也可以单一添加，前缀为hehe
```

​		**示例:**

```
<?php
    //这里的gpc是指 把 GET/POST/Cookie 变量导入全局作用域中。也可以单一添加，前缀为hehe
    import_request_variables("gpc", "hehe");

    //import_request_variables 函数可以在 register_global(全局注册变量) = off 时，把 GET/POST/Cookie 变量导入全局作用域中。
    //(PHP 4 >= 4.1.0, PHP 5 < 5.4.0)
    //$_COOKIE,$_POST,$_GET全部会注册为全局变量.
    //使用prefix 函数会添加默认前缀，给前缀默认会注册两个全局变量 前缀的全局变量直接进行拼接
    //之前使用的extract函数添加前缀后只会注册一个全局变量，默认的全局变量不会保留，使用下划线进行拼接
    echo "Register_globals status is: ".(int)ini_get('register_globals')."<br />";

    //$_REQUEST = ($_GET、$_POST、$_COOKIE)
    echo '$_GET["id"]:'.$_GET['id1']."<br />";
    echo '$_POST["id2"]:'.$_POST['id2']."<br />";
    echo '$_COOKIE["$username"]:'.$_COOKIE['username']."<br />";


    var_dump($GLOBALS);
    //变量覆盖 就是在没有调用的情况下直接使用
    //echo 'id1:'.$id1."<br />";
    //echo 'id2:'.$id2."<br />";
    //echo 'username:'.$username."<br />";
    //
    echo 'heheid1:'.$heheid1."<br />";
    echo 'heheid2:'.$heheid2."<br />";
    echo 'heheusername:'.$heheusername."<br />";
```

#### parse_str()

​		**功能:**

```
parse_str(string $encoded_string, array &$result)
将URL 传递入的查询字符串设置注册为全局变量                   
形如:aa=xx&b=xx2&c=xx3
//默认情况下(没有配置array参数)，则由该函数设置的变量将覆盖已存在的同名变量。parse_str( string $encoded_string)
如果设置了第二个变量 result， 变量将会以数组元素的形式存入到这个数组，作为替代,这样就防止了变量覆盖的问题
极度不建议 在没有 result 参数的情况下使用此函数，并且在 PHP 7.2 中将废弃不设置参数的行为。 
```

​		**示例:**

```
<?php
    //$hash1 = md5('QNKCDZO');
    //$hash2 = md5('240610708');
    //echo "hash1:    ".$hash1."<br />";
    //echo "hash2:    ".$hash2."<br />";
    //
    //if($hash1 == $hash2){
    //    echo 'equal';
    //}else {
    //    echo 'not equal';
    //}
    //hash1: 0e830400451993494058024219903391
    //hash2: 0e462097431906509019562988736854
    //equal
    //die();
    error_reporting(0);

    $a = "www.bihuo.cn";

    $id = $_GET['id'];
    //?id=
    @parse_str($id);

    $var = $id;
    //echo $id;
    var_dump($a);
    //这里需要进行变量覆盖，$a[0]需要通过接收的id参数进行变量覆盖之前原本存在的$a参数
    //id=a[0]=240610708
    if ($a[0] != "QNKCDZO" && md5($a[0]) == md5("QNKCDZO")) {

        echo "success";

    } else {

        exit("failure");

    }
```



