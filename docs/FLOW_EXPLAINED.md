# AI Voice Agent – Pura Flow (Start se End)

## Flow 1: Test Route – AI Query Process (Tool Calling)

**Start:** User browser ya API se request karta hai:  
`GET /test-ai/order_status/1`

---

### Step 1: Route (Entry Point)

**File:** `routes/web.php`

```php
Route::get('/test-ai/{queryType}/{customerId}', function (string $queryType, int $customerId, QueryProcessorService $processor) {
    $result = $processor->process($queryType, $customerId);
    return response()->json($result);
});
```

**Kya hota hai:**
- Laravel request ko match karta hai, `queryType` = `order_status`, `customerId` = `1`
- Laravel container se `QueryProcessorService` inject karta hai (constructor mein dependencies auto-inject)
- `$processor->process('order_status', 1)` call hota hai
- Jo array return hota hai (query, response, call_log_id) woh JSON mein bhej diya jata hai

**Purpose:** Test route – bina login ke AI flow check karne ke liye. Production mein is route ko hata dena ya auth laga dena.

---

### Step 2: QueryProcessorService::process()

**File:** `app/Services/QueryProcessorService.php`

**Flow:**
1. **Query nikalna:**  
   `$query = $this->simulatedQueries[$queryType] ?? $queryType;`  
   - Agar `queryType` = `order_status` hai to `$query` = "What is the status of my latest order?"  
   - Agar key nahi mili to `$queryType` hi use (free text)

2. **Executor banana:**  
   `$executor = new GeminiToolExecutor($customerId, $this->orderService, $this->ticketService);`  
   - Is executor ke paas customerId + OrderService + TicketService hote hain  
   - Purpose: Gemini jab tool call karega (e.g. get_latest_order), to yeh executor woh tool run karega aur result wapas dega

3. **Gemini se response (tool calling ke sath):**  
   `$response = $this->geminiService->generateWithToolCalling($query, $customerId, $executor);`  
   - Andar: user message + tools Gemini ko bheje jate hain, response mein agar functionCall aata hai to executor se run karke result wapas bhej kar dubara generateContent, jab tak final text na aaye

4. **Call log + messages save:**  
   - `CallLogService::createLog($customerId, $query)` → naya row `call_logs` mein  
   - `addMessage($callLog->id, 'user', $query)` → user message `conversation_messages` mein  
   - `addMessage($callLog->id, 'assistant', $response)` → AI ka final reply save  

5. **Return:**  
   `['query' => $query, 'response' => $response, 'call_log_id' => $callLog->id]`

**Purpose:** Ek customer “query” ko process karna – Gemini ko bhejna, tools chalwana, reply save karna, aur caller ko result dena.

---

### Step 3: GeminiService::generateWithToolCalling()

**File:** `app/Services/GeminiService.php`

**Flow (high level):**
1. API key check (`config('services.gemini.api_key')`)
2. **Tools define:**  
   `GeminiToolDefinitions::getTools()` se tools ka array (function declarations) – get_latest_order, create_ticket, search_faq, etc.
3. **Pehla request:**  
   - `contents` = ek user message: "What is the status of my latest order?"  
   - `tools` = yeh function declarations  
   - `systemInstruction` = customer support AI, tools use karo, short answer do  
   - POST → `generativelanguage.googleapis.com/.../generateContent`

4. **Response handle:**
   - Response ke `candidates[0].content.parts` check karo
   - Agar koi part mein **`text`** hai → yahi final answer, return kar do
   - Agar koi part mein **`functionCall`** hai (name + args):
     - **Tool run:** `$executor->execute($name, $args)`  
       - Example: name = `get_latest_order`, args = []  
       - Executor andar `OrderService::getLatestOrder($customerId)` call karega, result array return karega
     - **Conversation extend:**  
       - `contents` mein model ka part (functionCall) add karo  
       - Phir user part add karo jisme **functionResponse** (name + result)
     - Phir **dubara** `generateContent` call karo (same URL, updated contents, tools phir se bhej sakte ho)
5. **Loop:** Step 4 tab tak repeat (max 5 rounds) jab tak response mein **text** part na aa jaye; jo last text aata hai woh return hota hai

