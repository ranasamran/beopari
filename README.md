# Beopari API Documentation

Base URL:  
`http://localhost:8000/api/`

---

## **Authentication**

### Register
- **POST** `/register`
- **Body:** `form-data`
  - `name` (string, required)
  - `email` (string, required)
  - `password` (string, required)
  - `password_confirmation` (string, required)
  - `company_name` (string, required)
- **Response:** `{ user, company, token }`

### Login
- **POST** `/login`
- **Body:** `form-data`
  - `email` (string, required)
  - `password` (string, required)
- **Response:** `{ user, company, token }`

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
- **Body:** `application/json`
  - `name`, `contact`, `address`, `logo`, `shopname` (all optional)

---

## **Profile**

### Get Profile
- **GET** `/profile`
- **Header:** `Authorization: Bearer {token}`

### Update Profile
- **PUT** `/profile`
- **Header:** `Authorization: Bearer {token}`
- **Body:** `application/json`
  - `name`, `email` (optional)

### Change Password
- **PUT** `/profile/password`
- **Header:** `Authorization: Bearer {token}`
- **Body:** `form-data`
  - `current_password`, `password`, `password_confirmation`

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
  - `images[]` (file, multiple, optional)

### Get Product (with images)
- **GET** `/products/{id}`
- **Header:** `Authorization: Bearer {token}`

### Upload Images to Product
- **POST** `/products/{id}/images`
- **Header:** `Authorization: Bearer {token}`
- **Body:** `form-data`
  - `images[]` (file, multiple, required)

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

---

## **Order Details**

### List Order Details
- **GET** `/order-details`
- **Header:** `Authorization: Bearer {token}`

---

## **Payees**

### List Payees
- **GET** `/payees`
- **Header:** `Authorization: Bearer {token}`

---

## **Payee Transactions**

### List Payee Transactions
- **GET** `/payee-trans`
- **Header:** `Authorization: Bearer {token}`

---

## **Banks**

### List Banks
- **GET** `/banks`
- **Header:** `Authorization: Bearer {token}`

---

## **Bank Transactions**

### List Bank Transactions
- **GET** `/bank-trans`
- **Header:** `Authorization: Bearer {token}`

---

## **Authentication**

All endpoints (except `/register` and `/login`) require a Bearer token in the `Authorization` header.

---

## **Notes**
- For file uploads, use `form-data` and the field name `images[]`.
- All list endpoints return arrays of objects.
- All create/update endpoints return the created/updated object.

---

**For a ready-to-import Postman collection, see `beopari.postman_collection.json`.** 
