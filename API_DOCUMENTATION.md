# API Documentation

Base URL: `https://3ware.com.mx/tequila/erp/admin/eventos_actividades/eventos_api` (Default derived from `https://3ware.com.mx/tequila/erp/admin/api`)

## Authentication

**Login**
- **Endpoint**: `/login`
- **Method**: `POST`
- **Description**: Authenticates a user and returns a token.
- **Request Body**:
  ```json
  {
    "email": "user@example.com",
    "password": "password123"
  }
  ```
- **Response (200 OK)**:
  ```json
  {
    "token": "JWT_TOKEN_HERE",
    "user": {
      "firstname": "John",
      "lastname": "Doe",
      "avatar_url": "http://...",
      "points": 100
    }
  }
  ```

## Products & Content

**Get Products (Cava)**
- **Endpoint**: `/products`
- **Method**: `GET`
- **Description**: Retrieves a list of beverages/products.
- **Response (200 OK)**: `List<Product>`
  ```json
  [
    {
      "barcode": "123456",
      "name": "Tequila X",
      "brand": "Brand Y",
      "presentation": "750ml",
      "alcohol_degrees": 38.0,
      "price": 500.0
    }
  ]
  ```

**Get Mezcal Content**
- **Endpoint**: `/mezcal`
- **Method**: `GET`
- **Description**: Retrieves dynamic content for Mezcal section.
- **Response (200 OK)**:
  ```json
  {
    "images": [{"url": "...", "title": "..."}],
    "videos": [...],
    "texts": [...]
  }
  ```

**Get Tequila Content**
- **Endpoint**: `/tequila`
- **Method**: `GET`
- **Description**: Retrieves dynamic content for Tequila section.
- **Response (200 OK)**: Same structure as Mezcal.

**Get Banners**
- **Endpoint**: `/banners`
- **Method**: `GET`
- **Description**: Retrieves promotional banners/ads.
- **Response (200 OK)**: `List<Banner>`

**Get Marcas (Brands)**
- **Endpoint**: `/marcas`
- **Method**: `GET`
- **Description**: Retrieves list of brands.
- **Response (200 OK)**: `List<Brand>`

**Get Avatars**
- **Endpoint**: `/avatars`
- **Method**: `GET`
- **Description**: Retrieves available user avatars.
- **Response (200 OK)**: `List<Avatar>`

## User Actions

**Scan Barcode**
- **Endpoint**: `/scan`
- **Method**: `POST`
- **Description**: Uploads a barcode scan to the server.
- **Request Body**:
  ```json
  {
    "barcode": "123456",
    "latitude": 19.4326,
    "longitude": -99.1332,
    "username": "user",
    "date": "ISO8601_DATE"
  }
  ```
- **Response (200 OK)**:
  ```json
  {
    "points_awarded": 10,
    "message": "Success"
  }
  ```

**Add to Cava**
- **Endpoint**: `/add_to_cava`
- **Method**: `POST`
- **Description**: Adds a product to the user's personal cava.
- **Request Body**:
  ```json
  {
    "barcode": "123456"
  }
  ```
- **Response (200 OK)**: `{"success": true}` (or similar)
