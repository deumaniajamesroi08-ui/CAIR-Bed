<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Carebed AI Test</title>
    <style>
        body{font-family:Arial;background:#071b14;color:white;padding:30px}
        .box{max-width:900px;margin:auto;background:rgba(255,255,255,.08);padding:25px;border-radius:18px}
        button{background:#10b981;color:white;border:0;padding:12px 18px;border-radius:10px;font-weight:bold;cursor:pointer}
        pre{white-space:pre-wrap;background:rgba(0,0,0,.25);padding:20px;border-radius:14px}
    </style>
</head>
<body>
<div class="box">
    <h1>Carebed AI Analysis Test</h1>
    <p>This automatically analyzes the logged-in user's latest sensor reading.</p>
    <button onclick="runAI()">Run Again</button>
    <pre id="result">Loading latest AI analysis...</pre>
</div>

<script>
function runAI(){
    document.getElementById('result').textContent='Analyzing...';

    fetch('script/ai_analyze_latest.php')
        .then(r=>r.json())
        .then(data=>{
            if(data.status === 'success'){
                document.getElementById('result').textContent =
                    data.ai_response.analysis || JSON.stringify(data, null, 2);
            } else {
                document.getElementById('result').textContent = JSON.stringify(data, null, 2);
            }
        })
        .catch(err=>{
            document.getElementById('result').textContent = err;
        });
}

document.addEventListener('DOMContentLoaded', runAI);
</script>
</body>
</html>
