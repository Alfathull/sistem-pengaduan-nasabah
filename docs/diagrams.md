# Diagram SiPeNa — Sistem Informasi Pengaduan Nasabah

Dokumen ini memuat diagram perancangan sistem SiPeNa sesuai 3 role: Nasabah, Customer Service (CS), dan Pimpinan.

---

## 1. Use Case Diagram

```mermaid
flowchart TB
    subgraph Aktor
        N([Nasabah])
        CS([Customer Service])
        P([Pimpinan])
    end

    subgraph Sistem SiPeNa
        UC1[Registrasi & Login]
        UC2[Kirim Pengaduan]
        UC3[Lacak Status Pengaduan]
        UC4[Terima Notifikasi In-App]
        UC5[Kelola Tiket Pengaduan]
        UC6[Validasi & Set Prioritas]
        UC7[Ubah Status Tiket]
        UC8[Eskalasi ke Departemen]
        UC9[Catat Tindak Lanjut]
        UC10[Kelola Master Data & Pengguna]
        UC11[Lihat Laporan Rekapitulasi]
        UC12[Export Laporan PDF]
    end

    N --> UC1
    N --> UC2
    N --> UC3
    N --> UC4

    CS --> UC1
    CS --> UC5
    CS --> UC6
    CS --> UC7
    CS --> UC8
    CS --> UC9
    CS --> UC10

    P --> UC1
    P --> UC11
    P --> UC12
```

---

## 2. Activity Diagram — Alur Pengaduan dari Dibuat hingga Ditutup

```mermaid
flowchart TD
    A([Mulai]) --> B[Nasabah mengisi form pengaduan]
    B --> C[Sistem membuat nomor tiket PGD-YYYYMMDD-XXXX]
    C --> D[Status: Baru]
    D --> E{CS memvalidasi?}
    E -->|Ditolak| F[Status: Ditolak + alasan]
    E -->|Valid| G[Status: Divalidasi + set prioritas]
    G --> H[Status: Diproses]
    H --> I{Perlu eskalasi?}
    I -->|Ya| J[Status: Dieskalasi ke departemen]
    I -->|Tidak| K[Status: Selesai]
    J --> K
    K --> L[Nasabah menerima notifikasi in-app]
    F --> L
    L --> M([Selesai])
```

---

## 3. Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    USERS ||--o{ COMPLAINTS : "mengajukan"
    USERS ||--o{ COMPLAINT_HISTORIES : "mencatat"
    CATEGORIES ||--o{ COMPLAINTS : "mengkategorikan"
    DEPARTMENTS ||--o{ COMPLAINTS : "menangani"
    DEPARTMENTS ||--o{ COMPLAINT_HISTORIES : "tujuan eskalasi"
    COMPLAINTS ||--o{ COMPLAINT_HISTORIES : "memiliki riwayat"
    USERS ||--o{ NOTIFICATIONS : "menerima"

    USERS {
        bigint id PK
        string name
        string email
        string phone
        string account_type
        timestamp email_verified_at
    }
    CATEGORIES {
        bigint id PK
        string name
        boolean is_active
    }
    DEPARTMENTS {
        bigint id PK
        string name
        boolean is_active
    }
    COMPLAINTS {
        bigint id PK
        string ticket_number
        bigint user_id FK
        bigint category_id FK
        bigint department_id FK
        string reporter_name
        string reporter_email
        string reporter_phone
        string account_type
        string subject
        text description
        string status
        string priority
        text notes
        text rejection_reason
        timestamp created_at
    }
    COMPLAINT_HISTORIES {
        bigint id PK
        bigint complaint_id FK
        bigint user_id FK
        bigint department_id FK
        string status
        text follow_up
        timestamp created_at
    }
    NOTIFICATIONS {
        uuid id PK
        string type
        string notifiable_type
        bigint notifiable_id
        text data
        timestamp read_at
    }
```

---

## 4. Data Flow Diagram (DFD)

### 4.1 DFD Level 0 (Context Diagram)

```mermaid
flowchart LR
    N([Nasabah])
    CS([Customer Service])
    P([Pimpinan])

    SYS((Sistem SiPeNa))

    N -->|Data pengaduan| SYS
    SYS -->|Nomor tiket & status| N
    SYS -->|Notifikasi perubahan| N

    CS -->|Validasi, status, eskalasi, tindak lanjut| SYS
    SYS -->|Daftar & detail tiket| CS

    P -->|Permintaan laporan & filter periode| SYS
    SYS -->|Rekap & PDF| P
```

### 4.2 DFD Level 1

```mermaid
flowchart TB
    N([Nasabah])
    CS([Customer Service])
    P([Pimpinan])

    P1[1.0 Autentikasi & Registrasi]
    P2[2.0 Pengelolaan Pengaduan]
    P3[3.0 Pelacakan & Notifikasi]
    P4[4.0 Pengelolaan Master Data]
    P5[5.0 Pelaporan Rekapitulasi]

    DS1[(D1 Users)]
    DS2[(D2 Complaints)]
    DS3[(D3 Complaint Histories)]
    DS4[(D4 Categories/Departments)]
    DS5[(D5 Notifications)]

    N --> P1
    CS --> P1
    P --> P1
    P1 <--> DS1

    N -->|Buat pengaduan| P2
    CS -->|Ubah status/eskalasi| P2
    P2 <--> DS2
    P2 --> DS3
    P2 --> P3

    P3 --> DS5
    P3 -->|Status & notifikasi| N
    N -->|Lacak tiket| P3
    P3 <--> DS2

    CS --> P4
    P4 <--> DS4

    P --> P5
    P5 <--> DS2
    P5 -->|Laporan PDF| P
```
