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
   sudo apt install php8.4-xml // 需DOM document
   composer create-project laravel/laravel shop --ignore-platform-reqs
   //ignore-platform-reqs 可看error安裝對應的requirement pack
   ```
   建立laravel專案，cd shop
2. 設置環境
   ```
   npm install
   npm run build
   npm run dev
   ```
   這個步驟遇到很多次vite版本不相容 & nodejs版本升級後
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
    就可以正常運行npm
3.設定.env 環境
    ```
    DB_DATABASE=dbname
    DB_USERNAME=username
    DB_PASSWORD=password
    ```
4. 使用php artisan make:model "model_name" -m 來建立Eqolent及Migration
   共有 categories, products, carts, cartitems 四個table
    
5. composer require laravel/breeze
   使用breeze套件完成使用者登入、註冊、驗證等流程
   調用購物車時也可以直接使用auth()來確認使用者id
   **到user.php中建立與cart的關聯，不然無法讓使用者擁有購物車**

6. 撰寫關聯及table schema
7. MySQL 塞測試資料
    此步可使用db:seeder，但選擇用SQL語法進MySQL server新增
8. php artisan make:controller "controller_name" 建立controller
    共有 ProductController, CartController
    來控制DB行為
    原有多寫update function讓使用者更新自己購買的商品數量
    及CheckoutController控制結帳行為
    後update部分dd無法收到正確資料，放棄
    Checkout流程簡化，直接併入CartController內
9. 擴展layouts.app 撰寫前端
10. 部屬FTP上傳至虛擬主機
    這部分本想使用github pages，但github pages只支援靜態html
    只好申請網域並部屬至虛擬主機上
