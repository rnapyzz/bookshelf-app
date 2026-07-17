# [CT]

## 概要

## ER図
```mermaid
erDiagram
    users {
        bigint id PK
        varchar(255) name
        varchar(255) email "UNIQUE"
        timestamp email_verified_at "nullable"
        varchar(255) password
        varchar(100) remember_token "nullable"
        timestamp created_at
        timestamp updated_at
    }
    
    genres {
        bigint id PK
        varchar(50) name "UNIQUE"
        timestamp created_at
        timestamp updated_at
    }
    
    books {
        bigint id PK
        bigint user_id FK "ON DELETE ON CASCADE"
        varchar(255) title
        varchar(255) author
        varchar(13) isbn "UNIQUE, 13桁ハイフンなし"
        date published_date
        text description "nullable"
        text image_url "nullable"
        timestamp created_at
        timestamp updated_at
    }
    
    reviews {
        bigint id PK
        bigint user_id FK "ON DELETE CASCADE"
        bigint book_id FK "ON DELETE CASCADE"
        tinyint rating "1~5"
        text comment "nullable"
        timestamp created_at
        timestamp updated_at
    }
    
    book_genre {
        bigint id PK
        bigint book_id FK "ON DELETE CASCADE"
        bigint genre_id FK "ON DELETE RESTRICT"
        timestamp created_at
        timestamp updated_at
    }
    
    favorites {
        bigint id PK
        bigint user_id FK "ON DELETE CASCADE"
        bigint book_id FK "ON DELETE CASCADE"
        timestamp created_at
        timestamp updated_at
    }
    
    likes {
        bigint id PK
        bigint user_id FK "ON DELETE CASCADE"
        bigint review_id FK "ON DELETE CASCADE"
        timestamp created_at
        timestamp updated_at
    }
    
    users ||--o{ books : "hasMany"
    users ||--o{ reviews : "hasMany"
    users ||--o{ favorites : "hasMany"
    users ||--o{ likes : "hasMany"
    
    books ||--o{ reviews : "hasMany"
    books ||--o{ favorites : "hasMany"
    books ||--o{ book_genre: "hasMany"
    
    genres ||--o{ book_genre : "hasMany"
    
    reviews ||--o{ likes : "hasMany"
    
```
中間テーブルにはそれぞれ以下の複合ユニーク制約があります。
- reviews: `unique(user_id, book_id)`
- book_genre: `unique(book_id, genre_id)`
- favorites: `unique(user_id, book_id)`
- likes: `unique(user_id, review_id)`

## 環境構築手順

## テストの実行方法

## 
