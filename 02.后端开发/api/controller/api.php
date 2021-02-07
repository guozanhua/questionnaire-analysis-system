<?php

include_once 'connect.php'; //包含数据库连接
$str = file_get_contents("php://input"); //接收问卷星推送数据
$arr = json_decode($str, true); //转换json数组
//测试代码，检查是否可以接收到数据，运行此段代码需要赋予文本文件写入权限
// $data = file_get_contents("php://input");
// $dataFile = "data.txt";
// if (!empty($data)) {
//    $file = fopen($dataFile, "a") or die("文件不可写入");
//    fwrite($file, $data);
//    fwrite($file, "\n");
//    fclose($file);
// }
//
//将数据存储进数据库之前写入文件，作为备份（需要文件有权限，容易出错，不使用）
//$filePath = "data.txt";
//if (!empty($str)) {
//    $file = fopen($filePath, "a") or die("文件不可写入");
//    fwrite($file, $str);
//    fwrite($file, "\n");
//    fclose($file);
//}
//变量声明
$joinid = ""; //填写者填写提交的流水号
$questionnaireId = ""; //问卷id 问卷星推送数据中activity即为问卷id
$questionnaireName = ""; //问卷名称
$timeTaken = ""; //填写者耗时单位秒
$submitTime = ""; //填写者提交的时间
$totalValue = ""; //考试问卷总得分
$ipaddress = ""; //填写问卷用户的IP地址
$browser = ""; //用户使用浏览器
$os = ""; //用户使用操作系统
$name = ""; //填写问卷的用户姓名，从问卷答案中获取
$sex = ""; //男 女
$school = ""; //用户填写的学校，从问卷答案中获取
$grade = ""; //用户填写的年级，从问卷答案中获取
$class = ""; //用户填写的班级，从问卷答案中获取
$family = ""; //填写者家庭成员，从问卷答案中获取
$fatherPhone = ""; //父亲手机号码，从问卷答案中获取
$motherPhone = ""; //母亲手机号码，从问卷答案中获取
$teacherPhone = ""; //班主任手机号码，从问卷答案中获取
$otherPhone = ""; //其他人手机号码，从问卷答案中获取
//格式化数据
$joinid = $arr['joinid'];
$questionnaireId = $arr['activity'];
$questionnaireName = $arr['name'];
$timetaken = $arr['timetaken'];
$submittime = $arr['submittime'];
$totalvalue = $arr['totalvalue'];
$ipaddress = $arr['ipaddress'];
$name = $arr['q2']; //获取姓名
$sex = ($arr['q3'] == 1) ? '男' : '女'; //获取性别
switch ($arr['q1']) {//获取学校
    case '1':
        $school = "郑州市第十九中";
        break;
    case '2':
        $school = '郑州市第一0一中学';
        break;
    case '3':
        $school = '郑州市陇海中学';
        break;
    case '4':
        $school = "其它2";
        break;
    case '5':
        $school = '其它3';
        break;
    case '6':
        $school = $arr['q1_6'];
        break;
}
switch ($arr['q4']) { //获取年级
    case '1':
        $grade = "高一";
        break;
    case '2':
        $grade = '高二';
        break;
    case '3':
        $grade = '高三';
        break;
}
$class = $arr['q5']; //获取班级
if (strstr($arr['q6'], '1')) { //获取家庭成员
    $family .= '爸爸,';
}
if (strstr($arr['q6'], '2')) {
    $family .= '妈妈,';
}
if (strstr($arr['q6'], '3')) {
    $family .= '哥哥,';
}
if (strstr($arr['q6'], '4')) {
    $family .= '姐姐,';
}
if (strstr($arr['q6'], '5')) {
    $family .= '弟弟,';
}
if (strstr($arr['q6'], '6')) {
    $family .= '妹妹,';
}
if (strstr($arr['q6'], '7')) {
    $family .= '其它,';
}
$fatherPhone = $arr['q43_1']; //父亲手机号码
$motherPhone = $arr['q43_2']; //母亲手机号码
$teacherPhone = $arr['q43_3']; //班主任手机号码
$otherPhone = $arr['q43_4']; //其他人手机号码
$score = ""; //每道题目的得分，只有被设置过的选择题目才可以设置得分
$isExistP = false; //题目表中是否已经添加过该问卷的题目信息
$isExistA = false; //答案表中是否已经添加过该用户的答案
$isExistR = false; //问卷填写记录表中是否已经添加过该用户的记录
date_default_timezone_set(PRC); //获取时间与实际时间差8小时的解决办法
$time = date('Y-m-d H:i', time()); //获取时间
/* 判断是否为符合条件的数据
  问卷ID已经被添加过的
  处于发布状态的
  才能接收相对应的数据 */
$sqlStr = "select * from $db.questionnaire where questionnaireId='$questionnaireId' and qstatus=1";
$result = $link->query($sqlStr);
if ($result->fetch_assoc()) {
    if ($link) {
        $sqlStr = "select * from $db.problem where questionnaireId='$questionnaireId'"; //判断题目表中是否已经添加过该问卷的题目信息
        $result = $link->query($sqlStr);
        if ($result->fetch_assoc()) {
            $isExistP = true;
        }
        $sqlStr = "select * from $db.answer where joinid='$joinid'"; //判断答案表中是否已经添加过该用户的答案
        $result = $link->query($sqlStr);
        if ($result->fetch_assoc()) {
            $isExistA = true;
        }
        $sqlStr = "select * from $db.record where joinid='$joinid'"; //判断填写记录表中是否已经添加过该用户的答案
        $result = $link->query($sqlStr);
        if ($result->fetch_assoc()) {
            $isExistR = true;
        }

        if (!$isExistR) {
            //写入record-问卷填写记录表
            $sqlStr = "insert into $db.record(joinid,questionnaireId,questionnaireName,timeTaken,submitTime,totalValue,ipaddress,name,sex,school,grade,class,family,fatherPhone,motherPhone,teacherPhone,otherPhone) value('$joinid','$questionnaireId','$questionnaireName','$timetaken','$submittime','$totalvalue','$ipaddress','$name','$sex','$school','$grade','$class','$family','$fatherPhone','$motherPhone','$teacherPhone','$otherPhone')";
            if (!mysqli_query($link, $sqlStr))
                die('插入数据到record表失败');
        }
        //写入problem表和answer表
        foreach ($arr as $k => $value) {
            if (preg_match('/[a-zA-Z]/', substr(trim($k), 0, 1)) && is_numeric(substr(trim($k), 1, 1))) { //判断$k的前两个字符，如果为字母和数字的组合，代表为题目号，否则不进行插入
                if (strlen(trim($value)) > 0 && strlen(trim($value)) < 2) { //一般选项号为得分的分值，如果有改动，此处需要修改
                    if (is_numeric($value)) {
                        $score = $value;
                    }
                }
                if (!$isExistP) {
                    //写入problem-题目表
                    $sqlStr = "insert into $db.problem(questionnaireId,problemId,operateTime) value('$questionnaireId','$k','$time')";
                    if (!mysqli_query($link, $sqlStr))
                        die('插入数据到problem表失败');
                }
                if (!$isExistA) {
                    //写入answer-答案表
                    $sqlStr = "insert into $db.answer(joinid,questionnaireId,problemId,answerContent,score,operateTime) value('$joinid','$questionnaireId','$k','$value','$score','$time')";
                    if (!mysqli_query($link, $sqlStr))
                        die('插入数据到answer表失败');
                }
            }
        }
    } else {
        die('数据库连接失败');
    }
}
?>
