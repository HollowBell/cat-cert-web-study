<?php
    
    
    $words = [];

    
    if(isset($_POST['save'])) {
        if(!empty($_POST['words'])) {
            $words = explode(',',$_POST['words']);
        }
        setcookie("save", implode(',', $words), time()+60*10);;
        echo "<script>alert('저장!')</script>";
    }

    else if(isset($_POST['load'])) {
        if(!empty($_COOKIE['save'])) {
            $words=explode(',',$_COOKIE['save']);
            echo "<script>alert('로드!')</script>";
        } else {
            if(!empty($_POST['words'])) {
                $words = explode(',',$_POST['words']);
            } 
            echo "<script>alert('로드 실패!')</script>";
        }

    }

    else if(isset($_POST['clear'])) {
        $words = [];
    }

   else if(isset($_POST['post'])) {
        if(!empty($_POST['words'])) {
            $words = explode(',',$_POST['words']);
        }
        
        $str = $_POST['post'];
        
        if (empty($words)) {
            $words[] = $str;
        } else {
            $lastWord = end($words);
            $end = mb_substr($lastWord,-1,1,"UTF-8");
            $first = mb_substr($str,0,1,"UTF-8");

            if ($end === $first) {
                $words[] = $str;
            } else {
                echo "<script> alert('잘못된 입력입니다') </script>";
            } 

            
        }
    
    while(count($words) > 5) {
        array_shift($words);
        }    
    } 
    ?>
<html>
    <head><title>끝말잇기</title>
    <link rel="stylesheet" type="text/css" href="끝말잇기.css">
    </head>
    
    <body>
        <h2> 끝말잇기 </h2>
        <?php
            if(!empty($words)) {
            $lastWord = end($words);
            $end = mb_substr($lastWord,-1,1,"UTF-8");
            echo "<h3>".$end."</h3>";
            } else {
                echo "<h3>첫 단어 입력하세요<h3>";
            }
        ?>
        <form method="POST">
            <input type="hidden" name="words" value="<?php echo implode(',', $words) ?>">
            <input type="text" name="post">
            <input type="submit" value="전송">
            <input type="submit" name="save" value="저장">
            <input type="submit" name="load" value="불러오기">
            <input type="submit" name="clear" value="초기화">
            <div class="list">
            <?php
                foreach($words as $word) {
                    echo "<span class='word'>" . $word . "</span>";
                }
            ?>
            </div>
        </form>
    </body>
</html>

