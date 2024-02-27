<?php
// 连接到 MySQL 数据库
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "demo";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 通过 file_get_contents 获取 POST 数据
    $postData = json_decode(file_get_contents("php://input"), true);

    if (isset($postData['limit'])) {
        // $inputNumber = $postData['number']; //指定ID前缀
        $Tablename  = $postData['License']; //指定ID前缀
        $offset = isset($postData['offset']) ? $postData['offset'] : 0; // 获取偏移量
        $limit = isset($postData['limit']) ? $postData['limit'] : 10000; // 获取每批请求的数量

        // 创建连接
        $conn = new mysqli($servername, $username, $password, $dbname);

        // 检查连接是否成功
        if ($conn->connect_error) {
            die("连接失败: " . $conn->connect_error);
        }

        // 设置字符集为utf8
        $conn->set_charset("utf8");

        // 从数据库中读取匹配的数据
       // $idPrefix = $inputNumber; // 将输入数字作为ID前缀
        $sql = "SELECT * FROM $Tablename WHERE (MOD(ID, 10) = 0) LIMIT $offset, $limit";
        $result = $conn->query($sql);

        // 处理查询结果
        $response = array();
        if ($result && mysqli_num_rows($result) > 0) {
            // 输出每行数据
            while ($row = mysqli_fetch_assoc($result)) {
                $response[] = array(
                    'id' => $row["ID"],
                    'lng' => $row["LNG"],
                    'lat' => $row["LAT"],
                    'uptime' => $row["UPTIME"]
                );
            }
        } else {
           // echo "没有找到匹配的记录";
        }

        // 关闭连接
        $conn->close();

        // 将结果编码为JSON字符串并返回
        echo json_encode($response);
    } else {
        echo json_encode(['error' => '缺少必要的参数']);
    }
} else {
    // 如果不是POST请求，返回错误信息
    header('HTTP/1.1 405 Method Not Allowed');
    header('Allow: POST');
    echo '只允许POST请求';
}
?>