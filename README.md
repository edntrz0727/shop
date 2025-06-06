簡單的購物車網站
使用
+ Linux (Ubuntu20.04 LTS, Oracle VM)
+ Composer 2.8.9
+ PHP 8.4.7
+ Laravel Framework 12.7.0
+ MySQL 8.0.42-0ubuntu0.20.04.1

---

實作步驟：
1. 安裝環境 (PHP8.4 MySQL Server Composer) 後
   ```
   sudo apt install php8.4-xml // 需 DOM document
   composer create-project laravel/laravel shop --ignore-platform-reqs
   //ignore-platform-reqs 可看 error 安裝對應的 required extension packages
   ```
   建立 laravel 專案, VScode 打開 shop
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
    安裝 brew，完整移除 nodejs
    重新安裝 nodejs 再
    ```
    npm install
    npm run build
    npm run dev
    ```
    就可以正常運行 npm

3. 設定 .env 環境
    ```
    DB_DATABASE=dbname
    DB_USERNAME=username
    DB_PASSWORD=password
    ```
    [06-06 23:30] ==新增==
    * 更改 APP_ENV=production
    * 更改 APP_URL=ngrok-free.app 提供網址
    * 修改 AppServiceProvider boot()
        強制將 URL::scheme 轉為 https，才不會出現 Mixed Content 前端大跑板問題

4. 使用 php artisan make:model "model_name" -m 來建立 Eqolent 及 Migration
   共有 categories, products, carts, cart_items 四個 table
    
5. composer require laravel/breeze
   使用 breeze 套件完成使用者登入、註冊、驗證等流程
   調用購物車時也可以直接使用 auth() 來確認使用者 id
   **到 user.php 中建立與 cart 的關聯，不然無法讓使用者擁有購物車**

6. 撰寫關聯及 table schema
    * Migration -> 建立 table schema
        - [categories table]
            primary key: id, auto increment
            另設定 name 為 unique，避免出現重複名稱的分類

        - [products table]
            primary key: id, auto increment
            foreign key: category_id, delete on cascade
            為防止一個 product 有太多 categories 導致一個 column 內有多筆資料
            設定一個 product 僅含一種 category

        - [carts table]
            primary key: id, auto increment
            foreign key: user_id, 透過 breeze 建構的 users table 中取得目前登入的 user id
            boolean checked_out: 判斷購物車是否已結帳，未結帳的商品會在同一個購物車中，直到結帳完成->清空購物車

        - [cart_items table]
            primary key: id, auto increment
            foreign key: cart_id, product_id, 取得當前購物車資料及商品資料
            qunatity紀錄數量

    * Models -> Eqolent -> Relation
        - User
            由 breeze 套件協助產生，在裡面加上與 cart 的關聯 (一個 user 可以有很多購物車)
        - Category
            一個分類下面可以有很多產品
        - Product
            一個產品其實可以屬於很多分類，但實作起來在前端調動選擇分類時發生問題，為求簡單，選擇讓一個產品僅屬於一種分類
        - Cart
            一台購物車只能屬於一個使用者，且一台購物車內可以有多項產品
        - CartItem
            一個購物車內的產品僅屬於一台購物車，且有唯一的產品編號

    建立完 Schema 及 Eqolent, php artisan migrate
    MySQL server 創建 DB 及 tables
   
8. MySQL 塞測試資料
    此步可使用 seeder 操作，但選擇用 SQL 語法進 MySQL server 新增
    ~~所以資料僅 localhost 的 MySQL 可調閱......~~
    ~~未撰寫 DB trigger 上傳至申請網域的 MySQL DB~~
    [06-06 23:30] ==更新==
    已使用 ngrok 進行外網部屬，可調用本地 MySQL DB 內容
    未來考慮寫成打包完成的 .sql 檔案一次匯入較方便
   
10. php artisan make:controller "controller_name" 建立 controller
    由 ProductController, CartController 來控制 DB 行為
    原有多寫 update function 讓使用者更新自己購買的商品數量
    及 CheckoutController 控制結帳行為
    後 update 部分 dd 無法收到正確資料，放棄
    Checkout 流程簡化(無選擇哪些商品的流程，直接購買購物車內所有商品且購買完清空購物車)，直接併入 CartController 內實現

    * ProductController
        - index(Request $req)
            抓取所有 DB 內的 categories
            商品部份配合 Http Request，前端如果沒有選擇分類就顯示所有的商品 -> when($req->category_id = NULL) -> 不進入 function，跳到paginate(12);
            若有接收到前端 form action 傳送過來的 category_id，query->where() 來尋找屬於這個分類的商品
            每頁可展示 12 件商品
            回傳 products/index.blade.php 畫面
            compact() -> 可以在前端調用

    * CartController
        - index()
            設定 cart，取得目前的使用者->他有哪些購物車->選還沒結帳那一台 (選最新，first)
            如果都結帳了->建一台，設他的 checked_out status = false
            回傳 cart/index.blade.php =>購物車頁面
            cart->load($items.product) 取得購物車內有哪些商品 (會存在 cart_items 這個 table) 供前端調用

        - add(Product $product, Request $req)
            新增商品，和 index 邏輯相同，選最新尚未結帳的購物車，或新建一台購物車，設定狀態是未結帳
            dd($req->input('quantity')) 確定 Request 傳進來的 quantity 數量
            $item = $cart()->items()，在 Cart 與 CartItem 的關聯中有設定，如果購物車裡面沒有這個商品，就塞 product_id 進去
            設定 $item 的 quantity，取原本就有的值或是塞 0，加上傳入的 quantity 為此 CartItem 的新數量

            本應會出現已加入購物車的訊息，但沒有顯示 sad

        - remove(CartItem $item)
            先確定此物品屬於購物車且購物車的主人為使用者
            delete，出現移除訊息

        - checkout()
            本有多寫一個CheckoutController，但流程簡化
            變成只要按結帳鍵->確認購買？->購買成功，清空購物車
            一樣找到最新且未結帳的購物車，但如果沒有這輛購物車->method fail
            設定結帳狀態為 true -> 保存狀態
            重新導向 cart.index
    
12. 擴展 layouts.app 撰寫前端

13. ~~部屬 FTP 上傳至虛擬主機~~
    ~~這部分本想使用 github pages，但 github pages 只支援靜態 html~~
    ~~且嘗試 webhook 失敗，故申請網域上傳，惜無法在指定時間內完成~~
    [06-06 23:30] ==更新==
    已可使用 ngrok 進行外網連線
    惟每次都需更改 APP_URL 及重新 build 專案
    正在嘗試加入腳本自動抓取 ngrok 產生網址塞入 .env 的 APP_URL 並 build 專案

---
#### 06-06 23:30 更新
* 補充詳細 models, create_table, controller 實作想法
* 使用 ngrok 成功部屬外部可連接網址
    正在嘗試撰寫擷取 ngrok 產生網址並塞入 APP_URL 的自動化腳本