# Module-Wise API Documentation

This documentation details the API endpoints available for the Tequila Mobile App, organized by functional module. All endpoints are hosted under the `Eventos_actividades` module acting as the API Gateway.

**Base URL**: `https://3ware.com.mx/tequila/erp/admin/eventos_actividades/eventos_api`

## 1. Authentication Module

### Login
- **Endpoint**: `/login`
- **Method**: `POST`
- **Description**: Authenticates a user using email and password. Returns a JWT-like token for subsequent requests.
- **Parameters**:
  - `email` (string, required)
  - `password` (string, required)
- **Response**:
  ```json
  {
    "token": "a1b2c3d4...",
    "user": {
      "id": "1",
      "firstname": "John",
      "lastname": "Doe",
      "email": "john@example.com",
      "avatar_url": "https://.../avatar.png",
      "points": 150
    }
  }
  ```

### Get Current User (Me)
- **Endpoint**: `/me`
- **Method**: `GET`
- **Headers**: `Authorization: Bearer <token>`
- **Description**: Retrieves current user profile, including updated points and avatar.
- **Response**:
  ```json
  {
    "data": {
      "id": "1",
      "firstname": "John",
      "lastname": "Doe",
      "email": "john@example.com",
      "avatar_url": "https://.../avatar.png",
      "points": 150
    }
  }
  ```

---

## 2. Cava (Wine/Beverage) Module

### Get Products
- **Endpoint**: `/products`
- **Method**: `GET`
- **Headers**: `Authorization: Bearer <token>`
- **Description**: Retrieves the master catalog of beverages (wines/tequilas) available for the Cava.
- **Response**:
  ```json
  [
    {
      "id": "10",
      "name": "Don Julio 70",
      "barcode": "750123456789",
      "brand": "Don Julio",
      "type": "Tequila",
      "image": "don_julio.png"
    }
  ]
  ```

### Add to My Cava
- **Endpoint**: `/add_to_cava`
- **Method**: `POST`
- **Headers**: `Authorization: Bearer <token>`
- **Description**: Adds a specific product to the authenticated user's personal Cava.
- **Parameters**:
  - `barcode` (string, required)
- **Response**:
  ```json
  {
    "success": true
  }
  ```

---

## 3. Scanner Module

### Register Scan
- **Endpoint**: `/scan`
- **Method**: `POST`
- **Headers**: `Authorization: Bearer <token>`
- **Description**: Registers a barcode scan. Used for gamification (earning points) and tracking user activity.
- **Parameters**:
  - `barcode` (string, required)
  - `latitude` (float, optional)
  - `longitude` (float, optional)
  - `date` (string, optional, YYYY-MM-DD HH:mm:ss)
  - `username` (string, optional)
- **Response**:
  ```json
  {
    "success": true,
    "scan_id": 123,
    "points_awarded": 10
  }
  ```

---

## 4. Content Module

### Get Tequila Content
- **Endpoint**: `/tequila`
- **Method**: `GET`
- **Headers**: `Authorization: Bearer <token>`
- **Description**: Retrieves dynamic content (texts, images, videos) related to Tequila education/promotion.
- **Response**:
  ```json
  {
    "images": [{"url": "...", "title": "Agave Field"}],
    "videos": [{"url": "...", "title": "Distillation Process"}],
    "texts": [{"title": "History", "text": "Tequila was first produced..."}]
  }
  ```

### Get Mezcal Content
- **Endpoint**: `/mezcal`
- **Method**: `GET`
- **Headers**: `Authorization: Bearer <token>`
- **Description**: Retrieves dynamic content related to Mezcal.
- **Response**:
  ```json
  {
    "images": [],
    "videos": [],
    "texts": [{"title": "Mezcal vs Tequila", "text": "..."}]
  }
  ```

### Get Banners (Ads)
- **Endpoint**: `/banners`
- **Method**: `GET`
- **Headers**: `Authorization: Bearer <token>`
- **Description**: Retrieves promotional banners displayed on the home screen.
- **Response**:
  ```json
  [
    {
      "title": "Promo 2x1",
      "image_url": "https://.../banner1.jpg",
      "description": "Valid until Friday"
    }
  ]
  ```

### Get Marcas (Brands)
- **Endpoint**: `/marcas`
- **Method**: `GET`
- **Headers**: `Authorization: Bearer <token>`
- **Description**: Retrieves a list of partner brands with their logos.
- **Response**:
  ```json
  [
    {
      "id": "1",
      "name": "Jose Cuervo",
      "logo_url": "https://.../cuervo_logo.png",
      "website": "https://cuervo.com"
    }
  ]
  ```

---

## 5. Gamification Module

### Get Avatars
- **Endpoint**: `/avatars`
- **Method**: `GET`
- **Headers**: `Authorization: Bearer <token>`
- **Description**: Retrieves available avatars that users can unlock or select based on their points.
- **Response**:
  ```json
  [
    {
      "id": "1",
      "name": "Agave Farmer",
      "image_url": "https://.../avatar1.png",
      "points_required": "0"
    },
    {
      "id": "2",
      "name": "Master Distiller",
      "image_url": "https://.../avatar2.png",
      "points_required": "500"
    }
  ]
  ```
