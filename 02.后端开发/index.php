<?php

//ini_set("display_errors", "On"); //打开错误提示
//ini_set("error_reporting", E_ALL); //显示所有错误
define('BASE_PATH', __DIR__);
require_once 'controller/connect.php'; //包含数据库连接-使用原生SQL语句
require_once 'controller/medoo_connect.php'; //包含数据库连接-使用PHP数据库框架medoo

$questionnaireId = ""; //问卷id
$createUserId = ""; //创建该问卷人的用户id
$questionnaireName = ""; //问卷名称
$limiteCount = ""; //问卷接收数量限制 0 不限制 n 限制为n(n>0)
$qstatus = ""; //问卷状态 0 暂停 1 发布
$qresource = ""; //0 本地创建 1 问卷星 
$requireRegist = ""; //0 不需要注册登录填写 1 需要注册登录填写
$sharePicture = ""; //问卷发布图片二维码的文件地址
$shareUrl = ""; //问卷发布的分享链接
$qtime = ""; //问卷的创建时间
$fun = ""; //调用的函数名
$json = array(); //向前台返回的数据
$pageSize = ""; //每一页显示的条数
$current = ""; //当前处于第几页
$indexArr = array(); //查询涉及到两个表problem和dimension，先对第一个表数据进行去重后存储到数组
$countArr = array(); //一个维度对应题目的数量

$requestPayload = file_get_contents("php://input");
$requestPayload = !empty($requestPayload) ? json_decode($requestPayload, true) : array();
$fun = $_GET['function']; //获取调用方法

switch ($fun) {
    case 'add_wjx': {
            $questionnaireId = $requestPayload['questionnaireId']; //获取问卷ID
            $createUserId = $requestPayload['createUserId']; //获取问卷创建人的用户ID
            $questionnaireName = trim($requestPayload['questionnaireName']); //获取问卷标题，此标题为用户自定义标题，等接收推送数据后，推送数据中的标题将会被舍弃
            $qresource = 1;
            date_default_timezone_set(PRC); //获取时间与实际时间差8小时的解决办法
            $qtime = date('Y-m-d H:i', time()); //获取时间
            add_wjx($link, $db, $questionnaireId, $createUserId, $questionnaireName, $qresource, $sharePicture, $shareUrl, $qtime);
        };
        break;
    case 'wjx_getList': {
            $pageSize = $requestPayload['pageSize'];
            $current = $requestPayload['current'];
            wjx_getList($link, $db, $pageSize, $current);
        };
        break;
    case 'delete_wjx': {
            $questionnaireId = $requestPayload['questionnaireId'];
            delete_wjx($link, $db, $questionnaireId);
        };
        break;
    case 'pause_start': {
            $questionnaireId = $requestPayload['questionnaireId'];
            pause_start($link, $db, $questionnaireId);
        };
        break;
    case 'getQuestionnaireList': {
            getQuestionnaireList($link, $db);
        };
        break;
    case 'getRecordList': {
            $pageSize = $requestPayload['pageSize'];
            $current = $requestPayload['current'];
            $questionnaireId = $requestPayload['questionnaireId'];
            getRecordList($link, $db, $questionnaireId, $pageSize, $current);
        };
        break;
    case 'all_getList': {
            $pageSize = $requestPayload['pageSize'];
            $current = $requestPayload['current'];
            all_getList($link, $db, $pageSize, $current);
        };
        break;
    case 'showRecordContent': {
            $pageSize = $requestPayload['pageSize'];
            $current = $requestPayload['current'];
            $questionnaireId = $requestPayload['questionnaireId'];
            showRecordContent($link, $db, $questionnaireId, $pageSize, $current);
        };
        break;
    case 'getDimensionList': {
            $pageSize = $requestPayload['pageSize'];
            $current = $requestPayload['current'];
            $questionnaireId = $requestPayload['questionnaireId'];
            getDimensionList($link, $db, $questionnaireId, $pageSize, $current);
        };
        break;
    case 'editDimension': {
            $dimensionId = $requestPayload['dimensionId'];
            $questionnaireId = $requestPayload['questionnaireId'];
            $dimensionName = trim($requestPayload['dimensionName']);
            $dimensionMean = $requestPayload['dimensionMean'];
            $standardScore = $requestPayload['standardScore'];
            editDimension($link, $db, $dimensionId, $dimensionName, $dimensionMean, $standardScore, $questionnaireId);
        };
        break;
    case 'deleteDimension': {
            $questionnaireId = $requestPayload['questionnaireId'];
            $dimensionId = $requestPayload['dimensionId'];
            deleteDimension($link, $db, $dimensionId, $questionnaireId);
        };
        break;
    case 'add_dimension': {
            $dimensionName = trim($requestPayload['dimensionName']);
            $dimensionMean = $requestPayload['dimensionMean'];
            $questionnaireId = $requestPayload['questionnaireId'];
            add_dimension($link, $db, $dimensionName, $dimensionMean, $questionnaireId);
        };
        break;
    case 'getDimensionQList': {
            $questionnaireId = $requestPayload['questionnaireId'];
            $dimensionId = $requestPayload['dimensionId'];
            getDimensionQList($link, $db, $questionnaireId, $dimensionId);
        };
        break;
    case 'dimensionAddQ': {
            $questionnaireId = $requestPayload['questionnaireId'];
            $dimensionId = $requestPayload['dimensionId'];
            $submitSelection = $requestPayload['submitSelection'];
            dimensionAddQ($database_medoo, $questionnaireId, $dimensionId, $submitSelection);
        };
        break;
    case 'getRecordContent': {
            $questionnaireId = $requestPayload['questionnaireId'];
            $joinid = $requestPayload['joinid'];
            getRecordContent($link, $db, $questionnaireId, $joinid);
        };
        break;
    case 'getReportData': {
            $joinid = $requestPayload['joinid'];
            getReportData($link, $db, $joinid);
        };
        break;
    case 'login': {
            $username = trim($requestPayload['username']);
            $password = trim($requestPayload['password']);
            login($database_medoo, $username, $password);
        };
        break;
    case 'getAnalyseList': {
            $questionnaireId = $requestPayload['questionnaireId'];
            getAnalyseList($database_medoo, $questionnaireId);
        };
        break;
    case 'showAnalyseContent': {
            $questionnaireId = $requestPayload['questionnaireId'];
            $problemId = 'q' . $requestPayload['qid']; //之前传的时候去掉了q，现在要加上
            showAnalyseContent($database_medoo, $questionnaireId, $problemId);
        };
        break;
}