**Purpose:** Gemini ko user message do, tools do; Gemini decide kare ke kaun sa tool chahiye; hum tool run karke result wapas bhejen; Gemini final user-friendly reply generate kare.

---

### Step 4: GeminiToolDefinitions::getTools()

**File:** `app/Services/GeminiToolDefinitions.php`

**Kya hota hai:**  
Ek array return hota hai jisme **functionDeclarations** hote hain – har ek tool ke liye:
- `name` – e.g. get_latest_order, create_ticket
- `description` – kab use karna hai (Gemini isse dekh ke tool choose karta hai)
- `parameters` – OpenAPI-style schema (e.g. order_number string, issue_type, description)

**Purpose:** Gemini ko batana ke tumhare paas yeh yeh “functions” available hain, isliye model inke naam aur parameters ke hisaab se **functionCall** bhejega.

---

### Step 5: GeminiToolExecutor::execute()

**File:** `app/Services/GeminiToolExecutor.php`

**Flow:**  
`execute(string $name, array $args)` mein:
- **get_latest_order** → `OrderService::getLatestOrder($this->customerId)` → order ka array (order_number, status, delivery_date, amount) ya “no orders”
- **get_order_by_number** → `OrderService::getOrderByNumber($customerId, $args['order_number'])` → us order ka data
- **get_open_tickets** → `TicketService::getOpenTickets($customerId)` → open/in-progress tickets list
- **create_ticket** → `TicketService::createTicket($customerId, $args['issue_type'], $args['description'])` → DB mein ticket, success + ticket_id
- **search_faq** → `Faq` model se query se match karke question/answer

Return hamesha **array** (JSON-serializable) – yahi Gemini ko **functionResponse** mein bhejta hai.

**Purpose:** Gemini ke functionCall ko actual database/actions se run karna aur result wapas dena.

---

### Step 6: CallLogService (createLog + addMessage)

**File:** `app/Services/CallLogService.php`

- **createLog($customerId, $query):**  
  `call_logs` table mein naya row – customer_id, simulated_query, status, etc.  
  **Purpose:** Har “call”/query ka record rakhna.

- **addMessage($callLogId, $role, $content):**  
  `conversation_messages` mein row – call_log_id, role (user/assistant), content.  
  **Purpose:** User message aur AI reply dono save karna taake baad mein conversation dekh saken (admin/portal).

---

### Flow 1 Summary (Diagram)

```
Browser/API: GET /test-ai/order_status/1
       ↓
routes/web.php → QueryProcessorService::process('order_status', 1)
       ↓
QueryProcessorService:
  1. query = "What is the status of my latest order?"
  2. executor = new GeminiToolExecutor(1, OrderService, TicketService)
  3. response = GeminiService::generateWithToolCalling(query, 1, executor)
       ↓
GeminiService::generateWithToolCalling():
  → tools = GeminiToolDefinitions::getTools()
  → POST Gemini (user message + tools)
  → Response: functionCall get_latest_order
  → executor->execute('get_latest_order', []) → OrderService::getLatestOrder(1) → order data
  → POST Gemini again (previous + functionResponse)
  → Response: text "Your latest order #ORD-C1-1 is shipped..."
  → return that text
       ↓
QueryProcessorService:
  4. createLog(1, query)  → call_logs
  5. addMessage(..., 'user', query)
  6. addMessage(..., 'assistant', response)
  7. return [query, response, call_log_id]
       ↓
routes/web.php → response()->json($result) → Browser ko JSON
```

---

## Flow 2: Admin Panel (/admin)

**Start:** User browser mein `/admin` open karta hai.

---

### Step 1: Panel + Middleware

**File:** `app/Providers/Filament/AdminPanelProvider.php`

- Panel: id = admin, path = admin, login enabled
- **authMiddleware:**  
  1. `Authenticate` – login nahi hai to login page pe bhejta hai  
  2. `EnsureUserIsAdmin` – login ke baad check: user ke paas role super_admin / admin / support_agent hona chahiye, nahi to 403

**Purpose:** Sirf authenticated admin/support_agent hi /admin tak pahunch sakein.

---

### Step 2: EnsureUserIsAdmin

