@include('Layout::app')
<html>

<head>
    <meta name="viewport" content="width=device, initial-scale=0.8">
    <script>
        document.body.addEventListener('contextmenu', function(event) {
            event.preventDefault();
        });
        window.addEventListener('contextmenu', function(event) {
            event.preventDefault();
        });
        window.addEventListener('keydown', function(event) {
            if (event.key === 'F12' || (event.ctrlKey && event.shiftKey && (event.key === 'I' || event.key ===
                'C'))) {
                event.preventDefault();
            }
        });

        function isDevToolsOpen() {
            const widthThreshold = 160;
            const heightThreshold = 160;

            return window.outerWidth - window.innerWidth > widthThreshold || window.outerHeight - window.innerHeight >
                heightThreshold;
        }

        setInterval(() => {
            if (isDevToolsOpen()) {
                window.location.href = '/developer-tools-detected';
            }
        }, 3000);
    </script>
</head>

<body >

</body>

</html>