//问卷整体统计-点击查看详情-查看该题目所有人填写的信息
function showAnalyseContent($database_medoo, $questionnaireId, $problemId) {
    if ($database_medoo) {
        //从answer找填写内容
        $result = $database_medoo->select("answer", ["answerContent", "operateTime", "joinid"], ["questionnaireId" => $questionnaireId, "problemId" => $problemId]);
        foreach ($result as $key => $value) {
            $json['contentData'][$key]['id'] = $key;
            $json['contentData'][$key]['option'] = $result[$key]['answerContent'];
            $json['contentData'][$key]['time'] = $result[$key]['operateTime'];

            //使用joinid找姓名
            $temp = $result[$key]['joinid'];
            $result1 = $database_medoo->select("answer", ["answerContent"], ["questionnaireId" => $questionnaireId, "joinid" => $temp, "problemId" => "q2"]);
            $json['contentData'][$key]['name'] = $result1[0]['answerContent'];
        }
//        //从record表找填写时间
//        $result = $database_medoo->select("record", ["submitTime", "name"], ["questionnaireId" => $questionnaireId]);
//        foreach ($result as $key => $value) {
//            $json['contentData'][$key]['id'] = $key;
//            $json['contentData'][$key]['option'] = $result[$key]['answerContent'];
//        }
        $json['code'] = 1;
        $json['msg'] = "获取数据成功";
        echo json_encode($json);
    } else {
        $json['code'] = 0;
        $json['msg'] = "获取数据失败 数据库连接失败";
        echo json_encode($json);
    }
}

