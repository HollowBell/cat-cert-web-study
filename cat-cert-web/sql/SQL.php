
<?php
    //users.json 파일을 읽어서 PHP 배열로 바꾸는 함수
    function load_db() {
        $json = file_get_contents("users.json"); // 파일 내용 읽기
        $db = json_decode($json,true); //JSNO 문자열 -> PHP 배열
        return $db;  // 배열 반환
    }

    //PHP 배열을 다시 json 파일에 저장하는 함수
    function save_db($db) {
        $json = json_encode($db,JSON_PRETTY_PRINT); //PHP 배열 -> JSON 문자열
        file_put_contents("users.json",$json);
    }

    //문자열 값을 알맞은 자료형으로 바꾸는 함수
    //id, age는 int로 name은 문자열 그대로 작성
    function change_type($column, $value) {
        $value = trim($value); //앞뒤 공백 제거

        if($column == "id" || $column == "age") {
            return (int)$value; //id, age는 정수형으로 바꿈
        }
        return $value; //문자열은 그냥 작성
    }

    //WEHERE 조건 비교 함수
    //ex) age > 20, id = 1 같은 비교를 처리
    function where_operator($left,$operator,$right) {
        if($operator == "=") {
            return $left == $right;
        }
        else if ($operator == ">") {
            return $left > $right;
        }else if ($operator == "<") {
            return $left < $right;
        }

        return false;
    }

    function truncate() {
        $db = load_db(); //기존 DB 읽기
        $db["data"] = array(); //data 비우기
        save_db($db); //저장
        return "Truncated"; //결과 반환
    }

    function insert($matches) {
        $db = load_db(); //기존 DB 읽기

        //정규식으로 뽑아낸 값을 한 행(row)으로 만들기
        $row = array("id" => (int)$matches[1], "name" => $matches[2],"age" => (int)$matches[3]);
        $db["data"][] = $row; // data 배열 맨 뒤에 행 추가
        save_db($db); //DB 저장
        return "Inserted";
    }

    //SELECT * FROM users;
    //SELECT * FROM users WHERE age > 20;
    function select($matches) {
        $db = load_db(); //기존 DB 읽기
        $result = array(); //조건에 맞는 db저장

        //WHERE 없는 SELECT면 전체 반환
        if (count($matches)==0) {
            return $db["data"];
        }
        //WHERE 있는 경우
        $column = $matches[1]; //id, name, age
        $operator = $matches[2]; // 연산자
        $value = change_type($column,$matches[3]); // 값

        //하나씩 검사해서 조건에 맞는 것을 result에 넣기
        foreach($db["data"] as $row) {
            if(where_operator($row[$column],$operator,$value)){
                $result[] = $row;
            }
        }
        return $result;
    }

    function update($matches) {
        $db = load_db(); //기존 DB 읽기

        //SET부분
        $set_column = $matches[1]; 
        $set_value = change_type($set_column, $matches[2]);

        //WHERE부분
        $where_column = $matches[3];
        $operator = $matches[4];
        $where_value = change_type($where_column,$matches[5]);

        //각 행 돌면서 WEHRE 조건 맞으면 값 수정
        for ($i=0;$i<count($db["data"]);$i++) {
            if(where_operator($db["data"][$i][$where_column],$operator,$where_value)) {
                $db["data"][$i][$set_column] = $set_value;
            }
        }
        save_db($db); //DB갱신
        return "Updated";
    }

    function delete($matches) {
        $db = load_db();
        $new_data = array(); //삭제한 후의 배열

        $column = $matches[1];
        $operator = $matches[2];
        $value = change_type($column, $matches[3]);

        //조건에 맞지 않는 행을 새 배열에 넣기
        foreach($db["data"] as $row) {
            if (!where_operator($row[$column],$operator,$value)) {
                $new_data[] = $row;
            }
        }
        $db["data"] = $new_data;
        save_db($db);

        return "Deleted";
    }

    function execute($query) {
        $query = trim($query); 

        //TRUNCATE
        if (preg_match('/^TRUNCATE\s+users\s*;$/i', $query)) {
            return truncate();
        }

        //INSERT
        if (preg_match('/^INSERT\s+INTO\s+users\s*\(\s*id\s*,\s*name\s*,\s*age\s*\)\s*VALUES\s*\(\s*(\d+)\s*,\s*[\'"]([^\'"]+)[\'"]\s*,\s*(\d+)\s*\)\s*;$/i', $query, $matches)) {
            return insert($matches);
        }

        //WHERE 없는 SELECT
        if (preg_match('/^SELECT\s+\*\s+FROM\s+users\s*;$/i', $query)) {
            return select(array());
        }

        //WHERE 있는 SELECT
        if (preg_match('/^SELECT\s+\*\s+FROM\s+users\s+WHERE\s+(id|name|age)\s*(=|>|<)\s*[\'"]?([^\'\";]+)[\'"]?\s*;$/i', $query, $matches)) {
            return select($matches);
        }

        // UPDATE
        if (preg_match('/^UPDATE\s+users\s+SET\s+(id|name|age)\s*=\s*[\'"]?([^\'\";]+)[\'"]?\s+WHERE\s+(id|name|age)\s*(=|>|<)\s*[\'"]?([^\'\";]+)[\'"]?\s*;$/i', $query, $matches)) {
            return update($matches);
        }

        //DELETE
        if (preg_match('/^DELETE\s+FROM\s+users\s+WHERE\s+(id|name|age)\s*(=|>|<)\s*[\'"]?([^\'\";]+)[\'"]?\s*;$/i', $query, $matches)) {
            return delete($matches);
        }

        
        return "Syntax error";
    }

    
    $result = "";
    $query_text="";

    if(isset($_POST['SQL'])) {
        $query_text = $_POST['SQL'];
        $result = execute($query_text);
    }
?>

    
<html>
    <head><title>SQL</title></head>
    <body>
        <form method="POST">
            <div>
                <h3>SQL 구현 </h3>
                <input type="text" name="SQL" style="width:400px;" value="<?php echo $query_text;?>">
                <input type="submit" name="post" value="전송">
            </div>
        </form>
        <?php
            if($result !== "") {
                echo "<hr>";
                echo "<div>명령어 : ".$query_text."</div>";

                
            if(is_array($result)) {  
                echo "<table border = '1'>";
                echo "<tr>";
                echo "<td>id</td>";
                echo "<td>name</td>";
                echo "<td>age</td>";
                echo "</tr>";

                foreach($result as $row) {
                    echo "<tr>";
                    echo "<td>".$row['id']."</td>";
                    echo "<td>".$row['name']."</td>";
                    echo "<td>".$row['age']."</td>";
                    echo "</tr>";
                    }
                    echo "</table>";
            }
            else {
                echo "<div>결과 : ".$result."</div>";             
                }
            }
        ?>
    </body>
</html>
