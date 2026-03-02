<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Query Demo – {{ config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; max-width: 600px; margin: 2rem auto; padding: 0 1rem; background: #f5f5f5; }
        h1 { font-size: 1.5rem; margin-bottom: 0.5rem; }
        .sub { color: #666; font-size: 0.9rem; margin-bottom: 1.5rem; }
        .card { background: #fff; border-radius: 8px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,.1); margin-bottom: 1rem; }
        label { display: block; font-weight: 600; margin-bottom: 0.35rem; }
        select, input { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #ccc; border-radius: 6px; font-size: 1rem; margin-bottom: 1rem; }
        button { background: #2563eb; color: #fff; border: none; padding: 0.6rem 1.25rem; border-radius: 6px; font-size: 1rem; cursor: pointer; }
        button:hover { background: #1d4ed8; }
        button:disabled { opacity: 0.6; cursor: not-allowed; }
        #response { white-space: pre-wrap; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 1rem; min-height: 80px; margin-top: 1rem; }
        .loading { color: #64748b; }
        .err { color: #dc2626; }
        .meta { font-size: 0.85rem; color: #64748b; margin-top: 0.75rem; }
    </style>
</head>
<body>
    <h1>AI Query Demo</h1>
    <p class="sub">Static query select karein, Send pe click karein – Gemini tools ke sath response aayega.</p>

    <div class="card">
        <label for="queryType">Query type</label>
        <select id="queryType">
            <option value="order_status">Order status – What is the status of my latest order?</option>
            <option value="ticket_creation">Ticket creation – I have an issue, my delivered product is damaged.</option>
            <option value="ticket_status">Ticket status – Can you check the status of my open ticket?</option>
            <option value="general_faq">General FAQ – What are your customer support working hours?</option>
        </select>

        <label for="customerId">Customer ID</label>
        <input type="number" id="customerId" value="1" min="1" />

        <button type="button" id="btnSend">Send to Gemini</button>
    </div>

    <div class="card">
        <label>Response</label>
        <div id="response" class="loading">Response yahan aayega…</div>
        <div id="meta" class="meta"></div>
    </div>

    <script>
        document.getElementById('btnSend').addEventListener('click', async function () {
            const queryType = document.getElementById('queryType').value.trim();
            const customerId = document.getElementById('customerId').value.trim() || '1';
            const responseEl = document.getElementById('response');
            const metaEl = document.getElementById('meta');

            if (!queryType) {
                responseEl.textContent = 'Query type select karein.';
                responseEl.className = 'err';
                return;
            }

            responseEl.textContent = 'Loading… (Gemini + tools call ho rahe hain)';
            responseEl.className = 'loading';
            metaEl.textContent = '';
            this.disabled = true;

            try {
                const url = `/test-ai/${encodeURIComponent(queryType)}/${encodeURIComponent(customerId)}`;
                const res = await fetch(url);
                const data = await res.json();

                if (!res.ok) {
                    responseEl.textContent = (data.message || 'Request failed.') + (data.errors ? '\n' + JSON.stringify(data.errors) : '');
                    responseEl.className = 'err';
                } else {
                    responseEl.textContent = data.response || '(no response text)';
                    responseEl.className = '';
                    metaEl.textContent = 'Query: ' + (data.query || '') + (data.call_log_id ? '  •  Call log ID: ' + data.call_log_id : '');
                }
            } catch (e) {
                responseEl.textContent = 'Error: ' + e.message;
                responseEl.className = 'err';
                metaEl.textContent = '';
            }

            this.disabled = false;
        });
    </script>
</body>
</html>
