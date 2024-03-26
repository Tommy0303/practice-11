<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品一覧ページ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
        }

        main {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        form {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
        }

        form input, form select, form button {
            margin: 5px;
        }
    </style>
</head>
<body>

    <header>
        <h1>商品一覧ページ</h1>
    </header>

    <main>
        <form id="productForm">
            <input type="text" id="productName" placeholder="商品名" required>
            <input type="number" id="productPrice" placeholder="価格" required>
            <input type="number" id="productStock" placeholder="在庫数" required>
            <input type="text" id="productManufacturer" placeholder="メーカー" required>
            <button type="button" onclick="addProduct()">商品を登録</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>商品画像</th>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>在庫数</th>
                    <th>メーカー</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody id="productList">
                <!-- 商品一覧はここに表示されます -->
            </tbody>
        </table>

        <form id="searchForm">
            <input type="text" id="searchKeyword" placeholder="キーワード">
            <input type="text" id="searchManufacturer" placeholder="メーカー名">
            <button type="button" onclick="searchProducts()">検索</button>
        </form>
    </main>

    <script>
        // 商品データの一覧
        let products = [];

        function addProduct() {
            // 商品データを追加
            const product = {
                id: products.length + 1,
                name: document.getElementById('productName').value,
                price: document.getElementById('productPrice').value,
                stock: document.getElementById('productStock').value,
                manufacturer: document.getElementById('productManufacturer').value,
            };

            products.push(product);
            displayProducts();
            clearForm();
        }

        function editProduct(id) {
            // 商品データを編集
            const newName = prompt('新しい商品名:');
            const newPrice = prompt('新しい価格:');
            const newStock = prompt('新しい在庫数:');
            const newManufacturer = prompt('新しいメーカー:');

            const product = products.find(p => p.id === id);
            if (product) {
                product.name = newName;
                product.price = newPrice;
                product.stock = newStock;
                product.manufacturer = newManufacturer;
                displayProducts();
            }
        }

        function deleteProduct(id) {
            // 商品データを削除
            products = products.filter(p => p.id !== id);
            displayProducts();
        }

        function searchProducts() {
            // 商品データを検索
            const keyword = document.getElementById('searchKeyword').value.toLowerCase();
            const manufacturer = document.getElementById('searchManufacturer').value.toLowerCase();

            const filteredProducts = products.filter(p =>
                p.name.toLowerCase().includes(keyword) &&
                p.manufacturer.toLowerCase().includes(manufacturer)
            );

            displayProducts(filteredProducts);
        }

        function displayProducts(productArray = products) {
            // 商品データを表示
            const productList = document.getElementById('productList');
            productList.innerHTML = '';

            productArray.forEach(product => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${product.id}</td>
                    <td>商品画像</td>
                    <td>${product.name}</td>
                    <td>${product.price}</td>
                    <td>${product.stock}</td>
                    <td>${product.manufacturer}</td>
                    <td>
                        <button type="button" onclick="editProduct(${product.id})">編集</button>
                        <button type="button" onclick="deleteProduct(${product.id})">削除</button>
                    </td>
                `;
                productList.appendChild(row);
            });
        }

        function clearForm() {
            // フォームをクリア
            document.getElementById('productName').value = '';
            document.getElementById('productPrice').value = '';
            document.getElementById('productStock').value = '';
            document.getElementById('productManufacturer').value = '';
        }

        // 初期表示
        displayProducts();
    </script>

</body>
</html>