<?php
// 如果是POST请求，处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取前端传递过来的目标URL参数
    $targetUrl = isset($_POST['targetUrl']) ? $_POST['targetUrl'] : '';
    // 检查目标URL是否为空
    if (empty($targetUrl)) {
        echo json_encode(['success' => false, 'error' => 'URL不能为空']);
        exit();
    }
    // 仅保留「QQ/微信直链打开」功能：对目标URL进行Base64加密，拼接GitHub上的index.html地址
    $encodedUrl = base64_encode($targetUrl);
    // 替换【你的GitHub用户名】和【你的仓库名】为实际信息
    $shortUrl = 'https://wushang-002.github.io/home/xiaozheng.html?xzfh=' . $encodedUrl;
    // 返回生成结果
    $result = ['success' => true, 'shortUrl' => $shortUrl];
    echo json_encode($result);
    exit();
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <link rel="icon" href="https://api.vvhan.com/api/wallpaper/acg">
    <title>防红直链生成接口,全网升级首发,域名直链,直链打开,防红跳转</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <meta name="keywords" content="QQ微信直链生成,Base64加密链接">
    <meta name='description' content='仅需输入目标URL，一键生成QQ/微信可直接打开的链接'>
    <link rel="stylesheet" href="assets/css/main.css" />
    <style>
        #resultContainer {
            color: #1cb495;
            display: flex;
            align-items: center;
            margin-top: 20px;
        }
        #shortUrlDisplay {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            background: #fff;
        }
        body {
            background-color: #000;
            background-size: cover;
            background-position: center;
            padding: 20px;
            color: #fff;
        }
        #signup-form {
            margin: 20px 0;
        }
        #targetUrl {
            width: 70%;
            padding: 10px;
            border-radius: 100px;
            margin-right: 10px;
        }
        #generateButton {
            padding: 10px 20px;
            border-radius: 100px;
            background: #1cb495;
            color: #fff;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body class="is-preload">
<header id="header">
    <h1>小郑直链生成器</h1>
    <p>输入需要生成直链的URL，一键获取可直接打开的链接（点击生成结果可复制）</p>
    <p style="font-size: 14px; color: rgba(255,255,255,.5);">
        (请勿生成非法内容，否则后果自负&nbsp;用户反馈:<a id="Link" title="复制联系js">@Fis_p</a>)
    </p>
</header>

<!-- 仅保留URL输入框和生成按钮，删除接口选择下拉框 -->
<form id="signup-form" method="post">
    <input type="url" name="targetUrl" id="targetUrl" placeholder="请输入目标URL（如https://example.com）" onkeydown="handleKeyDown(event)" required>
    <input type="button" value="生成直链" id="generateButton" onclick="getUrl()" />
</form>

<!-- 协议同意勾选框 -->
<div style="display: flex;align-items: center; margin: 10px 0;">
    <input type="checkbox" name="agree" id="agree" style="width: 16px;height: 16px;display: inline-block;" required>
    <label for="agree" style="margin: 0 16px;color: rgba(255,255,255,.5);">
        我同意<a style="color: rgba(255,255,255,.5);" href="//weixin.qq.com/cgi-bin/readtemplate?t=weixin_external_links_content_management_specification">《链接内容管理规范》</a>
    </label>
</div>

<!-- 生成结果显示区域 -->
<div id="resultContainer">
    <!-- 短链接结果将在这里显示 -->
</div>

<footer id="footer" style="margin-top: 30px;">
    <ul class="icons">
        <li><a href="https://github.com" class="icon brands fa-github" target="_blank"><span class="label">GitHub</span></a></li>
    </ul>
    <ul class="copyright">
        <li>&copy; Powered by <a href="https://github.com" target="_blank">js</a></li>
    </ul>
</footer>

<script>
// 生成直链核心函数
function getUrl() {
    var targetUrl = document.getElementById('targetUrl').value;
    var generateButton = document.getElementById('generateButton');
    var resultContainer = document.getElementById('resultContainer');
    var agreeCheckbox = document.getElementById('agree');

    // 检查协议同意状态
    if (!agreeCheckbox.checked) {
        alert('请先同意《链接内容管理规范》');
        return;
    }

    // 显示加载状态
    generateButton.disabled = true;
    generateButton.value = '生成中...';
    resultContainer.innerHTML = '';

    // AJAX请求生成链接
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            generateButton.disabled = false;
            generateButton.value = '生成直链';

            if (response.success) {
                // 显示生成的直链，点击可复制
                resultContainer.innerHTML = `
                    <div id="shortUrlDisplay" onclick="copyToClipboard()">
                        生成成功：${response.shortUrl}
                    </div>
                `;
            } else {
                alert('生成失败：' + response.error);
            }
        }
    };
    xhr.send("targetUrl=" + encodeURIComponent(targetUrl));
}

// 复制链接到剪贴板
function copyToClipboard() {
    var shortUrl = document.getElementById('shortUrlDisplay').innerText.replace('生成成功：', '');
    var textArea = document.createElement('textarea');
    textArea.value = shortUrl;
    document.body.appendChild(textArea);
    textArea.select();

    try {
        document.execCommand('copy');
        document.getElementById('shortUrlDisplay').innerText = '复制成功！链接：' + shortUrl;
    } catch (err) {
        alert('复制失败，请手动复制：' + shortUrl);
    }
    document.body.removeChild(textArea);
}

// 回车触发生成
function handleKeyDown(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        getUrl();
    }
}
</script>

<!-- 背景图和基础JS引用 -->
<script src="assets/js/main.js" defer=""></script>
<div id="bg">
    <div style="background-image: url(&quot;https://moe.jitsu.top/img/?sort=pc&amp;random=0.7225703771330143&quot;); background-position: center center; background-size: cover; width: 100%; height: 100%; position: fixed; top: 0; left: 0; z-index: -1;" class="visible"></div>
</div>
</body>
</html>