//问卷整体统计
function getAnalyseList($database_medoo, $questionnaireId) {
    if ($database_medoo) {
        //获取题目信息
        $result = $database_medoo->select("problem", ["problemId", "problemContent", "problemType"], ["questionnaireId" => $questionnaireId]);
        foreach ($result as $key => $value) {
            $json['analyseTitleList'][$key]['id'] = $key;
            $json['analyseTitleList'][$key]['qid'] = str_replace('q', '', $result[$key]['problemId']);
            $json['analyseTitleList'][$key]['qcontent'] = $result[$key]['problemContent'];
            $json['analyseTitleList'][$key]['qtype'] = $result[$key]['problemType'];
        }
        //获取内容信息
        $json['code'] = 1;
        $json['msg'] = "获取数据成功";
        echo json_encode($json);
    } else {
        $json['code'] = 0;
        $json['msg'] = "获取数据失败 数据库连接失败";
        echo json_encode($json);
    }
}

//登录
function login($database_medoo, $username, $password) {
    if ($database_medoo) {
        //用户名是否存在检查
        $result = $database_medoo->select("user", ["id"], ["userName" => $username]);
        if (!empty($result)) {
            //密码正确性检查
            $result = $database_medoo->get("user", ["passWord"], ["userName" => $username]);
            $result = $result['passWord'];
            $result1 = password_verify($password, $result);
            if ($result1) {
                $json['token'] = md5($username . sha1(time()));
                $json['code'] = 1;
                $json['msg'] = "登陆成功";
                echo json_encode($json);
            } else {
                $json['code'] = 0;
                $json['msg'] = "登陆失败，用户名或密码错误-密码";
                echo json_encode($json);
            }
        } else {
            $json['code'] = 0;
            $json['msg'] = "登陆失败，用户名或密码错误-用户名";
            echo json_encode($json);
        }
    } else {
        $json['code'] = 0;
        $json['msg'] = "登录失败 数据库连接失败";
        echo json_encode($json);
    }
}

//获取测评报告的数据内容
function getReportData($link, $db, $joinid) {
    if ($link) {
        //从record表读取name
        $sqlStr = "select name from $db.record where joinid='$joinid'";
        $result = $link->query($sqlStr);
        $result = $result->fetch_assoc();
        $json['name'] = $result['name'];
        //用joinid从record表查询questionnaireId
        $sqlStr = "select questionnaireId from $db.record where joinid='$joinid'";
        $result = $link->query($sqlStr);
        $result = $result->fetch_assoc();
        $questionnaireId = $result['questionnaireId'];
        //用questionnaireId从dimension表查dimensionId
        $sqlStr = "select id from $db.dimension where questionnaireId='$questionnaireId'";
        $result = $link->query($sqlStr);
        $index = 0;
        $dimensionQCount = 1; //一个问卷下一个维度包含的题目数量
        while ($row = $result->fetch_assoc()) {
            //用dimensionId和questionnaireId从problem表查题号problemId
            $dimensionId = $row['id'];
            $sqlStr = "select count(problemId) from $db.problem where dimensionId='$dimensionId' and questionnaireId='$questionnaireId'";
            $result0 = $link->query($sqlStr);
            $result0 = $result0->fetch_assoc();
            $dimensionQCount = ($result0['count(problemId)'] == 0) ? 1 : ($result0['count(problemId)']);
            $sqlStr = "select problemId from $db.problem where dimensionId='$dimensionId' and questionnaireId='$questionnaireId'";
            $result1 = $link->query($sqlStr);
            $score = 0;
            while ($row1 = $result1->fetch_assoc()) {
                //用probleId和joinid从answer表中查score
                $problemId = $row1['problemId'];
                $sqlStr = "select score from $db.answer where joinid='$joinid' and problemId='$problemId'";
                $result2 = $link->query($sqlStr);
                $result2 = $result2->fetch_assoc();
                $score += $result2['score'];
            }
            $json['radar_person'][$index] = round($score / $dimensionQCount, 2);
            $index++;
        }
        //获取表格数据
        $sqlStr = "select * from $db.dimension where questionnaireId='$questionnaireId'";
        $result = $link->query($sqlStr);
        $index = 0;
        while ($row = $result->fetch_assoc()) {
            $json['table_all'][$index]['dname'] = $row['dimensionName'];
            $json['table_all'][$index]['score'] = $json['radar_person'][$index];
            $json['table_all'][$index]['stand'] = (float) $row['standardScore'];
            $json['table_all'][$index]['result'] = $row['dimensionMean'];
            //雷达图中标准分
            $json['radar_stand'][$index] = (float) $row['standardScore'];
            //雷达图中高三均值，没有根据数量填充对应数量的0
            $json['radar_gaosan'][$index] = 0;
            //雷达图中理想均值，没有根据数量填充对应数量的0
            $json['radar_lixiang'][$index] = 0;
            //雷达图中维度名称和最大值
            $json['radar_indicator'][$index]['name'] = $row['dimensionName'];
            $json['radar_indicator'][$index]['max'] = 10;
            $index++;
        }
        $json['code'] = 1;
        $json['msg'] = $dimensionId;
        echo json_encode($json);
    } else {
        $json['code'] = 0;
        $json['msg'] = "获取数据失败 数据库连接失败";
        echo json_encode($json);
    }
}

