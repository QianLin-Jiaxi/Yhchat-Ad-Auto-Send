<?php
define('USER_TOKEN', '***');

// 定义msgId数组，可以添加多个msgId
$msgIdPool = [
    '***',
    '***',
    '***'
];

function AdMsgSend($selectedMsgId) {
    $postData = json_encode([
        'msgId' => $selectedMsgId,
        'chatType' => 2,
        'receive' => [
            ['chatId' => 'big', 'chatType' => 2]
        ]
    ]);

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://chat-go.jwzhd.com/v1/msg/msg-forward',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'token: ' . USER_TOKEN,
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ],
        CURLOPT_TIMEOUT => 20,
        CURLOPT_FOLLOWLOCATION => true,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        throw new Exception('云湖API请求失败: ' . $curlError . ' (HTTP Code: ' . $httpCode . ')');
    }
    
    $result = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('解析API响应失败: ' . json_last_error_msg());
    }
    
    if (isset($result['error'])) {
        throw new Exception('云湖API返回错误: ' . ($result['error']['message'] ?? '未知错误'));
    }

    return $result['choices'][0]['message']['content'] ?? 0;
}

// 主处理逻辑
try {
    header('Content-Type: application/json; charset=utf-8');
    
    // 从msgId数组中随机选择一个
    $randomIndex = array_rand($msgIdPool);
    $selectedMsgId = $msgIdPool[$randomIndex];
    
    $result = AdMsgSend($selectedMsgId);

    echo json_encode([
        'status' => 'success',
        'message' => '请求处理完成',
        'data' => $result, // 广告是否发送成功
        'selected_msg_id' => $selectedMsgId, // 可选：返回使用的msgId，便于追踪发的是哪条广告
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
