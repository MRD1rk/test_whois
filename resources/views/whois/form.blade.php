<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>WHOIS Lookup</title>
    <script>
        async function doWhois(event) {
            event.preventDefault();
            const domain = document.getElementById('domain').value;
            const resp = await fetch(`/api/whois?domain=${encodeURIComponent(domain)}`);
            const data = await resp.json();

            document.getElementById('output').textContent = data.whois || JSON.stringify(data, null, 2);

            const jsonString = JSON.stringify(data, null, 2);
            const blob = new Blob([jsonString], { type: 'application/json' });
            const url = URL.createObjectURL(blob);

            const dl = document.getElementById('download');
            dl.href = url;
            dl.download = `${domain}-whois.json`;
            dl.style.display = 'inline-block';
        }
    </script>
    <style>
        #download {
            margin-left: 1em;
            display: none;
        }
    </style>
</head>
<body>
<h1>WHOIS Lookup</h1>

<form onsubmit="doWhois(event)">
    <input type="text" id="domain" placeholder="example.com" required>
    <button type="submit">Перевірити</button>
    <a id="download">Download JSON</a>
</form>

<pre id="output" style="margin-top:20px; background:#f4f4f4; padding:10px;"></pre>
</body>
</html>