//填写记录列表中，点击详情查看该条记录填写人的所有填写答案
function getRecordContent($link, $db, $questionnaireId, $joinid) {
    if ($link) {
        $sqlStr = "select * from $db.answer where joinid='$joinid' and questionnaireId='$questionnaireId'";
        $result = $link->query($sqlStr);
        $index = 0;
        while ($row = $result->fetch_assoc()) {
            $json['data'][$index]['id'] = $index;
            $json['data'][$index]['qId'] = $row['problemId'];

            $temp = $row['problemId'];
            $sqlStr = "select problemContent from $db.problem where questionnaireId='$questionnaireId' and problemId='$temp'";
            $result1 = $link->query($sqlStr);
            $result1 = $result1->fetch_assoc();
            $temp = $result1['problemContent'];

            $json['data'][$index]['qContent'] = $temp;
            $json['data'][$index]['qAnswer'] = $row['answerContent'];
            $index++;
        }
        $json['code'] = 1;
        $json['msg'] = "获取列表成功";
        echo json_encode($json);
    } else {
        $json['code'] = 0;
        $json['msg'] = "数据库连接失败";
        echo json_encode($json);
    }
}

//维度列表界面中，为维度添加或者删除题目
function dimensionAddQ($database_medoo, $questionnaireId, $dimensionId, $submitSelection) {
    if ($database_medoo) {
        //打开界面后无操作，直接点击确定关闭,此时为Array(1)
        if (!empty($submitSelection[0])) {
            //对$submitSelection进行处理，去除多余无用的数据，将二维数组变为一维数组
            $arr1 = array();
            foreach ($submitSelection as $key => $value) {
                $arr1[$key] = $submitSelection[$key]['qId'];
            }
            //对$result进行处理，去除无用的数据，将二维数组变为一维数组
            $arr2 = array();
            $result = $database_medoo->select("problem", ["problemId"], ["questionnaireId" => $questionnaireId, "dimensionId" => $dimensionId]);
            foreach ($result as $key => $value) {
                $arr2[$key] = $result[$key]['problemId'];
            }
            //用$arr1中的每个元素进行判断，判断是否在$arr2中,如果不在则进行维度添加题目操作
            foreach ($arr1 as $key => $value) {
                if (!in_array($arr1[$key], $arr2)) {
                    //在problem表中，该维度id下的题号为$arr[$key]的题目的维度id修改为$dimensionId
                    $database_medoo->update("problem", ["dimensionId" => $dimensionId], ["questionnaireId" => $questionnaireId, "problemId" => $arr1[$key]]);
                }
            }
            //更新$arr2的值
            $arr2 = array();
            $result = $database_medoo->select("problem", ["problemId"], ["questionnaireId" => $questionnaireId, "dimensionId" => $dimensionId]);
            foreach ($result as $key => $value) {
                $arr2[$key] = $result[$key]['problemId'];
            }
            //用$arr2中的每个元素进行判断，判断是否在$arr1中，如果不在进行维度删除题目操作
            foreach ($arr2 as $key => $value) {
                if (!in_array($arr2[$key], $arr1)) {
                    //在problem表中，该维度id下的题号为$arr[$key]的题目的维度id修改为0
                    $database_medoo->update("problem", ["dimensionId" => 0], ["questionnaireId" => $questionnaireId, "problemId" => $arr1[$key]]);
                }
            }
            $json['code'] = 1;
            $json['msg'] = "操作成功";
            echo json_encode($json);
//        } else {
//            //取消全部的选项，此时为Array(0)
//            
        }
        
/*
 * 此处有未来得及解决bug
 * 提交数组为空时，没有对应的解决方案  取消全部的选项，此时为Array(0)
 */
        
    } else {
        $json['code'] = 0;
        $json['msg'] = "操作失败 数据库连接失败";
        echo json_encode($json);
    }
}

