<?php
// 데이터베이스 연결 정보 수정
$servername = "localhost";
$username = "cookUser";
$password = "1234";
$dbname = "homPlus";

// TODO: 로그인 로직에 맞게 loggedInUserBusinessNumber 값을 설정하세요.
$loggedInUserBusinessNumber = 8888888; // 예시로 임의의 값을 지정하였습니다.

// 데이터베이스 연결
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("데이터베이스 연결 실패: " . $conn->connect_error);
}

// 폼 데이터가 제출되었을 때
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 폼 데이터 가져오기
    $거래구분 = $_POST["거래구분"];
    $거래날짜 = $_POST["거래날짜"];
    $품목명 = $_POST["품목명"];
    $공급가액 = $_POST["공급가액"];
    $거래처사업자등록번호 = $_POST["거래처사업자등록번호"];
    $상호명 = $_POST["상호명"];
    $대표명 = $_POST["대표명"];
    $주소 = $_POST["주소"];

    // 거래처 사업자등록번호로 계정 정보 조회
    $sql = "SELECT accountBusinessNumber FROM salesAccountsTbl WHERE accountBusinessNumber = '$거래처사업자등록번호'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // 이미 존재하는 거래처 계정일 경우
        $row = $result->fetch_assoc();
        $accountBusinessNumber = $row["accountBusinessNumber"];
    } else {
        // 새로운 거래처 계정 생성
        $sql = "INSERT INTO salesAccountsTbl (accountBusinessNumber, accountName, accountPerson, address, userBusinessNumber) ";
        $sql .= "VALUES ('$거래처사업자등록번호', '$상호명', '$대표명', '$주소', $loggedInUserBusinessNumber)";
        if ($conn->query($sql) === TRUE) {
            $accountBusinessNumber = $거래처사업자등록번호;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
            // 추가적인 처리나 오류 메시지를 표시하는 등 원하는 동작을 수행합니다.
            exit;
        }
    }

    // 거래내역 저장 (먼저 salesAccountsTbl에 값을 저장한 후 salesListTbl에 값을 저장)
    $sql = "INSERT INTO salesListTbl (salesType, salesDate, productName, price, userBusinessNumber, accountBusinessNumber) ";
    $sql .= "VALUES ('$거래구분', '$거래날짜', '$품목명', $공급가액, $loggedInUserBusinessNumber, '$accountBusinessNumber')";
    $sql .= " ON DUPLICATE KEY UPDATE salesType = VALUES(salesType), salesDate = VALUES(salesDate), productName = VALUES(productName), ";
    $sql .= "price = VALUES(price), userBusinessNumber = VALUES(userBusinessNumber), accountBusinessNumber = VALUES(accountBusinessNumber)";
    if ($conn->query($sql) === TRUE) {
        echo "거래내역이 성공적으로 저장되었습니다.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        // 추가적인 처리나 오류 메시지를 표시하는 등 원하는 동작을 수행합니다.
        exit;
    }

    // 저장 버튼 클릭 시 돌아갈 페이지 주소
    $returnUrl = '/_finding.php';
    echo '<script>window.location.href = "'.$returnUrl.'";</script>';
}

// 데이터베이스 연결 종료
$conn->close();
?>


<!DOCTYPE html>
<html>
<head>
  <title>거래내역 입력</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .outer-container {
      width: 100%;
      max-width: 400px;
      padding: 10px;
      margin-top: 20px;
      background-color: #f5f5f5;
    }

    .container {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .form-container {
      width: 100%;
      padding: 20px;
      border: 1px solid #ccc;
      border-radius: 10px;
      background-color: #fff;
    }

    form {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      margin-top: 20px;
    }

    label {
      font-weight: bold;
      font-size: 14px;
      margin-bottom: 5px;
    }

    input[type="text"],
    input[type="date"],
    select {
      width: 100%;
      padding: 8px;
      margin-bottom: 10px;
      font-size: 12px;
    }

    .button-container {
      display: flex;
      justify-content: flex-end;
      margin-top: 20px;
    }

    input[type="submit"],
    input[type="reset"] {
      padding: 10px 16px;
      font-size: 14px;
      margin-left: 10px;
    }
  </style>
  <script>
    window.onload = function() {
      var cancelButton = document.getElementById("cancelButton");
      cancelButton.onclick = function() {
        window.history.back();
      };
    };
  </script>
</head>
<body>
  <div class="outer-container">
    <div class="container">
      <div class="form-container">
        <h2>거래내역 입력</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
          <label for="거래구분">거래구분:</label>
          <div>
            <input type="radio" name="거래구분" value="1" id="매입">
            <label for="매입">매입</label>
            <input type="radio" name="거래구분" value="2" id="매출">
            <label for="매출">매출</label>
          </div>

          <label for="거래날짜">거래날짜:</label>
          <input type="date" name="거래날짜" id="거래날짜">

          <label for="품목명">품목명:</label>
          <input type="text" name="품목명" id="품목명">

          <label for="공급가액">공급가액:</label>
          <input type="text" name="공급가액" id="공급가액">

          <label for="거래처사업자등록번호">거래처 사업자등록번호:</label>
          <input type="text" name="거래처사업자등록번호" id="거래처사업자등록번호">

          <label for="상호명">상호명:</label>
          <input type="text" name="상호명" id="상호명">

          <label for="대표명">대표명:</label>
          <input type="text" name="대표명" id="대표명">

          <label for="주소">주소:</label>
          <input type="text" name="주소" id="주소">

          <div class="button-container">
            <input type="submit" value="저장">
            <input type="reset" value="취소" onclick="window.location.href = '/_finding.php'">
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
