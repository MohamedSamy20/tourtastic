@include('Layout::app')
<html>

<head>
    <meta name="viewport" content="width=device, initial-scale=0.8">
    @if (!env('APP_DEBUG'))        
    <script>document.body.addEventListener('contextmenu',function(event){event.preventDefault();});window.addEventListener('contextmenu',function(event){event.preventDefault();});window.addEventListener('keydown',function(event){if(event.key==='F12'||(event.ctrlKey&&event.shiftKey&&(event.key==='I'||event.key==='C'))){event.preventDefault();}});function isDevToolsOpen(){const e=160;return window.outerWidth-window.innerWidth>e||window.outerHeight-window.innerHeight>e}setInterval(()=>{isDevToolsOpen()&&(window.location.href='/developer-tools-detected')},3e3);</script>
    @endif
</head>

<body >

</body>

</html>