//维度列表界面中，点击获取题目列表
function getDimensionQList($link, $db, $questionnaireId, $dimensionId) {
    if ($link) {
        $sqlStr = "select * from $db.problem where questionnaireId='$questionnaireId'";
        $result = $link->query($sqlStr);
        $index = 0;
        while ($row = $result->fetch_assoc()) {
            $json['data'][$index]['id'] = $index;
            $json['data'][$index]['qId'] = $row['problemId'];
            $json['data'][$index]['qContent'] = $row['problemContent'];

            $temp = $row['problemId'];
            $sqlStr = "select * from $db.problem where questionnaireId='$questionnaireId' and problemId='$temp' and dimensionId='$dimensionId'";
            $result1 = $link->query($sqlStr);
            if ($result1->fetch_assoc()) {
                $json['data'][$index]['_checked'] = true;
            } else {
                $sqlStr = "select * from $db.problem where questionnaireId='$questionnaireId' and problemId='$temp' and dimensionId!='$dimensionId' and dimensionId!=0";
                $result2 = $link->query($sqlStr);
                if ($result2->fetch_assoc()) {
                    $json['data'][$index]['_disabled'] = true;
                } else {
                    $json['data'][$index]['_disabled'] = false;
                }
            }
            $temp = $row['dimensionId'];
            $sqlStr = "select * from $db.dimension where id='$temp'";
            $result1 = $link->query($sqlStr);
            while ($row = $result1->fetch_assoc()) {
                $json['data'][$index]['dimension'] = $row['dimensionName'];
            }
            $index ++;
        }
        $json['code'] = 1;
        $json['msg'] = "获取列表成功";
        echo json_encode($json);
    } else {
        $json['code'] = 0;
        $json['msg'] = "获取列表失败 数据库连接失败";
        echo json_encode($json);
    }
}

//添加维度
function add_dimension($link, $db, $dimensionName, $dimensionMean, $questionnaireId) {
    if ($link) {
        if ($dimensionName == '') {
            $json['code'] = 0;
            $json['msg'] = "添加失败";
            echo json_encode($json);
        } else {
            $sqlStr = "select * from $db.dimension where questionnaireId='$questionnaireId' and dimensionName='$dimensionName'";
            $result = $link->query($sqlStr);
            if ($result->fetch_assoc()) { //如果该问卷下有重复的维度名称，提示错误
                $json['code'] = 0;
                $json['msg'] = "添加失败 维度名称不能重复";
                echo json_encode($json);
            } else {
                $sqlStr = "insert into $db.dimension(dimensionName,dimensionMean,questionnaireId) value('$dimensionName','$dimensionMean','$questionnaireId')";
                $result = $link->query($sqlStr);
                if ($result) {
                    $json['code'] = 1;
                    $json['msg'] = "添加成功";
                    echo json_encode($json);
                } else {
                    $json['code'] = 0;
                    $json['msg'] = "添加失败";
                    echo json_encode($json);
                }
            }
        }
    } else {
        $json['code'] = 0;
        $json['msg'] = "添加失败 数据库连接失败";
        echo json_encode($json);
    }
}

//删除维度
function deleteDimension($link, $db, $dimensionId, $questionnaireId) {
    if ($link) {
        //删除dimension表中的整条记录
        $sqlStr = "delete from $db.dimension where id='$dimensionId'";
        $result = $link->query($sqlStr);
        //删除problem表中的dimension字段记录
        $sqlStr = "update $db.problem set dimensionId='0' where dimensionId='$dimensionId' and questionnaireId='$questionnaireId'";
        $result1 = $link->query($sqlStr);
        if ($result && $result1) {
            $json['code'] = 1;
            $json['msg'] = "删除成功";
            echo json_encode($json);
        } else {
            $json['code'] = 0;
            $json['msg'] = "删除失败";
            echo json_encode($json);
        }
    } else {
        $json['code'] = 0;
        $json['msg'] = "删除失败 数据库连接失败";
        echo json_encode($json);
    }
}

