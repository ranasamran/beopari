# Beopari API Documentation

Base URL:
`http://localhost:8000/api/`

---

## **Authentication**

### Signup
- **POST** `/signup`
- **Body:** `form-data`
  - `name` (string, required)
  - `email` (string, required)
  - `password` (string, required)
  - `password_confirmation` (string, required)
  - `company_name` (string, required)
  - `company_contact` (string, optional)
  - `company_address` (string, optional)
  - `company_logo` (string, optional)
  - `company_shopname` (string, optional)
- **Response:** `{ user, company, token }`

### Login
- **POST** `/login`
- **Body:** `form-data`
  - `email` (string, required)
  - `password` (string, required)
- **Response:** `{ user, company, token }`

### Test API
- **GET** `/test-api`
- Returns: `"API is working"`

### Logout
- **POST** `/logout`
- **Header:** `Authorization: Bearer {token}`

---

## **Company**

### Get Company
- **GET** `/company`
- **Header:** `Authorization: Bearer {token}`

### Update Company
- **PUT** `/company`
- **Header:** `Authorization: Bearer {token}`
- **Body:** `form-data`
  - `name` (string, optional)
  - `contact` (string, optional)
  - `address` (string, optional)
  - `logo` (file, image, optional)
  - `shopname` (string, optional)

---

## **Profile**

### Get Profile
- **GET** `/profile`
- **Header:** `Authorization: Bearer {token}`

### Update Profile
- **PUT** `/profile`
- **Header:** `Authorization: Bearer {token}`
- **Body:** `application/json`
  - `name` (string, optional)
  - `email` (string, optional)

### Change Password
- **PUT** `/profile/password`
- **Header:** `Authorization: Bearer {token}`
- **Body:** `form-data`
  - `current_password` (string, required)
  - `password` (string, required)
  - `password_confirmation` (string, required)

---

## **Products**

### List Products
- **GET** `/products`
- **Header:** `Authorization: Bearer {token}`

### Create Product (with images)
- **POST** `/products`
- **Header:** `Authorization: Bearer {token}`
- **Body:** `form-data`
  - `name` (string, required)
  - `quantity` (integer, required)
  - `cost_price` (number, required)
  - `retail_price` (number, required)
  - `margin` (number, required)
  - `type` (enum: 1=finished, 2=raw, required)
  - `description` (string, optional)
  - `images[]` (file, image, multiple, optional)

### Get Product (with images)
- **GET** `/products/{id}`
- **Header:** `Authorization: Bearer {token}`

### Update Product
- **PUT** `/products/{id}`
- **Header:** `Authorization: Bearer {token}`
- **Body:** `application/json`
  - Any of: `name`, `quantity`, `cost_price`, `retail_price`, `margin`, `description`, `type`

### Delete Product
- **DELETE** `/products/{id}`
- **Header:** `Authorization: Bearer {token}`

### Upload Images to Product
- **POST** `/products/{id}/images`
- **Header:** `Authorization: Bearer {token}`
- **Body:** `form-data`
  - `images[]` (file, image, multiple, required)

---

## **Orders**

### List Orders
- **GET** `/orders`
- **Header:** `Authorization: Bearer {token}`

### Create Order
- **POST** `/orders`
- **Header:** `Authorization: Bearer {token}`
- **Body:** `application/json`
  - `gross_total`, `discount`, `total_paid`, `balance`, `tyre`, `customer`, `number`, `payable`
  - `details` (array of objects, required)
    - `product_id`, `name`, `quantity`, `price`

### Get Order (with details)
- **GET** `/orders/{id}`
- **Header:** `Authorization: Bearer {token}`

### Update Order
- **PUT** `/orders/{id}`
- **Header:** `Authorization: Bearer {token}`
- **Body:** `application/json`
  - Any of: `gross_total`, `discount`, `total_paid`, `balance`, `tyre`, `customer`, `number`, `payable`
  - Optional `details` array replaces existing details if provided

### Delete Order
- **DELETE** `/orders/{id}`
- **Header:** `Authorization: Bearer {token}`

### Download Order PDF
- **GET** `/orders/pdf/{id}`
- **Header:** `Authorization: Bearer {token}`

---

## **Order Details**

### List Order Details
- **GET** `/order-details`
- **Header:** `Authorization: Bearer {token}`

### Create Order Detail
- **POST** `/order-details`
- **Header:** `Authorization: Bearer {token}`
- **Body:** `application/json`
  - `order_id`, `product_id`, `name`, `quantity`, `price`

### Get Order Detail
- **GET** `/order-details/{id}`
- **Header:** `Authorization: Bearer {token}`

