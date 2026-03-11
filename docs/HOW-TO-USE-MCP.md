# How to Use MCP in This Project

This app uses **MCP (Model Context Protocol)** so AI agents (and your voice call flow) can run the same tools: orders, tickets, FAQ, and Laravel Boost tools.

---

## 1. Where MCP Is Used

| Context | How MCP is used |
|--------|------------------|
| **Voice calls (Twilio)** | When a caller asks about orders/tickets, Gemini triggers tools → your app runs the **same MCP tools** in-process via `McpToolRunner` and returns the result. |
| **Gemini CLI** | You run `gemini` from the project root; it talks to `php artisan boost:mcp` and can call all MCP tools (orders, tickets, DB, routes, etc.). |
| **Cursor** | If Cursor is configured to use the Laravel Boost MCP server, the AI can use the same tools from the editor. |

---

## 2. Use MCP from Gemini CLI

### One-time setup (from project root)

```bash
cd /home/muhammad-hyder-ali/Desktop/Ai-Agent
gemini mcp add -s project -t stdio laravel-boost php artisan boost:mcp
```

Your project already has `.gemini/settings.json` with this server, so Gemini may already see it.

### Run Gemini and list tools

```bash
cd /home/muhammad-hyder-ali/Desktop/Ai-Agent
gemini
```

In the Gemini prompt:

```
/mcp list
```

You should see **laravel-boost** and its tools, including:

- **Order / Ticket tools:** GetLatestOrder, GetOrderByNumber, GetOpenTickets, CreateTicket, SearchFaq, OrderStatus  
- **Boost tools:** Application Info, Database Query, List Routes, Get Config, Search Docs, Tinker, etc.

### Example prompts in Gemini

- *“Get the latest order for customer_id 1”* → uses GetLatestOrder (or OrderStatus).
- *“List open tickets for customer 1”* → uses GetOpenTickets.
- *“Create a ticket for customer 1: Refund request, wrong item delivered”* → uses CreateTicket.
- *“Search FAQ for return policy”* → uses SearchFaq.

Gemini will call the right MCP tool with the arguments it needs.

---

## 3. Use MCP from Cursor

1. **Ensure Cursor has the MCP server config**  
   If you ran `boost:install` with Cursor enabled, you may have a config under `.cursor/` (e.g. `mcp.json`). If not, add the server in Cursor’s MCP settings.

2. **Open MCP settings in Cursor**  
   - **Command Palette** (`Ctrl+Shift+P` / `Cmd+Shift+P`) → **“Open MCP Settings”** or **“Cursor Settings”** and find MCP.

3. **Add or enable the Laravel Boost MCP server**  
   Example (exact keys may vary by Cursor version):

   ```json
   {
     "mcpServers": {
       "laravel-boost": {
         "command": "php",
         "args": ["artisan", "boost:mcp"],
         "cwd": "/home/muhammad-hyder-ali/Desktop/Ai-Agent"
       }
     }
   }
   ```

   Use `cwd` so the server runs in your project root (required for `artisan` and `.env`).

4. **Use it**  
   In chat, ask things like: “What’s the latest order for customer 1?” or “List routes for this app.” Cursor will use the MCP server and your tools.

---

## 4. Run the MCP Server Manually (for debugging)

From the project root:

```bash
php artisan boost:mcp
```

The server runs over stdio (stdin/stdout). MCP clients (Gemini CLI, Cursor) start this process and talk to it via JSON-RPC. You don’t need to run it yourself in normal use.

---

## 5. Your Custom MCP Tools (Order & Ticket)

These are registered in `AppServiceProvider` and run both on **calls** and when an **MCP client** uses them:

| MCP tool name | Purpose |
|---------------|--------|
| `get-latest-order` | Latest order for a customer |
| `get-order-by-number` | Order by order number |
| `get-open-tickets` | Open/in-progress tickets |
| `create-ticket` | Create a support ticket |
| `search-faq` | Search FAQ by query |
| `order-status` | Order status (list of recent orders) |

All of them use your **service classes** (`OrderService`, `TicketService`) and models, so behavior is the same whether the caller is a voice flow or an AI in Gemini/Cursor.

---

## 6. Quick checklist

- [ ] Run `gemini` from project root and run `/mcp list` to see tools.
- [ ] In Cursor: Command Palette → “Open MCP Settings” and add/enable **laravel-boost** with `php` + `artisan boost:mcp` and correct `cwd`.
- [ ] Always run Gemini CLI (and the MCP server) from the **project root** so `artisan` and `.env` work.

For more on Boost + Gemini, see [BOOST-GEMINI-SETUP.md](../BOOST-GEMINI-SETUP.md).