//编辑问卷维度的名称和含义
function editDimension($link, $db, $dimensionId, $dimensionName, $dimensionMean, $standardScore, $questionnaireId) {
    if ($link) {
        if (!($dimensionName == '')) {
            $sqlStr = "select * from $db.dimension where questionnaireId='$questionnaireId' and dimensionName='$dimensionName' and id!='$dimensionId'";
            $result = $link->query($sqlStr);
            if ($result->fetch_assoc()) { //如果该问卷下有重复的维度名称，提示错误
                $json['code'] = 0;
                $json['msg'] = "编辑失败 维度名称不能重复";
                echo json_encode($json);
            } else {
                $sqlStr = "update $db.dimension set dimensionName='$dimensionName',dimensionMean='$dimensionMean',standardScore='$standardScore' where id='$dimensionId'";
                $result = $link->query($sqlStr);
                if ($result) {
                    $json['code'] = 1;
                    $json['msg'] = "编辑成功";
                    echo json_encode($json);
                } else {
                    $json['code'] = 0;
                    $json['msg'] = "编辑失败";
                    echo json_encode($json);
                }
            }
        } else {
            $json['code'] = 0;
            $json['msg'] = "编辑失败 维度名称不能为空";
            echo json_encode($json);
        }
    } else {
        $json['code'] = 0;
        $json['msg'] = "编辑失败 数据库连接失败";
        echo json_encode($json);
    }
}

//获取单个问卷的维度记录
function getDimensionList($link, $db, $questionnaireId, $pageSize, $current) {
    if ($link) {
        $begin = $pageSize * ($current - 1);
        //使用questionnaireId从dimension表中查询一个问卷包含的维度数量,前台分页展示需要
        $sqlStr = "select count(*) from $db.dimension where questionnaireId='$questionnaireId'";
        $result = $link->query($sqlStr);
        $result = $result->fetch_assoc();
        $total = $result['count(*)'];
        //使用questionnaireId从dimension表中查询维度名称和维度含义，列表展示需要
        $sqlStr = "select * from $db.dimension where questionnaireId='$questionnaireId' limit $begin,$pageSize";
        $result = $link->query($sqlStr);
        $index = 0;
        while ($row = $result->fetch_assoc()) {
            $bak = $row['id'];
            $json['bak'][$index]['dimensionId'] = $bak;
            $json['data'][$index]['id'] = $index;
            $json['data'][$index]['name'] = $row['dimensionName'];
            $json['data'][$index]['mean'] = $row['dimensionMean'];
            $json['data'][$index]['standardScore'] = (float) $row['standardScore'];
            $sqlStr = "select count(*) from $db.problem where questionnaireId='$questionnaireId' and dimensionId='$bak'";
            $result2 = $link->query($sqlStr);
            $result2 = $result2->fetch_assoc();
            $count = $result2['count(*)'];
            $json['data'][$index]['count'] = $count;
            $index++;
        }
        if ($index == 0) {
            $json['data'] = array();
            $json['bak'] = array();
        }
        $json['code'] = 1;
        $json['total'] = (int) $total;
        $json['msg'] = "获取记录成功";
        echo json_encode($json);
    } else {
        $json['code'] = 0;
        $json['msg'] = "获取记录失败 数据库连接失败";
        echo json_encode($json);
    }
}

//获取单个问卷的填写记录列表
function getRecordList($link, $db, $questionnaireId, $pageSize, $current) {
    if ($link) {
        $begin = $pageSize * ($current - 1);
        $sqlStr = "select * from $db.record where questionnaireId=$questionnaireId limit $begin,$pageSize";
        $result = $link->query($sqlStr);
        $id = 0;
        while ($row = $result->fetch_assoc()) {
            $json['data'][$id]['id'] = $id;
            $json['data'][$id]['name'] = $row['name'];
            $json['data'][$id]['sex'] = $row['sex'];
            $json['data'][$id]['school'] = $row['school'];
            $json['data'][$id]['grade'] = $row['grade'];
            $json['data'][$id]['class'] = $row['class'];
            $json['data'][$id]['totalValue'] = $row['totalValue'];
            $json['data'][$id]['submitTime'] = $row['submitTime'];
            $json['data'][$id]['questionnaireId'] = $row['questionnaireId'];
            $json['bak'][$id]['joinid'] = $row['joinid'];
            $id++;
        }
        if ($id == 0) {
            $json['data'] = array();
            $json['bak'] = array();
        }
        $temp = $link->query("select count(*) from $db.record where questionnaireId=$questionnaireId");
        $temp = $temp->fetch_assoc();
        $json['total'] = (int) $temp['count(*)'];
        $json['code'] = 1;
        $json['msg'] = "获取列表成功";
        echo json_encode($json);
    } else {
        $json['code'] = 0;
        $json['msg'] = "获取记录失败 数据库连接失败";
        echo json_encode($json);
    }
}

