# SPMB CRUD API - Postman Collection

## Base URL
```
http://127.0.0.1:8000
```

## Endpoints

### 1. GET - List Semua Pendaftaran
**Endpoint:** `GET /api/pendaftaran`

**Response:**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "nama_lengkap": "Ahmad Ridho",
      "nisn": "0012345678",
      "nik": "3574123456789012",
      "tempat_lahir": "Surabaya",
      "tanggal_lahir": "2008-05-15",
      "jenis_kelamin": "Laki-laki",
      "alamat": "Jl. Banyu Urip No. 69",
      "asal_sekolah": "SMP Negeri 1 Surabaya",
      "no_hp": "085123456789",
      "email": "ahmad@example.com",
      "jurusan_pilihan": "RPL",
      "nama_orang_tua": "Bapak Ahmad",
      "no_hp_orang_tua": "085987654321",
      "status": "baru",
      "created_at": "2026-08-01T10:30:00.000000Z",
      "updated_at": "2026-08-01T10:30:00.000000Z"
    }
  ],
  "per_page": 15,
  "total": 1
}
```

---

### 2. POST - Create Pendaftaran Baru
**Endpoint:** `POST /api/pendaftaran`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "nama_lengkap": "Ahmad Ridho",
  "nisn": "0012345678",
  "nik": "3574123456789012",
  "tempat_lahir": "Surabaya",
  "tanggal_lahir": "2008-05-15",
  "jenis_kelamin": "Laki-laki",
  "alamat": "Jl. Banyu Urip Kidul No. 69, Putat Jaya, Sawahan, Surabaya",
  "asal_sekolah": "SMP Negeri 1 Surabaya",
  "no_hp": "085123456789",
  "email": "ahmad@example.com",
  "jurusan_pilihan": "RPL",
  "nama_orang_tua": "Bapak Ahmad",
  "no_hp_orang_tua": "085987654321"
}
```

**Response (201 Created):**
```json
{
  "message": "Pendaftaran berhasil dibuat",
  "data": {
    "id": 1,
    "nama_lengkap": "Ahmad Ridho",
    "nisn": "0012345678",
    "status": "baru",
    "created_at": "2026-08-01T10:30:00.000000Z",
    "updated_at": "2026-08-01T10:30:00.000000Z"
  }
}
```

---

### 3. GET - Detail Pendaftaran
**Endpoint:** `GET /api/pendaftaran/{id}`

**URL:** `http://127.0.0.1:8000/api/pendaftaran/1`

**Response:**
```json
{
  "id": 1,
  "nama_lengkap": "Ahmad Ridho",
  "nisn": "0012345678",
  "nik": "3574123456789012",
  "tempat_lahir": "Surabaya",
  "tanggal_lahir": "2008-05-15",
  "jenis_kelamin": "Laki-laki",
  "alamat": "Jl. Banyu Urip Kidul No. 69, Putat Jaya, Sawahan, Surabaya",
  "asal_sekolah": "SMP Negeri 1 Surabaya",
  "no_hp": "085123456789",
  "email": "ahmad@example.com",
  "jurusan_pilihan": "RPL",
  "nama_orang_tua": "Bapak Ahmad",
  "no_hp_orang_tua": "085987654321",
  "status": "baru",
  "created_at": "2026-08-01T10:30:00.000000Z",
  "updated_at": "2026-08-01T10:30:00.000000Z"
}
```

---

### 4. PUT - Update Pendaftaran
**Endpoint:** `PUT /api/pendaftaran/{id}`

**URL:** `http://127.0.0.1:8000/api/pendaftaran/1`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "nama_lengkap": "Ahmad Ridho Updated",
  "status": "diproses",
  "email": "newemail@example.com"
}
```

**Response:**
```json
{
  "message": "Pendaftaran berhasil diperbarui",
  "data": {
    "id": 1,
    "nama_lengkap": "Ahmad Ridho Updated",
    "status": "diproses",
    "email": "newemail@example.com",
    "updated_at": "2026-08-01T10:35:00.000000Z"
  }
}
```

---

### 5. DELETE - Hapus Pendaftaran
**Endpoint:** `DELETE /api/pendaftaran/{id}`

**URL:** `http://127.0.0.1:8000/api/pendaftaran/1`

**Response:**
```json
{
  "message": "Pendaftaran berhasil dihapus"
}
```

---

## Web Routes (View)

| Method | Route | Function |
|--------|-------|----------|
| GET | `/pendaftaran/create` | Tampil form pendaftaran |
| POST | `/pendaftaran` | Submit pendaftaran (form) |
| GET | `/pendaftaran` | List data pendaftaran |
| GET | `/pendaftaran/{id}/edit` | Edit form pendaftaran |
| PUT | `/pendaftaran/{id}` | Update pendaftaran (form) |
| DELETE | `/pendaftaran/{id}` | Hapus pendaftaran (form) |

---

## Testing dengan Postman

### Step 1: Create
```
POST http://127.0.0.1:8000/api/pendaftaran
```

### Step 2: List
```
GET http://127.0.0.1:8000/api/pendaftaran
```

### Step 3: Get Detail (ganti ID)
```
GET http://127.0.0.1:8000/api/pendaftaran/1
```

### Step 4: Update (ganti ID)
```
PUT http://127.0.0.1:8000/api/pendaftaran/1
```

### Step 5: Delete (ganti ID)
```
DELETE http://127.0.0.1:8000/api/pendaftaran/1
```

---

## Status Valid
- `baru` - Pendaftaran baru
- `diproses` - Sedang diproses
- `diterima` - Diterima
- `ditolak` - Ditolak

## Jurusan Valid
- `RPL` - Rekayasa Perangkat Lunak
