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
            position: relative;
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
            font-size: 16px;
            font-family: Arial, sans-serif;
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

        .logout-btn {
            padding: 10px;
            background-color: #FF0000;
            color: #fff;
            border: none;
            cursor: pointer;
            position: absolute;
            bottom: 10px;
            right: 10px;
        }

        .modify-btn {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        .modify-btn:hover {
            text-decoration: none;
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
                <input type="date" name="start_date" value="<?php echo isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d'); ?>"> ~
                <input type="date" name="end_date" value="<?php echo isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-d'); ?>">
                <br><br>
                회원사업자등록번호: <input type="text" name="company_name" value="<?php echo isset($_POST['company_name']) ? $_POST['company_name'] : ''; ?>">
                <br><br>
                상호명: <input type="text" name="account_name" value="<?php echo isset($_POST['account_name']) ? $_POST['account_name'] : ''; ?>">
                <br><br>
                품목명: <input type="text" name="item_name" value="<?php echo isset($_POST['item_name']) ? $_POST['item_name'] : ''; ?>">
                <br><br>
                거래금액 범위:
                <input type="number" name="min_amount" placeholder="최소 금액" value="<?php echo isset($_POST['min_amount']) ? $_POST['min_amount'] : ''; ?>">
                ~
                <input type="number" name="max_amount" placeholder="최대 금액" value="<?php echo isset($_POST['max_amount']) ? $_POST['max_amount'] : ''; ?>">
                <br><br>
                <input type="submit" name="search" value="조회하기">
                <a class="modify-btn" href="https://7ca7-223-38-81-187.ngrok-free.app/submit_transaction.php">거래 내역 입력</a>
            </form>
            <form method="post" action="_signin.php">
                <input class="logout-btn" type="submit" name="logout" value="로그아웃">
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
            $delete_query = "DELETE FROM saleslisttbl WHERE salesNumber = '$delete_sales_number'";
            $conn->query($delete_query);
        }

        session_start();
        if (isset($_SESSION['userBusinessNumber'])) {
            $userBusinessNumber = $_SESSION['userBusinessNumber'];

            if (isset($_POST['search']) && isset($_POST['transaction_type'])) {
                // Retrieve the form inputs
                $transaction_type = $_POST['transaction_type'];
                $start_date = $_POST['start_date'];
                $end_date = $_POST['end_date'];
                $company_name = $_POST['company_name'];
                $account_name = $_POST['account_name'];
                $item_name = $_POST['item_name'];
                $min_amount = $_POST['min_amount'];
                $max_amount = $_POST['max_amount'];

                // Build the query based on the form inputs
                $query = "SELECT sl.salesNumber, sl.salesDate, sl.salesType, sl.accountBusinessNumber, sa.accountName, sl.productName, sl.price
                          FROM saleslisttbl AS sl
                          INNER JOIN salesaccountstbl AS sa ON sl.accountBusinessNumber = sa.accountBusinessNumber
                          WHERE sl.salesType = " . ($transaction_type === "매출" ? "2" : "1") . " AND sl.userBusinessNumber = '$userBusinessNumber'";

                if (!empty($start_date) && !empty($end_date)) {
                    $query .= " AND sl.salesDate BETWEEN '$start_date' AND '$end_date'";
                }
                if (!empty($company_name)) {
                    $query .= " AND sl.accountBusinessNumber LIKE '%$company_name%'";
                }
                if (!empty($account_name)) {
                    $query .= " AND sa.accountName LIKE '%$account_name%'";
                }
                if (!empty($item_name)) {
                    $query .= " AND sl.productName LIKE '%$item_name%'";
                }
                if (!empty($min_amount)) {
                    $query .= " AND sl.price >= $min_amount";
                }
                if (!empty($max_amount)) {
                    $query .= " AND sl.price <= $max_amount";
                }

                // Execute the query
                $results = $conn->query($query);
                $rows = $results->fetch_all();
            }
        } else {
            echo '<p class="error-msg">로그인 후에 조회할 수 있습니다.</p>';
        }
        ?>

        <?php if (isset($_SESSION['userBusinessNumber']) && isset($_POST['search']) && isset($_POST['transaction_type']) && !empty($rows)): ?>
            <h2><?php echo $_POST['transaction_type']; ?> 조회 결과</h2>
            <table>
                <tr>
                    <th>거래일</th>
                    <th>회원사업자등록번호</th>
                    <th>상호명</th>
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
                        <td><?php echo $row[6]; ?></td>
                        <td>
                            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <input type="hidden" name="delete_sales_number" value="<?php echo $row[0]; ?>">
                                <input class="delete-btn" type="submit" name="delete" value="삭제">
                                <!-- 이전 검색 조건 유지 -->
                                <input type="hidden" name="transaction_type" value="<?php echo $prev_transaction_type; ?>">
                                <input type="hidden" name="start_date" value="<?php echo $prev_start_date; ?>">
                                <input type="hidden" name="end_date" value="<?php echo $prev_end_date; ?>">
                                <input type="hidden" name="company_name" value="<?php echo $prev_company_name; ?>">
                                <input type="hidden" name="account_name" value="<?php echo $prev_account_name; ?>">
                                <input type="hidden" name="item_name" value="<?php echo $prev_item_name; ?>">
                                <input type="hidden" name="min_amount" value="<?php echo $prev_min_amount; ?>">
                                <input type="hidden" name="max_amount" value="<?php echo $prev_max_amount; ?>">
                                <input type="hidden" name="search" value="조회하기">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php elseif (isset($_SESSION['userBusinessNumber']) && isset($_POST['search']) && isset($_POST['transaction_type'])): ?>
            <p>No results found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