//获取所有问卷的名称列表
function getQuestionnaireList($link, $db) {
    if ($link) {
        $sqlStr = "select questionnaireName,questionnaireId  from $db.questionnaire";
        $result = $link->query($sqlStr);
        $id = 0;
        while ($row = $result->fetch_assoc()) {
            $json['data'][$id]['label'] = $row['questionnaireName'];
            $json['data'][$id]['value'] = $row['questionnaireId'];
//            $json['data'][$id]['id'] = $id;
            $id++;
        }
        $json['code'] = 1;
        $json['msg'] = "获取列表成功";
        echo json_encode($json);
    } else {
        $json['code'] = 0;
        $json['msg'] = "获取列表失败 数据库连接失败";
        echo json_encode($json);
    }
}

//添加问卷星问卷
function add_wjx($link, $db, $questionnaireId, $createUserId, $questionnaireName, $qresource, $sharePicture, $shareUrl, $qtime) {
    if ($link) {
        if (preg_match("/^\d*$/", $questionnaireId)) {
            $sqlStr = "select * from $db.questionnaire where questionnaireId='$questionnaireId'";
            $result = $link->query($sqlStr);
            if ($result->fetch_assoc()) { //如果要添加的问卷id已经存在，提示
                $json['code'] = 0;
                $json['msg'] = "添加失败 此问卷已被添加过";
                echo json_encode($json);
            } else {
                //此处代码为组合系统域名或者IP生成图片二维码和分享链接
                $sqlStr = "insert into $db.questionnaire(questionnaireId, createUserId, questionnaireName, qresource, sharePicture, shareUrl, qtime) value('$questionnaireId', '$createUserId', '$questionnaireName', '$qresource', '$sharePicture', '$shareUrl', '$qtime')";
                if (!mysqli_query($link, $sqlStr)) {
                    $json['code'] = 0;
                    $json['msg'] = "添加失败 数据格式错误";
                    echo json_encode($json);
                } else {
                    $json['code'] = 1;
                    $json['msg'] = "添加问卷成功";
                    echo json_encode($json);
                }
            }
        } else {
            $json['code'] = 0;
            $json['msg'] = "添加失败 问卷ID为纯数字";
            echo json_encode($json);
        }
    } else {
        $json['code'] = 0;
        $json['msg'] = "添加失败 数据库连接失败";
        echo json_encode($json);
    }
}

//删除问卷星问卷
function delete_wjx($link, $db, $questionnaireId) {
    if ($link) {
        $sqlStr = "delete from $db.questionnaire where questionnaireId = $questionnaireId";
        $sqlStr2 = "delete from $db.record where questionnaireId = $questionnaireId";
        $sqlStr3 = "delete from $db.answer where questionnaireId = $questionnaireId";
        $sqlStr4 = "delete from $db.dimension where questionnaireId = $questionnaireId";
        $sqlStr5 = "delete from $db.problem where questionnaireId = $questionnaireId";
        $result = $link->query($sqlStr);
        $result2 = $link->query($sqlStr2);
        $result3 = $link->query($sqlStr3);
        $result4 = $link->query($sqlStr4);
        $result5 = $link->query($sqlStr5);
        if ($result && $result2 && $result3 && $result4 && $result5) {
            $json['code'] = 1;
            $json['msg'] = "删除问卷成功";
            echo json_encode($json);
        } else {
            $json['code'] = 0;
            $json['msg'] = "删除问卷失败";
            echo json_encode($json);
        }
    } else {
        $json['code'] = 0;
        $json['msg'] = "删除问卷失败 数据库连接失败";
        echo json_encode($json);
    }
}

