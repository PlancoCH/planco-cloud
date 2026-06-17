# Planco Cloud API Documentation

## Base URL
```
/api
```

## Authentication
Most endpoints require authentication using Sanctum tokens. The token should be sent in the `Authorization` header:
```
Authorization: ******
```

Device API endpoints use a custom `VerifyDeviceApiKey` middleware and require an API key in the request.

---

## Table of Contents
1. [Authentication Endpoints](#authentication-endpoints)
2. [Email Verification Endpoints](#email-verification-endpoints)
3. [Device Endpoints](#device-endpoints)
4. [Plant Type Endpoints](#plant-type-endpoints)
5. [Plant Endpoints](#plant-endpoints)
6. [Plant Image Endpoints](#plant-image-endpoints)
7. [Daily Insight Endpoints](#daily-insight-endpoints)
8. [Plant Data Endpoints](#plant-data-endpoints)
9. [Device API Endpoints](#device-api-endpoints)

---

## Authentication Endpoints

### POST /auth/signup
Create a new user account.

**Authentication:** Not required

**Request Body:**
```json
{
  "name": "string (required, max: 255)",
  "email": "string (required, email, unique, max: 255)",
  "password": "string (required, min: 8)",
  "password_confirmation": "string (required, must match password)"
}
```

**Response (201 Created):**
```json
{
  "message": "User created successfully.",
  "user": {
    "id": "integer",
    "name": "string",
    "email": "string",
    "created_at": "timestamp",
    "updated_at": "timestamp"
  }
}
```

---

### POST /auth/login
Authenticate a user and receive an API token.

**Authentication:** Not required

**Request Body:**
```json
{
  "email": "string (required, email)",
  "password": "string (required)"
}
```

**Response (200 OK):**
```json
{
  "message": "Login successful.",
  "user": {
    "id": "integer",
    "name": "string",
    "email": "string",
    "created_at": "timestamp",
    "updated_at": "timestamp"
  },
  "token": "string"
}
```

**Possible Errors:**
- 422: Invalid credentials provided
- 403: Email address not verified

---

### POST /auth/logout
Logout the authenticated user and invalidate the current token.

**Authentication:** Required

**Request Body:** Empty

**Response (200 OK):**
```json
{
  "message": "Logged out successfully."
}
```

---

### GET /auth/me
Retrieve the authenticated user's profile information.

**Authentication:** Required

**Request Body:** Empty

**Response (200 OK):**
```json
{
  "user": {
    "id": "integer",
    "name": "string",
    "email": "string",
    "created_at": "timestamp",
    "updated_at": "timestamp"
  }
}
```

---

### PUT /auth/me
Update the authenticated user's profile.

**Authentication:** Required

**Request Body:**
```json
{
  "name": "string (optional, max: 255)",
  "email": "string (optional, email, unique, max: 255)",
  "password": "string (optional, min: 8)",
  "password_confirmation": "string (optional, required if password is provided)"
}
```

**Response (200 OK):**
```json
{
  "message": "User updated successfully.",
  "user": {
    "id": "integer",
    "name": "string",
    "email": "string",
    "created_at": "timestamp",
    "updated_at": "timestamp"
  }
}
```

---

### PATCH /auth/me
Update the authenticated user's profile (alias for PUT).

**Authentication:** Required

**Request Body:** Same as PUT /auth/me

**Response:** Same as PUT /auth/me

---

### POST /auth/email/verification-notification
Resend the email verification notification. To prevent email enumeration, the endpoint returns the same success message even if the provided email does not exist in the system.

**Authentication:** Not required

**Rate Limit:** 6 requests per minute

**Request Body:**
```json
{
  "email": "string (required, email, max: 255)"
}
```

**Response (200 OK):**
```json
{
  "message": "Verification link sent."
}
```

**Possible Errors:**
- 400: Email already verified

---

## Email Verification Endpoints

### GET /email/verify/{id}/{hash}
Verify the user's email address using the verification link sent to their email.

**Note:** This endpoint returns an HTML web page (not JSON). It is intended to be opened in a browser via the verification link sent by email.

**Authentication:** Not required (validated via signed URL)

**Rate Limit:** 6 requests per minute

**URL Parameters:**
- `id`: User ID (integer)
- `hash`: Verification hash (string)

**Request Body:** Empty

**Response (200 OK):** An HTML page with one of the following states:
- **Success:** "Your email address has been verified successfully."
- **Already Verified:** "Your email address has already been verified."
- **Invalid Link:** "This verification link is invalid or has expired."

---

## Device Endpoints

### GET /devices
Get all devices mapped to the authenticated user.

**Authentication:** Required

**Query Parameters:** None

**Request Body:** Empty

**Response (200 OK):**
```json
[
  {
    "id": "integer",
    "name": "string",
    "notes": "string | null",
    "polling_rate": "integer",
    "wifi_rssi": "integer | null",
    "led_enabled": "boolean",
    "created_at": "timestamp",
    "updated_at": "timestamp"
  }
]
```

---

### POST /devices/map
Map a device to the authenticated user using a mapping key.

**Authentication:** Required

**Request Body:**
```json
{
  "mapping_key": "string (required)"
}
```

**Response (200 OK):**
```json
{
  "id": "integer",
  "name": "string",
  "notes": "string | null",
  "polling_rate": "integer",
  "wifi_rssi": "integer | null",
  "led_enabled": "boolean",
  "created_at": "timestamp",
  "updated_at": "timestamp"
}
```

**Possible Errors:**
- 404: Invalid mapping key
- 403: Device is already mapped to another user

---

### GET /devices/{device}
Get details of a specific device.

**Authentication:** Required

**URL Parameters:**
- `device`: Device ID (integer)

**Request Body:** Empty

**Response (200 OK):**
```json
{
  "id": "integer",
  "name": "string",
  "notes": "string | null",
  "polling_rate": "integer",
  "wifi_rssi": "integer | null",
  "led_enabled": "boolean",
  "created_at": "timestamp",
  "updated_at": "timestamp"
}
```

**Possible Errors:**
- 403: Unauthorized access to this device

---

### PUT /devices/{device}
Update a device's settings.

**Authentication:** Required

**URL Parameters:**
- `device`: Device ID (integer)

**Request Body:**
```json
{
  "name": "string (optional, max: 255)",
  "notes": "string (optional)",
  "polling_rate": "integer (optional, one of: 15, 30, 60, 120, 300, 600, 1800, 3600)",
  "led_enabled": "boolean (optional)"
}
```

**Response (200 OK):**
```json
{
  "id": "integer",
  "name": "string",
  "notes": "string | null",
  "polling_rate": "integer",
  "wifi_rssi": "integer | null",
  "led_enabled": "boolean",
  "created_at": "timestamp",
  "updated_at": "timestamp"
}
```

**Possible Errors:**
- 403: Unauthorized access to this device

---

### POST /devices/{device}/unmap
Unmap a device from the authenticated user.

**Authentication:** Required

**URL Parameters:**
- `device`: Device ID (integer)

**Request Body:** Empty

**Response (200 OK):**
```json
{
  "message": "Device unmapped successfully."
}
```

**Possible Errors:**
- 403: Unauthorized access to this device

---

## Plant Type Endpoints

### GET /plant-types
Get a paginated list of all plant types with optional search.

**Authentication:** Required

**Query Parameters:**
- `search` (optional): Search term to filter by common_name or scientific_name
- `page` (optional): Page number for pagination (default: 1)

**Request Body:** Empty

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": "integer",
      "common_name": "string",
      "description": "string | null",
      "scientific_name": "string",
      "ideal_temp": "numeric | null",
      "ideal_moisture": "numeric | null",
      "ideal_light_lux": "numeric | null",
      "ideal_humidity": "numeric | null",
      "created_at": "timestamp",
      "updated_at": "timestamp"
    }
  ],
  "links": {
    "first": "string | null",
    "last": "string | null",
    "prev": "string | null",
    "next": "string | null"
  },
  "meta": {
    "current_page": "integer",
    "from": "integer",
    "last_page": "integer",
    "per_page": "integer",
    "to": "integer",
    "total": "integer"
  }
}
```

---

### GET /plant-types/{plantType}
Get details of a specific plant type.

**Authentication:** Required

**URL Parameters:**
- `plantType`: Plant Type ID (integer)

**Request Body:** Empty

**Response (200 OK):**
```json
{
  "id": "integer",
  "common_name": "string",
  "description": "string | null",
  "scientific_name": "string",
  "ideal_temp": "numeric | null",
  "ideal_moisture": "numeric | null",
  "ideal_light_lux": "numeric | null",
  "ideal_humidity": "numeric | null",
  "created_at": "timestamp",
  "updated_at": "timestamp"
}
```

---

### GET /plant-types/{plantType}/image
Get the image for a specific plant type.

**Authentication:** Required

**URL Parameters:**
- `plantType`: Plant Type ID (integer)

**Request Body:** Empty

**Response (200 OK):** Binary image data (JPEG or other image format)

**Possible Errors:**
- 404: No image found for this plant type

---

## Plant Endpoints

### GET /plants
Get all plants associated with the authenticated user.

**Authentication:** Required

**Query Parameters:** None

**Request Body:** Empty

**Response (200 OK):**
```json
[
  {
    "id": "integer",
    "device_id": "integer | null",
    "plant_type_id": "integer",
    "nickname": "string",
    "notes": "string | null",
    "custom_image": "string | null",
    "sharing_token": "string | null",
    "created_at": "timestamp",
    "updated_at": "timestamp",
    "plant_type": {
      "id": "integer",
      "common_name": "string",
      "description": "string | null",
      "scientific_name": "string",
      "ideal_temp": "numeric | null",
      "ideal_moisture": "numeric | null",
      "ideal_light_lux": "numeric | null",
      "ideal_humidity": "numeric | null",
      "created_at": "timestamp",
      "updated_at": "timestamp"
    },
    "device": {
      "id": "integer",
      "name": "string",
      "notes": "string | null",
      "polling_rate": "integer",
      "wifi_rssi": "integer | null",
      "led_enabled": "boolean",
      "created_at": "timestamp",
      "updated_at": "timestamp"
    },
    "role": "owner | member"
  }
]
```

---

### POST /plants
Create a new plant.

**Authentication:** Required

**Request Body:**
```json
{
  "nickname": "string (required, max: 255)",
  "notes": "string (optional)",
  "plant_type_id": "integer (required, must exist in plant_types)",
  "custom_image": "string (optional)"
}
```

**Response (201 Created):**
```json
{
  "id": "integer",
  "device_id": "integer | null",
  "plant_type_id": "integer",
  "nickname": "string",
  "notes": "string | null",
  "custom_image": "string | null",
  "sharing_token": "string | null",
  "created_at": "timestamp",
  "updated_at": "timestamp",
  "plant_type": {
    "id": "integer",
    "common_name": "string",
    "description": "string | null",
    "scientific_name": "string",
    "ideal_temp": "numeric | null",
    "ideal_moisture": "numeric | null",
    "ideal_light_lux": "numeric | null",
    "ideal_humidity": "numeric | null",
    "created_at": "timestamp",
    "updated_at": "timestamp"
  },
  "device": null,
  "role": "owner"
}
```

---

### GET /plants/{plant}
Get details of a specific plant.

**Authentication:** Required

**URL Parameters:**
- `plant`: Plant ID (integer)

**Request Body:** Empty

**Response (200 OK):**
```json
{
  "id": "integer",
  "device_id": "integer | null",
  "plant_type_id": "integer",
  "nickname": "string",
  "notes": "string | null",
  "custom_image": "string | null",
  "sharing_token": "string | null",
  "created_at": "timestamp",
  "updated_at": "timestamp",
  "plant_type": {
    "id": "integer",
    "common_name": "string",
    "description": "string | null",
    "scientific_name": "string",
    "ideal_temp": "numeric | null",
    "ideal_moisture": "numeric | null",
    "ideal_light_lux": "numeric | null",
    "ideal_humidity": "numeric | null",
    "created_at": "timestamp",
    "updated_at": "timestamp"
  },
  "device": {
    "id": "integer",
    "name": "string",
    "notes": "string | null",
    "polling_rate": "integer",
    "wifi_rssi": "integer | null",
    "led_enabled": "boolean",
    "created_at": "timestamp",
    "updated_at": "timestamp"
  },
  "role": "owner | member"
}
```

**Possible Errors:**
- 403: Unauthorized access to this plant

---

### PUT /plants/{plant}
Update a plant's information.

**Authentication:** Required

**URL Parameters:**
- `plant`: Plant ID (integer)

**Request Body:**
```json
{
  "nickname": "string (optional, max: 255)",
  "notes": "string (optional)",
  "plant_type_id": "integer (optional, must exist in plant_types)",
  "custom_image": "string (optional)"
}
```

**Response (200 OK):**
```json
{
  "id": "integer",
  "device_id": "integer | null",
  "plant_type_id": "integer",
  "nickname": "string",
  "notes": "string | null",
  "custom_image": "string | null",
  "sharing_token": "string | null",
  "created_at": "timestamp",
  "updated_at": "timestamp"
}
```

**Possible Errors:**
- 403: Unauthorized access to this plant

---

### DELETE /plants/{plant}
Delete a plant.

**Authentication:** Required

**URL Parameters:**
- `plant`: Plant ID (integer)

**Request Body:** Empty

**Response (204 No Content):** Empty response

**Possible Errors:**
- 403: Unauthorized access to this plant

---

### POST /plants/{plant}/map
Map a device to a plant.

**Authentication:** Required

**URL Parameters:**
- `plant`: Plant ID (integer)

**Request Body:**
```json
{
  "device_id": "integer (required, must exist in devices)"
}
```

**Response (200 OK):**
```json
{
  "id": "integer",
  "device_id": "integer",
  "plant_type_id": "integer",
  "nickname": "string",
  "notes": "string | null",
  "custom_image": "string | null",
  "sharing_token": "string | null",
  "created_at": "timestamp",
  "updated_at": "timestamp",
  "plant_type": {
    "id": "integer",
    "common_name": "string",
    "description": "string | null",
    "scientific_name": "string",
    "ideal_temp": "numeric | null",
    "ideal_moisture": "numeric | null",
    "ideal_light_lux": "numeric | null",
    "ideal_humidity": "numeric | null",
    "created_at": "timestamp",
    "updated_at": "timestamp"
  },
  "device": {
    "id": "integer",
    "name": "string",
    "notes": "string | null",
    "polling_rate": "integer",
    "wifi_rssi": "integer | null",
    "led_enabled": "boolean",
    "created_at": "timestamp",
    "updated_at": "timestamp"
  },
  "role": "owner | member"
}
```

**Possible Errors:**
- 403: Unauthorized access to this plant
- 404: Device not found or not owned by user

---

### POST /plants/{plant}/unmap
Unmap the device from a plant.

**Authentication:** Required

**URL Parameters:**
- `plant`: Plant ID (integer)

**Request Body:** Empty

**Response (200 OK):**
```json
{
  "message": "Device unmapped from plant successfully."
}
```

**Possible Errors:**
- 403: Unauthorized access to this plant

---

### POST /plants/{plant}/share
Generate or retrieve a sharing token for a plant.

**Authentication:** Required

**URL Parameters:**
- `plant`: Plant ID (integer)

**Request Body:** Empty

**Response (200 OK):**
```json
{
  "sharing_token": "string"
}
```

**Possible Errors:**
- 403: Unauthorized access to this plant

---

### DELETE /plants/{plant}/share
Revoke the sharing token for a plant.

**Authentication:** Required

**URL Parameters:**
- `plant`: Plant ID (integer)

**Request Body:** Empty

**Response (200 OK):**
```json
{
  "message": "Sharing token revoked successfully."
}
```

**Possible Errors:**
- 403: Unauthorized access to this plant

---

### POST /plants/join
Join a plant using a sharing token.

**Authentication:** Required

**Request Body:**
```json
{
  "sharing_token": "string (required, must exist in plants)"
}
```

**Response (200 OK):**
```json
{
  "id": "integer",
  "device_id": "integer | null",
  "plant_type_id": "integer",
  "nickname": "string",
  "notes": "string | null",
  "custom_image": "string | null",
  "sharing_token": "string",
  "created_at": "timestamp",
  "updated_at": "timestamp"
}
```

**Possible Errors:**
- 409: User is already a member of this plant

---

### GET /plants/{plant}/image
Get the image for a specific plant. Returns the plant's custom image if set, otherwise falls back to the plant type's standard image.

**Authentication:** Required

**URL Parameters:**
- `plant`: Plant ID (integer)

**Request Body:** Empty

**Response (200 OK):** Binary image data (JPEG or other image format, Content-Type header set via MIME detection)

**Possible Errors:**
- 404: No image found for this plant

---

### POST /plants/{plant}/image
Upload or update the custom image for a plant. The raw binary image data should be sent as the entire request body (not wrapped in JSON). The image is validated and re-encoded as JPEG at quality 85.

**Authentication:** Required

**URL Parameters:**
- `plant`: Plant ID (integer)

**Request Body:** Raw binary image data (not JSON). The entire request body should contain the image bytes.

**Response (200 OK):**
```json
{
  "message": "Plant image updated successfully."
}
```

**Possible Errors:**
- 400: No image data provided in request body
- 400: Image exceeds maximum size of 1 GB
- 400: Invalid or unsupported image format
- 403: Unauthorized access to this plant

**Size Limit:** 1 GB (1,073,741,824 bytes)

---

## Daily Insight Endpoints

### GET /plants/{plant}/insights
Get all daily insights for a plant.

**Authentication:** Required

**URL Parameters:**
- `plant`: Plant ID (integer)

**Query Parameters:** None

**Request Body:** Empty

**Response (200 OK):**
```json
[
  {
    "id": "integer",
    "plant_id": "integer",
    "insight_type": "string",
    "message": "string",
    "is_read": "boolean",
    "created_at": "timestamp",
    "updated_at": "timestamp"
  }
]
```

---

### GET /plants/{plant}/insights/{dailyInsight}
Get a specific daily insight for a plant.

**Authentication:** Required

**URL Parameters:**
- `plant`: Plant ID (integer)
- `dailyInsight`: Daily Insight ID (integer)

**Request Body:** Empty

**Response (200 OK):**
```json
{
  "id": "integer",
  "plant_id": "integer",
  "insight_type": "string",
  "message": "string",
  "is_read": "boolean",
  "created_at": "timestamp",
  "updated_at": "timestamp"
}
```

**Possible Errors:**
- 404: Insight not found for this plant

---

### PATCH /plants/{plant}/insights/{dailyInsight}/read
Mark a daily insight as read.

**Authentication:** Required

**URL Parameters:**
- `plant`: Plant ID (integer)
- `dailyInsight`: Daily Insight ID (integer)

**Request Body:** Empty

**Response (200 OK):**
```json
{
  "message": "Insight marked as read.",
  "data": {
    "id": "integer",
    "plant_id": "integer",
    "insight_type": "string",
    "message": "string",
    "is_read": true,
    "created_at": "timestamp",
    "updated_at": "timestamp"
  }
}
```

**Possible Errors:**
- 404: Insight not found for this plant

---

## Plant Data Endpoints

### GET /plants/{plant}/data
Get plant data for a specific plant with optional date filtering.

**Authentication:** Required

**URL Parameters:**
- `plant`: Plant ID (integer)

**Query Parameters:**
- `start_date` (optional): ISO-8601 or SQL format date (filter records from this date onwards)
- `end_date` (optional): ISO-8601 or SQL format date (filter records up to this date)

**Request Body:** Empty

**Response (200 OK):**
```json
[
  {
    "id": "integer",
    "plant_id": "integer",
    "plant_score": "numeric | null",
    "temperature": "numeric",
    "humidity": "numeric",
    "air_pressure": "numeric | null",
    "light_intensity": "numeric",
    "soil_moisture": "numeric",
    "created_at": "timestamp",
    "updated_at": "timestamp"
  }
]
```

**Possible Errors:**
- 403: Unauthorized access to this plant

---

### GET /plants/{plant}/data/{plantData}
Get a specific plant data entry.

**Authentication:** Required

**URL Parameters:**
- `plant`: Plant ID (integer)
- `plantData`: Plant Data ID (integer)

**Request Body:** Empty

**Response (200 OK):**
```json
{
  "id": "integer",
  "plant_id": "integer",
  "plant_score": "numeric | null",
  "temperature": "numeric",
  "humidity": "numeric",
  "air_pressure": "numeric | null",
  "light_intensity": "numeric",
  "soil_moisture": "numeric",
  "created_at": "timestamp",
  "updated_at": "timestamp"
}
```

**Possible Errors:**
- 403: Unauthorized access to this plant
- 404: Data not found for this plant

---

## Device API Endpoints

These endpoints are protected by the `VerifyDeviceApiKey` middleware and are intended for device communication.

### POST /device-api/data
Store sensor data from a device to its mapped plants.

**Authentication:** Device API Key (via middleware)

**Request Body:**
```json
{
  "temperature": "numeric (required)",
  "humidity": "numeric (required)",
  "air_pressure": "numeric (optional)",
  "light_intensity": "numeric (required)",
  "soil_moisture": "numeric (required)"
}
```

**Response (201 Created):**
```json
{
  "message": "Sensor data recorded successfully.",
  "plants_updated": "integer"
}
```

**Possible Errors:**
- 401: Unauthorized. Device not authenticated.

---

### PUT /device-api/wifi-rssi
Update the device's WiFi signal strength (RSSI).

**Authentication:** Device API Key (via middleware)

**Request Body:**
```json
{
  "wifi_rssi": "integer (required)"
}
```

**Response (200 OK):**
```json
{
  "message": "WiFi RSSI updated successfully."
}
```

**Possible Errors:**
- 401: Unauthorized. Device not authenticated.

---

### GET /device-api/config
Get the device's configuration settings.

**Authentication:** Device API Key (via middleware)

**Request Body:** Empty

**Response (200 OK):**
```json
{
  "led_enabled": "boolean",
  "polling_rate": "integer"
}
```

**Possible Errors:**
- 401: Unauthorized. Device not authenticated.

---

## Error Responses

All endpoints may return the following error responses:

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": ["error message"]
  }
}
```

### 500 Internal Server Error
```json
{
  "message": "Internal server error"
}
```

---

## Response Headers

All responses include standard HTTP headers:
- `Content-Type: application/json` (for JSON responses)
- `X-RateLimit-Limit`: Rate limit for the endpoint
- `X-RateLimit-Remaining`: Remaining requests in current window
- `X-RateLimit-Reset`: Unix timestamp when the rate limit resets

---

## Notes

- All timestamps are in ISO-8601 format (UTC)
- All numeric values are validated based on the field type
- Pagination is implemented using Laravel's default pagination (15 items per page)
- The API follows RESTful conventions with appropriate HTTP methods
- Authentication tokens are valid for the lifetime of the browser session
