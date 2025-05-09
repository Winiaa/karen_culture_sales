# Karen Culture Sales - Use Case Details

## Use Case Name (Guest/Customer)
| Use Case Name | Description |
|--------------|-------------|
| Browse Products | User can view available products, filter by categories, view new arrivals and best sellers |
| Search Products | User can search for products by name, category, or description |
| Manage Cart | User can add/remove items from cart, update quantities, and view cart total |
| Login/Register | User can create new account or login to existing account |
| Place Order | User can checkout items in cart, enter shipping details, and select payment method |
| Track Order | User can view order status and delivery updates using order number |

## Use Case Name (Admin)
| Use Case Name | Description |
|--------------|-------------|
| Manage Products | Admin can add, edit, delete products including details, images, and inventory |
| Manage Categories | Admin can create, edit, delete product categories and set their status |
| Manage Orders | Admin can view all orders, update order status, and process refunds |
| Manage Users | Admin can view user list, edit user roles, and manage user access |
| Manage Drivers | Admin can add/remove drivers, assign orders, and monitor delivery performance |
| Review Management | Admin can moderate customer reviews, approve/reject reviews |
| Sales Reports | Admin can generate sales reports, view revenue statistics, and export data |

## Use Case Name (Delivery Driver)
| Use Case Name | Description |
|--------------|-------------|
| View Assigned Deliveries | Driver can see list of orders assigned for delivery |
| Update Delivery Status | Driver can update order status (picked up, in transit, delivered) |
| Collect Payment | Driver can collect and record cash payment for COD orders |
| Report Issues | Driver can report delivery issues or problems with orders |

## Detailed Flow Examples

### Place Order (Customer)
**Primary Actor:** Customer
**Precondition:** User is logged in and has items in cart
**Main Flow:**
1. Customer reviews items in cart
2. Customer clicks "Proceed to Checkout"
3. Customer enters/confirms shipping information
4. Customer selects payment method:
   - Credit Card (Stripe)
   - Cash on Delivery
5. Customer confirms order
6. System processes order and sends confirmation

**Alternative Flow:**
- If payment fails:
  1. System shows payment error
  2. Customer can try again or choose different payment method
- If items out of stock:
  1. System notifies customer
  2. Customer can remove items or wait for restock

### Manage Orders (Admin)
**Primary Actor:** Admin
**Precondition:** Admin is logged in
**Main Flow:**
1. Admin views list of all orders
2. Admin can:
   - View order details
   - Update order status
   - Assign driver for delivery
   - Process refunds if needed
3. System updates order status
4. System notifies customer of changes

**Alternative Flow:**
- If refund needed:
  1. Admin reviews refund request
  2. Processes refund through payment system
  3. Updates order status
  4. System notifies customer

### Deliver Order (Driver)
**Primary Actor:** Driver
**Precondition:** Driver is logged in and assigned to order
**Main Flow:**
1. Driver views assigned deliveries
2. Driver accepts delivery assignment
3. Driver picks up order
4. Driver updates delivery status
5. Driver delivers to customer
6. For COD orders:
   - Collects payment
   - Marks payment as received
7. Marks order as delivered

**Alternative Flow:**
- If delivery fails:
  1. Driver reports issue
  2. Updates delivery status
  3. System notifies admin and customer
  4. Reschedules delivery if needed 