//开始暂停问卷
function pause_start($link, $db, $questionnaireId) {
    if ($link) {
        $sqlStr = "select qstatus from $db.questionnaire where questionnaireId = $questionnaireId";
        $result = $link->query($sqlStr);
        $result = $result->fetch_assoc();
        $result = $result['qstatus'];
        if ($result == 1) {
            $sqlStr = "update $db.questionnaire set qstatus=0 where questionnaireId = $questionnaireId";
            $result = $link->query($sqlStr);
            if ($result) {
                $json['code'] = 1;
                $json['msg'] = "暂停成功";
                echo json_encode($json);
            } else {
                $json['code'] = 0;
                $json['msg'] = "操作失败 数据库连接失败";
                echo json_encode($json);
            }
        } else if ($result == 0) {
            $sqlStr = "update $db.questionnaire set qstatus=1 where questionnaireId = $questionnaireId";
            $result = $link->query($sqlStr);
            if ($result) {
                $json['code'] = 1;
                $json['msg'] = "发布成功";
                echo json_encode($json);
            } else {
                $json['code'] = 0;
                $json['msg'] = "操作失败 数据库连接失败";
                echo json_encode($json);
            }
        }
    } else {
        $json['code'] = 0;
        $json['msg'] = "操作失败 数据库连接失败";
        echo json_encode($json);
    }
}

//获取问卷星问卷列表
function wjx_getList($link, $db, $pageSize, $current) {
    if ($link) {
        $begin = $pageSize * ($current - 1);
        $sqlStr = "select * from $db.questionnaire where qresource=1 limit $begin,$pageSize"; // 存在并且是未被标记删除的
        $result = $link->query($sqlStr);
        $id = 0;
        while ($row = $result->fetch_assoc()) {
            $json['data'][$id]['questionnaireId'] = $row['questionnaireId'];

            $temp = $row['questionnaireId'];
            $qreceive = $link->query("select count(*) from $db.record where questionnaireId=$temp");
            $qreceive = $qreceive->fetch_assoc();
            $json['data'][$id]['qreceive'] = $qreceive['count(*)'];

            $json['data'][$id]['questionnaireName'] = $row['questionnaireName'];
            $json['data'][$id]['qresource'] = "问卷星";
            $json['data'][$id]['qstatus'] = ($row['qstatus']) ? '接收中' : '已暂停';
            $json['data'][$id]['qtime'] = $row['qtime'];
            $json['data'][$id]['id'] = $id;
            $id++;
        }
        $temp = $link->query("select count(*) from $db.questionnaire where qresource=1");
        $temp = $temp->fetch_assoc();
        $json['total'] = (int) $temp['count(*)'];
        $json['code'] = 1;
        $json['msg'] = "获取列表成功";
        echo json_encode($json);
    } else {
        $json['code'] = 0;
        $json['msg'] = "获取列表失败";
        echo json_encode($json);
    }
}

//获取所有问卷列表
function all_getList($link, $db, $pageSize, $current) {
    if ($link) {
        $begin = $pageSize * ($current - 1);
        $sqlStr = "select * from $db.questionnaire limit $begin,$pageSize"; // 存在并且是未被标记删除的
        $result = $link->query($sqlStr);
        $id = 0;
        while ($row = $result->fetch_assoc()) {
            $json['data'][$id]['questionnaireId'] = $row['questionnaireId'];

            $temp = $row['questionnaireId'];
            $qreceive = $link->query("select count(*) from $db.record where questionnaireId=$temp");
            $qreceive = $qreceive->fetch_assoc();
            $json['data'][$id]['qreceive'] = $qreceive['count(*)'];

            $json['data'][$id]['questionnaireName'] = $row['questionnaireName'];
            $json['data'][$id]['qresource'] = ($row['qresource'] == 1) ? '问卷星' : '本地';
            $json['data'][$id]['qstatus'] = ($row['qstatus']) ? '接收中' : '已暂停';
            $json['data'][$id]['qtime'] = $row['qtime'];
            $json['data'][$id]['id'] = $id;
            $id++;
        }
        $temp = $link->query("select count(*) from $db.questionnaire where qresource=1");
        $temp = $temp->fetch_assoc();
        $json['total'] = (int) $temp['count(*)'];
        $json['code'] = 1;
        $json['msg'] = "获取列表成功";
        echo json_encode($json);
    } else {
        $json['code'] = 0;
        $json['msg'] = "获取列表失败";
        echo json_encode($json);
    }
}

?>