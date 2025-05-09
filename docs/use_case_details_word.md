Karen Culture Sales - Use Case Details

1. Guest/Customer Use Cases

1.1 Browse Products
Description: Users can view available products in the store. They can filter products by categories and access special sections like new arrivals and best sellers. The browsing feature allows users to see product images, prices, and basic details.

1.2 Search Products
Description: Users can find specific products using the search function. They can search by product name, category, or description to quickly find items they're interested in.

1.3 Manage Cart
Description: Users can add products to their shopping cart, adjust quantities, and remove items. The cart automatically calculates the total price and shows a summary of selected items.

1.4 Login/Register
Description: Users can create a new account by providing their email and personal information, or login to an existing account using their credentials.

1.5 Place Order
Description: Users can proceed to checkout with items in their cart. They must provide shipping information and choose between payment methods (Credit Card via Stripe or Cash on Delivery).

1.6 Track Order
Description: Users can monitor their order status using their order number. They can view current status, delivery updates, and estimated delivery time.

2. Admin Use Cases

2.1 Manage Products
Description: Administrators can maintain the product catalog by:
- Adding new products with details, prices, and images
- Editing existing product information
- Managing product inventory levels
- Removing products from the catalog
- Setting product visibility and availability

2.2 Manage Categories
Description: Administrators can organize products by:
- Creating new product categories
- Editing category names and descriptions
- Setting category status (active/inactive)
- Deleting unused categories
- Arranging category hierarchy

2.3 Manage Orders
Description: Administrators oversee all orders by:
- Viewing complete order details
- Updating order status
- Processing refunds when necessary
- Assigning orders to delivery drivers
- Monitoring order fulfillment

2.4 Manage Users
Description: Administrators control user accounts by:
- Viewing all user accounts
- Managing user roles and permissions
- Activating or deactivating accounts
- Handling user access issues

2.5 Manage Drivers
Description: Administrators handle delivery personnel by:
- Adding new delivery drivers
- Assigning orders to drivers
- Monitoring delivery performance
- Managing driver schedules
- Handling driver-related issues

2.6 Review Management
Description: Administrators moderate customer reviews by:
- Reviewing submitted customer feedback
- Approving or rejecting reviews
- Managing product ratings
- Responding to customer feedback

2.7 Sales Reports
Description: Administrators can access business analytics through:
- Generating daily/weekly/monthly sales reports
- Viewing revenue statistics
- Analyzing sales trends
- Exporting data for further analysis
- Monitoring business performance

3. Delivery Driver Use Cases

3.1 View Assigned Deliveries
Description: Drivers can see their assigned deliveries, including:
- Delivery addresses
- Customer contact information
- Order details
- Delivery priorities

3.2 Update Delivery Status
Description: Drivers can update order status throughout the delivery process:
- Marking orders as picked up
- Updating delivery progress
- Confirming successful deliveries
- Recording delivery times

3.3 Collect Payment
Description: For Cash on Delivery orders, drivers can:
- Collect payment from customers
- Record payment collection
- Mark orders as paid
- Handle payment-related issues

3.4 Report Issues
Description: Drivers can report delivery problems:
- Documenting failed delivery attempts
- Reporting address issues
- Noting customer availability problems
- Requesting support for delivery challenges

4. Detailed Process Flows

4.1 Place Order Process
Primary Actor: Customer
Precondition: User is logged in and has items in cart

Main Flow:
1. Customer reviews items in shopping cart
2. Customer initiates checkout process
3. Customer enters or confirms shipping information
4. Customer selects preferred payment method:
   - Credit Card payment through Stripe
   - Cash on Delivery option
5. Customer confirms order placement
6. System processes order and sends confirmation

Alternative Flows:
A. Payment Failure:
   1. System displays payment error message
   2. Customer can retry payment or choose different method
   3. Order remains pending until payment is successful

B. Stock Issues:
   1. System checks inventory during checkout
   2. If items are out of stock, customer is notified
   3. Customer can remove unavailable items or wait for restock

4.2 Order Management Process
Primary Actor: Admin
Precondition: Admin is logged into the system

Main Flow:
1. Admin accesses order management dashboard
2. Admin reviews order details and status
3. Admin processes order by:
   - Confirming order details
   - Updating order status
   - Assigning delivery driver
   - Processing any necessary refunds
4. System updates order information
5. Customer receives notification of changes

Alternative Flow (Refund Process):
1. Admin reviews refund request
2. Processes refund through payment system
3. Updates order status accordingly
4. System sends refund confirmation to customer

4.3 Delivery Process
Primary Actor: Driver
Precondition: Driver is logged in and assigned to orders

Main Flow:
1. Driver reviews assigned deliveries for the day
2. Driver accepts delivery assignments
3. Driver collects orders for delivery
4. Driver updates delivery status throughout process
5. Driver completes delivery to customer
6. For COD orders:
   - Collects payment from customer
   - Records payment in system
7. Marks delivery as completed

Alternative Flow (Failed Delivery):
1. Driver documents delivery issue
2. Updates delivery status with problem
3. System notifies admin and customer
4. Delivery is rescheduled as needed 