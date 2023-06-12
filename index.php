<!DOCTYPE html>
<html>
<head>
    <META http-equiv="content-type" content="text/html; charset=utf-8">

    <title>조회 페이지</title>
    <style>
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 50px;
        }

        .search-box {
            border: 1px solid #ccc;
            padding: 20px;
            margin-bottom: 20px;
        }

        .search-box label {
            display: block;
            margin-bottom: 10px;
        }

        .search-box input[type="text"],
        .search-box input[type="number"] {
            width: 200px;
            margin-bottom: 10px;
        }

        .search-box input[type="submit"] {
            padding: 10px 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        .delete-btn {
            padding: 5px 10px;
            background-color: red;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="search-box">
            <h1>조회 페이지</h1>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <label>
                    <input type="radio" name="transaction_type" value="매출" <?php if (isset($_POST['transaction_type']) && $_POST['transaction_type'] === '매출') echo 'checked'; ?>> 매출
                </label>
                <label>
                    <input type="radio" name="transaction_type" value="매입" <?php if (isset($_POST['transaction_type']) && $_POST['transaction_type'] === '매입') echo 'checked'; ?>> 매입
                </label>
                <br><br>
                조회 기간:
                <input type="date" name="start_date" value="<?php if (isset($_POST['start_date'])) echo $_POST['start_date']; else echo date('Y-m-d'); ?>"> ~
                <input type="date" name="end_date" value="<?php if (isset($_POST['end_date'])) echo $_POST['end_date']; else echo date('Y-m-d'); ?>">
                <br><br>
                회원사업자등록번호: <input type="text" name="company_name" value="<?php if (isset($_POST['company_name'])) echo $_POST['company_name']; ?>">
                <br><br>
                품목명: <input type="text" name="item_name" value="<?php if (isset($_POST['item_name'])) echo $_POST['item_name']; ?>">
                <br><br>
                거래금액 범위:
                <input type="number" name="min_amount" placeholder="최소 금액" value="<?php if (isset($_POST['min_amount'])) echo $_POST['min_amount']; ?>">
                ~
                <input type="number" name="max_amount" placeholder="최대 금액" value="<?php if (isset($_POST['max_amount'])) echo $_POST['max_amount']; ?>">
                <br><br>
                <input type="submit" name="search" value="조회하기">
            </form>
        </div>

        <?php
        // MySQL 연결 설정
	    $servername = "localhost";
        $username = "cookUser";
        $password = "1234";
        $dbname = "homPlus";
        // MySQL 연결
        $conn = new mysqli($servername, $username, $password, $dbname);

        // 연결 확인
        if ($conn->connect_error) {
            die("MySQL 연결 실패: " . $conn->connect_error);
        }

        // 삭제 버튼이 클릭되었을 때
        if (isset($_POST['delete']) && isset($_POST['delete_sales_number'])) {
            $delete_sales_number = $_POST['delete_sales_number'];

            // 삭제 쿼리 실행
            $delete_query = "DELETE FROM saleslisttbl WHERE salesNumber = $delete_sales_number";
            $conn->query($delete_query);
        }

        if (isset($_POST['search']) && isset($_POST['transaction_type'])) {
            // Retrieve the form inputs
            $transaction_type = $_POST['transaction_type'];
            $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
            $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
            $company_name = $_POST['company_name'];
            $item_name = $_POST['item_name'];
            $min_amount = $_POST['min_amount'];
            $max_amount = $_POST['max_amount'];

            // Build the query based on the form inputs
            $query = "SELECT salesNumber, salesDate, salesType, accountBusinessNumber, productName, price
                      FROM saleslisttbl 
                      WHERE salesType = " . ($transaction_type === "매출" ? "2" : "1");

            if (!empty($start_date) && !empty($end_date)) {
                $query .= " AND salesDate BETWEEN '$start_date' AND '$end_date'";
            }
            if (!empty($company_name)) {
                $query .= " AND accountBusinessNumber LIKE '%$company_name%'";
            }
            if (!empty($item_name)) {
                $query .= " AND productName LIKE '%$item_name%'";
            }
            if (!empty($min_amount)) {
                $query .= " AND price >= $min_amount";
            }
            if (!empty($max_amount)) {
                $query .= " AND price <= $max_amount";
            }

            // Execute the query
            $results = $conn->query($query);
            $rows = $results->fetch_all();
        }
        ?>

       <?php if (isset($_POST['search']) && isset($_POST['transaction_type']) && !empty($rows)): ?>
            <h2><?php echo $_POST['transaction_type']; ?> 조회 결과</h2>
            <table>
                <tr>
                    <th>거래일</th>
                    <th>회원사업자등록번호</th>
                    <th>품목명</th>
                    <th>거래금액</th>
                    <th>삭제</th>
                </tr>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?php echo $row[1]; ?></td>
                        <td><?php echo $row[3]; ?></td>
                        <td><?php echo $row[4]; ?></td>
                        <td><?php echo $row[5]; ?></td>
                        <td>
                            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <input type="hidden" name="delete_sales_number" value="<?php echo $row[0]; ?>">
                                <input class="delete-btn" type="submit" name="delete" value="삭제">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php elseif (isset($_POST['search']) && isset($_POST['transaction_type'])): ?>
            <p>No results found.</p>
        <?php endif; ?> 
    </div>
</body>
</html>