### Update Order Detail
- **PUT** `/order-details/{id}`
- **Header:** `Authorization: Bearer {token}`
- **Body:** `application/json`
  - Any of: `name`, `quantity`, `price`

### Delete Order Detail
- **DELETE** `/order-details/{id}`
- **Header:** `Authorization: Bearer {token}`

---

## **Payees**

### List Payees
- **GET** `/payees`
- **Header:** `Authorization: Bearer {token}`

### Create Payee (supports multiple images)
- **POST** `/payees`
- **Header:** `Authorization: Bearer {token}`
- **Body:** `form-data`
  - `name` (string, required)
  - `contact` (string, required)
  - `payable` (number, required)
  - `type` (string, required)
  - `date` (date, optional)
  - `order_date` (date, optional)
  - `delivery_date` (date, optional)
  - `image` (file, image, optional) — add one image
  - `images[]` (file, image, multiple, optional) — add multiple images
- **Response:** Payee with `images: [{ id, url }]` and `image_url` (first image)

### Get Payee (with images)
- **GET** `/payees/{id}`
- **Header:** `Authorization: Bearer {token}`

### Update Payee (add/remove images)
- **PUT** `/payees/{id}`
- **Header:** `Authorization: Bearer {token}`
- **Body:** `form-data`
  - Any of: `name`, `contact`, `payable`, `type`, `date`, `order_date`, `delivery_date`
  - `image` (file, image, optional) — add one
  - `images[]` (file, image, multiple, optional) — add many
  - `remove_image` (boolean, optional) — remove legacy single image
  - `remove_image_ids[]` (integer[], optional) — delete specific images by id

### Delete Payee
- **DELETE** `/payees/{id}`
- **Header:** `Authorization: Bearer {token}`

---

## **Payee Transactions**

### List Payee Transactions
- **GET** `/payee-trans`
- **Header:** `Authorization: Bearer {token}`

### Create Payee Transaction
- **POST** `/payee-trans`
- **Header:** `Authorization: Bearer {token}`
- **Body:** `application/json`
  - `name`, `cus_id` (payee id), `amount`, `remain_amount`, `status`, `datetime`, `description`

### Get Payee Transaction
- **GET** `/payee-trans/{id}`
- **Header:** `Authorization: Bearer {token}`

### Update Payee Transaction
- **PUT** `/payee-trans/{id}`
- **Header:** `Authorization: Bearer {token}`
- **Body:** `application/json`
  - Any of: `name`, `amount`, `remain_amount`, `status`, `datetime`, `description`

### Delete Payee Transaction
- **DELETE** `/payee-trans/{id}`
- **Header:** `Authorization: Bearer {token}`

---

## **Banks**

### List Banks
- **GET** `/banks`
- **Header:** `Authorization: Bearer {token}`

### Create Bank
- **POST** `/banks`
- **Header:** `Authorization: Bearer {token}`
- **Body:** `application/json`
  - `title`, `number`, `name`, `balance`, `status`

### Get Bank
- **GET** `/banks/{id}`
- **Header:** `Authorization: Bearer {token}`

### Update Bank
- **PUT** `/banks/{id}`
- **Header:** `Authorization: Bearer {token}`
- **Body:** `application/json`
  - Any of: `title`, `number`, `name`, `balance`, `status`

### Delete Bank
- **DELETE** `/banks/{id}`
- **Header:** `Authorization: Bearer {token}`

---

## **Bank Transactions**

### List Bank Transactions
- **GET** `/bank-trans`
- **Header:** `Authorization: Bearer {token}`

### Create Bank Transaction
- **POST** `/bank-trans`
- **Header:** `Authorization: Bearer {token}`
- **Body:** `application/json`
  - `bank_id`, `name`, `cus_id`, `amount`, `status`, `datetime`, `description`

### Get Bank Transaction
- **GET** `/bank-trans/{id}`
- **Header:** `Authorization: Bearer {token}`

### Update Bank Transaction
- **PUT** `/bank-trans/{id}`
- **Header:** `Authorization: Bearer {token}`
- **Body:** `application/json`
  - Any of: `name`, `cus_id`, `amount`, `status`, `datetime`, `description`

### Delete Bank Transaction
- **DELETE** `/bank-trans/{id}`
- **Header:** `Authorization: Bearer {token}`

---

## **Notes**
- For file uploads, use `form-data`.
  - Products: `images[]`
  - Payees: `image` (single) and/or `images[]` (multiple)
  - Company logo: `logo`
- All list endpoints return arrays of objects.
- All create/update endpoints return the created/updated object.
- Authentication: all endpoints (except `/signup`, `/login`, and `/test-api`) require a Bearer token in the `Authorization` header.

---

**For a ready-to-import Postman collection, see `beopari.postman_collection.json`.** 