**File:** `app/Http/Middleware/EnsureUserIsAdmin.php`

- `$request->user()->hasRole(['super_admin', 'admin', 'support_agent'])`  
  Agar false → abort(403).  
**Purpose:** Role se admin area restrict karna.

---

### Step 3: Filament Resources (List / Create / Edit / View)

**Files:** `app/Filament/Resources/*Resource.php` (Customer, Order, Ticket, CallLog, Faq, User, Role, Permission)

- Har resource **canAccess()** mein: `auth()->user()->can('viewAny', Model::class)`  
  → Ye **Policy** se check hota hai (e.g. CustomerPolicy::viewAny → user can view_any_customers?)
- Table, form, pages (list, create, edit, view) Filament define karta hai
- **Role edit:** handleRecordUpdate mein role update + syncPermissions (checkboxes se selected permissions)

**Purpose:** Admin ko CRUD dikhana aur permission se control karna; role edit pe permissions sahi save hona.

---

### Step 4: Policies

**Files:** `app/Policies/CustomerPolicy.php`, etc.

- viewAny, view, create, update, delete → andar `$user->can('view_any_customers')` jaisa Spatie permission check  
**Purpose:** Har action ko Spatie permissions se bind karna taake role-based access theek rahe.

---

## Flow 3: Customer Portal (/portal)

**Start:** User `/portal` open karta hai.

---

### Step 1: Panel + Middleware

**File:** `app/Providers/Filament/PortalPanelProvider.php`

- path = portal
- **authMiddleware:**  
  1. `Authenticate` – login nahi to login  
  2. `EnsureUserIsCustomer` – role must be customer, aur user ka **customer** (profile) hona chahiye

**Purpose:** Sirf customer role + linked customer profile wale users portal use karein.

---

### Step 2: EnsureUserIsCustomer

**File:** `app/Http/Middleware/EnsureUserIsCustomer.php`

- hasRole('customer', 'web')
- $user->customer exist karta hai ya nahi  
  Agar nahi → 403.  
**Purpose:** Portal sirf real “customers” (with profile) ke liye.

---

### Step 3: Portal Resources (My Orders, My Tickets, My Call History)

**Files:** `app/Filament/Portal/Resources/MyOrderResource.php`, MyTicketResource, MyCallHistoryResource

- **getEloquentQuery():**  
  `Order::query()->where('customer_id', auth()->user()->customer->id)`  
  → Har resource sirf us logged-in customer ki data dikhata hai.
- List / view / create (ticket) Filament se; create ticket pe customer_id automatically set.

**Purpose:** Customer ko sirf apni orders, apne tickets, apni call history dikhana.

---

## Kis code ka kya purpose (short)

| Code | Purpose |
|------|--------|
| **routes/web.php** (test-ai) | Flow start – request ko QueryProcessorService tak pohonchana |
| **QueryProcessorService** | Query resolve karna, executor banana, Gemini se reply lena, call log + messages save karna |
| **GeminiService::generateWithToolCalling** | User message + tools Gemini ko bhejna, functionCall aane pe tool run karke result bhejna, final text return karna |
| **GeminiToolDefinitions** | Gemini ke liye tools declare karna (name, description, parameters) |
| **GeminiToolExecutor** | functionCall ko DB/services se run karke result array dena |
| **OrderService / TicketService / CallLogService** | Orders, tickets, call logs + messages ko read/write karna |
| **CallLogService (createLog, addMessage)** | Har query ka log + user/assistant messages save karna |
| **AdminPanelProvider + EnsureUserIsAdmin** | /admin sirf admin/support_agent ke liye kholna |
| **PortalPanelProvider + EnsureUserIsCustomer** | /portal sirf customer (with profile) ke liye kholna |
| **Policies** | Har resource/action ko Spatie permission se link karna |
| **Filament Resources (admin)** | Admin ko CRUD + roles/permissions manage karwana |
| **Filament Portal Resources** | Customer ko sirf apni data (orders, tickets, call history) dikhana |

Yahi se flow start hota hai (test route ya /admin ya /portal) aur upar diye steps ke hisaab se code execute hota hai; har code ka purpose table mein short form mein diya gaya hai.
