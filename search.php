<!DOCTYPE html>
<html>
<head>
    <title>조회 결과</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>조회 결과</h1>
    <?php
    // 입력된 데이터 받아오기
    $transactionType = $_POST['transaction_type'];
    $period = $_POST['period'];
    $companyName = $_POST['company_name'];
    $ceoName = $_POST['ceo_name'];
    $minAmount = $_POST['min_amount'];
    $maxAmount = $_POST['max_amount'];

    // 데이터베이스에서 결과 조회 및 처리
    // 여기에서는 가상의 결과를 생성하여 표시합니다.
    // 실제로는 데이터베이스 쿼리 등을 사용하여 처리해야 합니다.
    $results = array(
        array('거래일', '거래유형', '상호명', '대표자명', '거래금액'),
        array('2023-06-01', '매출', 'ABC 상사', '홍길동', '100,000원'),
        array('2023-06-02', '매입', 'XYZ 주식
