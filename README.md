# 📘 Smart Multi-Tenant Supply & CRM Platform

## 🏢 Project Overview

**Scope:**  
End-to-end multi-tenant CRM + Order & Supply Chain platform connecting:

- Super Admin (Platform Owner)
- Manufacturers (Tenant Admins)
- Suppliers
- Distributors (1 warehouse each)
- Vendors
- Consumers (End Users)

The system manages:

- CRM (Accounts, Contacts, Tickets)
- Product Catalog
- Multi-Level Pricing & Margins
- Inventory & Warehouse
- Negotiations & Quotes
- Orders & Fulfillment
- Full Audit Trail

---

# 🎯 Objectives

## Primary Objective
Build a scalable SaaS platform enabling manufacturers to manage their complete supply network with strict pricing governance.

## Success Criteria

- Support multiple manufacturers (strict tenant isolation)
- Handle 10k+ concurrent consumers
- End-to-end order traceability
- Configurable pricing & margin engine
- Complete audit trail

---

# 🏗 Multi-Tenancy Model

## Tenant Hierarchy

Level 0: Super Admin  
Level 1: Manufacturer (Tenant)  
Level 2+: Supplier → Distributor → Vendor → Consumer  

All entities belong to exactly one tenant.

## Isolation Rules

- All tables include `tenant_id`
- No cross-tenant access
- Strict RBAC enforcement

---

# 👥 Roles

- SuperAdmin
- ManufacturerAdmin
- SupplierUser
- DistributorUser
- VendorUser
- Consumer

Permissions scoped by:
- Catalog
- Pricing
- Inventory
- Orders
- CRM
- Reports

---

# 🧱 Core Domain Entities

## Tenant
- tenant_id
- name
- branding
- currency
- locale

## OrganizationUnit
- org_unit_id
- tenant_id
- type (SUPPLIER | DISTRIBUTOR | VENDOR)

## Warehouse
- warehouse_id
- org_unit_id (Distributor only)

## Product
- product_id
- tenant_id
- SKU
- base_unit
- base_price

## MarginRule
- level
- min_margin
- max_margin

## Inventory
- warehouse_id
- product_id
- quantity_on_hand
- reserved_quantity
- safety_stock

## Order
- order_id
- type
- status
- from_org
- to_org

## OrderLine
- product_id
- quantity
- unit_price
- margin_snapshot

## Quote
- status (DRAFT | SENT | COUNTERED | ACCEPTED | REJECTED | EXPIRED)

---

# 💰 Pricing & Margin Engine

Let:

P_M = Manufacturer Base Price  
m_S = Supplier Margin  
m_D = Distributor Margin  
m_V = Vendor Margin  

Supplier Price:
```
P_S = P_M × (1 + m_S)
```

Distributor Price:
```
P_D = P_S × (1 + m_D)
```

Vendor Price:
```
P_V = P_D × (1 + m_V)
```

### Validation Rules

- Must stay within configured margin range
- No negative margins (unless allowed)
- Margin snapshot stored per order line
- Approval workflow for overrides

---

# 📦 Order Flow (Simplified)

Consumer → Vendor  
Vendor → Distributor  
Distributor → Supplier  
Supplier → Manufacturer  

Warehouse inventory updated via:

- GRN (Goods Received Note)
- GIN (Goods Issue Note)
- Adjustments

---

# 🔁 Negotiation Workflow

States:

- DRAFT
- SENT
- COUNTERED
- ACCEPTED
- REJECTED
- EXPIRED

Rules:

- Each counter validated against margin constraints
- Approval required if outside allowed range
- Full audit logging

---

# 📊 CRM Features

- Accounts
- Contacts
- Activities
- Opportunities
- Tickets
- Escalation across levels

---

# ⚙ Functional Modules

## Super Admin
- Tenant management
- Subscription plans
- Global configs
- Platform monitoring

## Manufacturer
- Network onboarding
- Product & pricing management
- Margin policies
- Reporting

## Supplier
- Replenishment handling
- PO to manufacturer
- Quote generation

## Distributor
- Single warehouse management
- Inventory tracking
- Vendor pricing
- Reorder automation

## Vendor
- Consumer catalog
- Checkout system
- Bulk ordering to distributor

## Consumer
- Browse
- Order
- Track
- Support

---

## Sequence Diagram
```mermaid
sequenceDiagram
  participant C as Consumer
  participant V as Vendor
  participant D as Distributor
  participant W as Distributor_Warehouse
  participant S as Supplier
  participant M as Manufacturer

  C->>V: Place order
  V->>D: Bulk PO (if needed)
  D->>W: Check stock
  alt Stock sufficient
    D->>V: Confirm
    V->>C: Fulfil order
  else Stock insufficient
    D->>S: Replenishment request
    S->>M: Production order
    M->>S: Ship goods
    S->>D: Ship goods
    D->>W: Receive & update
    D->>V: Supply to vendor
    V->>C: Fulfil order
  end
```
# 🚀 Release Phases

## Phase 1 
- Multi-tenant structure
- Basic pricing engine
- Core order flow
- Inventory management
- Basic CRM

## Phase 2
- Advanced negotiations
- Approval workflows
- Campaign engine
- Advanced CRM

## Phase 3
- ERP integrations
- Multi-currency
- Dynamic pricing
- Advanced analytics

---

# 🔐 Non-Functional Requirements

## Security
- RBAC
- Tenant isolation
- Audit logs
- HTTPS encryption

## Performance
- Product listing < 2s
- Checkout < 3s

## Reliability
- 99.5% uptime target
- Graceful degradation

---

# 📌 Assumptions

- Single currency per tenant (MVP)
- One warehouse per distributor
- Vendors act as sales nodes (no warehouse initially)
- Consumer pays vendor directly
- Revenue sharing outside MVP scope

---

# 📎 Architecture (Conceptual)

Client Apps:
- Super Admin Portal
- Manufacturer Portal
- Supplier Portal
- Distributor Portal
- Vendor Portal
- Consumer App

Core Services:
- API Gateway
- Auth Service
- Tenant Service
- Product Service
- Order Service
- Inventory Service
- CRM Service
- Notification Service

Database:
- Logical Multi-Tenant DB
- All tables scoped by `tenant_id`

---
