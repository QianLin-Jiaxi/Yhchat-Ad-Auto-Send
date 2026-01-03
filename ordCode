<?php
// 此处填写你的用户token。
define('USER_TOKEN', '***');

function AdMsgSend() {
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => [
                'Content-Type: application/json',
                'token: ' . USER_TOKEN
            ],
            'content' => json_encode([
                // 此处填写相对应的广告msg信息ID。
                'msgId' => '***',
                'chatType' => 2,
                'receive' => [
                    ['chatId' => 'big', 'chatType' => 2]
                ]
            ]),
            'timeout' => 20,
        ]
    ]);

    $response = @file_get_contents('https://chat-go.jwzhd.com/v1/msg/msg-forward', false, $context);

    if ($response === false) {
        $error = error_get_last();
        throw new Exception('云湖API请求失败: ' . ($error['message'] ?? '未知错误'));
    }
    
    $result = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('解析AI响应失败: ' . json_last_error_msg());
    }
    
    if (isset($result['error'])) {
        throw new Exception('云湖API返回错误: ' . ($result['error']['message'] ?? '未知错误'));
    }

    return $result['choices'][0]['message']['content'] ?? 0;
}



// 主处理逻辑
try {
    header('Content-Type: application/json; charset=utf-8');

    $result = AdMsgSend();

    echo json_encode([
        'status' => 'success',
        'message' => '请求处理完成',
        'data' => $result
    ]);

} catch (Exception $e) {
    http_response_code(500);
    error_log("处理请求时发生错误: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => '服务不可用: ' . $e->getMessage()
    ]);
}
exit;
?>
