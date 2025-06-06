簡單的購物車網站
使用
+ Linux (Ubuntu20.04 LTS, Oracle VM)
+ Composer 2.8.9
+ PHP 8.4.7
+ Laravel Framework 12.7.0
+ MySQL 8.0.42-0ubuntu0.20.04.1

---

實作步驟：
1. 安裝環境後
   ```
   sudo apt install php8.4-xml // 需 DOM document
   composer create-project laravel/laravel shop --ignore-platform-reqs
   //ignore-platform-reqs 可看 error 安裝對應的 requirement pack
   ```
   建立 laravel 專案, cd shop
2. 設置環境
   ```
   npm install
   npm run build
   npm run dev
   ```
   這個步驟遇到很多次 vite 版本不相容 & nodejs 版本升級後
   npm install cannot find module 'semver'

    ↓
    ```
    npm cache clear --force // 清除 cache
    rm -rf node_modules package-lock.json // 刪除原本 npm install 設定的 node modules 和 lock
    ```
    安裝 brew，完整移除 nodejs 後重新安裝 nodejs
    再
    ```
    npm install
    npm run build
    npm run dev
    ```
    就可以正常運行 npm
3.設定 .env 環境
    ```
    DB_DATABASE=dbname
    DB_USERNAME=username
    DB_PASSWORD=password
    ```
4. 使用 php artisan make:model "model_name" -m 來建立 Eqolent 及 Migration
   共有 categories, products, carts, cart_items 四個 table
    
5. composer require laravel/breeze
   使用 breeze 套件完成使用者登入、註冊、驗證等流程
   調用購物車時也可以直接使用 auth() 來確認使用者 id
   **到 user.php 中建立與 cart 的關聯，不然無法讓使用者擁有購物車**

6. 撰寫關聯及 table schema
    設定 primary keys & foreign key
    [categories table]
    primary key: id, auto increment
    另設定 name 為 unique，避免出現重複名稱的分類
   
    [products table]
    primary key: id, auto increment
    foreign key: category_id, delete on cascade
    為防止一個 product 有太多 categories 導致一個 column 內有多筆資料
    設定一個 product 僅含一種 category

    [carts table]
    primary key: id, auto increment
    foreign key: user_id, 透過 breeze 建構的 users table 中取得目前登入的 user id
    boolean checked_out: 判斷購物車是否已結帳，未結帳的商品會在同一個購物車中，直到結帳完成->清空購物車

    [cart_items table]
    primary key: id, auto increment
    foreign key: cart_id, product_id, 取得當前購物車資料及商品資料
    qunatity紀錄數量

    建立完 Schema 及 Eqolent, php artisan migrate, MySQL server 創建 DB 及 tables
   
8. MySQL 塞測試資料
    此步可使用seeder操作，但選擇用SQL語法進 MySQL server 新增
    所以資料僅 localhost 的 MySQL 可調閱......
    未撰寫 DB trigger 上傳至申請網域的 MySQL DB
   
10. php artisan make:controller "controller_name" 建立 controller
    由 ProductController, CartController
    來控制 DB 行為
    原有多寫 update function 讓使用者更新自己購買的商品數量
    及 CheckoutController 控制結帳行為
    後 update 部分 dd 無法收到正確資料，放棄
    Checkout 流程簡化(無選擇哪些商品的流程，直接購買購物車內所有商品且購買完清空購物車)，直接併入 CartController 內實現
    
12. 擴展 layouts.app 撰寫前端

13. 部屬 FTP 上傳至虛擬主機
    這部分本想使用 github pages，但 github pages 只支援靜態 html
    且嘗試 webhook 失敗，故申請網域上傳，惜無法在指定時間內完成
